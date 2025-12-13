<?php
header('Content-Type: application/json');
include '../adminBackend/mydb.php';

$conn->begin_transaction();

try {

    // ------------------ BASIC ORDER ------------------
    $first = $_POST['first'] ?? '';
    $last = $_POST['last'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $datetime = $_POST['datetime'] ?? '';

    if (!$first || !$last || !$contact || !$datetime) {
        throw new Exception('Missing customer info');
    }

    $stmt = $conn->prepare("
        INSERT INTO orders_table (firstname, lastname, contact, date_time)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $first, $last, $contact, $datetime);
    $stmt->execute();

    $orderId = $stmt->insert_id;
    $stmt->close();

    // ------------------ ORDER ITEMS ------------------
    if (!isset($_POST['cartItems'])) {
        throw new Exception('No cart items');
    }

    foreach ($_POST['cartItems'] as $itemJson) {

        $item = json_decode($itemJson, true);
        $menuItemId = (int) $item['id'];
        $qty = (int) $item['qty'];

        // get item details from DB
        $stmt = $conn->prepare("
            SELECT name, price 
            FROM menu_items 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $menuItemId);
        $stmt->execute();
        $menu = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$menu)
            throw new Exception('Menu item not found');

        // insert order item
        $stmt = $conn->prepare("
            INSERT INTO order_items
            (order_fk_id, item_name, quantity, unit_price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isid",
            $orderId,
            $menu['name'],
            $qty,
            $menu['price']
        );
        $stmt->execute();

        $orderItemId = $stmt->insert_id;
        $stmt->close();

        // ------------------ ADDONS ------------------
        // ------------------ ADDONS ------------------
        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {

                $addonId = (int) $addon['addon_id'];
                $addonQty = (int) $addon['qty'];

                // fetch addon from menu_items_addons
                $stmt = $conn->prepare("
            SELECT name, price 
            FROM menu_items_addons
            WHERE id = ?
        ");
                $stmt->bind_param("i", $addonId);
                $stmt->execute();
                $addonRow = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$addonRow)
                    continue;

                // insert into order_item_addons
                $stmt = $conn->prepare("
                    INSERT INTO order_item_addons
                    (order_item_fk_id, addon_name, price, quantity)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "isdi",
                    $orderItemId,
                    $addonRow['name'],   // ✅ correct
                    $addonRow['price'],
                    $addonQty
                );
                $stmt->execute();
                $stmt->close();
            }
        }

    }

    // ------------------ TABLE TYPE + NUMBER ------------------
    // assumes advance booking stored in session
    // ------------------ TABLE TYPE + NUMBER ------------------
    session_start();

    if (!empty($_SESSION['advance_booking'])) {

        $booking = $_SESSION['advance_booking'];

        foreach ($booking['tables'] as $i => $tableNumberId) {

            $tableNumberId = (int) $tableNumberId;
            $tableTypeId = (int) $booking['tableTypes'][$i];

            // get table type name
            $stmt = $conn->prepare("
            SELECT table_name
            FROM table_types
            WHERE id = ?
        ");
            $stmt->bind_param("i", $tableTypeId);
            $stmt->execute();
            $typeRow = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$typeRow)
                continue;

            // get table number
            $stmt = $conn->prepare("
            SELECT table_number
            FROM table_number
            WHERE id = ?
        ");
            $stmt->bind_param("i", $tableNumberId);
            $stmt->execute();
            $numRow = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$numRow)
                continue;

            // ✅ CORRECT INSERT
            $stmt = $conn->prepare("
            INSERT INTO orders_table_type
            (table_booking_fk_id, table_type_fk_id, table_number_fk_id, table_name, table_number)
            VALUES (?, ?, ?, ?, ?)
        ");
            $stmt->bind_param(
                "iiisi",            // ✅ correct types & order
                $orderId,           // orders_table.id
                $tableTypeId,       // table_types.id
                $tableNumberId,     // table_number.id
                $typeRow['table_name'],
                $numRow['table_number']
            );
            $stmt->execute();
            $stmt->close();
        }
    }


    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'order_id' => $orderId
    ]);

} catch (Exception $e) {

    $conn->rollback();

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
