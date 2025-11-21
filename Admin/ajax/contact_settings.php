<?php
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_POST['action'];

switch ($action) {
    case 'add':
        $icon_class = mysqli_real_escape_string($con, $_POST['icon_class']);
        $display_text = mysqli_real_escape_string($con, $_POST['display_text']);
        $link = mysqli_real_escape_string($con, $_POST['link']);
        $is_external = isset($_POST['is_external']) ? 1 : 0;
        $display_order = (int)$_POST['display_order'];

        $query = "INSERT INTO contact_info (icon_class, display_text, link, is_external, display_order, active) 
                 VALUES ('$icon_class', '$display_text', '$link', $is_external, $display_order, 1)";
        
        if (mysqli_query($con, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
        }
        break;

    case 'get':
        $id = (int)$_POST['id'];
        $query = "SELECT * FROM contact_info WHERE id = $id";
        $result = mysqli_query($con, $query);
        
        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Contact not found']);
        }
        break;

    case 'update':
        $id = (int)$_POST['id'];
        $icon_class = mysqli_real_escape_string($con, $_POST['icon_class']);
        $display_text = mysqli_real_escape_string($con, $_POST['display_text']);
        $link = mysqli_real_escape_string($con, $_POST['link']);
        $is_external = isset($_POST['is_external']) ? 1 : 0;
        $display_order = (int)$_POST['display_order'];

        $query = "UPDATE contact_info 
                 SET icon_class = '$icon_class',
                     display_text = '$display_text',
                     link = '$link',
                     is_external = $is_external,
                     display_order = $display_order
                 WHERE id = $id";
        
        if (mysqli_query($con, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
        }
        break;

    case 'toggle':
        $id = (int)$_POST['id'];
        $query = "UPDATE contact_info SET active = NOT active WHERE id = $id";
        
        if (mysqli_query($con, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
} 