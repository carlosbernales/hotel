<?php
include '../adminBackend/mydb.php';

if (isset($_GET['id'])) {
    $category_id = intval($_GET['id']);

    $conn->begin_transaction();

    try {
        $itemsResult = $conn->query("SELECT id, image_path FROM menu_items WHERE category_id = $category_id");
        $menu_item_ids = [];
        $image_paths = [];

        if ($itemsResult) {
            while ($row = $itemsResult->fetch_assoc()) {
                $menu_item_ids[] = $row['id'];
                if (!empty($row['image_path'])) {
                    $image_paths[] = $row['image_path'];
                }
            }
        }

        if (!empty($menu_item_ids)) {
            $ids_string = implode(',', $menu_item_ids);
            $conn->query("DELETE FROM menu_items_addons WHERE menu_item_id IN ($ids_string)");
        }

        $conn->query("DELETE FROM menu_items WHERE category_id = $category_id");

        $conn->query("DELETE FROM menu_categories WHERE id = $category_id");

        foreach ($image_paths as $path) {
            $full_path = "../../Admin/adminBackend/menu_item_images/" . $path;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }

        $conn->commit();
        header("Location: ../../Admin/index.php?cafe_management");

        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error deleting category: " . $e->getMessage();
    }

} else {
    echo "No category ID provided.";
}
?>