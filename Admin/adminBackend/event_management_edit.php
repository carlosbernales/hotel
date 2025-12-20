<?php
include '../adminBackend/mydb.php';

function generateRandomFilename($length = 12)
{
    return bin2hex(random_bytes($length));
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("Invalid ID");
}

$imageDir = realpath(__DIR__ . '/event_packages_images') . DIRECTORY_SEPARATOR;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $duration = $conn->real_escape_string($_POST['duration'] ?? '');
    $time_limit = $conn->real_escape_string($_POST['time_limit'] ?? '');
    $max_pax = intval($_POST['max_pax'] ?? 0);
    $max_guests = intval($_POST['max_guests'] ?? 0);
    $status = $conn->real_escape_string($_POST['status'] ?? 'unavailable');
    $description = $conn->real_escape_string($_POST['description'] ?? '');
    $notes = $conn->real_escape_string($_POST['notes'] ?? '');

    $menuItems = $_POST['menu_items'] ?? [];
    $menuItems = implode(',', array_filter(array_map('trim', $menuItems)));

    $res = $conn->query("
        SELECT image_path, image_path2, image_path3
        FROM event_packages
        WHERE id = $id
    ");

    if (!$res || $res->num_rows === 0) {
        die("Record not found");
    }

    $old = $res->fetch_assoc();
    $oldImages = array_filter([
        $old['image_path'],
        $old['image_path2'],
        $old['image_path3']
    ]);

    $hasUpload = false;
    foreach ($_FILES['images']['name'] ?? [] as $n) {
        if (!empty($n)) {
            $hasUpload = true;
            break;
        }
    }

    if ($hasUpload) {

        foreach ($oldImages as $img) {
            $file = $imageDir . $img;
            if (is_file($file)) {
                unlink($file);
            }
        }

        $newImage1 = NULL;
        $newImage2 = NULL;
        $newImage3 = NULL;

        $limit = min(count($_FILES['images']['name']), 3);
        $uploaded = [];

        for ($i = 0; $i < $limit; $i++) {

            if (empty($_FILES['images']['name'][$i]))
                continue;
            if (!is_uploaded_file($_FILES['images']['tmp_name'][$i]))
                continue;

            $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            $filename = generateRandomFilename() . '.' . $ext;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $imageDir . $filename)) {
                $uploaded[] = $filename;
            }
        }

        $newImage1 = $uploaded[0] ?? NULL;
        $newImage2 = $uploaded[1] ?? NULL;
        $newImage3 = $uploaded[2] ?? NULL;

    } else {
        $newImage1 = $old['image_path'];
        $newImage2 = $old['image_path2'];
        $newImage3 = $old['image_path3'];
    }

    $sql = "
        UPDATE event_packages SET
            name='$name',
            price=$price,
            duration='$duration',
            time_limit='$time_limit',
            max_pax=$max_pax,
            max_guests=$max_guests,
            status='$status',
            description='$description',
            notes='$notes',
            menu_items='$menuItems',
            image_path=" . ($newImage1 === NULL ? "NULL" : "'$newImage1'") . ",
            image_path2=" . ($newImage2 === NULL ? "NULL" : "'$newImage2'") . ",
            image_path3=" . ($newImage3 === NULL ? "NULL" : "'$newImage3'") . "
        WHERE id=$id
    ";

    if ($conn->query($sql)) {
        header("Location: ../../Admin/index.php?event_management");
        exit;
    } else {
        echo "Update failed: " . $conn->error;
    }
}
