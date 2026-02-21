<?php
header('Content-Type: application/json');
require_once 'includes/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get database connection
        $pdo = Database::getInstance()->connect();
        
        // Start transaction
        $pdo->beginTransaction();

        // Validate user is logged in
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("Please login to make a reservation");
        }

        // Get form data
        $packageName = $_POST['packageName'];
        $basePrice = $_POST['basePrice'];
        $packagePrice = $_POST['packagePrice'];
        $reservationDate = $_POST['reservationDate'];
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];
        $guests = $_POST['numberOfGuests'];
        $paymentMethod = $_POST['paymentMethod'];
        $paymentType = $_POST['paymentType'];
        $overtimeHours = isset($_POST['overtimeHours']) ? $_POST['overtimeHours'] : 0;
        $overtimeCharge = isset($_POST['overtimeCharge']) ? $_POST['overtimeCharge'] : 0;
        $userId = $_SESSION['user_id'];
        $reserveType = $_POST['reserveType'];
        $referenceNumber = $_POST['referenceNumber'];
        $eventType = $_POST['eventType'];
        if ($eventType === 'Other') {
            $eventType = $_POST['otherEventType'];
        }
        $extraGuestCharge = isset($_POST['extraGuestCharge']) ? floatval($_POST['extraGuestCharge']) : 0;



        // Calculate extra guests and extra guest charge
        $extraGuests = 0;
        if (isset($_POST['numberOfGuests'])) {
            $numberOfGuests = intval($_POST['numberOfGuests']);
            
            if ($numberOfGuests > 50) {
                $extraGuests = $numberOfGuests - 50;
                $extraGuestCharge = $extraGuests * 1000; // ₱1,000 per extra guest
            }
        }

        // Generate booking reference
        $reference = 'TB' . date('YmdHis') . rand(100, 999);

        // Calculate payment amounts
        $totalAmount = floatval($packagePrice) + $extraGuestCharge;
        $paidAmount = ($paymentType === 'downpayment') ? ($totalAmount * 0.5) : $totalAmount;
        $remainingBalance = $totalAmount - $paidAmount;

        // First insert the booking
        $sql = "INSERT INTO event_bookings (
            id, user_id, package_name, base_price, package_price,
            overtime_hours, overtime_charge, extra_guests, extra_guest_charge,
            total_amount, paid_amount, remaining_balance, reservation_date, 
            start_time, end_time, number_of_guests, event_type,
            event_date, payment_method, payment_type, booking_status, reserve_type, 
            reference_number, created_at
        ) VALUES (
            :reference, :user_id, :package, :base_price, :price,
            :overtime_hours, :overtime_charge, :extra_guests, :extra_guest_charge,
            :total, :paid, :balance, :date, :start, :end, :guests, 
            :event_type, :event_date, :payment_method, :payment_type, 'pending', 
            :reserve_type, :reference_number, NOW()
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':reference' => $reference,
            ':user_id' => $userId,
            ':package' => $packageName,
            ':base_price' => $basePrice,
            ':price' => $packagePrice,
            ':overtime_hours' => $overtimeHours,
            ':overtime_charge' => $overtimeCharge,
            ':extra_guests' => $extraGuests,
            ':extra_guest_charge' => $extraGuestCharge,
            ':total' => $totalAmount,
            ':paid' => $paidAmount,
            ':balance' => $remainingBalance,
            ':date' => $reservationDate,
            ':start' => $startTime,
            ':end' => $endTime,
            ':guests' => $guests,
            ':event_type' => $eventType,
            ':event_date' => $eventDate,
            ':payment_method' => $paymentMethod,
            ':payment_type' => $paymentType,
            ':reserve_type' => $reserveType,
            ':reference_number' => $referenceNumber
        ]);

        // Then update the package availability for the specific date
        $updatePackageStmt = $pdo->prepare("
            UPDATE event_packages 
            SET is_available = 0, status = 'Occupied'
            WHERE name = :package_name
        ");
        
        $updatePackageStmt->execute([
            'package_name' => $packageName
        ]);

        // If everything is successful, commit the transaction
        $pdo->commit();
        
        // Send success response
        $response = [
            'status' => 'success',
            'message' => 'Booking successful!',
            'reference' => $reference,
            'data' => [
                'packageName' => $packageName,
                'reservationDate' => $reservationDate,
                'time' => $startTime . ' - ' . $endTime,
                'guests' => $guests,
                'basePrice' => $basePrice,
                'overtimeHours' => $overtimeHours,
                'overtimeCharge' => $overtimeCharge,
                'totalAmount' => $totalAmount,
                'paymentType' => $paymentType,
                'paidAmount' => $paidAmount,
                'remainingBalance' => $remainingBalance,
                'referenceNumber' => $referenceNumber,
                'paymentProof' => $paymentProofPath
            ]
        ];

        echo json_encode($response);

    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getPDO() {
    try {
        $db = Database::getInstance();
        return $db->connect();
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

function insertEventBooking($userId, $packageName, $packagePrice, $eventDate, $numberOfGuests, 
                          $startTime, $endTime, $paymentMethod, $paymentType, $overtimeHours = 0, 
                          $overtimeCharge = 0, $extraGuests = 0, $extraGuestCharge = 0) {
    try {
        $pdo = getPDO();
        if (!$pdo) {
            error_log("Database connection not established");
            return [
                'success' => false,
                'message' => 'Database connection error'
            ];
        }

        // Log incoming data for debugging
        error_log("Booking Data - Package: $packageName, Date: $eventDate, Overtime: $overtimeHours, Extra Guests: $extraGuests");
        
        // Calculate payment amounts including overtime and extra guest charges
        $baseAmount = floatval($packagePrice);
        $overtimeCharge = floatval($overtimeCharge);
        $extraGuestCharge = floatval($extraGuestCharge);
        $totalAmount = $baseAmount + $overtimeCharge + $extraGuestCharge;
        $paidAmount = ($paymentType === 'downpayment') ? ($totalAmount * 0.5) : $totalAmount;
        $remainingBalance = $totalAmount - $paidAmount;
        
        // Calculate duration in hours
        $duration = (strtotime($endTime) - strtotime($startTime)) / 3600;
        
        // Prepare the SQL statement with all fields
        $stmt = $pdo->prepare("
            INSERT INTO event_bookings (
                user_id, package_name, package_price, base_price, reservation_date, 
                number_of_guests, extra_guests, extra_guest_charge, start_time, end_time, 
                duration_hours, payment_method, payment_type, total_amount, paid_amount, 
                remaining_balance, booking_status, overtime_hours, overtime_charge, created_at
            ) VALUES (
                :user_id, :package_name, :package_price, :base_price, :reservation_date, 
                :number_of_guests, :extra_guests, :extra_guest_charge, :start_time, :end_time, 
                :duration_hours, :payment_method, :payment_type, :total_amount, :paid_amount, 
                :remaining_balance, 'pending', :overtime_hours, :overtime_charge, NOW()
            )
        ");
        
        // Execute the statement with the provided parameters
        $result = $stmt->execute([
            'user_id' => $userId,
            'package_name' => $packageName,
            'package_price' => $totalAmount, // Total including all charges
            'base_price' => $baseAmount,     // Base package price
            'reservation_date' => $eventDate,
            'number_of_guests' => $numberOfGuests,
            'extra_guests' => $extraGuests,
            'extra_guest_charge' => $extraGuestCharge,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_hours' => $duration,
            'payment_method' => $paymentMethod,
            'payment_type' => $paymentType,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'remaining_balance' => $remainingBalance,
            'overtime_hours' => $overtimeHours,
            'overtime_charge' => $overtimeCharge
        ]);

        if ($result) {
            $bookingId = $pdo->lastInsertId();
            return [
                'success' => true,
                'message' => 'Booking successfully created',
                'booking_id' => $bookingId,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_balance' => $remainingBalance,
                'payment_type' => $paymentType
            ];
        } else {
            error_log("Failed to insert booking");
            return [
                'success' => false,
                'message' => 'Failed to create booking'
            ];
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred. Please try again later.'
        ];
    }
}

// Function to validate booking data
function validateBookingData($data) {
    $errors = [];
    
    // Check required fields
    $requiredFields = ['packageName', 'packagePrice', 'eventDate', 'numberOfGuests', 
                      'startTime', 'endTime', 'paymentMethod', 'paymentType'];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst($field) . " is required";
        }
    }
    
    // Validate date
    if (!empty($data['eventDate'])) {
        $eventDate = strtotime($data['eventDate']);
        $today = strtotime(date('Y-m-d'));
        if ($eventDate < $today) {
            $errors[] = "Event date cannot be in the past";
        }
    }
    
    // Validate number of guests
    if (!empty($data['numberOfGuests'])) {
        if (!is_numeric($data['numberOfGuests']) || $data['numberOfGuests'] < 1) {
            $errors[] = "Number of guests must be at least 1";
        }
    }
    
    // Validate extra guests
    if (isset($data['extraGuests']) && $data['extraGuests'] > 0) {
        if (!is_numeric($data['extraGuests']) || $data['extraGuests'] < 0) {
            $errors[] = "Extra guests must be a positive number";
        }
        
        // If extra guests are specified, extra guest charge must also be specified and valid
        if (!isset($data['extraGuestCharge']) || !is_numeric($data['extraGuestCharge']) || $data['extraGuestCharge'] < 0) {
            $errors[] = "Invalid extra guest charge";
        }
    }
    
    // Validate overtime hours and charges
    if (isset($data['overtimeHours']) && $data['overtimeHours'] > 0) {
        if (!is_numeric($data['overtimeHours']) || $data['overtimeHours'] < 0) {
            $errors[] = "Overtime hours must be a positive number";
        }
        
        // If overtime hours are specified, overtime charge must also be specified and valid
        if (!isset($data['overtimeCharge']) || !is_numeric($data['overtimeCharge']) || $data['overtimeCharge'] < 0) {
            $errors[] = "Invalid overtime charge";
        }
    }
    
    // Validate times
    if (!empty($data['startTime']) && !empty($data['endTime'])) {
        $startTime = strtotime($data['startTime']);
        $endTime = strtotime($data['endTime']);
        if ($endTime <= $startTime) {
            $errors[] = "End time must be after start time";
        }
    }
    
    return $errors;
}

// Process booking request
function processBookingRequest($userId, $postData) {
    // Log the incoming data
    error_log("Processing booking request - Data: " . print_r($postData, true));
    
    // Validate the data
    $validationErrors = validateBookingData($postData);
    
    if (!empty($validationErrors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validationErrors
        ];
    }
    
    // Format times properly (convert from HH:MM to HH:MM:SS format)
    $postData['startTime'] = date('H:i:s', strtotime($postData['startTime']));
    $postData['endTime'] = date('H:i:s', strtotime($postData['endTime']));
    
    // Get overtime and extra guest charges
    $overtimeHours = isset($postData['overtimeHours']) ? $postData['overtimeHours'] : 0;
    $overtimeCharge = isset($postData['overtimeCharge']) ? $postData['overtimeCharge'] : 0;
    $extraGuests = isset($postData['extraGuests']) ? $postData['extraGuests'] : 0;
    $extraGuestCharge = isset($postData['extraGuestCharge']) ? $postData['extraGuestCharge'] : 0;
    
    // If validation passes, insert the booking with all charges
    return insertEventBooking(
        $userId,
        $postData['packageName'],
        $postData['packagePrice'],
        $postData['reservationDate'],
        $postData['numberOfGuests'],
        $postData['startTime'],
        $postData['endTime'],
        $postData['paymentMethod'],
        $postData['paymentType'],
        $overtimeHours,
        $overtimeCharge,
        $extraGuests,
        $extraGuestCharge
    );
}

// Display any messages
if (isset($_SESSION['success'])) {
    $message = $_SESSION['message'];
    if ($_SESSION['success']) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '" . $message['title'] . "',
                    html: `
                        <div class='text-left'>
                            <p><strong>Package:</strong> " . $message['package'] . "</p>
                            <p><strong>Date:</strong> " . $message['date'] . "</p>
                            <p><strong>Time:</strong> " . $message['time'] . "</p>
                            " . (is_array($message['amount']) 
                                ? "<p><strong>Downpayment:</strong> ₱" . $message['amount']['downpayment'] . "</p>
                                   <p><strong>Remaining Balance:</strong> ₱" . $message['amount']['remaining'] . "</p>"
                                : "<p><strong>Total Amount:</strong> ₱" . $message['amount'] . "</p>") . "
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d4af37',
                    customClass: {
                        container: 'custom-swal-container',
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        htmlContainer: 'custom-swal-html',
                        confirmButton: 'custom-swal-confirm-button'
                    }
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error',
                    text: '" . addslashes($message) . "',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d4af37'
                });
            });
        </script>";
    }
    unset($_SESSION['success']);
    unset($_SESSION['message']);
}

// Add this function to update package status
function updatePackageStatus($packageName, $status) {
    global $pdo;
    
    try {
        $sql = "UPDATE event_packages SET status = ? WHERE name = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $packageName]);
        return true;
    } catch (PDOException $e) {
        error_log("Error updating package status: " . $e->getMessage());
        return false;
    }
}

// Update the booking function to change status to Occupied
if (isset($_POST['packageName'])) {
    // ... existing booking code ...
    
    // After successful booking, update package status
    updatePackageStatus($_POST['packageName'], 'Occupied');
    
    // ... rest of the booking code ...
}

// Add this function to update package status when booking ends
function updatePackageStatusOnEnd($packageName) {
    global $pdo;
    
    try {
        // Check if there are any active bookings for the package
        $checkBookingsSQL = "SELECT COUNT(*) as active_bookings 
                            FROM event_bookings 
                            WHERE package_name = :package_name 
                            AND booking_status IN ('pending', 'confirmed')
                            AND reservation_date = CURRENT_DATE
                            AND end_time > NOW()";
        
        $stmt = $pdo->prepare($checkBookingsSQL);
        $stmt->execute(['package_name' => $packageName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If no active bookings, update package status to Available
        if ($result['active_bookings'] == 0) {
            $updateSQL = "UPDATE event_packages 
                         SET status = 'Available', is_available = 1 
                         WHERE name = :package_name";
            $stmt = $pdo->prepare($updateSQL);
            $stmt->execute(['package_name' => $packageName]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error updating package status: " . $e->getMessage());
        return false;
    }
}

// Add this to your booking completion logic
if ($bookingEndTime <= date('Y-m-d H:i:s')) {
    updatePackageStatusOnEnd($packageName);
}
?>