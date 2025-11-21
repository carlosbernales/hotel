<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'edit_payment_method':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $display_name = $_POST['display_name'];
            $account_name = $_POST['account_name'];
            $account_number = $_POST['account_number'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // Check if a new QR code image was uploaded
            if (!empty($_FILES['qr_code_image']['name'])) {
                $target_dir = "uploads/payment_qr_codes/";
                
                // Create directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                // Get file extension
                $file_extension = strtolower(pathinfo($_FILES['qr_code_image']['name'], PATHINFO_EXTENSION));
                
                // Generate unique filename
                $new_filename = $name . '_qr_' . time() . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;

                // Check if file is an actual image
                $check = getimagesize($_FILES['qr_code_image']['tmp_name']);
                if ($check === false) {
                    $_SESSION['error_message'] = "File is not an image.";
                    header('Location: Paymentss.php');
                    exit();
                }

                // Check file size (5MB limit)
                if ($_FILES['qr_code_image']['size'] > 5000000) {
                    $_SESSION['error_message'] = "File is too large. Maximum size is 5MB.";
                    header('Location: Paymentss.php');
                    exit();
                }

                // Allow certain file formats
                if ($file_extension != "jpg" && $file_extension != "jpeg" && $file_extension != "png") {
                    $_SESSION['error_message'] = "Only JPG, JPEG & PNG files are allowed.";
                    header('Location: Paymentss.php');
                    exit();
                }

                // Upload file
                if (move_uploaded_file($_FILES['qr_code_image']['tmp_name'], $target_file)) {
                    // Update database with new image path
                    $stmt = $con->prepare("UPDATE payment_methods SET 
                        display_name = ?, 
                        account_name = ?, 
                        account_number = ?, 
                        qr_code_image = ?,
                        is_active = ? 
                        WHERE id = ?");
                    $stmt->bind_param("ssssii", $display_name, $account_name, $account_number, $target_file, $is_active, $id);
                } else {
                    $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
                    header('Location: Paymentss.php');
                    exit();
                }
            } else {
                // Update without changing the QR code image
                $stmt = $con->prepare("UPDATE payment_methods SET 
                    display_name = ?, 
                    account_name = ?, 
                    account_number = ?, 
                    is_active = ? 
                    WHERE id = ?");
                $stmt->bind_param("sssii", $display_name, $account_name, $account_number, $is_active, $id);
            }

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Payment method updated successfully!";
            } else {
                $_SESSION['error_message'] = "Error updating payment method. Please try again.";
            }
            break;
    }
}

header('Location: Paymentss.php');
exit(); 