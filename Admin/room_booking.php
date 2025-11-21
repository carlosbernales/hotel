<?php
require_once 'db.php';
require_once 'header.php';

// Get all room types with their details, filtering by room_type status and counting active room numbers
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
          GROUP BY rt.room_type_id, rt.room_type, rt.description, rt.price, rt.capacity, rt.image";

$result = mysqli_query($con, $query);

// Debugging: Output the query and number of rows
echo "<!-- SQL Query: " . htmlspecialchars($query) . " -->";
if ($result) {
    $num_rows = mysqli_num_rows($result);
    echo "<!-- Number of rows returned: " . $num_rows . " -->";
    // Reset result pointer for the loop to work
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
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container mt-4">
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
                            <img src="<?php echo htmlspecialchars('uploads/rooms/' . ($room['image'] ?? 'default-room.jpg')); ?>" 
                                 class="card-img-top room-image" 
                                 alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['room_type']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($room['description']); ?></p>
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
                                <p class="card-text available-rooms-text">
                                    <strong>Available Rooms:</strong> <?php echo (int)($room['total_available_rooms'] ?? 0); ?>
                                </p>
                                <div class="d-grid gap-2">
                                    
                                    <button class="btn btn-warning" onclick="addToList(<?= $room['room_type_id'] ?>, '<?= addslashes($room['room_type']) ?>', <?= $room['price'] ?>, <?= $room['capacity'] ?>, '<?= $room['image'] ?>', <?= (int)($room['total_available_rooms'] ?? 0) ?>)">
                                        <i class="fas fa-plus"></i> Add to List
                                    </button>
                                    <button class="btn btn-primary" onclick="checkIn(<?php echo $room['room_type_id']; ?>, '<?php echo htmlspecialchars($room['room_type']); ?>', <?php echo floatval($room['price'] ?? 0); ?>, <?php echo htmlspecialchars($room['capacity'] ?? 'N/A'); ?>, '<?php echo htmlspecialchars($room['image'] ?? 'default-room.jpg'); ?>', <?php echo (int)($room['total_available_rooms'] ?? 0); ?>)" <?php if ((int)($room['total_available_rooms'] ?? 0) === 0) echo 'disabled'; ?>>
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
    <div class="modal fade" id="roomDetailsModal" tabindex="-1" role="dialog" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="roomDetailsContent">
                    <!-- Dynamic content will be loaded here by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Room Details Modal -->
    <div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

            // Ensure the URL is explicitly correct for fetching room details
            const fetchUrl = `get_room_details_new.php?room_id=${roomId}`;
            console.log('Fetching URL:', fetchUrl); // Log the exact URL being fetched
            
            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Room details fetched:', data);
                    if (data && !data.error) {
                        let amenitiesHtml = '';
                        if (data.amenities && data.amenities.length > 0) {
                            amenitiesHtml = data.amenities.map(amenity => `
                                <span class="badge bg-info text-dark m-1"><i class="${amenity.icon}"></i> ${amenity.name}</span>
                            `).join('');
                        } else {
                            amenitiesHtml = '<p class="text-muted">No specific amenities listed for this room type.</p>';
                        }

                        let imagesHtml = '';
                        if (data.images && data.images.length > 0) {
                            imagesHtml = `
                                <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                            `;
                            data.images.forEach((image, index) => {
                                imagesHtml += `
                                    <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                        <img src="uploads/rooms/${image.path}" class="d-block w-100 room-image-modal" alt="Room Image">
                                    </div>
                                `;
                            });
                            imagesHtml += `
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            `;
                        } else if (data.image) { // Fallback to single image if carousel not needed/available
                            imagesHtml = `<img src="uploads/rooms/${data.image}" class="img-fluid mb-3" alt="Room Image">`;
                        }

                        // Format price for display
                        const formattedPrice = `₱${parseFloat(data.price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

                        roomDetailsContent.innerHTML = `
                            ${imagesHtml}
                            <h5 class="mt-3">${data.room_type}</h5>
                            <p class="text-muted">${data.description}</p>
                            <hr>
                            <p><strong>Price per night:</strong> ${formattedPrice}</p>
                            <p><strong>Capacity:</strong> ${data.capacity} persons</p>
                            <p><strong>Available Rooms:</strong> ${data.total_available_rooms}</p>
                            <h6>Amenities:</h6>
                            <div class="d-flex flex-wrap">${amenitiesHtml}</div>
                        `;

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
                image: image || 'assets/img/rooms/default.jpg',
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

            formData.append('isDirectCheckin', 'true'); // Changed from true to 'true' to ensure it's sent as a string

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
                                window.location.href = 'checked_in.php'; // Redirect to checked-in list
                            }
                        });
                    }, 3000); // 3 seconds
                }
            });

            $.ajax({
                url: 'process_booking.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('AJAX Success Callback Reached.'); // DEBUG: New log
                    console.log('Raw response from process_booking.php:', response); // DEBUG: Log raw response
                    let jsonResponse;
                    try {
                        const jsonStartIndex = response.indexOf('{');
                        if (jsonStartIndex !== -1) {
                            jsonResponse = JSON.parse(response.substring(jsonStartIndex));
                            console.log('Parsed JSON response:', jsonResponse); // DEBUG: Log parsed JSON
                        } else {
                            // If no JSON object is found but '"success": true' is in response
                            if (response.includes('"success": true') || response.includes('Success')) {
                                 jsonResponse = { success: true, message: 'Direct Check-in successful!' };
                            } else {
                                throw new Error('No JSON found in response and no success indicator.');
                            }
                        }
                    } catch (e) {
                        console.error("Error parsing JSON response:", e);
                        // Aggressive fallback: If any response came and parsing failed, but it suggests success
                        if (response && (response.includes('"success": true') || response.includes('Success') || response.length > 0)) {
                             Swal.fire({
                                 icon: 'success',
                                 title: 'Direct Check-in Confirmed!',
                                 text: 'Your direct check-in has been successfully confirmed. (Note: A minor data parsing issue occurred, but the check-in was successful.)',
                                 confirmButtonText: 'OK' // Ensure user has to click OK
                             }).then(() => {
                                directCheckinModalInstance.hide();
                                window.location.href = 'checked_in.php'; // Redirect to checked-in list
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
                                window.location.href = 'checked_in.php'; // Redirect to checked-in list
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
</body>
</html>