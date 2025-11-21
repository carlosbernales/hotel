<?php
require_once 'db.php';

function renderBookingSidebar($booking_type) {
    global $con;
    
    // Get settings
    $stmt = $con->prepare("SELECT * FROM booking_display_settings WHERE booking_type = ?");
    $stmt->bind_param("s", $booking_type);
    $stmt->execute();
    $settings = $stmt->get_result()->fetch_assoc();
    
    if (!$settings) {
        return "No settings found for this booking type.";
    }

    $display_fields = json_decode($settings['display_fields'], true);
    $image_settings = json_decode($settings['image_settings'], true);

    // Get items based on booking type
    $table_name = $booking_type . 's'; // rooms, tables, events
    $items = $con->query("SELECT * FROM $table_name ORDER BY id DESC");

    $output = '<div class="booking-sidebar">';
    
    // Add Room Management Link for room bookings
    if ($booking_type === 'room') {
        $output .= '<div class="mb-3">
            <a href="room_management.php" class="btn btn-primary btn-block">
                <i class="fas fa-cog"></i> Manage Rooms
            </a>
        </div>';
    }
    
    while ($item = $items->fetch_assoc()) {
        $output .= '<div class="booking-item">';
        
        // Display image if enabled
        if ($image_settings['enable'] && !empty($item['image'])) {
            $output .= sprintf(
                '<img src="%s" width="%d" height="%d" class="booking-image">',
                htmlspecialchars($item['image']),
                $image_settings['width'],
                $image_settings['height']
            );
        }

        // Display selected fields
        foreach ($display_fields as $field) {
            if (isset($item[$field])) {
                $output .= sprintf(
                    '<div class="booking-field %s">%s: %s</div>',
                    htmlspecialchars($field),
                    ucfirst($field),
                    htmlspecialchars($item[$field])
                );
            }
        }

        $output .= '</div>';
    }

    $output .= '</div>';
    return $output;
}
?>
<style>
.booking-sidebar {
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.booking-item {
    margin-bottom: 20px;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.booking-image {
    margin-bottom: 10px;
    object-fit: cover;
}

.booking-field {
    margin: 5px 0;
}
</style>