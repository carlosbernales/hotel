<?php
include '../adminBackend/mydb.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$addon_id = intval($_GET['id']);

$delete = $conn->prepare("DELETE FROM menu_items_addons WHERE id = ?");
$delete->bind_param("i", $addon_id);

if ($delete->execute()) {
    echo "<script>
            alert('Add-on deleted.');
            history.back();
          </script>";
} else {
    echo "Delete failed.";
}
?>