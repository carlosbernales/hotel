<?php
// Room availability interface with date selection
?>

<!-- Room Availability Check Section -->
<div class="availability-section">
    <div class="container">
        <h2 class="section-title">Check Room Availability</h2>
        
        <form id="availabilityForm" class="availability-form">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="check_in_date">
                            <i class="fas fa-calendar-check"></i> Check-in Date
                        </label>
                        <input type="date" 
                               id="check_in_date" 
                               name="check_in_date" 
                               class="form-control" 
                               required
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="check_out_date">
                            <i class="fas fa-calendar-times"></i> Check-out Date
                        </label>
                        <input type="date" 
                               id="check_out_date" 
                               name="check_out_date" 
                               class="form-control" 
                               required
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="num_rooms">
                            <i class="fas fa-door-open"></i> Rooms
                        </label>
                        <select id="num_rooms" name="num_rooms" class="form-control">
                            <option value="1">1 Room</option>
                            <option value="2">2 Rooms</option>
                            <option value="3">3 Rooms</option>
                            <option value="4">4 Rooms</option>
                            <option value="5">5 Rooms</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="num_adults">
                            <i class="fas fa-user"></i> Adults
                        </label>
                        <select id="num_adults" name="num_adults" class="form-control">
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="num_children">
                            <i class="fas fa-child"></i> Children
                        </label>
                        <select id="num_children" name="num_children" class="form-control">
                            <option value="0" selected>0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn-check-availability">
                    <i class="fas fa-search"></i> Check Availability
                </button>
            </div>
        </form>
        
        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="loading-indicator" style="display: none;">
            <div class="spinner"></div>
            <p>Checking room availability...</p>
        </div>
        
        <!-- Results Container -->
        <div id="availabilityResults" class="availability-results" style="display: none;">
            <!-- Results will be loaded here via AJAX -->
        </div>
    </div>
</div>

<!-- Styles for Availability Section -->
<style>
.availability-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 0;
    margin-bottom: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.section-title {
    color: white;
    text-align: center;
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 30px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.availability-form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: block;
}

.form-group label i {
    margin-right: 5px;
    color: #667eea;
}

.form-control {
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    padding: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-check-availability {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 40px;
    font-size: 18px;
    font-weight: 600;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-check-availability:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.loading-indicator {
    text-align: center;
    padding: 40px;
    background: white;
    border-radius: 10px;
    margin-top: 20px;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.availability-results {
    margin-top: 30px;
}

.room-availability-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.room-availability-card:hover {
    transform: translateY(-2px);
}

.room-availability-card h4 {
    color: #333;
    margin-bottom: 15px;
}

.availability-status {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.status-available {
    background: #d4edda;
    color: #155724;
}

.status-unavailable {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .availability-section {
        padding: 20px 0;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .availability-form {
        padding: 20px;
    }
    
    .row .col-md-2,
    .row .col-md-3 {
        margin-bottom: 15px;
    }
}
</style>

<!-- JavaScript for Availability Check -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const availabilityForm = document.getElementById('availabilityForm');
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const resultsContainer = document.getElementById('availabilityResults');
    
    // Set minimum dates
    const today = new Date().toISOString().split('T')[0];
    checkInDate.min = today;
    checkOutDate.min = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    
    // Update check-out minimum date when check-in changes
    checkInDate.addEventListener('change', function() {
        const checkIn = new Date(this.value);
        const minCheckOut = new Date(checkIn.getTime() + 86400000);
        checkOutDate.min = minCheckOut.toISOString().split('T')[0];
        
        // Clear check-out if it's before the new minimum
        if (new Date(checkOutDate.value) <= checkIn) {
            checkOutDate.value = '';
        }
    });
    
    // Handle form submission
    availabilityForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const checkIn = formData.get('check_in_date');
        const checkOut = formData.get('check_out_date');
        
        // Validation
        if (!checkIn || !checkOut) {
            alert('Please select both check-in and check-out dates');
            return;
        }
        
        if (new Date(checkOut) <= new Date(checkIn)) {
            alert('Check-out date must be after check-in date');
            return;
        }
        
        // Show loading
        loadingIndicator.style.display = 'block';
        resultsContainer.style.display = 'none';
        
        // Show loading state
        loadingIndicator.style.display = 'block';
        resultsContainer.style.display = 'none';
        
        // Make AJAX call with error handling
        const url = `room_availability.php?check_in_date=${encodeURIComponent(checkIn)}&check_out_date=${encodeURIComponent(checkOut)}`;
        console.log('Making request to:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Network response was not ok');
                }).catch(() => {
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            loadingIndicator.style.display = 'none';
            
            if (data && data.success) {
                displayAvailabilityResults(data);
            } else {
                const errorMessage = data && data.message ? data.message : 'No availability data received';
                displayNoAvailability(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error details:', {
                error: error.toString(),
                message: error.message,
                stack: error.stack
            });
            loadingIndicator.style.display = 'none';
            alert('Error: ' + (error.message || 'An error occurred while checking availability. Please check the console for details.'));
        });
    });
    
    function displayAvailabilityResults(data) {
        let html = '<h3 class="mb-4">Available Rooms</h3>';
        html += '<div class="row">';
        
        data.available_rooms.forEach(room => {
            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="room-availability-card">
                        <h4>${room.room_type}</h4>
                        <p><strong>Available Rooms:</strong> ${room.available} of ${room.total_rooms}</p>
                        <span class="availability-status status-available">
                            <i class="fas fa-check-circle"></i> Available
                        </span>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
    }
    
    function displayNoAvailability(message) {
        let html = `
            <div class="alert alert-warning">
                <h4><i class="fas fa-exclamation-triangle"></i> No Availability</h4>
                <p>${message}</p>
            </div>
        `;
        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
    }
});
</script>

<?php
// This is a dedicated endpoint for checking room availability via AJAX

// Check if this is an AJAX request
if (isset($_GET['check_in']) && isset($_GET['check_out'])) {

// Clear any previous output
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Set headers first
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/availability_errors.log');

// Function to log errors
function log_error($message, $data = []) {
    $log_message = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
    if (!empty($data)) {
        $log_message .= 'Data: ' . print_r($data, true) . "\n";
    }
    error_log($log_message, 3, __DIR__ . '/availability_errors.log');
}

// Set JSON content type header
header('Content-Type: application/json');

// Check if this is an AJAX request
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Include database connection
try {
    require 'db_con.php';
} catch (Exception $e) {
    $error = 'Database connection failed: ' . $e->getMessage();
    log_error($error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

// Check if required parameters are provided
if (!isset($_GET['check_in_date']) || !isset($_GET['check_out_date'])) {
    $error = 'Missing required parameters: ' . print_r($_GET, true);
    log_error($error);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Check-in and check-out dates are required']);
    exit;
}

// Get and validate parameters
$check_in = trim($_GET['check_in_date']);
$check_out = trim($_GET['check_out_date']);

// Log the request
log_error('Availability check request', [
    'check_in' => $check_in,
    'check_out' => $check_out,
    'get_params' => $_GET
]);

// Validate dates
if (!strtotime($check_in) || !strtotime($check_out)) {
    $error = 'Invalid date format: ' . print_r(['check_in' => $check_in, 'check_out' => $check_out], true);
    log_error($error);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format. Please use YYYY-MM-DD format.']);
    exit;
}

// Convert to Y-m-d format
$check_in = date('Y-m-d', strtotime($check_in));
$check_out = date('Y-m-d', strtotime($check_out));

// Check if check-out is after check-in
if (strtotime($check_out) <= strtotime($check_in)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Check-out date must be after check-in date']);
    exit;
}

try {
    // Get all active room types first, optionally filtered by room_type_ids
    $sql = "
        SELECT rt.room_type_id, rt.room_type, COUNT(rn.room_number_id) as total_rooms
        FROM room_types rt
        LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id AND rn.status = 'active'
        WHERE rt.status = 'active'
    ";
    
    // If specific room types are requested, filter by them
    if ($room_type_ids) {
        $ids = explode(',', $room_type_ids);
        $ids = array_filter(array_map('intval', $ids)); // Sanitize IDs
        if (!empty($ids)) {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $sql .= " AND rt.room_type_id IN ($placeholders)";
        }
    }
    
    $sql .= " GROUP BY rt.room_type_id, rt.room_type";
    
    $stmt = $pdo->prepare($sql);
    
    if ($room_type_ids && !empty($ids)) {
        $stmt->execute($ids);
    } else {
        $stmt->execute();
    }
    
    $room_types = $stmt->fetchAll();
    $available_rooms = [];
    $hasAnyAvailability = false;
    $fullyBookedTypes = [];
    
    foreach ($room_types as $room_type) {
        // Check for available rooms of this type
        $sql = "
            SELECT COUNT(*) as available_rooms
            FROM room_numbers rn
            WHERE rn.room_type_id = ?
            AND rn.status = 'active'
            AND rn.room_number_id NOT IN (
                SELECT br.room_number_id 
                FROM booking_rooms br
                JOIN bookings b ON br.booking_id = b.booking_id
                WHERE b.status IN ('confirmed', 'checked_in')
                AND (
                    (b.check_in <= ? AND b.check_out > ?)  -- Existing booking overlaps with check-in
                    OR (b.check_in < ? AND b.check_out >= ?)  -- Existing booking overlaps with check-out
                    OR (b.check_in >= ? AND b.check_out <= ?)  -- Existing booking is within the requested dates
                )
            )
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $room_type['room_type_id'],
            $check_out,
            $check_in,
            $check_out,
            $check_in,
            $check_in,
            $check_out
        ]);
        
        $available = $stmt->fetch()['available_rooms'];
        
        if ($available > 0) {
            $hasAnyAvailability = true;
            $available_rooms[] = [
                'room_type_id' => $room_type['room_type_id'],
                'room_type' => $room_type['room_type'],
                'available' => $available,
                'total_rooms' => $room_type['total_rooms']
            ];
        } else {
            $fullyBookedTypes[] = $room_type['room_type'];
        }
    }
    
    if (!$hasAnyAvailability) {
        $message = 'All room types are fully booked for the selected dates.';
        if (!empty($fullyBookedTypes)) {
            $message .= ' The following room types are unavailable: ' . implode(', ', $fullyBookedTypes);
        }
        
        echo json_encode([
            'success' => false,
            'message' => $message,
            'fully_booked' => true,
            'unavailable_types' => $fullyBookedTypes
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'available_rooms' => $available_rooms,
            'check_in' => $check_in,
            'check_out' => $check_out
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while checking room availability',
        'error' => $e->getMessage()
    ]);
}

// Ensure no extra output
ob_end_flush();
} // Close the if statement from line 372
?>
