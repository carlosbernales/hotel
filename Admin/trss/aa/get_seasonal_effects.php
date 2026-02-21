<?php
function get_seasonal_effects($pdo) {
    try {
        $query = "SELECT * FROM seasonal_effects WHERE is_active = 1 
                  AND start_date <= CURRENT_DATE 
                  AND end_date >= CURRENT_DATE";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log error and return empty array
        error_log("Error fetching seasonal effects: " . $e->getMessage());
        return [];
    }
}