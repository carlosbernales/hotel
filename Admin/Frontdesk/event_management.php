<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create event_packages table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS event_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    image_path2 VARCHAR(255),
    image_path3 VARCHAR(255),
    max_guests INT NOT NULL,
    duration INT NOT NULL,
    is_available TINYINT(1) DEFAULT 1,
    menu_items TEXT,
    max_pax INT,
    time_limit VARCHAR(50),
    notes TEXT,
    status VARCHAR(50) DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$con->query($create_table)) {
    die("Error creating table: " . $con->error);
}

// Fetch existing packages
$query = "SELECT * FROM event_packages ORDER BY price ASC";
$result = $con->query($query);

if ($result === false) {
    die("Error fetching packages: " . $con->error);
}

$packages = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Management - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .package-section {
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #DAA520;
        }
        
        .add-package-btn {
            background: #DAA520;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .package-card {
            border: 1px solid #DAA520;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
            transition: all 0.3s ease;
        }
        
        .package-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .package-images {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .package-images img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .package-details {
            margin-bottom: 15px;
        }
        
        .package-price {
            font-size: 1.2em;
            color: #DAA520;
            font-weight: bold;
        }
        
        .package-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }
        
        .status-available {
            background: #28a745;
            color: white;
        }
        
        .status-occupied {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
    
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="active">Event Management</li>
            </ol>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="package-section">
                    <div class="section-header">
                        <h2>Event Package Management</h2>
                        <a href="add_event_package.php" class="add-package-btn">
                            <i class="fa fa-plus"></i> Add New Package
                        </a>
                    </div>

                    <div class="row">
                        <?php foreach ($packages as $package): ?>
                        <div class="col-md-6">
                            <div class="package-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                                    <span class="package-status <?php echo $package['status'] == 'Available' ? 'status-available' : 'status-occupied'; ?>">
                                        <?php echo htmlspecialchars($package['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="package-images">
                                    <?php if (!empty($package['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($package['image_path']); ?>" alt="Image 1">
                                    <?php endif; ?>
                                    <?php if (!empty($package['image_path2'])): ?>
                                        <img src="<?php echo htmlspecialchars($package['image_path2']); ?>" alt="Image 2">
                                    <?php endif; ?>
                                    <?php if (!empty($package['image_path3'])): ?>
                                        <img src="<?php echo htmlspecialchars($package['image_path3']); ?>" alt="Image 3">
                                    <?php endif; ?>
                                </div>

                                <div class="package-details">
                                    <p class="package-price">₱<?php echo number_format($package['price'], 2); ?></p>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($package['description']); ?></p>
                                    <p><strong>Max Guests:</strong> <?php echo htmlspecialchars($package['max_guests']); ?></p>
                                    <p><strong>Duration:</strong> <?php echo htmlspecialchars($package['duration']); ?> hours</p>
                                    <p><strong>Time Limit:</strong> <?php echo htmlspecialchars($package['time_limit']); ?></p>
                                    <p><strong>Menu Items:</strong> <?php echo htmlspecialchars($package['menu_items']); ?></p>
                                    <?php if (!empty($package['notes'])): ?>
                                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($package['notes']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="package-actions">
                                    <a href="edit_event_package.php?id=<?php echo $package['id']; ?>" class="btn btn-primary">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <button onclick="toggleStatus(<?php echo $package['id']; ?>)" class="btn <?php echo $package['status'] == 'Available' ? 'btn-warning' : 'btn-success'; ?>">
                                        <i class="fa fa-toggle-on"></i> 
                                        <?php echo $package['status'] == 'Available' ? 'Mark as Occupied' : 'Mark as Available'; ?>
                                    </button>
                                    <button onclick="deletePackage(<?php echo $package['id']; ?>)" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    function deletePackage(id) {
        if (confirm('Are you sure you want to delete this package? This action cannot be undone.')) {
            window.location.href = 'delete_event_package.php?id=' + id;
        }
    }

    function toggleStatus(id) {
        if (confirm('Are you sure you want to change the status of this package?')) {
            window.location.href = 'toggle_event_status.php?id=' + id;
        }
    }
    </script>
</body>
</html> 