<?php
header("Content-Type: application/json");
include '../adminBackend/mydb.php';

try {
    $first = $_POST['first'] ?? '';
    $last = $_POST['last'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $datetime = $_POST['datetime'] ?? '';
    $tableTypes = $_POST['tableTypes'] ?? [];
    $tables = $_POST['tables'] ?? [];

    if (!$first || !$last || !$contact || !$datetime) {
        echo json_encode(["status" => "error", "msg" => "Missing fields"]);
        exit;
    }

    if (count($tableTypes) !== count($tables)) {
        echo json_encode(["status" => "error", "msg" => "Mismatch table count"]);
        exit;
    }

    $orderId = "ORD-" . time();

    $stmt = $conn->prepare("INSERT INTO orders_table 
        (order_id, firstname, lastname, contact, date_time, status)
        VALUES (?, ?, ?, ?, ?, 'Cashier')");
    $stmt->bind_param("sssss", $orderId, $first, $last, $contact, $datetime);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "msg" => "Insert main order failed"]);
        exit;
    }

    $bookingId = $stmt->insert_id;

    for ($i = 0; $i < count($tableTypes); $i++) {
        $typeId = $tableTypes[$i];
        $tableNumId = $tables[$i];

        $tableName = $conn->query("SELECT table_name FROM table_types WHERE id = $typeId")->fetch_assoc()['table_name'];
        $tableNumber = $conn->query("SELECT table_number FROM table_number WHERE id = $tableNumId")->fetch_assoc()['table_number'];

        $ins = $conn->prepare("INSERT INTO orders_table_type
            (table_booking_fk_id, table_type_fk_id, table_number_fk_id, table_name, table_number)
            VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("iiiss", $bookingId, $typeId, $tableNumId, $tableName, $tableNumber);
        $ins->execute();
    }

    echo json_encode(["status" => "success", "order_id" => $orderId, "booking_id" => $bookingId]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
