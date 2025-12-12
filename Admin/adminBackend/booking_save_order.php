<?php
header("Content-Type: application/json");
include '../adminBackend/mydb.php';

try {

    // =============================
    // GET POST DATA
    // =============================
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

    // must match table count
    if (count($tableTypes) !== count($tables)) {
        echo json_encode(["status" => "error", "msg" => "Mismatch table count"]);
        exit;
    }

    // =============================
    // INSERT INTO orders_table
    // =============================
    $orderId = "ORD-" . time(); // simple unique order id

    $sql = "INSERT INTO orders_table 
            (order_id, firstname, lastname, contact, date_time, status)
            VALUES (?, ?, ?, ?, ?, 'Accepted')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $orderId, $first, $last, $contact, $datetime);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "msg" => "Insert main failed"]);
        exit;
    }

    $bookingId = $stmt->insert_id; // new booking ID


    // =============================
    // INSERT INTO orders_table_type
    // =============================
    for ($i = 0; $i < count($tableTypes); $i++) {

        $typeId = $tableTypes[$i];
        $tableNumId = $tables[$i];

        // ---- fetch table_name
        $q1 = $conn->prepare("SELECT table_name FROM table_types WHERE id = ?");
        $q1->bind_param("i", $typeId);
        $q1->execute();
        $r1 = $q1->get_result()->fetch_assoc();
        $tableName = $r1['table_name'];

        // ---- fetch table_number
        $q2 = $conn->prepare("SELECT table_number FROM table_number WHERE id = ?");
        $q2->bind_param("i", $tableNumId);
        $q2->execute();
        $r2 = $q2->get_result()->fetch_assoc();
        $tableNumber = $r2['table_number'];

        // insert into orders_table_type
        $ins = $conn->prepare("
            INSERT INTO orders_table_type
            (table_booking_fk_id, table_type_fk_id, table_number_fk_id, table_name, table_number)
            VALUES (?, ?, ?, ?, ?)
        ");
        $ins->bind_param("iiiss", $bookingId, $typeId, $tableNumId, $tableName, $tableNumber);
        $ins->execute();
    }

    echo json_encode([
        "status" => "success",
        "order_id" => $orderId,
        "booking_id" => $bookingId
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
