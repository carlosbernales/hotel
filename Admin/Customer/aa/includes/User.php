<?php
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Mailer.php';

class User {
    private $db;
    private $conn;

    public function __construct() {
        try {
            $this->conn = get_database_connection();
            $this->createUsersTable();
        } catch (Exception $e) {
            error_log("Error initializing User: " . $e->getMessage());
            throw $e;
        }
    }

    private function createUsersTable() {
        $query = "CREATE TABLE IF NOT EXISTS userss (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            contact_number VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            user_type VARCHAR(20) DEFAULT 'customer',
            is_verified BOOLEAN DEFAULT FALSE,
            verification_code VARCHAR(6),
            verification_expiry DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        try {
            $this->conn->exec($query);
        } catch (PDOException $e) {
            throw new Exception("Error creating users table: " . $e->getMessage());
        }
    }

    public function register($firstname, $lastname, $phone, $email, $password) {
        try {
            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT id FROM userss WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception("Email already registered");
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Generate verification code
            $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpiry = date('Y-m-d H:i:s', strtotime('+2 minutes'));

            // Insert new user with verification code
            $stmt = $this->conn->prepare("
                INSERT INTO userss (first_name, last_name, contact_number, email, password, user_type, verification_code, verification_expiry)
                VALUES (?, ?, ?, ?, ?, 'customer', ?, ?)
            ");

            $success = $stmt->execute([$firstname, $lastname, $phone, $email, $hashedPassword, $verificationCode, $verificationExpiry]);

            if ($success) {
                // Send verification email
                $this->sendVerificationEmail($email, $verificationCode);
            }

            return $success;
        } catch (PDOException $e) {
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    private function sendVerificationEmail($email, $code) {
        try {
            // Create instance of Mailer class
            $mailer = new Mailer();
            
            // Send verification email using Mailer class
            return $mailer->sendVerificationCode($email, $code);
            
        } catch (Exception $e) {
            error_log("Error in sendVerificationEmail: " . $e->getMessage());
            throw $e;
        }
    }

    public function resendVerificationCode($email) {
        try {
            // Check if user exists and needs verification
            $stmt = $this->conn->prepare("SELECT id, verification_code FROM userss WHERE email = ? AND is_verified = 0");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('No pending verification found for this email');
            }

            // Generate new verification code
            $verificationCode = sprintf('%06d', mt_rand(0, 999999));
            $verificationExpiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            // Update verification code in database
            $stmt = $this->conn->prepare("UPDATE userss SET verification_code = ?, verification_expiry = ? WHERE email = ?");
            $success = $stmt->execute([$verificationCode, $verificationExpiry, $email]);

            if ($success) {
                // Use the Mailer class to send the email
                $mailer = new Mailer();
                if ($mailer->sendVerificationCode($email, $verificationCode)) {
                    Session::set('pending_verification_email', $email);
                    return true;
                } else {
                    throw new Exception('Failed to send verification email');
                }
            }

            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function verifyCode($email, $code) {
        try {
            $sql = "SELECT verification_code, verification_expiry FROM userss WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception("User not found");
            }

            // Check if code is expired
            if (strtotime($user['verification_expiry']) < time()) {
                throw new Exception("Verification code has expired. Please request a new one.");
            }

            // Verify the code
            if ($user['verification_code'] === $code) {
                // Update user as verified
                $sql = "UPDATE userss SET is_verified = 1 WHERE email = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$email]);
            }

            return false;
        } catch (PDOException $e) {
            throw new Exception("Verification failed: " . $e->getMessage());
        }
    }

    public function regenerateVerificationCode($email) {
        try {
            // Generate new 6-digit code
            $code = sprintf("%06d", mt_rand(0, 999999));
            
            // Set expiry time (30 minutes from now)
            $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            // Update database with new code and expiry
            $sql = "UPDATE userss SET verification_code = ?, verification_expiry = ? WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([$code, $expiry, $email])) {
                // Send new verification email
                $mailer = new Mailer();
                return $mailer->sendVerificationCode($email, $code);
            }
            
            return false;
        } catch (PDOException $e) {
            throw new Exception("Failed to regenerate verification code: " . $e->getMessage());
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, email, password, is_verified, first_name, last_name, user_type 
                FROM userss 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception("Invalid email or password");
            }

            if (!password_verify($password, $user['password'])) {
                throw new Exception("Invalid email or password");
            }

            // Check if email is verified
            if (!$user['is_verified']) {
                throw new Exception("Please verify your email before logging in");
            }

            return $user;
        } catch (PDOException $e) {
            throw new Exception("Login failed: " . $e->getMessage());
        }
    }

    public function storeVerificationCode($email, $code) {
        $sql = "INSERT INTO verification_codes (email, code, expires_at) 
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 2 MINUTE))
                ON DUPLICATE KEY UPDATE 
                code = VALUES(code), 
                expires_at = VALUES(expires_at)";
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$email, $code]);
    }

    public function initiatePasswordReset($email) {
        try {
            // Check if user exists
            $stmt = $this->conn->prepare("SELECT id FROM userss WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Store reset token
                $stmt = $this->conn->prepare("
                    UPDATE userss 
                    SET reset_token = ?, 
                        reset_token_expires = ? 
                    WHERE email = ?
                ");
                $stmt->execute([$token, $expires, $email]);

                // Generate reset link with correct path
                $resetLink = sprintf(
                    "%s://%s/Capstone/reset-password.php?token=%s&email=%s",
                    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                    $_SERVER['SERVER_NAME'],
                    urlencode($token),
                    urlencode($email)
                );
                
                // Send reset email
                $mailer = new Mailer();
                $subject = "Password Reset Request - Casa Cafe";
                $message = "Hello,\n\n";
                $message .= "You have requested to reset your password. Click the link below to reset it:\n\n";
                $message .= $resetLink . "\n\n";
                $message .= "This link will expire in 1 hour.\n\n";
                $message .= "If you didn't request this, please ignore this email.\n\n";
                $message .= "Best regards,\nCasa Cafe Team";

                return $mailer->sendEmail($email, $subject, $message);
            }

            // Always return true even if email doesn't exist (security best practice)
            return true;
        } catch (Exception $e) {
            error_log("Password reset initiation failed: " . $e->getMessage());
            throw new Exception("Failed to process password reset request");
        }
    }

    public function resetPassword($email, $token, $newPassword) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id 
                FROM userss 
                WHERE email = ? 
                AND reset_token = ? 
                AND reset_token_expires > NOW()
                AND is_verified = 1
            ");
            $stmt->execute([$email, $token]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception("Invalid or expired reset link");
            }

            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password and clear reset token
            $stmt = $this->conn->prepare("
                UPDATE userss 
                SET password = ?,
                    reset_token = NULL,
                    reset_token_expires = NULL
                WHERE id = ?
            ");
            
            return $stmt->execute([$hashedPassword, $user['id']]);
        } catch (Exception $e) {
            error_log("Password reset failed: " . $e->getMessage());
            throw new Exception("Failed to reset password");
        }
    }
}
