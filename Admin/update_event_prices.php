<?php
require_once 'db.php';

// Package price mapping
$package_prices = [
    '30 PAX' => [
        'Package A' => 28000.00,
        'Package B' => 33000.00,
        'Package C' => 46000.00
    ],
    '50 PAX' => [
        'Package A' => 47500.00,
        'Package B' => 55000.00,
        'Package C' => 76800.00
    ]
];

// Get all event bookings with zero or null package_price
$query = "SELECT id, package_name, package_type FROM event_bookings WHERE package_price = 0 OR package_price IS NULL";
$result = mysqli_query($con, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $package_name = str_replace([' (30 PAX)', ' (50 PAX)'], '', $row['package_name']); // Remove PAX suffix if present
        $package_type = $row['package_type'];
        
        // Get the correct price for this package
        if (isset($package_prices[$package_type][$package_name])) {
            $price = $package_prices[$package_type][$package_name];
            
            // Update the record with the correct price
            $update_query = "UPDATE event_bookings SET package_price = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($stmt, "di", $price, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "Updated booking ID {$id} with price: ₱" . number_format($price, 2) . "<br>";
            } else {
                echo "Error updating booking ID {$id}: " . mysqli_error($con) . "<br>";
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo "Could not find price for package: {$package_name} ({$package_type})<br>";
        }
    }
    echo "Price update complete!";
} else {
    echo "Error fetching bookings: " . mysqli_error($con);
}

mysqli_close($con);
?> 