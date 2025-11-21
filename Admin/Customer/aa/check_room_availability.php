<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/availability_errors.log');

// Start session
session_start();

// Include database connection
require 'db_con.php';

header('Content-Type: application/json');

// Check if required parameters are provided
if (!isset($_GET['check_in']) || !isset($_GET['check_out'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Check-in and check-out dates are required']);
    exit;
}

$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];
$room_type_ids = isset($_GET['room_type_ids']) ? $_GET['room_type_ids'] : null;

// Validate dates
if (!strtotime($check_in) || !strtotime($check_out)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
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
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND rt.room_type_id IN ($placeholders)";
        }
    }
    
    $sql .= " GROUP BY rt.room_type_id, rt.room_type ORDER BY rt.room_type";
    
    $roomTypesStmt = $pdo->prepare($sql);
    
    // Bind parameters if filtering by room_type_ids
    if ($room_type_ids && !empty($ids)) {
        $roomTypesStmt->execute($ids);
    } else {
        $roomTypesStmt->execute();
    }

    $roomTypes = $roomTypesStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($roomTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'No active room types found in the system'
        ]);
        exit;
    }

    $availabilityInfo = [];
    $fullyBookedTypes = [];
    $partiallyBookedTypes = [];
    $availableTypes = [];
    $allRoomsOccupied = true; // Flag to track if all room numbers are occupied

    foreach ($roomTypes as $roomType) {
        $roomTypeId = $roomType['room_type_id'];
        $totalRooms = (int)$roomType['total_rooms'];

        if ($totalRooms === 0) {
            continue; // Skip room types with no active rooms
        }

        // Check for overlapping bookings for this specific room type
        // Try different possible column names for status
        $bookedRooms = 0;
        
        // First, check if bookings table has the columns we need
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as booked_rooms
                FROM bookings b
                WHERE b.room_type_id = :room_type_id
                AND (
                    b.booking_status IN ('confirmed', 'pending', 'checked_in')
                    OR b.status IN ('confirmed', 'pending', 'checked_in')
                )
                AND (
                    (b.check_in < :check_out AND b.check_out > :check_in)
                )
            ");

            $stmt->execute([
                ':room_type_id' => $roomTypeId,
                ':check_in' => $check_in,
                ':check_out' => $check_out
            ]);

            $bookedRooms = (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            // If the above query fails, try simpler version without status check
            error_log("First query failed: " . $e->getMessage());
            try {
                $stmt = $pdo->prepare("
                    SELECT COUNT(*) as booked_rooms
                    FROM bookings b
                    WHERE b.room_type_id = :room_type_id
                    AND (
                        (b.check_in < :check_out AND b.check_out > :check_in)
                    )
                ");

                $stmt->execute([
                    ':room_type_id' => $roomTypeId,
                    ':check_in' => $check_in,
                    ':check_out' => $check_out
                ]);

                $bookedRooms = (int)$stmt->fetchColumn();
            } catch (PDOException $e2) {
                error_log("Second query also failed: " . $e2->getMessage());
                $bookedRooms = 0; // Assume no bookings if query fails
            }
        }
        $availableRooms = $totalRooms - $bookedRooms;

        // Check if any rooms are available for this type
        if ($availableRooms > 0) {
            $allRoomsOccupied = false;
        }

        $roomInfo = [
            'room_type_id' => $roomTypeId,
            'room_type' => $roomType['room_type'],
            'total_rooms' => $totalRooms,
            'booked_rooms' => $bookedRooms,
            'available_rooms' => $availableRooms,
            'status' => $availableRooms > 0 ? 'available' : 'fully_booked'
        ];

        $availabilityInfo[] = $roomInfo;

        if ($availableRooms === 0) {
            $fullyBookedTypes[] = $roomType['room_type'];
        } elseif ($bookedRooms > 0) {
            $partiallyBookedTypes[] = $roomType['room_type'] . " ({$availableRooms} available)";
        } else {
            $availableTypes[] = $roomType['room_type'];
        }
    }

    // Determine overall availability
    $hasAnyAvailability = count($availableTypes) > 0 || count($partiallyBookedTypes) > 0;

    if (!$hasAnyAvailability) {
        $message = 'All room types are fully booked for the selected dates.';
        if (!empty($fullyBookedTypes)) {
            $message .= ' The following room types are unavailable: ' . implode(', ', $fullyBookedTypes);
        }
        
        // Add advance booking message when all rooms are occupied
        if ($allRoomsOccupied) {
            $message .= ' This would be an advance booking as all room numbers are currently occupied for these dates.';
        }
    } else {
        $message = 'Rooms are available for the selected dates.';
        if (!empty($availableTypes)) {
            $message .= ' Available room types: ' . implode(', ', $availableTypes);
        }
        if (!empty($partiallyBookedTypes)) {
            $message .= ' Partially available: ' . implode(', ', $partiallyBookedTypes);
        }
    }

    echo json_encode([
        'success' => $hasAnyAvailability,
        'message' => $message,
        'is_advance_booking' => $allRoomsOccupied,
        'availability_info' => $availabilityInfo,
        'summary' => [
            'available_types' => $availableTypes,
            'partially_booked_types' => $partiallyBookedTypes,
            'fully_booked_types' => $fullyBookedTypes,
            'total_available_rooms' => array_sum(array_column($availabilityInfo, 'available_rooms'))
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while checking room availability'
    ]);
}
?>
