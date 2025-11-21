<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // First get the space details to delete associated images
    $stmt = $con->prepare("SELECT image_path, gallery_images FROM event_spaces WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $space = $result->fetch_assoc();
    
    if ($space) {
        // Delete main image
        if (!empty($space['image_path']) && file_exists($space['image_path'])) {
            unlink($space['image_path']);
        }
        
        // Delete gallery images
        if (!empty($space['gallery_images'])) {
            $gallery_images = json_decode($space['gallery_images'], true);
            if (is_array($gallery_images)) {
                foreach ($gallery_images as $image) {
                    if (file_exists($image)) {
                        unlink($image);
                    }
                }
            }
        }
        
        // Delete the space record
        $stmt = $con->prepare("DELETE FROM event_spaces WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Error deleting event space: ' . $con->error
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Event space not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request'
    ]);
}
