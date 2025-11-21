<?php
require_once 'db_con.php';
session_start();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$packageName = $data['packageName'] ?? '';
$checkAll = $data['checkAll'] ?? false;
$selectedDate = $data['date'] ?? null; // Add this to handle date checking

try {
    if ($selectedDate) {
        // Check bookings for the selected date across all packages
        $sql = "SELECT eb.package_name, eb.event_date, eb.start_time, eb.end_time 
                FROM event_bookings eb
                WHERE eb.booking_status IN ('pending', 'confirmed')
                AND eb.event_date = :selected_date
                ORDER BY eb.start_time ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['selected_date' => $selectedDate]);
        $dateBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Separate bookings by package
        $samePackageBookings = [];
        $otherPackageBookings = [];
        
        foreach ($dateBookings as $booking) {
            if ($booking['package_name'] === $packageName) {
                $samePackageBookings[] = $booking;
            } else {
                $otherPackageBookings[] = $booking;
            }
        }

        $response = [
            'status' => 'success',
            'date_status' => [
                'has_same_package_bookings' => !empty($samePackageBookings),
                'has_other_package_bookings' => !empty($otherPackageBookings),
                'same_package_bookings' => $samePackageBookings,
                'other_package_bookings' => $otherPackageBookings,
                'total_bookings' => count($dateBookings)
            ],
            'message' => ''
        ];

        // Set appropriate message based on booking status
        if (!empty($samePackageBookings) && !empty($otherPackageBookings)) {
            $response['message'] = "This package and others are already booked for this date.";
        } elseif (!empty($samePackageBookings)) {
            $response['message'] = "This package is already booked for this date.";
        } elseif (!empty($otherPackageBookings)) {
            $response['message'] = "Other packages have events scheduled for this date.";
        } else {
            $response['message'] = "Date is available for booking.";
        }

    } else {
        // Original availability check for current date
        $sql = "SELECT eb.package_name, eb.event_date, eb.start_time, eb.end_time 
                FROM event_bookings eb
                WHERE eb.booking_status IN ('pending', 'confirmed')
                AND eb.event_date = CURRENT_DATE
                AND TIME(NOW()) BETWEEN eb.start_time AND eb.end_time
                AND eb.booking_status != 'finished'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $currentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all packages
        $packagesStmt = $pdo->query("SELECT name FROM event_packages");
        $allPackages = $packagesStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Initialize response
        $response = [
            'status' => 'success',
            'is_available' => true,
            'booked_packages' => [],
            'available_packages' => $allPackages
        ];

        if (!empty($currentBookings)) {
            // If there's any booking, mark all packages as unavailable
            $response['is_available'] = false;
            $bookedPackage = $currentBookings[0]['package_name']; // Get the first booked package
            
            // Get the latest ending booking
            $latestEndTime = max(array_column($currentBookings, 'end_time'));
            $response['booking_ends'] = $latestEndTime;
            
            // Calculate next available time
            $nextAvailable = new DateTime($latestEndTime);
            $response['next_available'] = $nextAvailable->format('Y-m-d H:i:s');
            
            // Add status for all packages
            $response['package_status'] = [];
            foreach ($allPackages as $package) {
                $response['package_status'][$package] = [
                    'is_available' => false,
                    'status' => $package === $bookedPackage ? 
                        'Currently Booked' : 
                        "Not Available (Package $bookedPackage is booked)",
                    'can_advance_book' => true
                ];
                // Add all packages to booked_packages to mark them as unavailable
                $response['booked_packages'][] = $package;
            }

            $response['message'] = "Package $bookedPackage is currently booked. Other packages are not available.";
            $response['booked_package'] = $bookedPackage;
        } else {
            // If no bookings, all packages are available
            $response['package_status'] = [];
            foreach ($allPackages as $package) {
                $response['package_status'][$package] = [
                    'is_available' => true,
                    'status' => 'Available',
                    'can_advance_book' => true
                ];
            }
        }

        // If checking specific package
        if (!empty($packageName)) {
            $response['specific_package'] = [
                'name' => $packageName,
                'is_available' => empty($currentBookings),
                'status' => empty($currentBookings) ? 
                    'Available' : 
                    ($packageName === ($currentBookings[0]['package_name'] ?? '') ? 
                        'Currently Booked' : 
                        'Not Available')
            ];
        }
    }

} catch (PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ];
}

// Add cache control headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/json');
echo json_encode($response);

function findAvailableTimeSlot($date, $bookedSlots) {
    $defaultSlots = [
        ['start' => '08:00', 'end' => '13:00'],
        ['start' => '14:00', 'end' => '19:00']
    ];
    
    foreach ($defaultSlots as $slot) {
        $isAvailable = true;
        foreach ($bookedSlots as $booked) {
            if (isTimeOverlap($slot['start'], $slot['end'], $booked['start'], $booked['end'])) {
                $isAvailable = false;
                break;
            }
        }
        if ($isAvailable) {
            return $slot;
        }
    }
    return null;
}

function isTimeOverlap($start1, $end1, $start2, $end2) {
    return !($end1 <= $start2 || $start1 >= $end2);
} 