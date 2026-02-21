<?php
require_once 'db_con.php';

class VerificationManager {
    private $db;
    private const EXPIRY_MINUTES = 15; // Verification code expires in 15 minutes

    public function __construct($db) {
        $this->db = $db;
    }

    public function generateCode() {
        return sprintf("%06d", mt_rand(0, 999999));
    }

    public function createVerification($email, $code, $type = 'email') {
        try {
            // Calculate expiry time (15 minutes from now)
            $expiry = date('Y-m-d H:i:s', strtotime('+' . self::EXPIRY_MINUTES . ' minutes'));
            
            // First, invalidate any existing codes for this email/phone
            $sql = "UPDATE verification_codes SET used = 1 WHERE email = ? AND used = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);

            // Insert new verification code
            $sql = "INSERT INTO verification_codes (email, code, expiry, created_at) 
                   VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([$email, $code, $expiry]);

            if (!$success) {
                error_log("Failed to insert verification code for $email");
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Error creating verification: " . $e->getMessage());
            return false;
        }
    }

    public function verifyCode($email, $code) {
        try {
            $sql = "SELECT * FROM verification_codes 
                   WHERE email = ? 
                   AND code = ? 
                   AND used = 0 
                   AND expiry > NOW() 
                   ORDER BY created_at DESC 
                   LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email, $code]);
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($verification) {
                // Mark code as used
                $updateSql = "UPDATE verification_codes SET used = 1 WHERE id = ?";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([$verification['id']]);
                return true;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error verifying code: " . $e->getMessage());
            return false;
        }
    }

    public function isCodeExpired($email) {
        try {
            $sql = "SELECT * FROM verification_codes 
                   WHERE email = ? 
                   AND used = 0 
                   AND expiry > NOW() 
                   ORDER BY created_at DESC 
                   LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            return !$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error checking code expiry: " . $e->getMessage());
            return true;
        }
    }

    public function getActiveCode($email) {
        try {
            $sql = "SELECT * FROM verification_codes 
                   WHERE email = ? 
                   AND used = 0 
                   AND expiry > NOW() 
                   ORDER BY created_at DESC 
                   LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['code'] : null;
        } catch (Exception $e) {
            error_log("Error getting active code: " . $e->getMessage());
            return null;
        }
    }
} 