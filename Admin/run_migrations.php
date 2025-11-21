<?php
require_once 'db.php';

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create migrations table if it doesn't exist
$createMigrationsTable = "
    CREATE TABLE IF NOT EXISTS `migrations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `migration` varchar(255) NOT NULL,
        `batch` int(11) NOT NULL,
        `ran_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `migration` (`migration`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!$con->query($createMigrationsTable)) {
    die("Error creating migrations table: " . $con->error);
}

// Get all migration files
$migrations = [];
$migrationFiles = glob('migrations/*.sql');

if (empty($migrationFiles)) {
    die("No migration files found in the migrations directory.\n");
}

// Sort migrations by name
sort($migrationFiles);

// Get already ran migrations
$ranMigrations = [];
$result = $con->query("SELECT migration FROM migrations");
while ($row = $result->fetch_assoc()) {
    $ranMigrations[] = basename($row['migration']);
}

// Get the current batch number
$batch = 1;
$result = $con->query("SELECT MAX(batch) as max_batch FROM migrations");
if ($row = $result->fetch_assoc()) {
    $batch = $row['max_batch'] + 1;
}

// Run new migrations
$ran = 0;
foreach ($migrationFiles as $file) {
    $migrationName = basename($file);
    
    // Skip already run migrations
    if (in_array($migrationName, $ranMigrations)) {
        echo "Skipping already run migration: $migrationName\n";
        continue;
    }
    
    echo "Running migration: $migrationName\n";
    
    // Read the SQL file
    $sql = file_get_contents($file);
    
    if ($sql === false) {
        echo "Error reading migration file: $file\n";
        continue;
    }
    
    // Split the SQL into individual queries
    $queries = array_filter(
        array_map('trim', 
            preg_split("/;\s*(?=([^\'\"]*[\'\"][^\'\"]*[\'\"])*[^\'\"]*$)/", $sql)
        )
    );
    
    // Start transaction
    $con->begin_transaction();
    
    try {
        // Execute each query
        foreach ($queries as $query) {
            if (empty($query)) continue;
            
            echo "  Executing query: " . substr($query, 0, 100) . (strlen($query) > 100 ? '...' : '') . "\n";
            
            if (!$con->query($query)) {
                throw new Exception("Error executing query: " . $con->error . "\nQuery: " . $query);
            }
        }
        
        // Record the migration
        $stmt = $con->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->bind_param('si', $migrationName, $batch);
        
        if (!$stmt->execute()) {
            throw new Exception("Error recording migration: " . $stmt->error);
        }
        
        // Commit transaction
        $con->commit();
        echo "  Migration completed successfully.\n";
        $ran++;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();
        echo "  Error running migration: " . $e->getMessage() . "\n";
        exit(1);
    }
}

if ($ran === 0) {
    echo "No new migrations to run.\n";
} else {
    echo "Successfully ran $ran migration(s).\n";
}

// Show current migration status
echo "\nCurrent Migration Status:\n";
$result = $con->query("SELECT migration, ran_at, batch FROM migrations ORDER BY batch, migration");
if ($result->num_rows > 0) {
    echo str_pad("Migration", 50) . str_pad("Ran At", 30) . "Batch\n";
    echo str_repeat("-", 90) . "\n";
    
    while ($row = $result->fetch_assoc()) {
        echo str_pad($row['migration'], 50) . 
             str_pad($row['ran_at'], 30) . 
             $row['batch'] . "\n";
    }
} else {
    echo "No migrations have been run yet.\n";
}

$con->close();
?>
