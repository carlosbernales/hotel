<?php
include 'db.php';
include 'header.php';
include 'sidebar.php';
?>

<!-- Add SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add Star Rating CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .star-rating {
        color: #FFD700;
        font-size: 16px;
    }
    .rating-filter {
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .filter-container {
        margin-bottom: 20px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .label-rating {
        font-size: 85%;
        padding: 0.3em 0.6em;
    }
    .label-5 { background-color: #5cb85c; color: white; }
    .label-4 { background-color: #5bc0de; color: white; }
    .label-3 { background-color: #f0ad4e; color: white; }
    .label-2 { background-color: #d9534f; color: white; }
    .label-1 { background-color: #d9534f; color: white; }
    
    .progress {
        height: 20px;
        margin-bottom: 10px;
    }
    .huge {
        font-size: 30px;
        font-weight: bold;
    }
</style>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Customer Reviews</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Room Reviews & Ratings</h3>
                </div>
                <div class="panel-body">
                    <!-- Filter Options -->
                    <div class="filter-container">
                        <h4>Filter by Rating:</h4>
                        <div class="rating-filter">
                            <button class="btn btn-default rating-filter-btn active" data-rating="all">All Ratings</button>
                        </div>
                        <div class="rating-filter">
                            <button class="btn btn-default rating-filter-btn" data-rating="5"><span class="star-rating">★★★★★</span></button>
                        </div>
                        <div class="rating-filter">
                            <button class="btn btn-default rating-filter-btn" data-rating="4"><span class="star-rating">★★★★</span></button>
                        </div>
                        <div class="rating-filter">
                            <button class="btn btn-default rating-filter-btn" data-rating="3"><span class="star-rating">★★★</span></button>
                        </div>
                        <div class="rating-filter">
                            <button class="btn btn-default rating-filter-btn" data-rating="2"><span class="star-rating">★★</span></button>
                        </div>
                        <div class="rating-filter">
                            <button class="btn btn-default rating-filter-btn" data-rating="1"><span class="star-rating">★</span></button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="reviewsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Room Type</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Build the SQL query with filters - using only simple joins
                            $sql = "SELECT r.review_id, r.user_id, r.room_type_id, r.rating, r.review, r.created_at 
                                    FROM room_reviews r";
                            
                            // Apply rating filter if specified
                            if (isset($_GET['rating']) && $_GET['rating'] != 'all' && is_numeric($_GET['rating'])) {
                                $rating = intval($_GET['rating']);
                                $sql .= " WHERE r.rating = $rating";
                            }
                            
                            $sql .= " ORDER BY r.created_at DESC";
                            
                            $result = mysqli_query($con, $sql);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                $count = 1;
                                while ($review = mysqli_fetch_assoc($result)) {
                                    // Get user information
                                    $user_info = array('name' => 'Guest');
                                    if ($review['user_id'] > 0) {
                                        $user_sql = "SELECT first_name, last_name, name FROM userss WHERE id = " . $review['user_id'];
                                        $user_result = mysqli_query($con, $user_sql);
                                        if ($user_result && mysqli_num_rows($user_result) > 0) {
                                            $user_data = mysqli_fetch_assoc($user_result);
                                            if (!empty($user_data['name'])) {
                                                $user_info['name'] = $user_data['name'];
                                            } else if (!empty($user_data['first_name'])) {
                                                $user_info['name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
                                            }
                                        }
                                    }
                                    
                                    // Get room type name (queries separately to avoid join issues)
                                    $room_type_name = 'Unknown Room';
                                    if ($review['room_type_id'] > 0) {
                                        $room_sql = "SELECT * FROM room_types WHERE room_type_id = " . $review['room_type_id'];
                                        $room_result = mysqli_query($con, $room_sql);
                                        if ($room_result && mysqli_num_rows($room_result) > 0) {
                                            $room_data = mysqli_fetch_assoc($room_result);
                                            // Try multiple possible column names for room type
                                            if (!empty($room_data['name'])) {
                                                $room_type_name = $room_data['name'];
                                            } else if (!empty($room_data['room_type'])) {
                                                $room_type_name = $room_data['room_type'];
                                            } else if (!empty($room_data['type_name'])) {
                                                $room_type_name = $room_data['type_name'];
                                            }
                                        }
                                    }
                                    
                                    // Display star rating
                                    $star_rating = '';
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $review['rating']) {
                                            $star_rating .= '<i class="fas fa-star"></i>';
                                        } else {
                                            $star_rating .= '<i class="far fa-star"></i>';
                                        }
                                    }
                                    
                                    // Format date
                                    $review_date = date('M j, Y g:i A', strtotime($review['created_at']));
                                    
                                    echo "<tr>";
                                    echo "<td>{$count}</td>";
                                    echo "<td>" . htmlspecialchars($user_info['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($room_type_name) . "</td>";
                                    echo "<td class='star-rating-cell' data-rating='{$review['rating']}'><span class='star-rating'>{$star_rating}</span></td>";
                                    echo "<td>" . htmlspecialchars($review['review']) . "</td>";
                                    echo "<td>{$review_date}</td>";
                                    echo "</tr>";
                                    
                                    $count++;
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">No reviews found.</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Panel -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Review Statistics</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?php
                        // Get overall statistics
                        $stats_sql = "SELECT 
                            COUNT(*) as total_reviews,
                            ROUND(AVG(rating), 1) as avg_rating,
                            COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                            COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                            COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                            COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                            COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                            FROM room_reviews";
                        
                        $stats_result = mysqli_query($con, $stats_sql);
                        $stats = mysqli_fetch_assoc($stats_result);
                        
                        $total_reviews = $stats['total_reviews'] > 0 ? $stats['total_reviews'] : 1; // Avoid division by zero
                        
                        // Calculate percentages
                        $five_star_percent = round(($stats['five_star'] / $total_reviews) * 100);
                        $four_star_percent = round(($stats['four_star'] / $total_reviews) * 100);
                        $three_star_percent = round(($stats['three_star'] / $total_reviews) * 100);
                        $two_star_percent = round(($stats['two_star'] / $total_reviews) * 100);
                        $one_star_percent = round(($stats['one_star'] / $total_reviews) * 100);
                        ?>
                        
                        <div class="col-md-3">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-star fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"><?php echo $stats['avg_rating']; ?></div>
                                            <div>Average Rating</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-comments fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"><?php echo $stats['total_reviews']; ?></div>
                                            <div>Total Reviews</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Rating Distribution</h4>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $five_star_percent; ?>%">
                                    5★ (<?php echo $stats['five_star']; ?>)
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-info" style="width: <?php echo $four_star_percent; ?>%">
                                    4★ (<?php echo $stats['four_star']; ?>)
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $three_star_percent; ?>%">
                                    3★ (<?php echo $stats['three_star']; ?>)
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $two_star_percent; ?>%">
                                    2★ (<?php echo $stats['two_star']; ?>)
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-danger" style="width: <?php echo $one_star_percent; ?>%">
                                    1★ (<?php echo $stats['one_star']; ?>)
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room Type Ratings -->
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Ratings by Room Type</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Room Type</th>
                                            <th>Average Rating</th>
                                            <th>Total Reviews</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get all room types first
                                        $room_types_query = "SELECT * FROM room_types";
                                        $room_types_result = mysqli_query($con, $room_types_query);
                                        
                                        if ($room_types_result && mysqli_num_rows($room_types_result) > 0) {
                                            while ($room_type = mysqli_fetch_assoc($room_types_result)) {
                                                // Find the name field - try different possibilities
                                                $room_type_name = 'Unknown';
                                                if (!empty($room_type['name'])) {
                                                    $room_type_name = $room_type['name'];
                                                } else if (!empty($room_type['room_type'])) {
                                                    $room_type_name = $room_type['room_type'];
                                                } else if (!empty($room_type['type_name'])) {
                                                    $room_type_name = $room_type['type_name'];
                                                }
                                                
                                                // Get ratings for this room type
                                                $rating_query = "SELECT 
                                                    ROUND(AVG(rating), 1) as avg_rating,
                                                    COUNT(*) as review_count
                                                    FROM room_reviews
                                                    WHERE room_type_id = " . $room_type['room_type_id'];
                                                
                                                $rating_result = mysqli_query($con, $rating_query);
                                                $rating_data = mysqli_fetch_assoc($rating_result);
                                                
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($room_type_name) . '</td>';
                                                
                                                // Show stars for average rating
                                                echo '<td>';
                                                $avg_rating = $rating_data['avg_rating'];
                                                if ($avg_rating > 0) {
                                                    echo '<span class="star-rating">';
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= floor($avg_rating)) {
                                                            echo '<i class="fas fa-star"></i>';
                                                        } elseif ($i - $avg_rating < 1 && $i - $avg_rating > 0) {
                                                            echo '<i class="fas fa-star-half-alt"></i>';
                                                        } else {
                                                            echo '<i class="far fa-star"></i>';
                                                        }
                                                    }
                                                    echo '</span> (' . $avg_rating . ')';
                                                } else {
                                                    echo 'No ratings';
                                                }
                                                echo '</td>';
                                                
                                                echo '<td>' . $rating_data['review_count'] . '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="3">No room types available</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#reviewsTable').DataTable({
        "pageLength": 10,
        "order": [[5, "desc"]], // Sort by date column by default
        "responsive": true
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Animate progress bars on page load
    $('.progress-bar').each(function() {
        var width = $(this).css('width');
        $(this).css('width', '0');
        $(this).animate({width: width}, 1000);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.rating-filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove active from all
            document.querySelectorAll('.rating-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const rating = btn.getAttribute('data-rating');
            document.querySelectorAll('#reviewsTable tbody tr').forEach(function(row) {
                if (!rating || rating === 'all') {
                    row.style.display = '';
                } else {
                    const cell = row.querySelector('.star-rating-cell');
                    if (cell && cell.getAttribute('data-rating') === rating) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    });
});
</script>

<?php include 'footer.php'; ?>