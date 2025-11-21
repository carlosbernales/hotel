<?php

class AuthManager
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function validateLogin($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM userss WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function startSession($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['email'] = $user['email'];

        // Generate and store CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        session_unset();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function getUserRole()
    {
        return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
    }

    public function validateCSRF($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function redirectBasedOnRole()
    {
        if (!$this->isLoggedIn()) {
            return;
        }

        // Debug log to check what role is being used for redirection
        error_log("Redirecting user with role: " . $this->getUserRole());

        switch ($this->getUserRole()) {
            case 'admin':
                header("Location: ../../../Admin/index.php?dashboard");  // Use absolute path to Admin directory
                break;
            case 'frontdesk':
                header("Location: /Admin/Frontdesk/index.php?dashboard");
                break;
            case 'cashier':
                header("Location: /Admin/Cashier/index.php?POS");
                break;
            case 'customer':
                header("Location: index.php");  // Stay in current directory
                break;
            default:
                header("Location: /login.php");
        }
        exit();
    }

    public function register($firstname, $lastname, $phone, $email, $password)
    {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM userss WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("Email already exists");
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user with is_verified = 1
            $stmt = $this->db->prepare("
                INSERT INTO userss (
                    first_name, last_name, contact_number, email, password, 
                    user_type, is_verified
                ) VALUES (?, ?, ?, ?, ?, 'customer', 1)
            ");

            $stmt->bind_param(
                "sssss",
                $firstname,
                $lastname,
                $phone,
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to create account");
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            throw $e;
        }
    }

    public function createAdminUser($firstname, $lastname, $phone, $email, $password)
    {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM userss WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                throw new Exception("Email already exists");
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin user with is_verified = 1
            $stmt = $this->db->prepare("
                INSERT INTO userss (
                    first_name, last_name, contact_number, email, password, 
                    actual_password, user_type, is_verified
                ) VALUES (?, ?, ?, ?, ?, ?, 'admin', 1)
            ");

            $stmt->bind_param(
                "ssssss",
                $firstname,
                $lastname,
                $phone,
                $email,
                $hashed_password,
                $password  // Store actual password for admin visibility
            );

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to create admin account");
            }
        } catch (Exception $e) {
            error_log("Admin creation error: " . $e->getMessage());
            throw $e;
        }
    }
}
?>