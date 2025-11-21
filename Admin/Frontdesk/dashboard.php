<?php
require_once "db.php";
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to safely execute prepared statements
function executePreparedStatement($con, $query, $types, $params) {
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($con));
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    
    return $stmt;
}

// Check and create tables if they don't exist
$tables = [
    'customers' => "CREATE TABLE IF NOT EXISTS customers (
        customer_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        is_vip BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'rooms' => "CREATE TABLE IF NOT EXISTS rooms (
        room_id INT AUTO_INCREMENT PRIMARY KEY,
        room_number VARCHAR(10) NOT NULL,
        room_type VARCHAR(50) NOT NULL,
        rate DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) DEFAULT 'Available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'bookings' => "CREATE TABLE IF NOT EXISTS bookings (
        booking_id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        room_id INT,
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        number_of_guests INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_status VARCHAR(20) DEFAULT 'Pending',
        status VARCHAR(20) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
        FOREIGN KEY (room_id) REFERENCES rooms(room_id)
    )",

    'payments' => "CREATE TABLE IF NOT EXISTS payments (
        payment_id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50),
        payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'Completed',
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
    )",

    'messages' => "CREATE TABLE IF NOT EXISTS messages (
        message_id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        subject VARCHAR(200),
        message TEXT,
        status VARCHAR(20) DEFAULT 'Unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
    )",

    'activities' => "CREATE TABLE IF NOT EXISTS activities (
        activity_id INT AUTO_INCREMENT PRIMARY KEY,
        activity_type VARCHAR(50) NOT NULL,
        description TEXT,
        reference_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    'staff' => "CREATE TABLE IF NOT EXISTS staff (
        staff_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        position VARCHAR(50) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        status VARCHAR(20) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    'services' => "CREATE TABLE IF NOT EXISTS services (
        service_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) DEFAULT 'Available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    'service_bookings' => "CREATE TABLE IF NOT EXISTS service_bookings (
        service_booking_id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT,
        service_id INT,
        booking_date DATE NOT NULL,
        status VARCHAR(20) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
        FOREIGN KEY (service_id) REFERENCES services(service_id)
    )"
];

// Create tables
foreach ($tables as $table => $sql) {
    if (!mysqli_query($con, $sql)) {
        error_log("Error creating table $table: " . mysqli_error($con));
    }
}

// Add indexes for better performance
$indexes = [
    "CREATE INDEX IF NOT EXISTS idx_bookings_dates ON bookings(check_in, check_out)",
    "CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status)",
    "CREATE INDEX IF NOT EXISTS idx_messages_status ON messages(status)",
    "CREATE INDEX IF NOT EXISTS idx_activities_type ON activities(activity_type)",
    "CREATE INDEX IF NOT EXISTS idx_customers_vip ON customers(is_vip)"
];

foreach ($indexes as $index) {
    if (!mysqli_query($con, $index)) {
        error_log("Error creating index: " . mysqli_error($con));
    }
}

// Create trigger for activity logging
$activity_trigger = "
CREATE TRIGGER IF NOT EXISTS after_booking_insert 
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    INSERT INTO activities (activity_type, description, reference_id) 
    VALUES ('booking', CONCAT('New booking created for Room ', NEW.room_id), NEW.booking_id);
END;";

if (!mysqli_query($con, $activity_trigger)) {
    error_log("Error creating trigger: " . mysqli_error($con));
}

// Check if sample data needs to be inserted
$check_data = mysqli_query($con, "SELECT COUNT(*) as count FROM bookings");
if (!$check_data) {
    die("Error checking data: " . mysqli_error($con));
}
$data_exists = mysqli_fetch_assoc($check_data)['count'] > 0;

if (!$data_exists) {
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        // Insert sample customers with proper error handling
        $customer_insert_query = "INSERT INTO customers (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)";
        $customer_stmt = mysqli_prepare($con, $customer_insert_query);
        
        if (!$customer_stmt) {
            throw new Exception("Failed to prepare customer statement: " . mysqli_error($con));
        }

        $customers = [
            ['John', 'Doe', 'john@email.com', '1234567890'],
            ['Jane', 'Smith', 'jane@email.com', '2345678901'],
            ['Mike', 'Johnson', 'mike@email.com', '3456789012'],
            ['Sarah', 'Williams', 'sarah@email.com', '4567890123'],
            ['Robert', 'Brown', 'robert@email.com', '5678901234']
        ];

        foreach ($customers as $customer) {
            mysqli_stmt_bind_param($customer_stmt, "ssss", $customer[0], $customer[1], $customer[2], $customer[3]);
            
            if (!mysqli_stmt_execute($customer_stmt)) {
                throw new Exception("Failed to insert customer: " . mysqli_stmt_error($customer_stmt));
            }
        }
        
        mysqli_stmt_close($customer_stmt);

        // Insert sample rooms with proper error handling
        $room_insert_query = "INSERT INTO rooms (room_number, room_type, rate, status) VALUES (?, ?, ?, 'Available')";
        $room_stmt = mysqli_prepare($con, $room_insert_query);
        
        if (!$room_stmt) {
            throw new Exception("Failed to prepare room statement: " . mysqli_error($con));
        }

        $room_types = [
            ['101', 'Standard', 2500.00],
            ['102', 'Deluxe', 3500.00],
            ['103', 'Suite', 5000.00],
            ['201', 'Family', 4500.00],
            ['202', 'Standard', 2500.00],
            ['203', 'Deluxe', 3500.00],
            ['301', 'Suite', 5000.00],
            ['302', 'Family', 4500.00]
        ];

        foreach ($room_types as $room) {
            mysqli_stmt_bind_param($room_stmt, "ssd", $room[0], $room[1], $room[2]);
            
            if (!mysqli_stmt_execute($room_stmt)) {
                throw new Exception("Failed to insert room: " . mysqli_stmt_error($room_stmt));
            }
        }
        
        mysqli_stmt_close($room_stmt);

        // Insert sample bookings with proper error handling
        $booking_insert_query = "INSERT INTO bookings (customer_id, room_id, check_in, check_out, number_of_guests, total_amount, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
        $booking_stmt = mysqli_prepare($con, $booking_insert_query);
        
        if (!$booking_stmt) {
            throw new Exception("Failed to prepare booking statement: " . mysqli_error($con));
        }

        $current_date = date('Y-m-d');
        $bookings = [
            [1, 1, $current_date, date('Y-m-d', strtotime('+2 days')), 2, 5000.00, 'Checked In'],
            [2, 2, $current_date, date('Y-m-d', strtotime('+3 days')), 2, 7000.00, 'Pending'],
            [3, 3, date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+4 days')), 3, 15000.00, 'Confirmed'],
            [4, 4, date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+2 days')), 4, 13500.00, 'Checked In'],
            [5, 5, date('Y-m-d', strtotime('+7 days')), date('Y-m-d', strtotime('+9 days')), 2, 5000.00, 'Confirmed']
        ];

        foreach ($bookings as $booking) {
            mysqli_stmt_bind_param($booking_stmt, "iissids", 
                $booking[0], $booking[1], $booking[2], $booking[3], 
                $booking[4], $booking[5], $booking[6]
            );
            
            if (!mysqli_stmt_execute($booking_stmt)) {
                throw new Exception("Failed to insert booking: " . mysqli_stmt_error($booking_stmt));
            }
        }
        
        mysqli_stmt_close($booking_stmt);

        // Commit transaction if all insertions successful
        mysqli_commit($con);
        error_log("Sample data inserted successfully");
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        error_log("Error inserting sample data: " . $e->getMessage());
        // Continue execution without sample data
    }
}

// Initialize metrics array with default values
$metrics = [
    'bookings_today' => 0,
    'bookings_trend' => 0,
    'occupied_rooms' => 0,
    'total_rooms' => 0,
    'revenue_today' => 0,
    'revenue_trend' => 0,
    'checkins_today' => 0,
    'pending_checkins' => 0,
    'checkouts_today' => 0,
    'pending_checkouts' => 0,
    'new_messages' => 0,
    'pending_requests' => 0,
    'pending_payments' => 0,
    'pending_transactions' => 0,
    'guest_count' => 0,
    'vip_guests' => 0,
    'total_pending_bookings' => 0,
    'todays_guests' => 0,
    'event_reservations' => 0,
    'cafe_revenue' => 0,
    'total_hotel_revenue' => 0
];

// Get current date and previous dates for comparisons
$current_date = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$last_week = date('Y-m-d', strtotime('-7 days'));
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));

// Debug dates
error_log("Current date: $current_date");
error_log("Week start: $week_start");
error_log("Week end: $week_end");

// Get today's bookings and calculate trend
$today_bookings_query = "SELECT COUNT(*) as count FROM bookings 
    WHERE DATE(created_at) = ? 
    AND status NOT IN ('Cancelled', 'Rejected')";
$stmt = mysqli_prepare($con, $today_bookings_query);
if ($stmt === false) {
    error_log("Prepare failed: " . mysqli_error($con));
    $metrics['bookings_today'] = 0;
} else {
    mysqli_stmt_bind_param($stmt, "s", $current_date);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $metrics['bookings_today'] = mysqli_fetch_assoc($result)['count'];
        }
    } else {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Get total pending bookings
$pending_bookings_query = "SELECT COUNT(*) as count FROM bookings 
    WHERE status = 'Pending'";
$stmt = mysqli_prepare($con, $pending_bookings_query);
if ($stmt === false) {
    error_log("Prepare failed: " . mysqli_error($con));
    $metrics['total_pending_bookings'] = 0;
} else {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $metrics['total_pending_bookings'] = mysqli_fetch_assoc($result)['count'];
        }
    } else {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Get last week's bookings for trend calculation
$last_week_bookings_query = "SELECT COUNT(*) as count FROM bookings 
    WHERE DATE(created_at) = ? 
    AND status NOT IN ('Cancelled', 'Rejected')";
$stmt = mysqli_prepare($con, $last_week_bookings_query);
if ($stmt === false) {
    error_log("Prepare failed: " . mysqli_error($con));
    $metrics['bookings_trend'] = 0;
} else {
    mysqli_stmt_bind_param($stmt, "s", $last_week);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $last_week_bookings = mysqli_fetch_assoc($result)['count'];
            if ($last_week_bookings > 0) {
                $metrics['bookings_trend'] = round((($metrics['bookings_today'] - $last_week_bookings) / $last_week_bookings) * 100);
            }
        }
    } else {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Get room occupancy data
$rooms_query = "SELECT 
    (SELECT COUNT(*) FROM rooms) as total_rooms,
    (SELECT COUNT(*) FROM bookings 
     WHERE ? BETWEEN check_in AND check_out 
     AND status = 'Checked In') as occupied_rooms";
$stmt = mysqli_prepare($con, $rooms_query);
if ($stmt === false) {
    error_log("Prepare failed: " . mysqli_error($con));
    $metrics['total_rooms'] = 0;
    $metrics['occupied_rooms'] = 0;
} else {
    mysqli_stmt_bind_param($stmt, "s", $current_date);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $rooms_data = mysqli_fetch_assoc($result);
            $metrics['total_rooms'] = $rooms_data['total_rooms'];
            $metrics['occupied_rooms'] = $rooms_data['occupied_rooms'];
        }
    } else {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Get today's revenue and calculate trend
$revenue_query = "SELECT 
    (SELECT COALESCE(SUM(total_amount), 0) FROM bookings 
     WHERE DATE(created_at) = ? 
     AND status NOT IN ('Cancelled', 'Rejected')) as today_revenue,
    (SELECT COALESCE(SUM(total_amount), 0) FROM bookings 
     WHERE DATE(created_at) = ? 
     AND status NOT IN ('Cancelled', 'Rejected')) as yesterday_revenue";
$stmt = mysqli_prepare($con, $revenue_query);
if ($stmt === false) {
    error_log("Prepare failed: " . mysqli_error($con));
    $metrics['revenue_today'] = 0;
    $metrics['revenue_trend'] = 0;
} else {
    mysqli_stmt_bind_param($stmt, "ss", $current_date, $yesterday);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $revenue_data = mysqli_fetch_assoc($result);
            $metrics['revenue_today'] = $revenue_data['today_revenue'];
            if ($revenue_data['yesterday_revenue'] > 0) {
                $metrics['revenue_trend'] = round((($revenue_data['today_revenue'] - $revenue_data['yesterday_revenue']) / $revenue_data['yesterday_revenue']) * 100);
            }
        }
    } else {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Function to safely execute prepared statements
function executeQuery($con, $query, $types, $params, &$metrics, $metric_keys, $default_values) {
    $stmt = mysqli_prepare($con, $query);
    if ($stmt === false) {
        error_log("Prepare failed: " . mysqli_error($con));
        foreach ($metric_keys as $index => $key) {
            $metrics[$key] = $default_values[$index];
        }
        return;
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            foreach ($metric_keys as $index => $key) {
                $metrics[$key] = $data[$key] ?? $default_values[$index];
            }
        }
    } else {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        foreach ($metric_keys as $index => $key) {
            $metrics[$key] = $default_values[$index];
        }
    }
    mysqli_stmt_close($stmt);
}

// Get check-ins data
$checkins_query = "SELECT  
    (SELECT COUNT(*) FROM bookings 
     WHERE DATE(check_in) = ? 
     AND status IN ('Checked in', 'Extended')) as checkins_today,
     
    (SELECT COUNT(*) FROM bookings 
     WHERE DATE(check_in) = ? 
     AND status = 'Confirmed') as pending_checkins";

executeQuery(
    $con,
    $checkins_query,
    "ss",
    [$current_date, $current_date],
    $metrics,
    ['checkins_today', 'pending_checkins'],
    [0, 0]
);


// Get check-outs data
$checkouts_query = "SELECT 
    (SELECT COUNT(*) FROM bookings 
     WHERE status = 'Checked Out') as checkouts_today,
    (SELECT COUNT(*) FROM bookings 
     WHERE DATE(check_out) = ? 
     AND status = 'Checked In') as pending_checkouts";
executeQuery(
    $con,
    $checkouts_query,
    "s",
    [$current_date],
    $metrics,
    ['checkouts_today', 'pending_checkouts'],
    [0, 0]
);

// Get messages and requests data
$messages_query = "SELECT 
    COUNT(*) as new_messages,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_requests
    FROM messages 
    WHERE DATE(created_at) = ?";

$stmt = executePreparedStatement($con, $messages_query, "s", [$current_date]);
if ($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $messages_data = mysqli_fetch_assoc($result);
        $metrics['new_messages'] = $messages_data['new_messages'] ?? 0;
        $metrics['pending_requests'] = $messages_data['pending_requests'] ?? 0;
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Failed to execute messages query");
    $metrics['new_messages'] = 0;
    $metrics['pending_requests'] = 0;
}

// Get pending payments data
$payments_query = "SELECT 
    COALESCE(SUM(downpayment_amount), 0) as pending_amount,
    COUNT(*) as pending_count
FROM bookings 
WHERE payment_option = 'Downpayment'";
$result = mysqli_query($con, $payments_query);
if ($result) {
    $payments_data = mysqli_fetch_assoc($result);
    $metrics['pending_payments'] = $payments_data['pending_amount'];
    $metrics['pending_transactions'] = $payments_data['pending_count'];
}

// Get guest data including VIP guests
$guests_query = "SELECT 
    COUNT(*) as total_guests,
    SUM(CASE WHEN is_vip = 1 THEN 1 ELSE 0 END) as vip_count
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.customer_id
    WHERE ? BETWEEN check_in AND check_out
    AND b.status = 'Checked In'";

// Get most booked room types data
$room_types_query = "SELECT 
    r.room_type,
    COUNT(b.booking_id) as booking_count
    FROM rooms r
    LEFT JOIN bookings b ON r.room_id = b.room_id
    WHERE b.status NOT IN ('Cancelled', 'Rejected') OR b.status IS NULL
    GROUP BY r.room_type
    ORDER BY booking_count DESC";

$room_type_result = mysqli_query($con, $room_types_query);
$room_type_data = [];

if ($room_type_result) {
    while ($row = mysqli_fetch_assoc($room_type_result)) {
        $room_type_data[$row['room_type']] = (int)$row['booking_count'];
    }
} else {
    error_log("Error fetching room type data: " . mysqli_error($con));
}

// If no data, add some default room types with sample data
if (empty($room_type_data)) {
    $room_type_data = [
        'Standard' => 5,
        'Deluxe' => 3,
        'Suite' => 2,
        'Family' => 1
    ];
}

// Debug room type data
error_log("Room type data: " . print_r($room_type_data, true));

$stmt = executePreparedStatement($con, $guests_query, "s", [$current_date]);
if ($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $guests_data = mysqli_fetch_assoc($result);
        $metrics['guest_count'] = $guests_data['total_guests'] ?? 0;
        $metrics['vip_guests'] = $guests_data['vip_count'] ?? 0;
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Failed to execute guests query");
    $metrics['guest_count'] = 0;
    $metrics['vip_guests'] = 0;
}

// Get recent activities with proper table structure
$recent_activities_query = "
    (SELECT 
        'booking' as type,
        CONCAT('New booking: ', first_name, ' ', last_name) as description,
        created_at as timestamp,
        'fas fa-calendar-check' as icon,
        'var(--primary)' as color
    FROM bookings 
    WHERE status = 'Pending'
    ORDER BY created_at DESC
    LIMIT 5)
    
    UNION ALL
    
    (SELECT 
        'checkin' as type,
        CONCAT('Check-in: ', first_name, ' ', last_name) as description,
        created_at as timestamp,
        'fas fa-sign-in-alt' as icon,
        'var(--success)' as color
    FROM bookings
    WHERE status = 'Checked In'
    ORDER BY created_at DESC
    LIMIT 5)
    
    UNION ALL
    
    (SELECT 
        'payment' as type,
        CONCAT('Payment received: ₱', FORMAT(total_amount, 2)) as description,
        order_date as timestamp,
        'fas fa-credit-card' as icon,
        'var(--success)' as color
    FROM orders
    ORDER BY order_date DESC
    LIMIT 5)
    
    UNION ALL
    
    (SELECT 
        'event' as type,
        CONCAT('Event booking: ', package_name) as description,
        created_at as timestamp,
        'fas fa-glass-cheers' as icon,
        'var(--primary)' as color
    FROM event_bookings
    WHERE id LIKE 'EVT%'
    ORDER BY created_at DESC
    LIMIT 5)
    
    ORDER BY timestamp DESC
    LIMIT 10";

$recent_activities_result = mysqli_query($con, $recent_activities_query);
$recent_activities = [];

if ($recent_activities_result) {
    while ($activity = mysqli_fetch_assoc($recent_activities_result)) {
        $recent_activities[] = $activity;
    }
} else {
    error_log("Recent activities query error: " . mysqli_error($con));
}

// Get weekly occupancy data for chart with proper error handling
$occupancy_data = [];
$weekly_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weekly_labels[] = date('M d', strtotime($date));
    
    $occupancy_query = "SELECT 
        ROUND(
            (COUNT(CASE WHEN ? BETWEEN check_in AND check_out AND status = 'Checked In' THEN 1 END) * 100.0) / 
            NULLIF((SELECT COUNT(*) FROM rooms), 0)
        ) as occupancy_rate
        FROM bookings";
    
    $stmt = executePreparedStatement($con, $occupancy_query, "s", [$date]);
    if ($stmt) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $occupancy_data[] = $row['occupancy_rate'] ?? 0;
        } else {
            $occupancy_data[] = 0;
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Failed to execute occupancy query for date: $date");
        $occupancy_data[] = 0;
    }
}

// Debug metrics
error_log("Updated Metrics: " . print_r($metrics, true));

// Get weekly arrivals
$arrivals_query = "SELECT COUNT(*) as arrivals 
                  FROM bookings 
    WHERE check_in BETWEEN ? AND ?
    AND status NOT IN ('Cancelled', 'Rejected')";
$stmt = mysqli_prepare($con, $arrivals_query);
mysqli_stmt_bind_param($stmt, "ss", $week_start, $week_end);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $metrics['arrivals'] = mysqli_fetch_assoc($result)['arrivals'];
}

// Get weekly departures (checked out count)
$departures_query = "SELECT COUNT(*) as departures 
    FROM bookings b
    WHERE b.status = 'Checked Out'";
$departures_result = mysqli_query($con, $departures_query);
if ($departures_result) {
    $metrics['departures'] = mysqli_fetch_assoc($departures_result)['departures'];
} else {
    error_log("Error in departures query: " . mysqli_error($con));
    $metrics['departures'] = 0;
}

// Get currently occupied rooms
$occupied_query = "SELECT COUNT(*) as occupied 
                   FROM bookings 
    WHERE ? BETWEEN check_in AND check_out
    AND status = 'Checked In'";
$stmt = mysqli_prepare($con, $occupied_query);
mysqli_stmt_bind_param($stmt, "s", $current_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $metrics['rooms_occupied'] = mysqli_fetch_assoc($result)['occupied'];
}

// Get bookings made today
$booked_query = "SELECT COUNT(*) as booked 
                 FROM bookings 
    WHERE DATE(created_at) = ?
    AND status NOT IN ('Cancelled', 'Rejected')";
$stmt = mysqli_prepare($con, $booked_query);
mysqli_stmt_bind_param($stmt, "s", $current_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $metrics['booked_today'] = mysqli_fetch_assoc($result)['booked'];
}

// Get today's guest count (checked in guests including extended)
$todays_guests_query = "SELECT 
    COUNT(*) as current_guests
    FROM bookings 
    WHERE LOWER(status) IN ('checked in', 'extended')";

$current_guests_result = mysqli_query($con, $todays_guests_query);
if ($current_guests_result) {
    $current_guests_data = mysqli_fetch_assoc($current_guests_result);
    $metrics['todays_guests'] = $current_guests_data['current_guests'] ?? 0;
} else {
    error_log("Error in current guests query: " . mysqli_error($con));
    $metrics['todays_guests'] = 0;
}

// Get today's revenue
$revenue_query = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
                   FROM bookings 
    WHERE DATE(created_at) = ?
    AND status NOT IN ('Cancelled', 'Rejected')";
$stmt = mysqli_prepare($con, $revenue_query);
mysqli_stmt_bind_param($stmt, "s", $current_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $metrics['revenue'] = mysqli_fetch_assoc($result)['revenue'];
}

// Get total rooms and available rooms
$rooms_query = "SELECT 
    SUM(total_rooms) as total_rooms,
    SUM(available_rooms) as available_rooms
    FROM rooms";
$rooms_result = mysqli_query($con, $rooms_query);
if ($rooms_result) {
    $rooms_data = mysqli_fetch_assoc($rooms_result);
    $metrics['total_rooms'] = $rooms_data['total_rooms'] ?: 0;
    $metrics['available_rooms'] = $rooms_data['available_rooms'] ?: 0;
} else {
    $metrics['total_rooms'] = 0;
    $metrics['available_rooms'] = 0;
}

// Debug metrics
error_log("Metrics: " . print_r($metrics, true));

// Get recent reservations
$reservations = mysqli_query($con, "SELECT 
    booking_id,
    CONCAT(first_name, ' ', last_name) as guest_name,
    booking_type as room_type,
    check_in,
    status
    FROM bookings
    WHERE status NOT IN ('Cancelled', 'Rejected')
    ORDER BY created_at DESC
    LIMIT 5");

// Debug reservations
if (!$reservations) {
    error_log("Error fetching reservations: " . mysqli_error($con));
}

// Get weekly revenue data for chart
$weekly_revenue = [];
$weekly_labels = [];
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weekly_labels[] = date('d', strtotime("-$i days"));
    
    $revenue_query = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
        FROM bookings 
        WHERE DATE(created_at) = ?
        AND status NOT IN ('Cancelled', 'Rejected')";
    $stmt = mysqli_prepare($con, $revenue_query);
    mysqli_stmt_bind_param($stmt, "s", $date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result) {
        $weekly_revenue[] = mysqli_fetch_assoc($result)['revenue'];
    } else {
        $weekly_revenue[] = 0;
    }
}

$weekly_labels = array_reverse($weekly_labels);
$weekly_revenue = array_reverse($weekly_revenue);

// Debug weekly data
error_log("Weekly labels: " . print_r($weekly_labels, true));
error_log("Weekly revenue: " . print_r($weekly_revenue, true));

// Get checked out bookings for departures tab
$checked_out_query = "SELECT 
                                b.booking_id,
                                b.first_name,
                                b.last_name,
    b.contact,
                                b.check_in,
                                b.check_out,
                                b.number_of_guests,
                                b.total_amount,
    b.room_type,
    b.created_at as checkout_timestamp
                            FROM bookings b
WHERE b.status = 'Checked Out'
ORDER BY b.created_at DESC";

$checked_out_result = mysqli_query($con, $checked_out_query);
if (!$checked_out_result) {
    error_log("Error in checked out query: " . mysqli_error($con));
}

// Get event reservations count
$event_reservations_query = "SELECT 
    COUNT(*) as event_count 
    FROM event_bookings 
    WHERE id LIKE 'EVT%'";  // Count only EVT prefixed bookings

$stmt = executePreparedStatement($con, $event_reservations_query, "", []);
if ($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $event_data = mysqli_fetch_assoc($result);
        $metrics['event_reservations'] = $event_data['event_count'] ?? 0;
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Failed to execute event reservations query");
    $metrics['event_reservations'] = 0;
}

// Get café revenue from orders
$cafe_revenue_query = "SELECT 
    COALESCE(SUM(total_amount), 0) as total_revenue 
    FROM orders";

$stmt = executePreparedStatement($con, $cafe_revenue_query, "", []);
if ($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $revenue_data = mysqli_fetch_assoc($result);
        $metrics['cafe_revenue'] = $revenue_data['total_revenue'] ?? 0;
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Failed to execute café revenue query");
    $metrics['cafe_revenue'] = 0;
}

// Check if event_bookings table exists and create if not
$create_event_bookings_table = "CREATE TABLE IF NOT EXISTS event_bookings (
    id VARCHAR(20) PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    guest_count INT NOT NULL,
    package_name VARCHAR(100) NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($con, $create_event_bookings_table)) {
    error_log("Error creating event_bookings table: " . mysqli_error($con));
}

// Check if we need to insert sample data
$check_events = mysqli_query($con, "SELECT COUNT(*) as count FROM event_bookings");
$events_exist = mysqli_fetch_assoc($check_events)['count'] > 0;

if (!$events_exist) {
    // Insert sample event data
    $sample_events = [
        [
            'EVT001', 
            'Wedding', 
            'Santos-Garcia Wedding', 
            date('Y-m-d', strtotime('+5 days')), 
            150, 
            'Premium Wedding Package',
            'Confirmed'
        ],
        [
            'EVT002', 
            'Corporate', 
            'Tech Company Annual Meeting', 
            date('Y-m-d', strtotime('+3 days')), 
            50, 
            'Business Conference Package',
            'Pending'
        ],
        [
            'EVT003', 
            'Birthday', 
            'Maria\'s 18th Birthday', 
            date('Y-m-d', strtotime('+7 days')), 
            80, 
            'Debut Package',
            'Confirmed'
        ],
        [
            'EVT004', 
            'Special', 
            'Family Reunion', 
            date('Y-m-d', strtotime('+2 days')), 
            40, 
            'Family Gathering Package',
            'Pending'
        ]
    ];

    foreach ($sample_events as $event) {
        $insert_query = "INSERT INTO event_bookings (id, event_type, event_name, event_date, guest_count, package_name, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $insert_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssiss", ...$event);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

// Get upcoming events with proper query matching the actual table structure
$upcoming_events_query = "
    SELECT 
        id,
        event_type,
        customer_name as event_name,
        reservation_date as event_date,
        number_of_guests as guest_count,
        package_name,
        booking_status as status,
        CASE 
            WHEN event_type = 'Wedding' THEN 'fas fa-rings' 
            WHEN event_type = 'Corporate' THEN 'fas fa-briefcase'
            WHEN event_type = 'Birthday' THEN 'fas fa-birthday-cake'
            ELSE 'fas fa-glass-cheers'
        END as icon,
        CASE 
            WHEN event_type = 'Wedding' THEN 'var(--primary)'
            WHEN event_type = 'Corporate' THEN 'var(--success)'
            WHEN event_type = 'Birthday' THEN 'var(--warning)'
            ELSE 'var(--info)'
        END as color
    FROM event_bookings 
    WHERE reservation_date >= CURRENT_DATE
    AND booking_status NOT IN ('Cancelled', 'Completed')
    ORDER BY reservation_date ASC
    LIMIT 10";

$upcoming_events_result = mysqli_query($con, $upcoming_events_query);
$upcoming_events = [];

if ($upcoming_events_result) {
    while ($event = mysqli_fetch_assoc($upcoming_events_result)) {
        $upcoming_events[] = $event;
    }
} else {
    error_log("Upcoming events query error: " . mysqli_error($con));
}

// Get today's table bookings
$table_bookings_query = "SELECT 
    id,
    name as customer_name,
    booking_date,
    booking_time,
    num_guests,
    package_name,
    total_amount,
    payment_method,
    status
FROM table_bookings 
WHERE DATE(booking_date) = CURRENT_DATE
ORDER BY booking_time ASC";

$table_bookings_result = mysqli_query($con, $table_bookings_query);
$table_bookings = [];

if ($table_bookings_result) {
    while ($booking = mysqli_fetch_assoc($table_bookings_result)) {
        $table_bookings[] = $booking;
    }
} else {
    error_log("Table bookings query error: " . mysqli_error($con));
}

// ... existing code ...
// Get café performance data for the last 7 days
$cafe_performance_query = "
    SELECT 
        DATE_FORMAT(order_date, '%a') as day,
        COALESCE(SUM(total_amount), 0) as daily_revenue
    FROM orders
    WHERE order_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY)
        AND order_type = 'café'
        AND status = 'Completed'
    GROUP BY DATE(order_date), DATE_FORMAT(order_date, '%a')
    ORDER BY DATE(order_date)";

// Debug: Log the query
error_log("Café Performance Query: " . $cafe_performance_query);

$cafe_performance_result = mysqli_query($con, $cafe_performance_query);

if (!$cafe_performance_result) {
    error_log("MySQL Error: " . mysqli_error($con));
}

$cafe_daily_revenue = [];
$cafe_days = [];
$total_records = 0;

if ($cafe_performance_result) {
    while ($row = mysqli_fetch_assoc($cafe_performance_result)) {
        $cafe_days[] = $row['day'];
        $cafe_daily_revenue[] = (float)$row['daily_revenue'];
        $total_records++;
        // Debug: Log each day's data
        error_log("Day: " . $row['day'] . ", Revenue: " . $row['daily_revenue']);
    }
    error_log("Total records found: " . $total_records);
} else {
    error_log("Error fetching café performance data: " . mysqli_error($con));
}

// Debug: Log final arrays
error_log("Final days array: " . json_encode($cafe_days));
error_log("Final revenue array: " . json_encode($cafe_daily_revenue));

// If we have less than 7 days of data, pad with zeros
$days_of_week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
if (empty($cafe_days)) {
    $cafe_days = $days_of_week;
    $cafe_daily_revenue = array_fill(0, 7, 0);
} else {
    while (count($cafe_days) < 7) {
        array_unshift($cafe_days, '');
        array_unshift($cafe_daily_revenue, 0);
    }
}

// Debug: Log padded arrays
error_log("Padded days array: " . json_encode($cafe_days));
error_log("Padded revenue array: " . json_encode($cafe_daily_revenue));
// ... existing code ...

// Get total hotel revenue (sum of all bookings and café revenue)
$total_hotel_revenue_query = "SELECT 
    (SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status != 'Cancelled') +
    (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'Completed') as total_revenue";

$total_revenue_result = mysqli_query($con, $total_hotel_revenue_query);
if ($total_revenue_result) {
    $total_revenue_data = mysqli_fetch_assoc($total_revenue_result);
    $metrics['total_hotel_revenue'] = $total_revenue_data['total_revenue'] ?? 0;
} else {
    $metrics['total_hotel_revenue'] = 0;
}
// ... existing code ...

// Get current guests (checked in and extended bookings)
$current_guests_query = "SELECT COUNT(*) as current_guests 
    FROM bookings 
    WHERE LOWER(status) IN ('checked in', 'extended')";

$current_guests_result = mysqli_query($con, $current_guests_query);
if ($current_guests_result) {
    $current_guests_data = mysqli_fetch_assoc($current_guests_result);
    $metrics['todays_guests'] = $current_guests_data['current_guests'] ?? 0;
} else {
    error_log("Error in current guests query: " . mysqli_error($con));
    $metrics['todays_guests'] = 0;
}
// ... existing code ...

// Query to get current guests (Checked in and Extended bookings)
$current_guests_sql = "SELECT 
    booking_id,
    CONCAT(first_name, ' ', last_name) as guest_name,
    check_in,
    check_out,
    room_type,
    status,
    DATEDIFF(check_out, CURRENT_DATE()) as remaining_days
FROM bookings 
WHERE LOWER(status) IN ('checked in', 'extended')
ORDER BY check_in DESC";


?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<style>
/* Reset and base styles */
:root {
    --primary: #B8860B;
    --secondary: #DAA520;
    --dark: #3D2B1F;
    --light: #FFF8E7;
    --success: #8B6914;
    --warning: #CD853F;
    --danger: #8B4513;
    --gray-100: #F4E4BC;
    --gray-200: #E6C88A;
    --gray-300: #D2B48C;
    --gray-400: #BC8F4A;
    --gray-500: #996515;
    --card-shadow: 0 2px 8px rgba(139, 105, 20, 0.15);
    --hover-shadow: 0 4px 12px rgba(139, 105, 20, 0.25);
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--light);
    color: var(--dark);
    line-height: 1.2;
    font-size: 13px;
}

.main-content {
    margin-left: 250px;
    padding: 0.5rem;
    min-height: 100vh;
    background: var(--light);
}

.container-fluid {
    padding: 0.25rem;
}

/* Stats Cards */
.stats-card {
    background: white;
    padding: 8px;
    border-radius: 4px;
    box-shadow: var(--card-shadow);
    transition: all 0.2s ease;
    height: 45px;
    width: 100%;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
}

.stats-card:hover {
    transform: translateY(-1px);
    box-shadow: var(--hover-shadow);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 2px;
    height: 100%;
    background: var(--primary);
    opacity: 0.7;
}

.stats-card-content {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.stats-card .number {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    line-height: 1;
    margin: 0;
}

.stats-card .label {
    font-size: 11px;
    color: var(--gray-500);
    font-weight: 500;
    margin: 2px 0 0 0;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.stats-card .period {
    font-size: 10px;
    color: var(--gray-400);
    margin: 0;
}

.icon-wrapper {
    font-size: 12px;
    color: var(--primary);
    background: var(--gray-100);
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    flex-shrink: 0;
}

/* Revenue card special styling */
.revenue-card {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
}

.revenue-card::before {
    display: none;
}

.revenue-card .number,
.revenue-card .label,
.revenue-card .period,
.revenue-card .icon-wrapper {
    color: white;
}

.revenue-card .icon-wrapper {
    background: rgba(255, 255, 255, 0.2);
}

/* Row spacing for stats cards */
.row.g-4 {
    margin-top: 4px !important;
    margin-bottom: 4px !important;
}

.row > [class*='col-'] {
    padding-right: 4px;
    padding-left: 4px;
}

/* Charts */
.card {
    background: #FFF8E7;
    border-radius: 10px;
    border: 1px solid var(--gray-200);
    box-shadow: var(--card-shadow);
    margin-bottom: 0.5rem;
}

.card-header {
    padding: 1rem;
    background: #FFF8E7;
    border-bottom: 1px solid var(--gray-200);
}

.card-title {
    color: var(--dark);
    font-size: 0.9rem !important;
    font-weight: 600 !important;
    margin: 0;
}

.card-body {
    padding: 0.5rem;
}

/* Chart container styles */
.chart-container {
    background: #FFF8E7;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}

#cafeRevenueChart {
    height: 150px !important;
}

/* Activity Feed */
.activity-feed {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    padding: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
    color: var(--dark);
}

/* Additional Services Section */
.events-list, .table-bookings-list {
    max-height: 150px;
}

.event-item, .booking-item {
    padding: 0.375rem;
}

.event-date, .booking-time {
    font-size: 0.65rem;
}

.event-title, .guest-name {
    font-size: 0.75rem;
}

/* Calendar Customization */
.fc {
    background: #FFF8E7;
    border-radius: 10px;
    padding: 1rem;
}

.fc-theme-standard td,
.fc-theme-standard th {
    border-color: var(--gray-200);
}

.fc-button-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #FFF8E7 !important;
}

.fc-button-primary:hover {
    background: var(--secondary) !important;
    border-color: var(--secondary) !important;
}

.fc .fc-toolbar-title {
    font-size: 0.8rem;
}

.fc .fc-button {
    padding: 0.125rem 0.375rem;
    font-size: 0.7rem;
}

.fc td, .fc th {
    padding: 0.125rem !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-card .number {
        font-size: 1rem;
    }
    
    .card-body {
        padding: 0.375rem;
}

.chart-container {
        height: 120px !important;
    }
}

/* Badge adjustments */
.badge {
    padding: 0.25em 0.5em;
    font-size: 0.65rem;
}

/* Table adjustments */
.table th, .table td {
    padding: 0.375rem;
    font-size: 0.75rem;
}

/* Stats Boxes */
.stats-box {
    background: linear-gradient(145deg, #DAA520, #B8860B);
    border-radius: 10px;
    padding: 15px;
    height: 90px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(184, 134, 11, 0.3);
    transition: all 0.3s ease;
}

.stats-box:hover {
    transform: translateY(-2px);
    box-shadow: var(--hover-shadow);
}

.stats-box::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2));
    border-radius: 50%;
    transform: translate(20px, -20px);
}

.stats-content {
    position: relative;
    z-index: 2;
}

.stats-box .number {
    font-size: 20px;
    font-weight: 700;
    color: #FFF8E7;
    margin-bottom: 4px;
    line-height: 1;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.stats-box .label {
    font-size: 12px;
    color: #FFF8E7;
    font-weight: 600;
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-box .sub-info {
    font-size: 11px;
    color: #FFF8E7;
    opacity: 0.9;
    font-weight: 500;
}

.stats-icon {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.2);
    color: #FFF8E7;
    backdrop-filter: blur(4px);
}

.progress-mini {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: rgba(61, 43, 31, 0.2);
    overflow: hidden;
    border-radius: 0 0 10px 10px;
}

.progress-mini::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 45%;
    background: rgba(255, 248, 231, 0.3);
    border-radius: 4px;
}

/* Container spacing */
.container-fluid {
    padding: 15px 10px;
}

.row.g-2 {
    margin: -6px;
}

.row.g-2 > [class*='col-'] {
    padding: 6px;
}

.mt-2 {
    margin-top: 12px !important;
}

/* Add smooth animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.stats-box {
    animation: fadeIn 0.5s ease-out forwards;
}

.row.g-2 > [class*='col-']:nth-child(1) .stats-box { animation-delay: 0.1s; }
.row.g-2 > [class*='col-']:nth-child(2) .stats-box { animation-delay: 0.2s; }
.row.g-2 > [class*='col-']:nth-child(3) .stats-box { animation-delay: 0.3s; }
.row.g-2 > [class*='col-']:nth-child(4) .stats-box { animation-delay: 0.4s; }

/* Add custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--gray-100);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary);
}

/* Chart customization */
.chart-js-render-monitor {
    --chart-color1: #B8860B;
    --chart-color2: #DAA520;
    --chart-color3: #CD853F;
    --chart-color4: #8B6914;
    --chart-color5: #996515;
}

.modal-content {
    border-radius: 10px;
}

.modal-header {
    background: var(--light);
    border-radius: 10px 10px 0 0;
}

.modal-body {
    background: var(--light);
}

.table {
    margin-bottom: 0;
}

.table th {
    background: var(--gray-100);
    color: var(--dark);
    font-weight: 600;
    border-bottom: 2px solid var(--gray-200);
}

.table td {
    vertical-align: middle;
    color: var(--dark);
    border-bottom: 1px solid var(--gray-200);
}

.badge {
    padding: 6px 10px;
    font-weight: 500;
}

.card h3 {
    color: var(--primary);
    font-weight: 600;
}

.card h6 {
    color: var(--dark);
    font-weight: 500;
    margin-bottom: 10px;
}

/* Additional styles for calendar and chart alignment */
.fc {
    background: var(--light);
    border-radius: 10px;
    height: 100% !important;
}

.fc-header-toolbar {
    padding: 0.5rem 1rem !important;
    margin: 0 !important;
}

.fc-view-harness {
    height: calc(100% - 50px) !important;
}

.fc-daygrid-body {
    height: 100% !important;
}

.fc-scroller {
    height: 100% !important;
}

.fc-view-harness-active {
    height: 100% !important;
}

.card-body {
    padding: 0.75rem;
}

.fc-view {
    height: 100% !important;
}

.fc td, .fc th {
    border-color: var(--gray-200);
}

.fc-day-today {
    background: rgba(184, 134, 11, 0.1) !important;
}

.fc-button-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: white !important;
    padding: 0.25rem 0.75rem !important;
    font-size: 0.8rem !important;
}

.fc-button-primary:hover {
    background: var(--secondary) !important;
    border-color: var(--secondary) !important;
}

.card {
    border: 1px solid var(--gray-200);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.card-header {
    background: var(--light);
    border-bottom: 1px solid var(--gray-200);
    padding: 0.75rem 1rem;
}

.btn-group .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
}

.btn-group .btn:hover {
    background: var(--secondary) !important;
}

/* Chart container styles */
.card {
    background: var(--light);
    border: 1px solid var(--gray-200);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    height: 100%;
}

.card-header {
    background: var(--light) !important;
    border-bottom: 1px solid var(--gray-200);
    padding: 1rem;
}

.card-body {
    padding: 1rem;
}

.activity-item {
    padding: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
}

.activity-time {
    font-size: 0.8rem;
    color: var(--gray-500);
}

.activity-text {
    margin: 0.25rem 0 0;
    color: var(--dark);
}

/* Chart specific styles */
#revenueChart, #roomTypesChart {
    margin: 0 auto;
}

/* Activity Feed Styles */
.activity-feed {
    height: 100%;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--primary) var(--light);
}

.activity-feed::-webkit-scrollbar {
    width: 6px;
}

.activity-feed::-webkit-scrollbar-track {
    background: var(--light);
}

.activity-feed::-webkit-scrollbar-thumb {
    background-color: var(--primary);
    border-radius: 3px;
}

.activity-item {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    transition: all 0.2s ease;
}

.activity-item:hover {
    background-color: rgba(184, 134, 11, 0.05);
}

.activity-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(184, 134, 11, 0.1);
    flex-shrink: 0;
}

.activity-text {
    color: var(--dark);
    font-size: 0.875rem;
    line-height: 1.4;
}

.activity-time {
    font-size: 0.75rem;
    color: var(--gray-500);
    display: block;
    margin-top: 2px;
}

.no-activity {
    padding: 2rem;
    text-align: center;
    color: var(--gray-500);
}

/* Ensure all cards have same height */
.card {
    height: 100%;
}

.card-body {
    position: relative;
}

#revenueChart, #roomTypesChart {
    height: 100% !important;
}

/* Additional Services Section Styles */
.events-list, .table-bookings-list {
    scrollbar-width: thin;
    scrollbar-color: var(--primary) var(--light);
}

.events-list::-webkit-scrollbar, .table-bookings-list::-webkit-scrollbar {
    width: 6px;
}

.events-list::-webkit-scrollbar-track, .table-bookings-list::-webkit-scrollbar-track {
    background: var(--light);
}

.events-list::-webkit-scrollbar-thumb, .table-bookings-list::-webkit-scrollbar-thumb {
    background-color: var(--primary);
    border-radius: 3px;
}

.event-item, .booking-item {
    transition: background-color 0.2s ease;
}

.event-item:hover, .booking-item:hover {
    background-color: rgba(184, 134, 11, 0.05);
}

.event-date, .booking-time {
    font-size: 0.75rem;
}

.event-title, .guest-name {
    font-size: 0.875rem;
}

.table-info {
    font-size: 0.75rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
}

/* Ensure consistent card heights */
.card {
    margin-bottom: 0;
}

.card-header {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.card-body {
    padding: 1rem;
}

/* Add these styles to your existing CSS */
.event-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(184, 134, 11, 0.1);
}

.event-item {
    transition: all 0.2s ease;
}

.event-item:hover {
    background-color: rgba(184, 134, 11, 0.05);
    transform: translateX(4px);
}

.event-title {
    font-size: 0.9rem;
    line-height: 1.3;
}

.event-details, .package-info {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.badge {
    font-size: 0.7rem;
    padding: 0.25em 0.5em;
    font-weight: 500;
}

// ... existing code ...
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(61, 43, 31, 0.95)',
                        titleColor: '#FFF8E7',
                        bodyColor: '#FFF8E7',
                        titleFont: {
                            size: 16,
                            weight: 'bold',
                            family: "'Inter', sans-serif"
                        },
                        bodyFont: {
                            size: 14,
                            family: "'Inter', sans-serif"
                        },
                        padding: 16,
                        cornerRadius: 8,
                        displayColors: false,
                        boxWidth: 200,
                        boxHeight: 80,
                        boxPadding: 12,
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            },
                            label: function(context) {
                                return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
// ... existing code ...

// Update the stats box tooltip styling
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = 'Click to view booking details';
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(61, 43, 31, 0.95);
                color: #FFF8E7;
                padding: 12px 16px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
    z-index: 1000;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                min-width: 180px;
                text-align: center;
                border: 1px solid rgba(255, 248, 231, 0.1);
            `;
// ... existing code ...
</style>
</head>
<body>
    <?php include('header.php'); ?>
    
    <div class="main-content">
        <!-- Dashboard Title -->
        <div class="container-fluid px-4 py-3">
            <h1 class="h3 mb-4" style="color: var(--dark); font-weight: 600;">Casa Estela Dashboard</h1>
        </div>

        <!-- Dashboard Container -->
        <div class="container-fluid px-4">
            <!-- Stats Cards -->
            <div class="container-fluid px-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-content">
                                <div class="number"><?php echo $metrics['total_pending_bookings'] ?? 0; ?></div>
                        <div class="label">Total Bookings</div>
                                <div class="sub-info">Pending bookings only</div>
                        </div>
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                    </div>
                            <div class="progress-mini"></div>
                </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-content">
                                <div class="number"><?php 
                                    $occupied = $metrics['occupied_rooms'] ?? 0;
                                    $total = $metrics['total_rooms'] ?? 1; // Prevent division by zero
                                    echo $occupied . '/' . $total;
                                ?></div>
                        <div class="label">Rooms Occupied</div>
                                <div class="sub-info"><?php echo round(($occupied / $total) * 100); ?>% occupancy</div>
                    </div>
                            <div class="stats-icon">
                                <i class="fas fa-bed"></i>
                </div>
                            <div class="progress-mini"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-content">
                                <div class="number"><?php echo $metrics['event_reservations'] ?? 0; ?></div>
                                <div class="label">Event Reservations</div>
                                <div class="sub-info">Total event bookings</div>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-calendar-star"></i>
                            </div>
                            <div class="progress-mini"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-box">
                            <div class="stats-content">
                                <div class="number">₱<?php echo number_format($metrics['cafe_revenue'] ?? 0, 2); ?></div>
                                <div class="label">Café Revenue</div>
                                <div class="sub-info">Total orders amount</div>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <div class="progress-mini"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row - 4 equal columns -->
            <div class="row g-2 mt-3">
                <div class="col-md-3">
                    <div class="stats-box">
                        <div class="stats-content">
                            <div class="number"><?php echo $metrics['checkouts_today'] ?? 0; ?></div>
                        <div class="label">Total Check-outs</div>
                            <div class="sub-info">All completed check-outs</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-sign-out-alt"></i>
                    </div>
                        <div class="progress-mini"></div>
                </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <div class="stats-content">
                            <div class="number"><?php echo $metrics['todays_guests'] ?? 0; ?></div>
                            <div class="label">Current Guests</div>
                            <div class="sub-info">Checked-in & Extended bookings</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="progress-mini"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <div class="stats-content">
                            <div class="number">₱<?php echo number_format($metrics['pending_payments'] ?? 0, 2); ?></div>
                        <div class="label">Downpayment Amounts</div>
                            <div class="sub-info"><?php echo $metrics['pending_transactions'] ?? 0; ?> customers used downpayment</div>
                    </div>
                        <div class="stats-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="progress-mini"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box" style="background: linear-gradient(145deg, #8B4513, #DAA520);">
                        <div class="stats-content">
                            <div class="number" style="font-size: 20px;">₱<?php echo number_format($metrics['total_hotel_revenue'], 2); ?></div>
                            <div class="label">Total Hotel Revenue</div>
                            <div class="sub-info">Combined revenue from all sources</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="progress-mini"></div>
                    </div>
                </div>
            </div>

            <!-- Calendar and Occupancy Trend side by side -->
            <div class="row mt-4">
                <div class="col-md-7">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Booking Calendar
                            </h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm" style="background: var(--primary); color: white;" id="calendarView">
                                    Month
                                </button>
                        </div>
                        </div>
                        <div class="card-body" style="height: 400px;">
                            <div id="bookingCalendar" style="height: 100%;"></div>
                    </div>
                </div>
                        </div>
                <div class="col-md-5">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>Occupancy Trends
                            </h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm" style="background: var(--primary); color: white;" id="trendView">
                                    Weekly
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="height: 400px;">
                            <canvas id="occupancyChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Analysis, Most Booked Room Types, and Recent Activity -->
            <div class="row g-4 mt-4">
                <!-- Revenue Analysis -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Revenue Analysis
                            </h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="revenueChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Most Booked Room Types -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Most Booked Room Types
                            </h5>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="roomTypesChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>Recent Activity
                            </h5>
                        </div>
                        <div class="card-body" style="height: 300px; padding: 0;">
                            <div class="activity-feed">
                                <?php if (!empty($recent_activities)): ?>
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <div class="activity-item">
                                            <div class="d-flex align-items-center">
                                                <div class="activity-icon" style="color: <?php echo $activity['color']; ?>">
                                                    <i class="<?php echo $activity['icon']; ?>"></i>
                                                </div>
                                                <div class="ms-3 flex-grow-1">
                                                    <p class="activity-text mb-0"><?php echo $activity['description']; ?></p>
                                                    <small class="activity-time text-muted">
                                                        <?php 
                                                        $timestamp = strtotime($activity['timestamp']);
                                                        $now = time();
                                                        $diff = $now - $timestamp;
                                                        
                                                        if ($diff < 60) {
                                                            echo "Just now";
                                                        } elseif ($diff < 3600) {
                                                            $mins = floor($diff / 60);
                                                            echo $mins . " min" . ($mins > 1 ? "s" : "") . " ago";
                                                        } elseif ($diff < 86400) {
                                                            $hours = floor($diff / 3600);
                                                            echo $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
                                                        } else {
                                                            echo date('M d, h:i A', $timestamp);
                                                        }
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-activity d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center">
                                            <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                            <p class="text-muted mb-0">No recent activities</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Services Section -->
            <div class="row g-4" style="margin-top: 2rem;">
                <!-- Event Bookings -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-star me-2"></i>
                                Upcoming Events
                            </h5>
                        </div>
                        <div class="card-body" style="height: 250px; overflow-y: auto;">
                            <div class="events-list h-100">
                                <?php if (!empty($upcoming_events)): ?>
                                    <?php foreach($upcoming_events as $event): ?>
                                        <div class="event-item d-flex align-items-center p-2 border-bottom">
                                            <div class="event-icon me-3" style="color: <?php echo $event['color']; ?>">
                                                <i class="<?php echo $event['icon']; ?> fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="event-date text-muted"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></div>
                                                        <div class="event-title fw-medium"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                                        <div class="event-details small text-muted">
                                                            <span class="me-2"><?php echo htmlspecialchars($event['event_type']); ?></span>
                                                            <span class="me-2">•</span>
                                                            <span><?php echo htmlspecialchars($event['guest_count']); ?> guests</span>
                                                        </div>
                                                    </div>
                                                    <div class="event-status ms-2">
                                                        <span class="badge" style="background: <?php echo $event['color']; ?>">
                                                            <?php echo htmlspecialchars($event['status']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="package-info small text-muted mt-1">
                                                    <?php echo htmlspecialchars($event['package_name']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center">
                                            <i class="fas fa-calendar-plus fa-3x mb-3 text-muted"></i>
                                            <p class="text-muted mb-0">No upcoming events</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

                <!-- Table Reservations -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-utensils me-2"></i>
                                Today's Table Bookings
                            </h5>
            </div>
                        <div class="card-body" style="height: 250px; overflow-y: auto;">
                            <div class="table-bookings-list h-100">
                                <?php if (!empty($table_bookings)): ?>
                                    <?php foreach($table_bookings as $booking): ?>
                                        <div class="booking-item d-flex align-items-center p-2 border-bottom">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="booking-time text-muted">
                                                            <?php echo date('h:i A', strtotime($booking['booking_time'])); ?>
                                                        </div>
                                                        <div class="guest-name fw-medium">
                                                            <?php echo htmlspecialchars($booking['customer_name']); ?>
                                                        </div>
                                                        <div class="table-info small text-muted">
                                                            <?php echo htmlspecialchars($booking['num_guests']); ?> guests • 
                                                            ₱<?php echo number_format($booking['total_amount'], 2); ?>
                                                        </div>
                                                    </div>
                                                    <div class="booking-status ms-2">
                                                        <span class="badge bg-<?php echo $booking['status'] === 'Confirmed' ? 'success' : 'warning'; ?>">
                                                            <?php echo htmlspecialchars($booking['status']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="payment-info small text-muted mt-1">
                                                    <i class="fas fa-credit-card me-1"></i>
                                                    <?php echo htmlspecialchars($booking['payment_method']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center">
                                            <i class="fas fa-utensils fa-3x mb-3 text-muted"></i>
                                            <p class="text-muted mb-0">No table reservations for today</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Café Analytics -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Café Performance
                            </h5>
                        </div>
                        <div class="card-body" style="height: 250px;">
                            <canvas id="cafeRevenueChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-container" style="height: 250px; margin-top: 20px;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Weekly Stats
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm" style="background: var(--primary); color: white;" id="weeklyStatsType">
                            Revenue
                        </button>
                    </div>
                </div>
                <div class="card-body" style="height: 200px;">
            <canvas id="weeklyStats"></canvas>
                </div>
            </div>
        </div>

        <script>
        // Update Weekly Stats Chart with fixed height
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('weeklyStats').getContext('2d');
            const weeklyStatsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($weekly_labels); ?>,
                    datasets: [{
                        label: 'Revenue',
                        data: <?php echo json_encode($weekly_revenue); ?>,
                        backgroundColor: '#B8860B',
                        borderColor: '#DAA520',
                        borderWidth: 1,
                        borderRadius: 4,
                        maxBarThickness: 25
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(61, 43, 31, 0.9)',
                            titleColor: '#FFF8E7',
                            bodyColor: '#FFF8E7',
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(184, 134, 11, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#3D2B1F',
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#3D2B1F'
                            }
                        }
                    }
                }
            });
        });
        </script>

        <!-- Add Modal for Total Bookings -->
        <div class="modal fade" id="bookingsModal" tabindex="-1" aria-labelledby="bookingsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="background: var(--light); border: 1px solid var(--gray-200);">
                    <div class="modal-header" style="border-bottom: 1px solid var(--gray-200);">
                        <h5 class="modal-title" id="bookingsModalLabel">Booking Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Today's Bookings</h6>
                                        <h3 class="mb-0" id="todayBookings">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Pending Bookings</h6>
                                        <h3 class="mb-0" id="pendingBookings">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Revenue</h6>
                                        <h3 class="mb-0" id="totalRevenue">₱0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="bookingsTable">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Guest Name</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Room Type</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script>
        // Common tooltip configuration for all charts
        const commonTooltipConfig = {
            enabled: true,
            backgroundColor: 'rgba(0, 0, 0, 0.9)',
            titleFont: {
                size: 20,
                weight: 'bold',
                family: "'Inter', sans-serif"
            },
            bodyFont: {
                size: 18,
                family: "'Inter', sans-serif"
            },
            padding: 16,
            cornerRadius: 8,
            boxPadding: 8,
            displayColors: true,
            borderColor: 'rgba(255, 255, 255, 0.2)',
            borderWidth: 1,
            caretSize: 12,
            caretPadding: 8,
            titleMarginBottom: 12,
            bodySpacing: 8,
            multiKeyBackground: '#000'
        };

        // Update Weekly Stats Chart
        const ctx = document.getElementById('weeklyStats').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($weekly_labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($weekly_revenue); ?>,
                    backgroundColor: [
                        '#FF6B6B', '#4ECDC4', '#FFE66D', '#6C5CE7',
                        '#FF9F89', '#6BE5DB', '#A5A8FF'
                    ],
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: commonTooltipConfig,
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(78, 205, 196, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });

        function showTab(tabName) {
            // Hide all table sections
            document.getElementById('arrivals-table').style.display = 'none';
            document.getElementById('departures-table').style.display = 'none';
            
            // Show the selected table section
            document.getElementById(tabName + '-table').style.display = 'block';
            
            // Update active tab
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Find and activate the clicked tab
            if (tabName === 'arrivals') {
                tabs[0].classList.add('active');
            } else if (tabName === 'departures') {
                tabs[1].classList.add('active');
            }
        }

        // Initialize with arrivals tab active
document.addEventListener('DOMContentLoaded', function() {
            showTab('arrivals');
        });

        document.addEventListener('DOMContentLoaded', function() {
                // Initialize Calendar with fixed height
            var calendarEl = document.getElementById('bookingCalendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                    height: '100%',
                events: 'get_calendar_events.php',
                eventDidMount: function(info) {
                        info.el.style.backgroundColor = 'var(--primary)';
                        info.el.style.borderColor = 'var(--secondary)';
                },
                displayEventTime: false,
                    dayMaxEvents: true
            });
            calendar.render();

                // Initialize Occupancy Chart with matching height
            var occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
            new Chart(occupancyCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($weekly_labels); ?>,
                    datasets: [{
                        label: 'Occupancy Rate',
                        data: <?php echo json_encode($occupancy_data); ?>,
                            borderColor: '#B8860B',
                            backgroundColor: 'rgba(184, 134, 11, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#DAA520',
                            pointBorderColor: '#B8860B',
                        pointBorderWidth: 2,
                            tension: 0.4,
                            fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(61, 43, 31, 0.9)',
                                titleColor: '#FFF8E7',
                                bodyColor: '#FFF8E7',
                                padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                        return 'Occupancy: ' + context.parsed.y + '%';
                                    }
                                }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                    color: 'rgba(184, 134, 11, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                        size: 11
                                },
                                    color: '#3D2B1F',
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                        size: 11
                                },
                                    color: '#3D2B1F'
                            }
                        }
                    }
                }
            });

            // Initialize Revenue Chart
            var revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($weekly_labels); ?>,
                    datasets: [{
                        label: 'Revenue',
                        data: <?php echo json_encode($weekly_revenue); ?>,
                        backgroundColor: [
                            '#FF6B6B', '#4ECDC4', '#FFE66D', '#6C5CE7',
                            '#FF9F89', '#6BE5DB', '#A5A8FF'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            ...commonTooltipConfig,
                            callbacks: {
                                label: function(context) {
                                    return `Revenue: ₱${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(78, 205, 196, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Initialize Room Types Chart
            var roomTypesCtx = document.getElementById('roomTypesChart').getContext('2d');
            new Chart(roomTypesCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_keys($room_type_data)); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_values($room_type_data)); ?>,
                        backgroundColor: [
                            '#FF6B6B', '#4ECDC4', '#FFE66D', '#6C5CE7'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            ...commonTooltipConfig,
                            callbacks: {
                                label: function(context) {
                                    return `Bookings: ${context.parsed}`;
                                }
                            }
                        },
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    size: 14
                                },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    },
                    cutout: '60%',
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 20,
                            left: 20,
                            right: 20
                        }
                    }
                }
                });

                // Initialize Café Revenue Chart
                var cafeCtx = document.getElementById('cafeRevenueChart').getContext('2d');
                new Chart(cafeCtx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Café Revenue',
                            data: [12000, 19000, 15000, 21000, 24000, 28000, 23000],
                            borderColor: '#B8860B',
                            backgroundColor: 'rgba(184, 134, 11, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#FFF8E7',
                            pointBorderColor: '#B8860B',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#B8860B',
                            pointHoverBorderColor: '#FFF8E7',
                            pointHoverBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(61, 43, 31, 0.95)',
                                titleColor: '#FFF8E7',
                                bodyColor: '#FFF8E7',
                                titleFont: {
                                    size: 16,
                                    weight: 'bold',
                                    family: "'Inter', sans-serif"
                                },
                                bodyFont: {
                                    size: 14,
                                    family: "'Inter', sans-serif"
                                },
                                padding: 16,
                                cornerRadius: 8,
                                displayColors: false,
                                boxWidth: 200,
                                boxHeight: 80,
                                boxPadding: 12,
                                callbacks: {
                                    title: function(tooltipItems) {
                                        return tooltipItems[0].label;
                                    },
                                    label: function(context) {
                                        return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(184, 134, 11, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 10,
                                        weight: '500'
                                    },
                                    color: '#3D2B1F',
                                    callback: function(value) {
                                        return '₱' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10,
                                        weight: '500'
                                    },
                                    color: '#3D2B1F'
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            });

        document.addEventListener('DOMContentLoaded', function() {
            // Get the stats box element
            const totalBookingsBox = document.querySelector('.stats-box:first-child');
            const bookingsModal = new bootstrap.Modal(document.getElementById('bookingsModal'));
            let bookingsData = null;

            // Function to fetch booking data
            async function fetchBookingData() {
                try {
                    const response = await fetch('get_bookings_data.php');
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error fetching booking data:', error);
                    return null;
                }
            }

            // Function to update modal content
            function updateModalContent(data) {
                if (!data) return;

                // Update summary cards
                document.getElementById('todayBookings').textContent = data.summary.today || 0;
                document.getElementById('pendingBookings').textContent = data.summary.pending || 0;
                document.getElementById('totalRevenue').textContent = '₱' + (data.summary.revenue || 0).toLocaleString();

                // Update table
                const tbody = document.querySelector('#bookingsTable tbody');
                tbody.innerHTML = '';

                data.bookings.forEach(booking => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${booking.booking_id}</td>
                        <td>${booking.guest_name}</td>
                        <td>${booking.check_in}</td>
                        <td>${booking.check_out}</td>
                        <td>${booking.room_type}</td>
                        <td><span class="badge bg-${getStatusColor(booking.status)}">${booking.status}</span></td>
                        <td>₱${parseFloat(booking.amount).toLocaleString()}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            // Helper function to get status color
            function getStatusColor(status) {
                const colors = {
                    'Pending': 'warning',
                    'Confirmed': 'success',
                    'Checked In': 'primary',
                    'Checked Out': 'secondary',
                    'Cancelled': 'danger'
                };
                return colors[status] || 'info';
            }

            // Add click event listener to the stats box
            totalBookingsBox.addEventListener('click', async function() {
                if (!bookingsData) {
                    bookingsData = await fetchBookingData();
                }
                updateModalContent(bookingsData);
                bookingsModal.show();
            });

            // Add hover effect and cursor style
            totalBookingsBox.style.cursor = 'pointer';
            
            // Add tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = 'Click to view booking details';
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(61, 43, 31, 0.95);
                color: #FFF8E7;
                padding: 12px 16px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                min-width: 180px;
                text-align: center;
                border: 1px solid rgba(255, 248, 231, 0.1);
            `;
            document.body.appendChild(tooltip);

            totalBookingsBox.addEventListener('mousemove', (e) => {
                tooltip.style.opacity = '1';
                tooltip.style.left = (e.pageX + 10) + 'px';
                tooltip.style.top = (e.pageY + 10) + 'px';
            });

            totalBookingsBox.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
            });
        });
</script>
</body>
</html>


