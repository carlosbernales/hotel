<?php
function getActiveDiscounts($con, $room_type_id) {
    // Initialize empty array for discounts
    $discounts = array();
    
    // Check if the seasonal_discounts table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'seasonal_discounts'");
    if (!$table_check || mysqli_num_rows($table_check) == 0) {
        error_log("No seasonal_discounts table found");
        return $discounts;
    }

    try {
        // Get current date for comparison
        $current_date = date('Y-m-d');
        
        // Query to get active discounts for the room type
        $query = "SELECT sd.*, rt.room_type as name
                 FROM seasonal_discounts sd
                 LEFT JOIN room_types rt ON sd.room_type_id = rt.room_type_id
                 WHERE (sd.room_type_id = ? OR sd.room_type_id IS NULL)
                 AND sd.is_active = 1
                 AND (sd.start_date IS NULL OR sd.start_date <= ?)
                 AND (sd.end_date IS NULL OR sd.end_date >= ?)
                 ORDER BY sd.discount_percentage DESC";
        
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iss", $room_type_id, $current_date, $current_date);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $discounts[] = array(
                    'discount_id' => $row['id'],
                    'name' => $row['discount_name'] ?? 'Seasonal Discount',
                    'discount_percentage' => $row['discount_percentage'],
                    'is_active' => $row['is_active']
                );
            }
            
            mysqli_stmt_close($stmt);
        }
    } catch (Exception $e) {
        error_log("Error in getActiveDiscounts: " . $e->getMessage());
    }
    
    error_log("Retrieved discounts for room_type_id $room_type_id: " . json_encode($discounts));
    return $discounts;
}

function calculateDiscountedPrice($original_price, $discounts) {
    error_log("Calculating price for: " . $original_price . " with discounts: " . json_encode($discounts));
    
    // Return original price if no discounts array provided
    if (!is_array($discounts) || empty($discounts)) {
        error_log("No discounts provided, returning original price");
        return [
            'original_price' => $original_price,
            'final_price' => $original_price,
            'discount_percentage' => 0,
            'discount_name' => ''
        ];
    }
    
    try {
        $final_price = $original_price;
        $applied_discount = 0;
        $discount_name = '';
        
        // Get the highest discount
        $highest_discount = null;
        foreach ($discounts as $discount) {
            error_log("Checking discount: " . json_encode($discount));
            if (isset($discount['discount_percentage']) && 
                isset($discount['is_active']) && 
                $discount['is_active'] == 1 && 
                (!$highest_discount || $discount['discount_percentage'] > $highest_discount['discount_percentage'])) {
                $highest_discount = $discount;
                error_log("Found higher discount: " . json_encode($highest_discount));
            }
        }
        
        // Apply the highest discount if found
        if ($highest_discount) {
            $applied_discount = $highest_discount['discount_percentage'];
            $discount_name = $highest_discount['name'];
            $final_price = $original_price - ($original_price * ($applied_discount / 100));
            $final_price = max(0, $final_price);
            error_log("Applied discount: $applied_discount%, Final price: $final_price");
        } else {
            error_log("No valid discount found");
            // No valid discount found
            return [
                'original_price' => $original_price,
                'final_price' => $original_price,
                'discount_percentage' => 0,
                'discount_name' => ''
            ];
        }
        
        $result = [
            'original_price' => $original_price,
            'final_price' => $final_price,
            'discount_percentage' => $applied_discount,
            'discount_name' => $discount_name
        ];
        error_log("Final result: " . json_encode($result));
        return $result;
        
    } catch (Exception $e) {
        error_log("Error in calculateDiscountedPrice: " . $e->getMessage());
        return [
            'original_price' => $original_price,
            'final_price' => $original_price,
            'discount_percentage' => 0,
            'discount_name' => ''
        ];
    }
}

// Helper function to format price with proper currency symbol
function formatPrice($price) {
    return '₱' . number_format($price, 2);
}