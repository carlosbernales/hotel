<?php
// Start output buffering to prevent header issues
ob_start();

// Include necessary files
include_once "header.php";
include_once "sidebar.php";

// Database connection and other setup code here
?>

<div class="room-cards-container" style="margin-left: 250px; padding: 20px;">
    <div class="row">
        <?php
        // Fetch rooms from database
        $sql = "SELECT * FROM room_type";
        $result = mysqli_query($con, $sql);

        while ($room = mysqli_fetch_assoc($result)) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100" style="max-width: 400px;">
                    <img src="<?php echo $room['image']; ?>" class="card-img-top" alt="<?php echo $room['room_type']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $room['room_type']; ?></h5>
                        <div class="rating">
                            <?php echo str_repeat('★', 5); ?> <span>(5.0)</span>
                        </div>
                        <p class="price">₱<?php echo number_format($room['price'], 2); ?> per night</p>
                        <p class="rooms-left text-success">
                            <i class="fas fa-check-circle"></i>
                            Only <?php echo $room['available_rooms']; ?> rooms left
                        </p>
                        <p class="capacity">
                            <i class="fas fa-users"></i>
                            Max capacity: <?php echo $room['capacity']; ?> persons
                        </p>
                        <div class="amenities">
                            <?php if ($room['air_conditioning']) { ?>
                                <span><i class="fas fa-snowflake"></i> Air Conditioning</span>
                            <?php } ?>
                            <?php if ($room['private_bathroom']) { ?>
                                <span><i class="fas fa-bath"></i> Private Bathroom</span>
                            <?php } ?>
                            <?php if ($room['flat_screen_tv']) { ?>
                                <span><i class="fas fa-tv"></i> Flat-screen TV</span>
                            <?php } ?>
                            <?php if ($room['free_wifi']) { ?>
                                <span><i class="fas fa-wifi"></i> Free WiFi</span>
                            <?php } ?>
                            <?php if ($room['hot_shower']) { ?>
                                <span><i class="fas fa-shower"></i> Hot Shower</span>
                            <?php } ?>
                        </div>
                        <div class="mt-3">
                            <a href="#" class="btn btn-primary btn-block" onclick="viewDetails(<?php echo $room['id']; ?>)">
                                <i class="fas fa-info-circle"></i> VIEW DETAILS
                            </a>
                        </div>
                        <div class="mt-2">
                            <a href="#" class="btn btn-info btn-block" onclick="addToList(<?php echo $room['id']; ?>)">
                                <i class="fas fa-plus-circle"></i> ADD TO LIST
                            </a>
                        </div>
                        <div class="mt-2">
                            <a href="#" class="btn btn-success btn-block" onclick="checkIn(<?php echo $room['id']; ?>)">
                                <i class="fas fa-sign-in-alt"></i> CHECK IN
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<style>
.card {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s;
    margin: 0 auto;
}

.card:hover {
    transform: translateY(-5px);
}

.rating {
    color: #ffc107;
    margin-bottom: 10px;
}

.price {
    font-size: 1.2em;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 10px;
}

.rooms-left {
    font-size: 0.9em;
    margin-bottom: 10px;
}

.capacity {
    font-size: 0.9em;
    color: #6c757d;
    margin-bottom: 15px;
}

.amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.amenities span {
    font-size: 0.8em;
    color: #6c757d;
    background: #f8f9fa;
    padding: 5px 10px;
    border-radius: 15px;
}

.btn-block {
    width: 100%;
}

.room-cards-container {
    background-color: #f8f9fa;
    border-radius: 10px;
}
</style>

<?php
// Flush the output buffer
ob_end_flush();
?> 