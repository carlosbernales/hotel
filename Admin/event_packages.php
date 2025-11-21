<!DOCTYPE html>
<html>
<head>
    <title>Event Packages - Casa Estela</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        .package-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 20px;
            transition: transform 0.3s ease;
        }
        
        .package-card:hover {
            transform: translateY(-5px);
        }
        
        .package-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 15px;
        }
        
        .package-price {
            font-size: 2rem;
            color: #007bff;
            font-weight: bold;
            margin: 15px 0;
        }
        
        .package-details {
            margin: 15px 0;
        }
        
        .package-details ul {
            list-style: none;
            padding: 0;
        }
        
        .package-details li {
            margin-bottom: 8px;
            color: #666;
        }
        
        .package-details i {
            color: #28a745;
            margin-right: 10px;
        }
        
        .btn-book {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            margin-top: 15px;
        }
        
        .btn-book:hover {
            background: #0056b3;
        }
        
        .container {
            padding-top: 50px;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php
    // Include database connection
    require_once('db.php');
    
    // Fetch packages from database
    $sql = "SELECT * FROM event_packages ORDER BY price ASC";
    $result = $con->query($sql);
    
    // Check for database errors
    if (!$result) {
        echo '<div class="error-message">Error fetching packages: ' . $con->error . '</div>';
    }
    ?>

    <?php include('header.php'); ?>
    
    <div class="container">
        <h2 class="text-center mb-5">Our Event Packages</h2>
        
        <div class="row">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($package = $result->fetch_assoc()) {
            ?>
                <div class="col-md-3">
                    <div class="package-card">
                        <h3 class="package-title"><?php echo htmlspecialchars($package['name']); ?></h3>
                        <div class="package-price">₱<?php echo number_format($package['price'], 2); ?></div>
                        <div class="package-details">
                            <ul>
                                <li><i class="fa fa-check"></i> <?php echo htmlspecialchars($package['duration']); ?>-hour venue rental</li>
                                <li><i class="fa fa-check"></i> Up to <?php echo htmlspecialchars($package['max_guests']); ?> Pax</li>
                                <?php
                                $description = explode("\n", $package['description']);
                                foreach ($description as $feature) {
                                    if (!empty(trim($feature))) {
                                        echo '<li><i class="fa fa-check"></i> ' . htmlspecialchars(trim($feature)) . '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <button class="btn btn-book" data-package-id="<?php echo $package['id']; ?>">Book Now</button>
                    </div>
                </div>
            <?php
                }
            } else {
                // If no packages found, show default packages
            ?>
                <!-- Standard Package -->
                <div class="col-md-3">
                    <div class="package-card">
                        <h3 class="package-title">Standard Package</h3>
                        <div class="package-price">₱47,500</div>
                        <div class="package-details">
                            <ul>
                                <li><i class="fa fa-check"></i> 5-hour venue rental</li>
                                <li><i class="fa fa-check"></i> Basic sound system</li>
                                <li><i class="fa fa-check"></i> Up to 30 Pax</li>
                                <li><i class="fa fa-check"></i> Standard decoration</li>
                                <li><i class="fa fa-check"></i> Basic catering service</li>
                            </ul>
                        </div>
                        <button class="btn btn-book" data-package-id="1">Book Now</button>
                    </div>
                </div>
                
                <!-- Premium Package -->
                <div class="col-md-3">
                    <div class="package-card">
                        <h3 class="package-title">Premium Package</h3>
                        <div class="package-price">₱55,000</div>
                        <div class="package-details">
                            <ul>
                                <li><i class="fa fa-check"></i> 5-hour venue rental</li>
                                <li><i class="fa fa-check"></i> Premium sound system</li>
                                <li><i class="fa fa-check"></i> Up to 30 Pax</li>
                                <li><i class="fa fa-check"></i> Premium decoration</li>
                                <li><i class="fa fa-check"></i> Full catering service</li>
                            </ul>
                        </div>
                        <button class="btn btn-book" data-package-id="2">Book Now</button>
                    </div>
                </div>
                
                <!-- Deluxe Package -->
                <div class="col-md-3">
                    <div class="package-card">
                        <h3 class="package-title">Deluxe Package</h3>
                        <div class="package-price">₱76,800</div>
                        <div class="package-details">
                            <ul>
                                <li><i class="fa fa-check"></i> 5-hour venue rental</li>
                                <li><i class="fa fa-check"></i> Professional DJ</li>
                                <li><i class="fa fa-check"></i> Up to 30 Pax</li>
                                <li><i class="fa fa-check"></i> Luxury decoration</li>
                                <li><i class="fa fa-check"></i> Premium catering</li>
                            </ul>
                        </div>
                        <button class="btn btn-book" data-package-id="3">Book Now</button>
                    </div>
                </div>
                
                <!-- Venue Rental Only -->
                <div class="col-md-3">
                    <div class="package-card">
                        <h3 class="package-title">Venue Rental Only</h3>
                        <div class="package-price">₱20,000</div>
                        <div class="package-details">
                            <ul>
                                <li><i class="fa fa-check"></i> 5-hour venue rental</li>
                                <li><i class="fa fa-check"></i> Tables and chairs</li>
                                <li><i class="fa fa-check"></i> Basic lighting setup</li>
                                <li><i class="fa fa-check"></i> Air-conditioned space</li>
                                <li><i class="fa fa-check"></i> Basic amenities</li>
                            </ul>
                        </div>
                        <button class="btn btn-book" data-package-id="4">Book Now</button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="../js/jquery-1.11.1.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.btn-book').click(function() {
                var packageId = $(this).data('package-id');
                // Redirect to booking form with package ID
                window.location.href = 'event_booking.php?package_id=' + packageId;
            });
        });
    </script>
</body>
</html> 