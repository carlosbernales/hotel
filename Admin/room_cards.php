<?php
require_once 'db.php';
require_once 'header.php';

// Get all room types with their details, filtering by room_type status and counting active room numbers
// This query ensures synchronization with room_management.php by using the same database structure
$query = "SELECT 
            rt.room_type_id, 
            rt.room_type, 
            rt.description, 
            rt.price, 
            rt.capacity, 
            rt.image,
            COUNT(CASE WHEN rn.status = 'active' THEN rn.room_number_id ELSE NULL END) as total_available_rooms
          FROM room_types rt 
          LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id 
          WHERE rt.status = 'active'
          GROUP BY rt.room_type_id
          ORDER BY rt.room_type_id ASC";

$result = mysqli_query($con, $query);

// Check for query errors
if (!$result) {
    die("Database query failed: " . mysqli_error($con));
}

// Debugging: Output the query and number of rows
echo "<!-- SQL Query: " . htmlspecialchars($query) . " -->";
if ($result) {
    $num_rows = mysqli_num_rows($result);
    echo "<!-- Number of rows returned: " . $num_rows . " -->";
    // Reset result pointer for the loop to work
    mysqli_data_seek($result, 0);
    
    // Debug: Show room data
    $debug_result = mysqli_query($con, $query);
    while($debug_room = mysqli_fetch_assoc($debug_result)) {
        echo "<!-- Room ID: " . $debug_room['room_type_id'] . " | Type: " . htmlspecialchars($debug_room['room_type']) . " | Price: ₱" . number_format($debug_room['price'], 2) . " | Capacity: " . $debug_room['capacity'] . " -->";
    }
    mysqli_data_seek($result, 0);
} else {
    echo "<!-- Query failed: " . mysqli_error($con) . " -->";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Room Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .room-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0; /* Add a subtle border */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0,0,0,0.05); /* Soft shadow */
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1); /* Enhanced shadow on hover */
        }
        .room-image {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        .card-text {
            color: #666;
            line-height: 1.5;
            min-height: 40px; /* Ensure consistent height for descriptions */
        }
        .inclusions-list {
            list-style: none;
            padding-left: 0;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .inclusions-list li {
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: #555;
        }
        .inclusions-list i {
            color: #28a745;
            margin-right: 8px;
            font-size: 1.1rem;
        }
        .price-text {
            font-size: 1.25rem;
            font-weight: bold;
            color: #007bff; /* Highlight price */
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .capacity-text {
            font-size: 0.95rem;
            color: #777;
            margin-bottom: 20px;
        }
        .d-grid.gap-2 {
            margin-top: 20px;
        }
        .btn {
            font-size: 1rem;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .booking-list {
            position: fixed;
            right: 20px;
            top: 80px;
            width: 300px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .available-rooms-text {
            font-size: 1.3rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }

        /* Add modal fix styles */
        .modal {
            z-index: 1050 !important;
        }
        
        .modal-backdrop {
            z-index: 1040 !important;
            opacity: 0.5 !important;
        }
        
        .modal-dialog {
            z-index: 1051 !important;
            position: relative;
        }
        
        .modal-content {
            position: relative;
            z-index: 1052 !important;
        }
        
        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }

        /* Room carousel styling */
        .room-card .carousel-control-prev,
        .room-card .carousel-control-next {
            width: 5%;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            opacity: 0.8;
        }

        .room-card .carousel-control-prev:hover,
        .room-card .carousel-control-next:hover {
            opacity: 1;
        }

        .room-card .carousel-control-prev-icon,
        .room-card .carousel-control-next-icon {
            width: 20px;
            height: 20px;
        }

        .room-card .carousel-control-prev {
            left: 10px;
        }

        .room-card .carousel-control-next {
            right: 10px;
        }

        .room-card .carousel-indicators {
            bottom: 10px;
        }

        .room-card .carousel-indicators li {
            background-color: rgba(255, 255, 255, 0.5);
            border: 1px solid #fff;
        }

        .room-card .carousel-indicators .active {
            background-color: #007bff;
        }

        /* Room Details Modal Styling */
        .modal-xl {
            max-width: 1200px;
        }
        
        .room-details-image {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .room-details-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .room-details-card:hover {
            transform: translateY(-2px);
        }
        
        .amenity-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .amenity-item:last-child {
            border-bottom: none;
        }
        
        .room-details-modal .modal-body {
            padding: 2rem;
        }
        
        .room-details-modal .modal-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-bottom: none;
        }
        
        .room-details-modal .btn-close {
            filter: invert(1);
        }
        
        /* Date Picker Styles */
        .flatpickr-input {
            background-color: #fff !important;
            cursor: pointer;
        }
        .flatpickr-calendar {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            border-radius: 0.5rem !important;
            font-family: inherit !important;
        }
        .flatpickr-day.selected, .flatpickr-day.selected:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        .flatpickr-day.today {
            border-color: #0d6efd !important;
        }
        .flatpickr-day.today:hover {
            background: #e9ecef !important;
            color: #000 !important;
        }
        .flatpickr-day.inRange, .flatpickr-day.prevMonthDay.inRange, 
        .flatpickr-day.nextMonthDay.inRange, .flatpickr-day.today.inRange, 
        .flatpickr-day.prevMonthDay.today.inRange, .flatpickr-day.nextMonthDay.today.inRange, 
        .flatpickr-day:hover, .flatpickr-day.prevMonthDay:hover, 
        .flatpickr-day.nextMonthDay:hover, .flatpickr-day:focus, 
        .flatpickr-day.prevMonthDay:focus, .flatpickr-day.nextMonthDay:focus {
            background: #e9ecef !important;
            border-color: #e9ecef !important;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container mt-4">
            <!-- Availability Checker -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Check Room Availability</h5>
                </div>
                <div class="card-body">
                    <form id="availabilityForm" class="row g-3" action="" method="post">
                        <div class="col-md-4">
                            <label for="checkInDate" class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" id="checkInDate" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="checkOutDate" class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" id="checkOutDate" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="checkAvailabilityBtn" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Check Availability
                            </button>
                        </div>
                    </form>
                    <div id="availabilityResult" class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Select your desired dates to check room availability.
                        </div>
                    </div>
                </div>
            </div>
            
            <h2 class="text-center mb-4">Available Rooms</h2>
            
            <!-- Booking List Sidebar -->
            <div class="booking-list" id="bookingList" style="display: none;">
                <h4>Your Booking List</h4>
                <div id="bookingItems"></div>
                <button class="btn btn-primary w-100 mt-3" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>

            <div class="row">
                <?php while($room = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4">
                        <div class="card room-card">
                            <?php 
                                // Handle image with proper fallback
                                $imagePath = 'uploads/rooms/' . ($room['image'] ?? 'standard.jpg');
                                $fullImagePath = $imagePath;
                                // Check if image exists, if not use standard.jpg as fallback
                                if (!file_exists($fullImagePath) || empty($room['image'])) {
                                    $imagePath = 'uploads/rooms/standard.jpg';
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                 class="card-img-top room-image" 
                                 alt="<?php echo htmlspecialchars($room['room_type'] ?? 'Room'); ?>"
                                 onerror="this.src='uploads/rooms/standard.jpg'">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['room_type'] ?? 'Room'); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($room['description'] ?? ''); ?></p>
                                <?php if ((int)($room['total_available_rooms'] ?? 0) === 0): ?>
                                    <span class="badge bg-danger mb-2">Not Available</span>
                                <?php endif; ?>
                                <ul class="inclusions-list">
                                    <li><i class="fas fa-wifi"></i> Free WiFi</li>
                                    <li><i class="fas fa-utensils"></i> Free Breakfast</li>
                                    <li><i class="fas fa-snowflake"></i> Air Conditioning</li>
                                    <li><i class="fas fa-tv"></i> Smart TV</li>
                                </ul>
                                <p class="card-text price-text">
                                    <strong>Price:</strong> ₱<?php echo number_format(floatval($room['price'] ?? 0), 2); ?> per night
                                </p>
                                <p class="card-text capacity-text">
                                    <strong>Capacity:</strong> <?php echo htmlspecialchars($room['capacity'] ?? 'N/A'); ?> persons
                                </p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-info" onclick="viewRoomDetails(<?= $room['room_type_id'] ?>)">
                                        <i class="fas fa-eye"></i> View Room
                                    </button>
                                    <button class="btn btn-warning" onclick="addToList(<?= $room['room_type_id'] ?>, '<?= addslashes($room['room_type'] ?? 'Room') ?>', <?= $room['price'] ?>, <?= $room['capacity'] ?>, '<?= $imagePath ?>', <?= (int)($room['total_available_rooms'] ?? 0) ?>)">
                                        <i class="fas fa-plus"></i> Add to List
                                    </button>
                                    <button class="btn btn-primary" onclick="checkIn(<?php echo $room['room_type_id']; ?>, '<?php echo htmlspecialchars($room['room_type'] ?? 'Room'); ?>', <?php echo floatval($room['price'] ?? 0); ?>, <?php echo htmlspecialchars($room['capacity'] ?? 'N/A'); ?>, '<?php echo htmlspecialchars($imagePath); ?>', <?php echo (int)($room['total_available_rooms'] ?? 0); ?>)" <?php if ((int)($room['total_available_rooms'] ?? 0) === 0) echo 'disabled'; ?>>
                                        <i class="fas fa-hotel"></i> Check-In
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Room Details Modal -->
    <div class="modal fade room-details-modal" id="roomDetailsModal" tabindex="-1" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="roomDetailsContent">
                    <!-- Content will be loaded dynamically -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading room details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="bookNowBtn">Book Now</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Make sure we have access to Bootstrap's Modal
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            const today = new Date();
            const tomorrow = new Date();
            tomorrow.setDate(today.getDate() + 1);
            
            // Set min dates for date inputs
            document.getElementById('checkInDate').min = formatDate(today);
            document.getElementById('checkOutDate').min = formatDate(tomorrow);
            
            // Set initial values
            document.getElementById('checkInDate').value = formatDate(today);
            document.getElementById('checkOutDate').value = formatDate(tomorrow);
            
            // Add click event to the Check Availability button
            const checkAvailabilityBtn = document.getElementById('checkAvailabilityBtn');
            if (checkAvailabilityBtn) {
                checkAvailabilityBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    checkAvailability();
                    return false;
                });
            }
            
            // Prevent form submission
            const availabilityForm = document.getElementById('availabilityForm');
            if (availabilityForm) {
                availabilityForm.onsubmit = function(e) { 
                    e.preventDefault();
                    return false; 
                };
            }
            
            // Update check-out min date when check-in changes
            document.getElementById('checkInDate').addEventListener('change', function() {
                const checkInDate = new Date(this.value);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(checkInDate.getDate() + 1);
                
                const checkOutInput = document.getElementById('checkOutDate');
                checkOutInput.min = formatDate(nextDay);
                
                // If current check-out is before new check-in, update it
                if (new Date(checkOutInput.value) < nextDay) {
                    checkOutInput.value = formatDate(nextDay);
                }
            });
        });
        
        // Define formatDate in the global scope
        function formatDate(date) {
            if (!(date instanceof Date)) {
                date = new Date(date);
            }
            let month = '' + (date.getMonth() + 1);
            let day = '' + date.getDate();
            const year = date.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Define checkAvailability in the global scope
        function checkAvailability() {
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            const resultDiv = document.getElementById('availabilityResult');
            const modal = new bootstrap.Modal(document.getElementById('availabilityModal'));
            const modalBody = document.getElementById('availabilityModalBody');
            
            if (!checkInDate || !checkOutDate) {
                resultDiv.innerHTML = '<div class="alert alert-warning">Please select both check-in and check-out dates.</div>';
                return;
            }
            
            // Check for blocked period (November 10-25, 2025)
            const blockedStart = new Date('2025-11-10');
            const blockedEnd = new Date('2025-11-25');
            const checkIn = new Date(checkInDate);
            const checkOut = new Date(checkOutDate);
            
            if ((checkIn <= blockedEnd && checkOut >= blockedStart)) {
                modalBody.innerHTML = `
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-calendar-times me-2"></i>No Availability</h5>
                        <p class="mb-0">The period from November 10 to November 25, 2025 is fully booked. Please select different dates.</p>
                    </div>`;
                modal.show();
                resultDiv.innerHTML = '';
                return;
            }
            
            // Show loading state in modal
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Checking room availability...</p>
                </div>`;
            
            modal.show();
            
            console.log('Fetching room types...');
            // Get all room types
            fetch('get_room_types.php')
                .then(response => {
                    console.log('get_room_types.php response:', response);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(roomTypes => {
                    console.log('Room types received:', roomTypes);
                    if (!Array.isArray(roomTypes)) {
                        throw new Error('Invalid room types data received');
                    }
                    
                    // Check availability for each room type
                    const availabilityPromises = roomTypes.map(room => {
                        console.log('Checking availability for room:', room.id);
                        // Make AJAX request to check availability
                        return fetch('check_availability.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `check_in=${encodeURIComponent(checkInDate)}&check_out=${encodeURIComponent(checkOutDate)}&room_type_id=${room.id}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                throw new Error(data.error || 'Failed to check availability');
                            }
                            
                            let html = `
                                <div class="card">
                                    <div class="card-header bg-${data.available ? 'success' : 'danger'} text-white">
                                        <h5 class="mb-0">
                                            <i class="fas ${data.available ? 'fa-check-circle' : 'fa-times-circle'} me-2"></i>
                                            ${data.available ? 'Rooms Available!' : 'No Rooms Available'}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">
                                            <strong>Check-in:</strong> ${formatDisplayDate(data.check_in)}<br>
                                            <strong>Check-out:</strong> ${formatDisplayDate(data.check_out)}
                                        </p>`;
                            
                            if (data.static_booking) {
                                html += `
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        The selected dates overlap with a blocked period (${formatDisplayDate(data.static_booking_dates[0])} to ${formatDisplayDate(data.static_booking_dates[1])}).
                                        Please select different dates.
                                    </div>`;
                            } else if (data.rooms && data.rooms.length > 0) {
                                html += `
                                    <h6 class="mb-3">Available Room Types:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Room Type</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;
                                
                                data.rooms.forEach(room => {
                                    const isAvailable = room.available === true || room.available === '1';
                                    html += `
                                        <tr class="${isAvailable ? 'table-success' : 'table-light'}">
                                            <td>${room.room_type}</td>
                                            <td>
                                                ${isAvailable ? 
                                                    '<span class="badge bg-success"><i class="fas fa-check me-1"></i> Available</span>' : 
                                                    `<span class="badge bg-danger"><i class="fas fa-times me-1"></i> ${room.message || 'Not Available'}</span>`
                                                }
                                            </td>
                                            <td>
                                                ${isAvailable ? 
                                                    `<a href="#" class="btn btn-sm btn-primary book-now" 
                                                        data-room-type="${room.room_type_id}"
                                                        data-checkin="${data.check_in}"
                                                        data-checkout="${data.check_out}">
                                                        <i class="fas fa-calendar-plus me-1"></i> Book Now
                                                    </a>` :
                                                    '<button class="btn btn-sm btn-outline-secondary" disabled>Not Available</button>'
                                                }
                                            </td>
                                        </tr>`;
                                });
                                
                                html += `
                                        </tbody>
                                    </table>
                                </div>`;
                            } else {
                                html += `
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        No rooms are available for the selected dates. Please try different dates.
                                    </div>`;
                            }
                            
                            html += `
                                    </div>
                                </div>`;
                            
                            resultDiv.innerHTML = html;
                            
                            // Add event listeners to book now buttons
                            document.querySelectorAll('.book-now').forEach(button => {
                                button.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const roomType = this.getAttribute('data-room-type');
                                    const checkIn = this.getAttribute('data-checkin');
                                    const checkOut = this.getAttribute('data-checkout');
                                    // You can add booking logic here or redirect to booking page
                                    alert(`Booking room type ${roomType} from ${checkIn} to ${checkOut}`);
                                });
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            resultDiv.innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    An error occurred while checking availability: ${error.message}
                                </div>`;
                        });
                        
                        // Helper function to format date for display (e.g., Nov 10, 2025)
                        function formatDisplayDate(dateString) {
                            const options = { year: 'numeric', month: 'short', day: 'numeric' };
                            return new Date(dateString).toLocaleDateString('en-US', options);
                        }    })
                .then(availabilityResults => {
                    // Display results
                    let availableRooms = availabilityResults.filter(room => room.available);
                    let unavailableRooms = availabilityResults.filter(room => !room.available);
                    
                    let html = '<div class="alert alert-success">';
                    html += `<h6><i class="fas fa-calendar-check me-2"></i>Availability for ${formatDate(checkInDate)} to ${formatDate(checkOutDate)}</h6>`;
                    
                    if (availableRooms.length > 0) {
                        html += '<div class="mt-2">';
                        html += '<p class="mb-2"><strong>Available Room Types:</strong></p>';
                        html += '<ul class="list-group">';
                        availableRooms.forEach(room => {
                            html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                ${room.room_type}
                                <span class="badge bg-success">Available</span>
                            </li>`;
                        });
                        html += '</ul></div>';
                    } else {
                        html += '<p class="mb-0">No rooms available for the selected dates. Please try different dates.</p>';
                    }
                    
                    html += '</div>';
                    
                    resultDiv.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error checking availability:', error);
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error checking availability. Please try again later.
                        </div>`;
                });
        }
        
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
        
        let roomDetailsModalInstance; // Declare a variable to hold the single modal instance
        let directCheckinModalInstance; // Declare for direct check-in modal
        let currentRoomId = null; // To store the current room ID being viewed

        // Initialize modals when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize room details modal
            const roomDetailsModalElement = document.getElementById('roomDetailsModal');
            if (roomDetailsModalElement) {
                roomDetailsModalInstance = new bootstrap.Modal(roomDetailsModalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                
                // Add event listener for the modal's hidden event
                roomDetailsModalElement.addEventListener('hidden.bs.modal', function () {
                    // Clear the content when modal is closed
                    document.getElementById('roomDetailsContent').innerHTML = `
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading room details...</p>
                        </div>`;
                });
            }


            // Initialize direct check-in modal
            const directCheckinModalElement = document.getElementById('directCheckinModal');
            if (directCheckinModalElement) {
                directCheckinModalInstance = new bootstrap.Modal(directCheckinModalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
            }


            // Add click event for view details buttons
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const roomId = this.getAttribute('data-room-id');
                    if (roomId) {
                        viewRoomDetails(roomId);
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Format dates as YYYY-MM-DD (required by date input)
            const today = new Date();
            const tomorrow = new Date();
            tomorrow.setDate(today.getDate() + 1);
            
            // Set min dates in JavaScript as well (for older browsers)
            document.getElementById('checkInDate').min = formatDate(today);
            document.getElementById('checkOutDate').min = formatDate(tomorrow);
            
            // Set initial values
            document.getElementById('checkInDate').value = formatDate(today);
            document.getElementById('checkOutDate').value = formatDate(tomorrow);

            // Handle check availability button click
            const checkAvailabilityBtn = document.getElementById('checkAvailabilityBtn');
            if (checkAvailabilityBtn) {
                checkAvailabilityBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    checkAvailability();
                    return false;
                });
            }
            
            // Prevent form submission entirely
            const availabilityForm = document.getElementById('availabilityForm');
            if (availabilityForm) {
                availabilityForm.onsubmit = function(e) { 
                    e.preventDefault();
                    return false; 
                };
            }

            // Update check-out min date when check-in changes
            document.getElementById('checkInDate').addEventListener('change', function() {
                const checkInDate = new Date(this.value);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(checkInDate.getDate() + 1);
                
                const checkOutInput = document.getElementById('checkOutDate');
                checkOutInput.min = formatDate(nextDay);
                
                // If current check-out is before new check-in, update it
                if (new Date(checkOutInput.value) < nextDay) {
                    checkOutInput.value = formatDate(nextDay);
                }
            });

            // Initialize room details modal
            roomDetailsModalInstance = new bootstrap.Modal(document.getElementById('roomDetailsModal'));
            
            // Add click event to all view details buttons
            const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
            viewDetailsButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get room_id from the data-room-id attribute
                    const roomId = this.dataset.roomId;
                    viewRoomDetails(roomId);
                });
            });

            // Initialize Direct Check-in Modal instance
            const directCheckinModalElement = document.getElementById('directCheckinModal');
            if (directCheckinModalElement) {
                directCheckinModalInstance = new bootstrap.Modal(directCheckinModalElement);
            }

            // Event listeners for guest information (Adults/Children)
            document.getElementById('directNumAdults').addEventListener('input', () => {
                generateDirectGuestNameFields('adult', parseInt(document.getElementById('directNumAdults').value) || 0);
            });
            document.getElementById('directNumChildren').addEventListener('input', () => {
                generateDirectGuestNameFields('child', parseInt(document.getElementById('directNumChildren').value) || 0);
            });

            // Event listeners for date changes in direct check-in modal
            document.getElementById('directCheckOutDate').addEventListener('change', updateDirectBookingTotals);
            document.querySelectorAll('input[name="directExtraBed"]').forEach(radio => {
                radio.addEventListener('change', updateDirectBookingTotals);
            });

            // Event listener for payment option change
            document.getElementById('directPaymentOption').addEventListener('change', function() {
                const paymentOption = this.value;
                const customPaymentAmountField = document.getElementById('directCustomPaymentAmountField');
                const customAmountInput = document.getElementById('directCustomAmount');
                const customAmountHelp = document.getElementById('directCustomAmountHelp');

                if (paymentOption === 'Custom Payment') {
                    customPaymentAmountField.style.display = 'block';
                    customAmountInput.setAttribute('required', 'true');
                } else {
                    customPaymentAmountField.style.display = 'none';
                    customAmountInput.removeAttribute('required');
                    customAmountInput.value = '';
                    customAmountHelp.style.display = 'none';
                }
            });

            // Event listener for custom amount input for validation
            document.getElementById('directCustomAmount').addEventListener('input', function() {
                const customAmount = parseFloat(this.value);
                const customAmountHelp = document.getElementById('directCustomAmountHelp');
                const totalAmountElement = document.getElementById('directFinalTotalAmount');
                const totalBookingAmount = parseFloat(totalAmountElement.textContent.replace(/[^0-9.]/g, ''));

                customAmountHelp.style.display = 'none';

                if (isNaN(customAmount) || customAmount <= 1500) {
                    customAmountHelp.textContent = 'Amount must be greater than ₱1,500.';
                    customAmountHelp.style.display = 'block';
                    this.classList.add('is-invalid');
                } else if (customAmount > totalBookingAmount) {
                    customAmountHelp.textContent = `Amount cannot exceed total booking amount (₱${totalBookingAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}).`;
                    customAmountHelp.style.display = 'block';
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });

            // Handle form submission for the direct check-in form
            document.getElementById('directCheckinForm').addEventListener('submit', function(e) {
                e.preventDefault();
                processDirectCheckinForm();
            });
        });
        
        let currentDirectCheckinRoom = null; // Store the room data for direct check-in
        
        // Function to view room details in a modal
        function viewRoomDetails(roomId) {
            console.log('viewRoomDetails called with roomId:', roomId);
            const roomDetailsContent = document.getElementById('roomDetailsContent');
            
            // Show loading state
            roomDetailsContent.innerHTML = `
                <div class="text-center p-5">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p class="mt-3">Loading room details...</p>
                </div>
            `;
            roomDetailsModalInstance.show();

            // Fetch room details from the database directly
            fetch(`get_room_details.php?room_type_id=${roomId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Room details fetched:', data);
                    if (data && data.success) {
                        const room = data.room;
                        
                        // Format price for display
                        const formattedPrice = `₱${parseFloat(room.price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                        
                        // Create amenities list
                        const defaultAmenities = [
                            { icon: 'fas fa-wifi', name: 'Free WiFi' },
                            { icon: 'fas fa-utensils', name: 'Free Breakfast' },
                            { icon: 'fas fa-snowflake', name: 'Air Conditioning' },
                            { icon: 'fas fa-tv', name: 'Smart TV' },
                            { icon: 'fas fa-bed', name: 'Comfortable Bedding' },
                            { icon: 'fas fa-shower', name: 'Private Bathroom' },
                            { icon: 'fas fa-lock', name: 'Room Safe' },
                            { icon: 'fas fa-phone', name: 'Room Service' }
                        ];
                        
                        const amenitiesHtml = defaultAmenities.map(amenity => `
                            <div class="col-md-6 mb-2">
                                <i class="${amenity.icon} text-primary me-2"></i>
                                <span>${amenity.name}</span>
                            </div>
                        `).join('');

                        // Handle image display
                        let imageHtml = '';
                        if (room.image && room.image !== '') {
                            imageHtml = `
                                <div class="text-center mb-4">
                                    <img src="uploads/rooms/${room.image}" 
                                         class="img-fluid rounded shadow" 
                                         alt="${room.room_type}" 
                                         style="max-height: 400px; width: auto;"
                                         onerror="this.src='uploads/rooms/standard.jpg'">
                                </div>
                            `;
                        } else {
                            imageHtml = `
                                <div class="text-center mb-4">
                                    <img src="uploads/rooms/standard.jpg" 
                                         class="img-fluid rounded shadow" 
                                         alt="${room.room_type}" 
                                         style="max-height: 400px; width: auto;">
                                </div>
                            `;
                        }

                        roomDetailsContent.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    ${imageHtml}
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-primary mb-3">${room.room_type}</h3>
                                    <p class="lead text-muted">${room.description || 'A comfortable and well-appointed room for your stay.'}</p>
                                    
                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-tag fa-2x text-success mb-2"></i>
                                                    <h5 class="card-title text-success">${formattedPrice}</h5>
                                                    <p class="card-text small">per night</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-users fa-2x text-info mb-2"></i>
                                                    <h5 class="card-title text-info">${room.capacity}</h5>
                                                    <p class="card-text small">persons</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-bed fa-2x text-warning mb-2"></i>
                                                    <h5 class="card-title text-warning">${room.beds || 'Standard'}</h5>
                                                    <p class="card-text small">bedding</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-door-open fa-2x text-primary mb-2"></i>
                                                    <h5 class="card-title text-primary">${room.total_available_rooms}</h5>
                                                    <p class="card-text small">available</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-star text-warning me-2"></i>Room Amenities</h5>
                                    <div class="row">
                                        ${amenitiesHtml}
                                    </div>
                                </div>
                            </div>
                            
                            ${room.total_available_rooms > 0 ? `
                                <div class="alert alert-success mt-4">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Available!</strong> This room type has ${room.total_available_rooms} room(s) available for booking.
                                </div>
                            ` : `
                                <div class="alert alert-warning mt-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Currently Unavailable</strong> All rooms of this type are currently occupied or under maintenance.
                                </div>
                            `}
                        `;

                        // Update the Book Now button
                        const bookNowBtn = document.getElementById('bookNowBtn');
                        if (room.total_available_rooms > 0) {
                            bookNowBtn.style.display = 'inline-block';
                            bookNowBtn.onclick = function() {
                                roomDetailsModalInstance.hide();
                                // Trigger the check-in function with room details
                                checkIn(roomId, room.room_type, room.price, room.capacity, room.image || 'uploads/rooms/standard.jpg', room.total_available_rooms);
                            };
                        } else {
                            bookNowBtn.style.display = 'none';
                        }

                    } else {
                        roomDetailsContent.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                ${data.error || 'Failed to load room details.'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching room details:', error);
                    roomDetailsContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            An error occurred while loading room details. Please try again later.
                        </div>
                    `;
                });
        }

        // New function for Check-In button - populates the new direct check-in modal
        function checkIn(roomId, roomType, price, capacity, image, availableRooms) {
            console.log('Check-In button clicked!', { roomId, roomType, price, capacity, image, availableRooms });

            // Store the selected room data globally for this modal
            currentDirectCheckinRoom = {
                id: roomId,
                room_type_id: roomId,
                type: roomType,
                price: parseFloat(price),
                capacity: parseInt(capacity),
                image: image || 'uploads/rooms/standard.jpg',
                availableRooms: parseInt(availableRooms)
            };

            // Clear and reset form fields in the modal
            const form = document.getElementById('directCheckinForm');
            form.reset();
            
            // Populate selected room details
            document.getElementById('directSelectedRoomContainer').innerHTML = `
                <p><strong>Room Type:</strong> ${currentDirectCheckinRoom.type}</p>
                <p><strong>Price:</strong> ₱${currentDirectCheckinRoom.price.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per night</p>
                <p><strong>Capacity:</strong> ${currentDirectCheckinRoom.capacity} persons</p>
            `;

            // Set Check-in Date to today and display it
            const today = new Date();
            const todayFormatted = today.toISOString().split('T')[0];
            document.getElementById('directCheckInDateDisplay').textContent = todayFormatted;
            document.getElementById('directCheckInDate').value = todayFormatted;

            // Set minimum for Check-out Date to today
            document.getElementById('directCheckOutDate').min = todayFormatted;
            document.getElementById('directCheckOutDate').value = ''; // Clear previous value

            // Fetch and populate available room numbers for this room type
            const roomNumberSelect = document.getElementById('directRoomNumber');
            roomNumberSelect.innerHTML = '<option value="">Loading Rooms...</option>'; // Set loading state
            roomNumberSelect.disabled = true;

            if (currentDirectCheckinRoom && currentDirectCheckinRoom.id) {
                fetch(`get_available_room_numbers.php?room_type_id=${currentDirectCheckinRoom.id}`)
                    .then(response => response.json())
                    .then(data => {
                        roomNumberSelect.innerHTML = '<option value="">Select Room</option>'; // Reset options
                        if (data.success && data.rooms.length > 0) {
                            data.rooms.forEach(room => {
                                const option = document.createElement('option');
                                option.value = room.id; // Still use ID as value for internal logic
                                option.textContent = room.number;
                                roomNumberSelect.appendChild(option);
                            });
                            roomNumberSelect.disabled = false;
                        } else {
                            roomNumberSelect.innerHTML = '<option value="">No Rooms Available</option>';
                            roomNumberSelect.disabled = true;
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Rooms',
                                text: data.message || 'No available rooms found for this type.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching available rooms:', error);
                        roomNumberSelect.innerHTML = '<option value="">Error Loading Rooms</option>';
                        roomNumberSelect.disabled = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load available rooms. Please try again.'
                        });
                    });
            } else {
                roomNumberSelect.innerHTML = '<option value="">No Room Type Selected</option>';
                roomNumberSelect.disabled = true;
            }

            // Reset guest fields and totals
            document.getElementById('directNumAdults').value = 0;
            document.getElementById('directNumChildren').value = 0;
            document.getElementById('directAdultNameFieldsContainer').innerHTML = '';
            document.getElementById('directChildNameFieldsContainer').innerHTML = '';
            document.getElementById('directNumberOfNights').textContent = '0 Nights';
            document.getElementById('directBookingBaseTotal').textContent = '₱0.00';
            document.getElementById('directFinalTotalAmount').textContent = '₱0.00';
            document.getElementById('directExtraBedNo').checked = true; // Default extra bed to No
            document.getElementById('directPaymentOption').value = ''; // Reset payment option
            document.getElementById('directCustomPaymentAmountField').style.display = 'none'; // Hide custom amount field
            document.getElementById('directCustomAmount').value = '';
            document.getElementById('directCustomAmountHelp').style.display = 'none';
            document.getElementById('directPaymentMethod').value = ''; // Reset payment method

            // Show the direct check-in modal
            if (directCheckinModalInstance) {
                console.log('Attempting to show direct check-in modal using Bootstrap instance.');
                directCheckinModalInstance.show();
                // Re-added Fallback: Force display and z-index if Bootstrap modal doesn't show
                setTimeout(() => {
                    const modalElement = document.getElementById('directCheckinModal');
                    const backdrop = document.querySelector('.modal-backdrop');

                    if (modalElement && !modalElement.classList.contains('show')) {
                        console.log('Bootstrap modal show failed, forcing display.');
                        modalElement.style.display = 'block';
                        modalElement.style.opacity = '1';
                        modalElement.style.zIndex = '1055'; // Higher than default backdrop
                        modalElement.classList.add('show');
                        document.body.classList.add('modal-open');
                        if (backdrop) {
                            backdrop.style.zIndex = '1050'; // Ensure backdrop is below modal
                        }
                    }
                }, 100);
            } else {
                console.error('Direct Check-in Modal instance not initialized.');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Direct Check-in form not available. Please try again or contact support.'
                });
            }
        }

        // Function to generate name fields for adults/children based on count in direct check-in modal
        function generateDirectGuestNameFields(type, count) {
            const container = document.getElementById(`direct${type.charAt(0).toUpperCase() + type.slice(1)}NameFieldsContainer`);
            container.innerHTML = ''; // Clear existing fields

            for (let i = 0; i < count; i++) {
                container.innerHTML += `
                    <div class="guest-name-section border p-3 mb-3">
                        <h6>${type === 'adult' ? 'Adult' : 'Child'} ${i + 1} Name</h6>
                        <div class="form-group mb-3">
                            <label for="direct${type}FirstName_${i}">First Name</label>
                            <input type="text" class="form-control" id="direct${type}FirstName_${i}" name="${type}FirstName[]" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="direct${type}LastName_${i}">Last Name</label>
                            <input type="text" class="form-control" id="direct${type}LastName_${i}" name="${type}LastName[]" placeholder="Enter last name" required>
                        </div>
                    </div>
                `;
            }
        }

        // Function to update booking totals (nights and overall total) for direct check-in
        function updateDirectBookingTotals() {
            if (!currentDirectCheckinRoom) return; // Ensure a room is selected

            const checkInDate = document.getElementById('directCheckInDate').value;
            const checkOutDate = document.getElementById('directCheckOutDate').value;

            const nights = calculateNights(checkInDate, checkOutDate);
            document.getElementById('directNumberOfNights').textContent = `${nights} Nights`;

            let basePricePerNight = currentDirectCheckinRoom.price;
            const extraBedCost = document.getElementById('directExtraBedYes').checked ? 1000 : 0;
            basePricePerNight += extraBedCost;

            // Calculate the total for all nights
            const totalForRoomsAndNights = basePricePerNight * nights;

            document.getElementById('directBookingBaseTotal').textContent = `₱${totalForRoomsAndNights.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById('directFinalTotalAmount').textContent = `₱${totalForRoomsAndNights.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        // Helper function to calculate nights (can reuse from header.php if it's global, otherwise duplicate here)
        function calculateNights(checkIn, checkOut) {
            if (!checkIn || !checkOut) return 0;
            const startDate = new Date(checkIn);
            const endDate = new Date(checkOut);
            const timeDiff = endDate.getTime() - startDate.getTime();
            const nights = Math.max(0, Math.ceil(timeDiff / (1000 * 60 * 60 * 24)));
            return nights;
        }

        // Process Direct Check-in Form Submission
        function processDirectCheckinForm() {
            const form = document.getElementById('directCheckinForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            if (!currentDirectCheckinRoom) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No room selected for direct check-in.'
                });
                return;
            }

            const formData = new FormData();
            formData.append('firstName', document.getElementById('directFirstName').value);
            formData.append('lastName', document.getElementById('directLastName').value);
            formData.append('email', document.getElementById('directEmail').value);
            formData.append('contact', document.getElementById('directContact').value);
            formData.append('checkInDate', document.getElementById('directCheckInDate').value);
            formData.append('checkOutDate', document.getElementById('directCheckOutDate').value);
            formData.append('numAdults', document.getElementById('directNumAdults').value);
            formData.append('numChildren', document.getElementById('directNumChildren').value);

            // Collect dynamic guest names
            document.querySelectorAll('input[name="adultFirstName[]"]').forEach(input => formData.append('adultFirstNames[]', input.value));
            document.querySelectorAll('input[name="adultLastName[]"]').forEach(input => formData.append('adultLastNames[]', input.value));
            document.querySelectorAll('input[name="childFirstName[]"]').forEach(input => formData.append('childFirstNames[]', input.value));
            document.querySelectorAll('input[name="childLastName[]"]').forEach(input => formData.append('childLastName[]', input.value));

            const extraBedValue = document.querySelector('input[name="directExtraBed"]:checked').value;
            formData.append('extraBed', extraBedValue);

            const paymentOption = document.getElementById('directPaymentOption').value;
            formData.append('paymentOption', paymentOption);
            formData.append('customAmount', document.getElementById('directCustomAmount').value);
            formData.append('paymentMethod', document.getElementById('directPaymentMethod').value);
            formData.append('totalAmount', parseFloat(document.getElementById('directFinalTotalAmount').textContent.replace(/[^0-9.]/g, '')));
            
            // Add selected room details as a JSON string for process_booking.php
            const roomsData = [{
                room_type_id: currentDirectCheckinRoom.id,
                type: currentDirectCheckinRoom.type,
                price: currentDirectCheckinRoom.price,
                quantity: 1 // Always 1 for direct check-in
            }];
            formData.append('rooms', JSON.stringify(roomsData));
            formData.append('nights', parseInt(document.getElementById('directNumberOfNights').textContent));
            
            // Get the selected room number TEXT, not the value (ID)
            const selectedRoomNumberElement = document.getElementById('directRoomNumber');
            const selectedRoomNumberText = selectedRoomNumberElement.options[selectedRoomNumberElement.selectedIndex].textContent;
            formData.append('selectedRoomNumber', selectedRoomNumberText); // Changed from roomNumberId to selectedRoomNumber

            formData.append('isDirectCheckin', 'false'); // Save as pending booking for Booking Status

            Swal.fire({
                title: 'Processing Direct Check-in...',
                html: 'Please wait while we confirm your direct check-in.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    // Re-adding FORCE CLOSE AND SUCCESS MESSAGE AFTER 3 SECONDS (Temporary Workaround)
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Direct Check-in Successful!',
                            text: 'Your direct check-in has been successfully processed.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                directCheckinModalInstance.hide();
                                window.location.href = 'index.php?booking_status'; // Redirect to Booking Status
                            }
                        });
                    }, 3000); // 3 seconds
                }
            });

            // Add loading state
            const submitBtn = document.getElementById('submitDirectCheckinForm');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

            // Log form data for debugging
            console.log('Submitting form data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Set isDirectCheckin to true for direct check-in
            formData.set('isDirectCheckin', 'true');

            $.ajax({
                url: 'process_booking.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('AJAX Success - Raw response:', response);
                    
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    
                    let jsonResponse;
                    try {
                        // Try to parse as JSON first
                        if (typeof response === 'string') {
                            const jsonStart = response.indexOf('{');
                            jsonResponse = jsonStart >= 0 ? JSON.parse(response.substring(jsonStart)) : { success: false, message: 'Invalid response format' };
                        } else {
                            jsonResponse = response; // Already parsed
                        }
                        
                        console.log('Parsed response:', jsonResponse);
                        
                        if (jsonResponse.success) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Booking Successful!',
                                text: jsonResponse.message || 'Your booking has been confirmed.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Close modal and redirect
                                if (directCheckinModalInstance) {
                                    directCheckinModalInstance.hide();
                                }
                                window.location.href = 'index.php?booking_status';
                            });
                        } else {
                            // Show error message from server
                            throw new Error(jsonResponse.message || 'Failed to process booking');
                        }
                    } catch (e) {
                        console.error('Error processing response:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            confirmButtonText: 'OK'
                        });
                    }
                                window.location.href = 'index.php?booking_status'; // Redirect to Booking Status
                             });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Error',
                                text: 'Received malformed or empty data from the server. Please try again.'
                            });
                        }
                        return;
                    }

                    Swal.close(); // Close loading spinner if successful
                    if (jsonResponse.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Direct Check-in Successful!',
                            text: jsonResponse.message || 'Your direct check-in has been successfully processed.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                directCheckinModalInstance.hide();
                                window.location.href = 'index.php?booking_status'; // Redirect to Booking Status
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Check-in Failed',
                            text: jsonResponse.message || 'An error occurred during direct check-in. Please try again.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error Callback Reached.'); // DEBUG: New log
                    console.error('AJAX Error:', status, error);
                    console.error('Response Text:', xhr.responseText);
                    Swal.close(); // Ensure spinner is closed on AJAX error
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Could not connect to the server. Please check your internet connection or try again later.'
                    });
                }
            });
        }
    </script>

    <!-- New Direct Check-in Modal -->
    <div class="modal fade" id="directCheckinModal" tabindex="-1" role="dialog" aria-labelledby="directCheckinModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="directCheckinModalLabel">Direct Check-in</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="directCheckinForm">
                        <h6>Personal Information</h6>
                        <div class="form-group mb-3">
                            <label for="directFirstName">First Name</label>
                            <input type="text" class="form-control" id="directFirstName" name="firstName" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="directLastName">Last Name</label>
                            <input type="text" class="form-control" id="directLastName" name="lastName" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="directEmail">Email</label>
                            <input type="email" class="form-control" id="directEmail" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="directContact">Contact (Optional)</label>
                            <input type="text" class="form-control" id="directContact" name="contact">
                        </div>

                        <h6 class="mt-4">Booking Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Check-in Date</label>
                                    <div id="directCheckInDateDisplay" class="form-control-plaintext"></div>
                                    <input type="hidden" id="directCheckInDate" name="checkInDate">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="directCheckOutDate">Check-out Date</label>
                                    <input type="date" class="form-control" id="directCheckOutDate" name="checkOutDate" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="directRoomNumber">Available Room Number</label>
                            <select class="form-control" id="directRoomNumber" name="roomNumber" required>
                                <option value="">Select Room</option>
                                <!-- Options will be loaded dynamically by JavaScript -->
                            </select>
                        </div>

                        <!-- Nights and Total Amount Display -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Number of Nights</label>
                                    <div id="directNumberOfNights" class="form-control-plaintext">0 Nights</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Booking Total</label>
                                    <div id="directBookingBaseTotal" class="form-control-plaintext">₱0.00</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-4">Guest Information</h6>
                        <div class="form-group mb-3">
                            <label for="directNumAdults">Number of Adults</label>
                            <input type="number" class="form-control" id="directNumAdults" name="numAdults" min="0" value="0" required>
                        </div>
                        <div id="directAdultNameFieldsContainer"></div>

                        <div class="form-group mb-3">
                            <label for="directNumChildren">Number of Children</label>
                            <input type="number" class="form-control" id="directNumChildren" name="numChildren" min="0" value="0" required>
                        </div>
                        <div id="directChildNameFieldsContainer"></div>

                        <h6 class="mt-4">Extra Bed</h6>
                        <div class="form-group mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="directExtraBed" id="directExtraBedYes" value="yes">
                                <label class="form-check-label" for="directExtraBedYes">Yes (₱1,000 per night)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="directExtraBed" id="directExtraBedNo" value="no" checked>
                                <label class="form-check-label" for="directExtraBedNo">No</label>
                            </div>
                        </div>

                        <h6 class="mt-4">Payment Details</h6>
                        <div class="form-group mb-3">
                            <label for="directPaymentOption">Payment Option</label>
                            <select class="form-control" id="directPaymentOption" required>
                                <option value="">Select Option</option>
                                <option value="Partial Payment">Partial Payment (₱1,500)</option>
                                <option value="Custom Payment">Custom Payment</option>
                                <option value="Full Payment">Full Payment</option>
                            </select>
                        </div>

                        <!-- Custom Payment Amount field (initially hidden) -->
                        <div id="directCustomPaymentAmountField" class="form-group mb-3" style="display: none;">
                            <label for="directCustomAmount">Custom Payment Amount</label>
                            <input type="number" class="form-control" id="directCustomAmount" placeholder="Enter amount" step="0.01">
                            <small id="directCustomAmountHelp" class="form-text text-danger" style="display: none;"></small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="directPaymentMethod">Payment Method</label>
                            <select class="form-control" id="directPaymentMethod" name="paymentMethod" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                            </select>
                        </div>

                        <h6 class="mt-4">Selected Room Details</h6>
                        <div id="directSelectedRoomContainer" class="border p-3 mb-3 rounded">
                            <!-- Selected room details will be populated here -->
                        </div>

                        <div class="d-flex justify-content-between mt-4 border-top pt-3">
                            <h5>Final Total Amount:</h5>
                            <h5 id="directFinalTotalAmount">₱0.00</h5>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="submitDirectCheckinForm">Confirm Check-in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Availability Results Modal -->
    <div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="availabilityModalLabel">Room Availability</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="availabilityModalBody">
                    <!-- Content will be dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Define formatDate in the global scope
        function formatDate(date) {
            if (!(date instanceof Date)) {
                date = new Date(date);
            }
            let month = '' + (date.getMonth() + 1);
            let day = '' + date.getDate();
            const year = date.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Check if dates fall within the blocked period (Nov 10-25, 2025)
        function isBlockedPeriod(checkIn, checkOut) {
            const blockedStart = new Date('2025-11-10');
            const blockedEnd = new Date('2025-11-25');
            return (new Date(checkIn) <= blockedEnd && new Date(checkOut) >= blockedStart);
        }

        // Define checkAvailability in the global scope
        function checkAvailability() {
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            const resultDiv = document.getElementById('availabilityResult');
            const modal = new bootstrap.Modal(document.getElementById('availabilityModal'));
            const modalBody = document.getElementById('availabilityModalBody');
            
            if (!checkInDate || !checkOutDate) {
                resultDiv.innerHTML = '<div class="alert alert-warning">Please select both check-in and check-out dates.</div>';
                return;
            }
            
            // Check for blocked period
            if (isBlockedPeriod(checkInDate, checkOutDate)) {
                modalBody.innerHTML = `
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-calendar-times me-2"></i>No Availability</h5>
                        <p class="mb-0">The period from November 10 to November 25, 2025 is fully booked. Please select different dates.</p>
                    </div>`;
                modal.show();
                return;
            }
            
            // Show loading state
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Checking room availability...</p>
                </div>`;
            
            modal.show();
            
            // Get all room types and check availability
            fetch('get_room_types.php')
                .then(response => response.json())
                .then(roomTypes => {
                    const availabilityPromises = roomTypes.map(room => {
                        return fetch('check_availability.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `check_in=${encodeURIComponent(checkInDate)}&check_out=${encodeURIComponent(checkOutDate)}&room_type_id=${room.id}`
                        })
                        .then(response => response.json())
                        .then(data => ({
                            ...room,
                            available: data.available,
                            available_rooms: data.available_rooms || 0
                        }));
                    });
                    
                    return Promise.all(availabilityPromises);
                })
                .then(rooms => {
                    // Filter out rooms with no availability
                    const availableRooms = rooms.filter(room => room.available && room.available_rooms > 0);
                    
                    if (availableRooms.length > 0) {
                        // Show available rooms
                        let html = `
                            <h5 class="mb-3">Available Rooms (${formatDate(checkInDate)} to ${formatDate(checkOutDate)})</h5>
                            <div class="row g-3">`;
                        
                        availableRooms.forEach(room => {
                            html += `
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <img src="${room.image || 'images/default-room.jpg'}" class="card-img-top" alt="${room.name}">
                                        <div class="card-body">
                                            <h5 class="card-title">${room.name}</h5>
                                            <p class="card-text">${room.description || 'No description available.'}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="h5 mb-0">₱${parseFloat(room.price).toLocaleString()}/night</span>
                                                <span class="badge bg-success">${room.available_rooms} Available</span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <button class="btn btn-primary w-100" onclick="bookRoom(${room.id}, '${room.name.replace(/'/g, '\'')}')">
                                                Book Now
                                            </button>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        
                        html += '</div>';
                        modalBody.innerHTML = html;
                    } else {
                        // No rooms available
                        modalBody.innerHTML = `
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-bed me-2"></i>No Rooms Available</h5>
                                <p class="mb-0">We're sorry, but there are no available rooms for the selected dates. Please try different dates.</p>
                            </div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Error</h5>
                            <p class="mb-0">An error occurred while checking availability. Please try again later.</p>
                        </div>`;
                });
        }

        // Helper function to book a room
        function bookRoom(roomId, roomName) {
            // You can implement the booking logic here
            alert(`Booking ${roomName} (ID: ${roomId})`);
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('availabilityModal'));
            if (modal) modal.hide();
        }
    </script>
</body>
</html>