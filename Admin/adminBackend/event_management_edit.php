<?php
include '../adminBackend/mydb.php';

function generateRandomFilename($length = 12) {
    return bin2hex(random_bytes($length));
}

$imageDir = '../../Admin/adminBackend/event_packages_images/';
$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $time_limit = $conn->real_escape_string($_POST['time_limit']);
    $max_pax = intval($_POST['max_pax']);
    $max_guests = intval($_POST['max_guests']);
    $is_available = intval($_POST['is_available']);
    $status = $conn->real_escape_string($_POST['status']);
    $description = $conn->real_escape_string($_POST['description']);
    $notes = $conn->real_escape_string($_POST['notes']);

    $sql_select = "SELECT image_path, image_path2, image_path3 FROM event_packages WHERE id = $id";
    $result = $conn->query($sql_select);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentImages = [
            $row['image_path'],
            $row['image_path2'],
            $row['image_path3']
        ];
    } else {
        die("Record not found.");
    }

    $newImages = ["", "", ""]; 

    if (!empty($_FILES['image']['name'][0])) {
        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['image']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['image']['name'][$key], PATHINFO_EXTENSION);
                $newFilename = generateRandomFilename() . '.' . $ext;
                $destination = $imageDir . $newFilename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    if (!empty($currentImages[$key]) && file_exists($imageDir . $currentImages[$key])) {
                        unlink($imageDir . $currentImages[$key]);
                    }
                    $newImages[$key] = $newFilename;
                }
            }
        }
        for ($i = count($_FILES['image']['name']); $i < 3; $i++) {
            if (!empty($currentImages[$i]) && file_exists($imageDir . $currentImages[$i])) {
                unlink($imageDir . $currentImages[$i]);
            }
            $newImages[$i] = ""; 
        }
    } else {
        $newImages = $currentImages;
    }

    $sql_update = "UPDATE event_packages SET
        name = '$name',
        price = $price,
        duration = '$duration',
        time_limit = '$time_limit',
        max_pax = $max_pax,
        max_guests = $max_guests,
        is_available = $is_available,
        status = '$status',
        description = '$description',
        notes = '$notes',
        image_path = '" . $newImages[0] . "',
        image_path2 = '" . $newImages[1] . "',
        image_path3 = '" . $newImages[2] . "'
        WHERE id = $id";

    if ($conn->query($sql_update)) {
        header("Location: ../../Admin/index.php?event_management");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
