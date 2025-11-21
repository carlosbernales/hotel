<?php
require "db.php"; // Include the database connection file

// Query to fetch customer information
$sql = "SELECT * FROM orders";
$stmt = $connection->query($sql);
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
            <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
                </a></li>
            <li class="active">Orders</li>
        </ol>
    </div><!--/.row-->

    <br>

    <div class="row">
        <div class="col-lg-12">
            <div id="success"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Orders
                    
                </div>
                <div class="panel-body">
                   
                    <table class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%"
                           id="rooms">
                        <thead>
                        <tr>
                            <th>Order_ID</th>
                            <th>User_Id</th>
                           
                            <th>Payment_Method</th>
                            <th>Total Price</th>
                            <th>Ordered at</th>
                            
                          
                          
                            
                        </tr>
                        </thead>
                        <?php
                   foreach ($stmt as $data) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($data["order_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($data["userid"]) . "</td>";
                   
                    echo "<td>" . htmlspecialchars($data["payment_method"]) . "</td>";
                    echo "<td>" . htmlspecialchars($data["total_price"]) . "</td>";
                    echo "<td>" . htmlspecialchars($data["created_at"]) . "</td>";
                  
                 
                  
                    echo "</tr>";
                }
                
                    ?>
                      

</div>    <!--/.main-->



