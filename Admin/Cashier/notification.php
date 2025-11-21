<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active">Notification</li>
        </ol>
    </div><!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Notification</h1>
        </div>
    </div><!--/.row-->

    <!-- Add notification content -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="notification-container">
                        <?php
                        // Add database connection here
                        include 'includes/database.php';
                        
                        // Query to get notifications ordered by latest first
                        $query = "SELECT * FROM notifications ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $query);

                        while($row = mysqli_fetch_assoc($result)) {
                            $icon = ($row['type'] == 'order') ? 'fa-shopping-bag' : 'fa-bell';
                            ?>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fa <?php echo $icon; ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-text"><?php echo $row['message']; ?></p>
                                    <small class="notification-time"><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></small>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add CSS styles -->
    <style>
        .notification-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-icon {
            background-color: #f8f9fa;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .notification-icon i {
            font-size: 18px;
            color: #666;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-text {
            margin: 0;
            color: #333;
            font-size: 14px;
        }
        
        .notification-time {
            color: #888;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }

        .panel {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .panel-body {
            padding: 0;
        }

        .notification-item:last-child {
            border-bottom: none;
        }
    </style>
</div>    <!--/.main-->


