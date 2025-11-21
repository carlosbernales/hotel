<?php
require_once 'db.php';

// Default passwords for existing users
$default_passwords = [
    'admin@example.com' => 'adminpassword',
    'frontdesk@example.com' => 'frontdesk123',
    'cashier@example.com' => 'cashier123'
];

try {
    // Update each user's actual_password
    foreach ($default_passwords as $email => $password) {
        $query = "UPDATE userss SET actual_password = ? WHERE email = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();
    }

    // For any remaining users without an actual_password, set it to their email username
    $query = "UPDATE userss SET actual_password = SUBSTRING_INDEX(email, '@', 1) WHERE actual_password IS NULL";
    mysqli_query($con, $query);

    echo "Passwords updated successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 