<?php
require_once 'db_con.php'; // change path if needed

$availabilityMessage = '';
$availabilityType = '';

// Get submitted dates or use null if not set
$submittedCheckin = $_POST['checkin'] ?? null;
$submittedCheckout = $_POST['checkout'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $checkin  = $_POST['checkin'] ?? '';
    $checkout = $_POST['checkout'] ?? '';

    if ($checkin && $checkout && $checkin < $checkout) {
        // Get room types with accurate availability check
        $sql = "SELECT 
                    rt.room_type_id,
                    rt.room_type,
                    rt.price,
                    rt.capacity,
                    rt.beds,
                    rt.description,
                    rt.image,
                    rt.discount_percent,
                    rt.discount_valid_until,
                    COUNT(DISTINCT rn.room_number_id) as total_rooms,
                    COUNT(DISTINCT rn.room_number_id) - COALESCE(booked_rooms.booking_count, 0) as available_rooms
                FROM room_types rt
                JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id AND rn.status = 'active'
                LEFT JOIN (
                    SELECT 
                        rn2.room_type_id,
                        COUNT(DISTINCT CASE 
                            WHEN br.room_number_fk_id IS NOT NULL THEN br.room_number_fk_id 
                            ELSE rn2.room_number_id 
                        END) as booking_count
                    FROM room_numbers rn2
                    JOIN booked_rooms br ON (rn2.room_number_id = br.room_number_fk_id OR rn2.room_type_id = br.room_type_id)
                    JOIN bookings b ON br.booking_id = b.booking_id
                    WHERE b.status IN ('confirmed', 'checked_in')
                    AND (
                        (b.check_in < ? AND b.check_out > ?) OR  -- Existing booking starts before and ends after checkin
                        (b.check_in < ? AND b.check_out > ?) OR  -- Existing booking starts before checkout and ends after
                        (b.check_in >= ? AND b.check_out <= ?)   -- Existing booking is completely within the range
                    )
                    AND b.check_out > ?
                    GROUP BY rn2.room_type_id
                ) as booked_rooms ON rt.room_type_id = booked_rooms.room_type_id
                WHERE rt.status = 'active'
                GROUP BY rt.room_type_id";  // Only show rooms with availability

        $stmt = $pdo->prepare($sql);
        // Parameters for the date range check (repeated for each condition)
        $params = [
            $checkout, $checkin,   // For first condition (check_in < checkout AND check_out > checkin)
            $checkin, $checkout,   // For second condition (check_in < checkin AND check_out > checkout)
            $checkin, $checkout,   // For third condition (check_in >= checkin AND check_out <= checkout)
            $checkin               // Additional check for check_out > checkin
        ];
        error_log("Checking availability for $checkin to $checkout");
        $stmt->execute($params);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate overall availability status
        $totalRooms = 0;
        $totalAvailable = 0;
        $allAvailable = true;
        $someAvailable = false;
        $fullyBooked = true;

        foreach ($rooms as $room) {
            $totalRooms += $room['total_rooms'];
            $totalAvailable += $room['available_rooms'];
            
            if ($room['available_rooms'] < $room['total_rooms']) {
                $allAvailable = false;
                $someAvailable = true;
            }
            
            if ($room['available_rooms'] > 0) {
                $fullyBooked = false;
            }
        }

        // Set appropriate message based on availability
        if ($allAvailable && $totalRooms > 0) {
            $availabilityMessage = 'All rooms are available for the selected dates.';
            $availabilityType = 'success';
        } elseif ($someAvailable) {
            $availabilityMessage = 'Some rooms are available for the selected dates.';
            $availabilityType = 'warning';
        } elseif ($fullyBooked) {
            $availabilityMessage = 'Sorry, no rooms are available for the selected dates.';
            $availabilityType = 'danger';
        } else {
            $availabilityMessage = 'No rooms found for the selected criteria.';
            $availabilityType = 'info';
        }
        
        // Debug logging
        error_log("=== ROOM AVAILABILITY DEBUG ===");
        error_log("Check-in: $checkin, Check-out: $checkout");
        error_log("Found " . count($rooms) . " room types with availability");
        error_log("Total rooms: $totalRooms, Available: $totalAvailable");
        error_log("Status: " . $availabilityMessage);

        // Update room status and availability text
        foreach ($rooms as &$room) {
            $room['is_available'] = ($room['available_rooms'] > 0);
            if ($room['available_rooms'] > 0) {
                $room['availability_text'] = "{$room['available_rooms']} of {$room['total_rooms']} rooms available";
            } else {
                $room['availability_text'] = 'Fully booked';
            }
        }
        unset($room); // Break the reference

        // Store in session
        $_SESSION['room_availability'] = [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'rooms' => $rooms
        ];

        // Message already set in the availability check above

    } else {
        $availabilityType = 'danger';
        $availabilityMessage = 'Invalid check-in or check-out date.';
    }
}
?>

<div class="room-availability-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="room-availability">
                    <h3>Check Room Availability</h3>
                    <form id="roomAvailabilityForm" method="POST" action="">
                        <div class="row g-3">
                            <!-- Check-in Date -->
                            <div class="col-12 col-md-6 col-lg-5">
                                <div class="form-group h-100">
                                    <label for="checkin" class="form-label">Check-in Date</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control form-control-lg" id="checkin" name="checkin" value="<?= htmlspecialchars($submittedCheckin) ?>" required>
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Check-out Date -->
                            <div class="col-12 col-md-6 col-lg-5">
                                <div class="form-group h-100">
                                    <label for="checkout" class="form-label">Check-out Date</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control form-control-lg" id="checkout" name="checkout" value="<?= htmlspecialchars($submittedCheckout) ?>" required>
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="col-12 col-md-12 col-lg-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fa fa-search me-2"></i>Check Availability
                                </button>
                            </div>
                    </form>
                    
                    <?php if (!empty($availabilityMessage)): ?>
                        <div class="alert alert-<?= $availabilityType ?> mt-3" role="alert">
                            <i class="fa fa-info-circle"></i> <?= $availabilityMessage ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .room-availability-container{
        padding-top: 50px;
    }
/* Base styles */
.room-availability {
    background: #fff;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.room-availability h3 {
    margin: 0 0 1.5rem 0;
    color: #2c3e50;
    font-weight: 700;
    font-size: 1.5rem;
    text-align: center;
}

/* Form elements */
.room-availability .form-group {
    margin-bottom: 1rem;
    height: 100%;
}

.room-availability .form-label {
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.room-availability .form-control,
.room-availability .form-select {
    height: 50px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 1rem;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
}

.room-availability .form-control:focus,
.room-availability .form-select:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
}

.room-availability .input-group-text {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: none;
    color: #718096;
    padding: 0 1rem;
}

.room-availability .input-group .form-control:not(:first-child) {
    border-left: 0;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Button styles */
.room-availability .btn-primary {
    background: linear-gradient(45deg, #3498db, #2ecc71);
    border: none;
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1.5rem;
}

.room-availability .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
}

.room-availability .btn-primary i {
    margin-right: 8px;
}

/* Alert styles */
.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1.5rem;
    font-size: 0.95rem;
    border: 1px solid transparent;
}

.alert i {
    margin-right: 8px;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .room-availability {
        padding: 1.25rem;
        margin: 0 -15px 2rem;
        border-radius: 0;
        box-shadow: none;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .room-availability h3 {
        font-size: 1.3rem;
        margin-bottom: 1.25rem;
    }
    
    .room-availability .form-control,
    .room-availability .form-select {
        height: 46px;
        font-size: 0.95rem;
    }
    
    .room-availability .btn-primary {
        height: 46px;
        font-size: 0.95rem;
        margin-top: 0.5rem;
    }
    
    .room-availability .col-6 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* Animation for form elements */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.room-availability .form-group {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for check-in to today
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Format date as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set min date for check-in and check-out
    document.getElementById('checkin').min = formatDate(today);
    document.getElementById('checkout').min = formatDate(tomorrow);
    
    // Only set default values if the fields are empty
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    
    if (!checkinInput.value) {
        checkinInput.value = formatDate(today);
    }
    if (!checkoutInput.value) {
        checkoutInput.value = formatDate(tomorrow);
    }
    
    // Update min check-out date when check-in date changes
    document.getElementById('checkin').addEventListener('change', function() {
        const checkinDate = new Date(this.value);
        const nextDay = new Date(checkinDate);
        nextDay.setDate(nextDay.getDate() + 1);
        document.getElementById('checkout').min = formatDate(nextDay);
        
        // If current check-out is before new min date, update it
        const currentCheckout = new Date(document.getElementById('checkout').value);
        if (currentCheckout <= checkinDate) {
            document.getElementById('checkout').value = formatDate(nextDay);
        }
    });
});
</script>

