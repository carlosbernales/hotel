<?php
require_once 'Database.php';

class VerificationMethod {
    private $db;

    public function __construct() {
        try {
            $this->db = new Database();
            
            // Check if verification_codes table exists
            $tableExists = $this->db->query("SHOW TABLES LIKE 'verification_codes'")->rowCount() > 0;
            if (!$tableExists) {
                $this->createVerificationCodesTable();
            }
            
            // Check if verification_methods table exists
            $methodsTableExists = $this->db->query("SHOW TABLES LIKE 'verification_methods'")->rowCount() > 0;
            if (!$methodsTableExists) {
                $this->createVerificationMethodsTable();
            }
        } catch (Exception $e) {
            error_log("VerificationMethod initialization error: " . $e->getMessage());
            throw new Exception("Service temporarily unavailable");
        }
    }

    public function getAllMethods() {
        try {
            // First check if table exists
            $tableExists = $this->db->query("SHOW TABLES LIKE 'verification_methods'")->rowCount() > 0;
            
            if (!$tableExists) {
                // Create table if it doesn't exist
                $this->createVerificationMethodsTable();
            }

            $stmt = $this->db->prepare("SELECT * FROM verification_methods");
            $stmt->execute();
            $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($methods)) {
                // Insert default methods if table is empty
                $this->insertDefaultMethods();
                $stmt = $this->db->prepare("SELECT * FROM verification_methods");
                $stmt->execute();
                $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $methods;
        } catch (PDOException $e) {
            error_log("Error getting verification methods: " . $e->getMessage());
            throw new Exception("Unable to retrieve verification methods");
        }
    }

    private function createVerificationMethodsTable() {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS verification_methods (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    method_name VARCHAR(50) NOT NULL,
                    is_active BOOLEAN DEFAULT TRUE,
                    maintenance_message TEXT,
                    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
        } catch (PDOException $e) {
            error_log("Error creating verification_methods table: " . $e->getMessage());
            throw new Exception("Unable to initialize verification system");
        }
    }

    private function insertDefaultMethods() {
        try {
            $this->db->exec("
                INSERT INTO verification_methods (method_name, is_active, maintenance_message) VALUES
                ('email', true, 'Email verification is currently under maintenance. Please try phone verification.'),
                ('phone', true, 'Phone verification is currently under maintenance. Please try email verification.')
            ");
        } catch (PDOException $e) {
            error_log("Error inserting default verification methods: " . $e->getMessage());
            throw new Exception("Unable to initialize verification methods");
        }
    }

    public function getActiveMethods() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM verification_methods WHERE is_active = true");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting active verification methods: " . $e->getMessage());
            throw new Exception("Unable to retrieve active verification methods");
        }
    }

    public function getMethodStatus($methodName) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM verification_methods WHERE method_name = ?");
            $stmt->execute([$methodName]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting verification method status: " . $e->getMessage());
            throw new Exception("Unable to check verification method status");
        }
    }

    public function updateMethodStatus($methodName, $isActive, $message = null) {
        try {
            $stmt = $this->db->prepare("UPDATE verification_methods SET is_active = ?, maintenance_message = ? WHERE method_name = ?");
            $success = $stmt->execute([$isActive, $message, $methodName]);
            
            if (!$success) {
                throw new Exception("Failed to update verification method status");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error updating verification method status: " . $e->getMessage());
            throw new Exception("Unable to update verification method status");
        }
    }

    private function createVerificationCodesTable() {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS verification_codes (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NULL,
                    email VARCHAR(255) NULL,
                    phone VARCHAR(20) NULL,
                    code VARCHAR(6) NOT NULL,
                    type ENUM('email', 'phone') NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    expires_at TIMESTAMP NULL,
                    is_used BOOLEAN DEFAULT FALSE,
                    INDEX (email),
                    INDEX (phone),
                    INDEX (code)
                )
            ");
        } catch (PDOException $e) {
            error_log("Error creating verification_codes table: " . $e->getMessage());
            throw new Exception("Unable to initialize verification system");
        }
    }

    public function createVerificationCode($type, $identifier, $user_id = null) {
        try {
            // Generate a random 6-digit code
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Set expiration time (2 minutes from now)
            $expiresAt = date('Y-m-d H:i:s', strtotime('+2 minutes'));
            
            $stmt = $this->db->prepare("
                INSERT INTO verification_codes 
                (code, type, expires_at, " . ($type === 'email' ? 'email' : 'phone') . ", user_id) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$code, $type, $expiresAt, $identifier, $user_id]);
            
            return $code;
        } catch (PDOException $e) {
            error_log("Error creating verification code: " . $e->getMessage());
            throw new Exception("Unable to generate verification code");
        }
    }

    public function verifyCode($type, $identifier, $code) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM verification_codes 
                WHERE " . ($type === 'email' ? 'email' : 'phone') . " = ?
                AND code = ? 
                AND type = ?
                AND is_used = FALSE
                AND expires_at > NOW()
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            
            $stmt->execute([$identifier, $code, $type]);
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($verification) {
                // Mark code as used
                $updateStmt = $this->db->prepare("
                    UPDATE verification_codes 
                    SET is_used = TRUE 
                    WHERE id = ?
                ");
                $updateStmt->execute([$verification['id']]);
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error verifying code: " . $e->getMessage());
            throw new Exception("Unable to verify code");
        }
    }
} 