<?php
require_once 'db.php';

header('Content-Type: text/plain');

echo "=== Database Connection Test ===\n";
if ($con) {
    echo "✅ Connected to database successfully!\n";
    echo "Database: " . mysqli_get_host_info($con) . "\n\n";
    
    // Check if amenities table exists
    $result = mysqli_query($con, "SHOW TABLES LIKE 'amenities'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Amenities table exists\n";
        
        // Get table structure
        echo "\n=== Table Structure ===\n";
        $structure = mysqli_query($con, "DESCRIBE amenities");
        while ($row = mysqli_fetch_assoc($structure)) {
            echo "- {$row['Field']}: {$row['Type']} " . ($row['Null'] === 'NO' ? 'NOT NULL' : '') . "\n";
        }
        
        // Get row count
        $count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM amenities"))['count'];
        echo "\n=== Row Count ===\n";
        echo "Total amenities: $count\n";
        
        // Show sample data
        if ($count > 0) {
            echo "\n=== Sample Data (first 5 rows) ===\n";
            $data = mysqli_query($con, "SELECT * FROM amenities LIMIT 5");
            while ($row = mysqli_fetch_assoc($data)) {
                print_r($row);
                echo "\n";
            }
        }
    } else {
        echo "❌ Amenities table does not exist\n";
        
        // Try to create the table
        echo "\n=== Attempting to create amenities table ===\n";
        $create_sql = "CREATE TABLE IF NOT EXISTS amenities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            is_available BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (mysqli_query($con, $create_sql)) {
            echo "✅ Created amenities table successfully\n";
            
            // Insert sample data
            $sample_data = [
                ['Breakfast', 'Delicious breakfast buffet', 250.00],
                ['Airport Transfer', 'One way airport transfer', 500.00],
                ['Spa Access', 'Full day spa access', 800.00],
                ['Laundry Service', 'Same day laundry service', 150.00],
                ['Mini Bar', 'Mini bar restocking', 350.00]
            ];
            
            $stmt = mysqli_prepare($con, "INSERT INTO amenities (name, description, price) VALUES (?, ?, ?)");
            $inserted = 0;
            
            foreach ($sample_data as $item) {
                mysqli_stmt_bind_param($stmt, 'ssd', $item[0], $item[1], $item[2]);
                if (mysqli_stmt_execute($stmt)) {
                    $inserted++;
                }
            }
            
            echo "✅ Inserted $inserted sample amenities\n";
            
        } else {
            echo "❌ Failed to create amenities table: " . mysqli_error($con) . "\n";
        }
    }
    
} else {
    echo "❌ Database connection failed: " . mysqli_connect_error() . "\n";
    echo "Host: $host, User: $username, DB: $database\n";
}

// Close connection
if (isset($con)) {
    mysqli_close($con);
}
?>
