<?php
session_start();
require 'db_con.php';

// Check if wishlist exists
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Get rooms in wishlist
$wishlistRooms = [];
if (!empty($_SESSION['wishlist'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['wishlist']), '?'));
    
    try {
        $sql = "SELECT rt.*, 
                (SELECT COUNT(*) FROM rooms r WHERE r.room_type_id = rt.room_type_id AND r.status = 'Available') as available_rooms,
                (SELECT COUNT(*) FROM rooms r WHERE r.room_type_id = rt.room_type_id) as total_rooms
                FROM room_types rt
                WHERE rt.room_type_id IN ($placeholders)
                ORDER BY rt.room_type";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute($_SESSION['wishlist']);
        $wishlistRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure numeric values
        foreach ($wishlistRooms as &$room) {
            $room['available_rooms'] = (int)($room['available_rooms'] ?? 0);
            $room['total_rooms'] = (int)($room['total_rooms'] ?? 0);
        }
    } catch(PDOException $e) {
        error_log("Error fetching wishlist rooms: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E Akomoda - My Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .container.my-5 {
            margin-top: 8rem !important;
        }
        .room-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .room-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        .room-details {
            padding: 20px;
        }
        .room-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .rating {
            color: #ffc107;
            margin-bottom: 10px;
        }
        .price {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            font-weight: 500;
        }
        .empty-wishlist {
            text-align: center;
            padding: 50px 0;
        }
        .empty-wishlist i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        .wishlist-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar (same as roomss.php) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <span class="fw-bold text-warning">E Akomoda</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="roomss.php">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cafe.php">Café</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item position-relative">
                        <a class="nav-link active" href="wishlist.php">
                            <i class="fas fa-heart"></i>
                            <?php if(count($_SESSION['wishlist']) > 0): ?>
                                <span class="badge bg-danger rounded-circle position-absolute top-0 end-0 wishlist-badge">
                                    <?php echo count($_SESSION['wishlist']); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="notifications.php"><i class="fas fa-bell"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mybookings.php"><i class="fas fa-calendar-check"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">My Wishlist</h2>
        
        <?php if (empty($wishlistRooms)): ?>
            <div class="empty-wishlist">
                <i class="fas fa-heart-broken"></i>
                <h3>Your wishlist is empty</h3>
                <p class="text-muted">Browse our rooms and add your favorites to the wishlist</p>
                <a href="roomss.php" class="btn btn-warning mt-3">
                    <i class="fas fa-search"></i> Browse Rooms
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($wishlistRooms as $room): ?>
                    <div class="col-md-4">
                        <div class="room-card">
                            <img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="room-image">
                            <div class="room-details">
                                <h3 class="room-title"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                                <div class="rating">
                                    <?php 
                                    $rating = round($room['rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fas fa-star"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                    <span class="ms-1">(<?php echo $room['rating']; ?>)</span>
                                </div>
                                <div class="price">₱<?php echo number_format($room['price'], 2); ?> per night</div>
                                <div class="text-success mb-3">
                                    <i class="fas fa-check-circle"></i> 
                                    <?php echo $room['available_rooms']; ?> rooms available
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <button class="btn btn-warning" onclick="location.href='roomss.php?view=<?php echo $room['room_type_id']; ?>'">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-outline-danger remove-from-list" data-room-id="<?php echo $room['room_type_id']; ?>">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle remove from list buttons
            const removeButtons = document.querySelectorAll('.remove-from-list');
            
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roomId = this.getAttribute('data-room-id');
                    
                    // Send AJAX request to remove room from list
                    const formData = new FormData();
                    formData.append('room_type_id', roomId);
                    
                    fetch('remove_from_list.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#ffc107'
                            }).then(() => {
                                // Reload the page to update the list
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Failed to remove room from list',
                                icon: 'error',
                                confirmButtonColor: '#ffc107'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'An unexpected error occurred',
                            icon: 'error',
                            confirmButtonColor: '#ffc107'
                        });
                    });
                });
            });
        });
    </script>
</body>
</html> 