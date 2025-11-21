<?php
// Set content type to JSON
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error logging to a file
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require_once 'db_con.php';

// Function to send JSON response and exit
function sendResponse($status, $message, $data = []) {
    error_log("Sending response - Status: $status, Message: $message");
    $response = [
        'status' => $status,
        'message' => $message
    ];
    
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit();
}

// Log the request data
error_log("=== New Request ===");
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        error_log("Error: User not logged in");
        sendResponse('error', 'Please login first');
    }

    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("Error: Invalid CSRF token");
        error_log("POST token: " . ($_POST['csrf_token'] ?? 'not set'));
        error_log("SESSION token: " . ($_SESSION['csrf_token'] ?? 'not set'));
        sendResponse('error', 'Invalid request');
    }

    // Validate and sanitize input
    $user_id = $_SESSION['user_id'];
    $firstname = filter_var(trim($_POST['firstname'] ?? ''), FILTER_SANITIZE_STRING);
    $lastname = filter_var(trim($_POST['lastname'] ?? ''), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST['phone'] ?? ''), FILTER_SANITIZE_STRING);

    error_log("Processing update for user ID: $user_id");
    error_log("First Name: $firstname, Last Name: $lastname, Email: $email, Phone: $phone");

    // Basic validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($phone)) {
        error_log("Validation failed: All fields are required");
        sendResponse('error', 'All fields are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Validation failed: Invalid email format");
        sendResponse('error', 'Invalid email format');
    }

    // Initialize profile photo variable
    $profile_photo = null;
    $upload_path = __DIR__ . '/../../uploads/profile/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($upload_path)) {
        if (!mkdir($upload_path, 0777, true) && !is_dir($upload_path)) {
            error_log("Failed to create directory: $upload_path");
            sendResponse('error', 'Failed to create upload directory');
        }
        error_log("Created upload directory: $upload_path");
    }

    // Handle file upload if a new photo was submitted
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        error_log("Processing file upload");
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES['profile_photo']['size'];
        $tmp_name = $_FILES['profile_photo']['tmp_name'];
        
        error_log("Uploaded file - Name: $filename, Type: $filetype, Size: $filesize, Tmp: $tmp_name");
        
        if (!in_array($filetype, $allowed)) {
            error_log("Invalid file type: $filetype");
            sendResponse('error', 'Invalid file type. Only JPG, PNG and GIF allowed');
        }

        // Check file size (max 2MB)
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($filesize > $max_size) {
            error_log("File too large: $filesize bytes");
            sendResponse('error', 'File is too large. Maximum size is 2MB');
        }

        // Generate unique filename
        $new_filename = uniqid('profile_', true) . '.' . $filetype;
        $target_file = $upload_path . $new_filename;
        
        error_log("Attempting to move uploaded file to: $target_file");

        // Move uploaded file
        if (move_uploaded_file($tmp_name, $target_file)) {
            error_log("File uploaded successfully: $new_filename");
            $profile_photo = $new_filename;
            
            try {
                // Get old photo path to delete it later
                $stmt = $pdo->prepare("SELECT profile_photo FROM userss WHERE id = ?");
                $stmt->execute([$user_id]);
                $old_photo = $stmt->fetchColumn();
                
                // Delete old photo if it exists and is different from the new one
                if ($old_photo && $old_photo !== $new_filename) {
                    $old_photo_path = $upload_path . $old_photo;
                    if (file_exists($old_photo_path)) {
                        if (unlink($old_photo_path)) {
                            error_log("Deleted old profile photo: $old_photo");
                        } else {
                            error_log("Failed to delete old profile photo: $old_photo");
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Error handling old photo: " . $e->getMessage());
                // Continue with the update even if old photo deletion fails
            }
        } else {
            $error = error_get_last();
            error_log("Failed to move uploaded file: " . ($error['message'] ?? 'Unknown error'));
            error_log("Upload directory permissions: " . substr(sprintf('%o', fileperms($upload_path)), -4));
            error_log("Is directory writable: " . (is_writable($upload_path) ? 'Yes' : 'No'));
            sendResponse('error', 'Failed to upload profile photo. Please try again.');
        }
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Prepare the SQL query
        if ($profile_photo) {
            $sql = "UPDATE userss SET first_name = ?, last_name = ?, email = ?, contact_number = ?, profile_photo = ? WHERE id = ?";
            $params = [$firstname, $lastname, $email, $phone, $profile_photo, $user_id];
            error_log("Updating user with profile photo");
        } else {
            $sql = "UPDATE userss SET first_name = ?, last_name = ?, email = ?, contact_number = ? WHERE id = ?";
            $params = [$firstname, $lastname, $email, $phone, $user_id];
            error_log("Updating user without profile photo");
        }

        error_log("Executing SQL: $sql");
        error_log("With params: " . print_r($params, true));

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result === false) {
            $error = $stmt->errorInfo();
            throw new Exception("Database error: " . ($error[2] ?? 'Unknown error'));
        }

        // Check if any rows were affected
        $rowCount = $stmt->rowCount();
        error_log("Rows affected: $rowCount");
        
        if ($rowCount === 0) {
            // No rows updated, but this might be okay if the data is the same
            error_log("No rows were updated - data might be the same");
            // We'll continue and return success since this might not be an error
        }
        
        // Commit transaction
        $pdo->commit();
        error_log("Transaction committed successfully");
        
        // Return success response with updated data
        $response_data = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => $phone
        ];
        
        if ($profile_photo) {
            $response_data['profile_photo'] = $profile_photo;
        }
        
        error_log("Sending success response");
        sendResponse('success', 'Profile updated successfully', $response_data);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            error_log("Transaction rolled back due to error");
        }
        error_log("Error in transaction: " . $e->getMessage());
        throw $e;
    }

} catch (PDOException $e) {
    $error = $e->getMessage();
    error_log("Database error: $error");
    sendResponse('error', 'A database error occurred. Please try again.');
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Error: $error");
    sendResponse('error', 'An error occurred: ' . $error);
}
?>
