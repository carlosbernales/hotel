<?php
header('Content-Type: application/json');
include '../adminBackend/mydb.php';


function generateOrderId($conn)
{
    do {
        $randomNumber = random_int(10000000000, 99999999999);
        $orderId = 'ORD' . $randomNumber;

        $stmt = $conn->prepare("SELECT id FROM orders_table WHERE order_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

    } while ($exists);

    return $orderId;
}


$conn->begin_transaction();

try {

    // ------------------ BASIC ORDER ------------------
    $first = $_POST['first'] ?? '';
    $last = $_POST['last'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $datetime = $_POST['datetime'] ?? '';

    $orderCode = generateOrderId($conn);

    $total = isset($_POST['total']) ? (float) $_POST['total'] : 0;

    $downpayment = isset($_POST['downpayment']) ? (float) $_POST['downpayment'] : 0;
    $dpPaymentMethod = $_POST['dp_payment_method'] ?? '';

    if ($downpayment <= 0 || $downpayment > $total) {
        throw new Exception('Invalid downpayment amount');
    }

    if (!$dpPaymentMethod) {
        throw new Exception('Missing downpayment payment method');
    }


    if ($total <= 0) {
        throw new Exception('Invalid order total');
    }


    if (!$first || !$last || !$contact || !$datetime) {
        throw new Exception('Missing customer info');
    }

    $stmt = $conn->prepare("
        INSERT INTO orders_table 
        (order_id, firstname, lastname, contact, date_time, total, downpayment, dp_payment_method, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $status = 'Cashier';

    $stmt->bind_param(
        "sssssddss",
        $orderCode,
        $first,
        $last,
        $contact,
        $datetime,
        $total,
        $downpayment,
        $dpPaymentMethod,
        $status
    );

    $stmt->execute();



    $orderId = $stmt->insert_id;
    $stmt->close();

    if (!isset($_POST['cartItems'])) {
        throw new Exception('No cart items');
    }

    foreach ($_POST['cartItems'] as $itemJson) {

        $item = json_decode($itemJson, true);
        $menuItemId = (int) $item['id'];
        $qty = (int) $item['qty'];

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
        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {

                $addonId = (int) $addon['addon_id'];
                $addonQty = (int) $addon['qty'];

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
    session_start();

    if (!empty($_SESSION['advance_order'])) {

        $booking = $_SESSION['advance_order'];

        foreach ($booking['tables'] as $i => $tableNumberId) {

            $tableNumberId = (int) $tableNumberId;
            $tableTypeId = (int) $booking['tableTypes'][$i];

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

            $stmt = $conn->prepare("
            INSERT INTO orders_table_type
            (table_booking_fk_id, table_type_fk_id, table_number_fk_id, table_name, table_number)
            VALUES (?, ?, ?, ?, ?)
        ");
            $stmt->bind_param(
                "iiiss",
                $orderId,
                $tableTypeId,
                $tableNumberId,
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
