<?php
require_once "db.php";

// Check if database connection is successful
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get current date for comparison
$today = date('Y-m-d');

// Query to fetch all users
$query = "SELECT 
    firstname,
    lastname,
    phone,
    email,
    is_verified,
    created_at
FROM users 
ORDER BY created_at DESC";

$result = mysqli_query($con, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Fetch all users into an array
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

// Close the result set
mysqli_free_result($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        
        .breadcrumb {
            background: transparent;
            margin: 15px 0;
            padding: 0;
        }
        
        .breadcrumb img {
            width: 20px;
            height: 20px;
        }
        
        .panel {
            margin-top: 20px;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        
        .panel-heading {
            background-color: #fff !important;
            border-bottom: 1px solid #e7e7e7;
            padding: 15px;
            font-size: 16px;
            font-weight: 500;
        }
        
        .panel-body {
            padding: 15px;
            background: #fff;
        }
        
        table.dataTable thead th {
            background-color: #fff;
            border-bottom: 2px solid #ddd !important;
            font-weight: 600;
            padding: 12px 8px;
        }
        
        table.dataTable tbody td {
            padding: 12px 8px;
            vertical-align: middle;
        }
        
        .verified {
            background-color: #5cb85c;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .pending {
            background-color: #f0ad4e;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            padding: 6px 12px;
            border-radius: 4px;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            padding: 6px 12px;
            border-radius: 4px;
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#">
                    <img src="img/house.png" alt="Home Icon">
                </a></li>
                <li class="active">User Information</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">User Information</div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Joined Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                            <td>
                                                <?php if ($user['is_verified']): ?>
                                                    <span class="verified">Verified</span>
                                                <?php else: ?>
                                                    <span class="pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                order: [[4, 'desc']], // Sort by joined date by default
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search users..."
                }
            });
        });
    </script>
</body>
</html>
