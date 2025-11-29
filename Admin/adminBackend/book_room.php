<?php
include '../adminBackend/mydb.php';

function generateBookingReference($length = 12)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return 'BK-' . $randomString;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $number_of_guests = intval($_POST['number_of_guests'] ?? 0);
    $num_adults = intval($_POST['num_adults'] ?? 0);
    $num_children = intval($_POST['num_children'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? '';
    $payment_option = $_POST['payment_option'] ?? 'full';
    $room_quantity = intval($_POST['room_quantity'] ?? 0);
    $total_amount = floatval(str_replace(['₱', ',', ' '], '', $_POST['total_amount'] ?? 0));
    $total_discount_percent = floatval(str_replace('%', '', $_POST['total_discount_percent'] ?? 0));
    $total_discount_amount = floatval(str_replace(['₱', ','], '', $_POST['total_discount_amount'] ?? 0));
    $status = 'uncounted';
    $user_id = null;
    $booking_type = 'walkin';
    $room_type_id = null;
    $user_types = 'admin';
    $arrival_time = null;
    $extra_beds = $_POST['extra_beds'] ?? [];

    $discounts = [];
    for ($i = 1; $i <= $num_adults + $num_children; $i++) {
        $guestDiscount = $_POST["guest_discount_$i"] ?? '';
        if ($guestDiscount && !in_array($guestDiscount, $discounts)) {
            $discounts[] = $guestDiscount;
        }
    }
    $discount_type = implode(' & ', $discounts) ?: 'none';

    $nights = (new DateTime($check_out))->diff(new DateTime($check_in))->days ?: 1;
    $downpayment_amount = 0.0;
    $remaining_balance = 0.0;

    $booking_reference = generateBookingReference();

    $stmt = $conn->prepare("
        INSERT INTO bookings (
            booking_reference, user_id, first_name, last_name, email, contact,
            booking_type, check_in, check_out, arrival_time, number_of_guests, room_type_id,
            room_quantity, payment_option, payment_method, total_amount, nights,
            downpayment_amount, remaining_balance, user_types, num_adults, num_children,
            discount_type, discount_percentage, discount_amount, status
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => $conn->error]));
    }

    $stmt->bind_param(
        "sisssssssiiiiisssddssiiiss",
        $booking_reference,
        $user_id,
        $first_name,
        $last_name,
        $email,
        $contact,
        $booking_type,
        $check_in,
        $check_out,
        $arrival_time,
        $number_of_guests,
        $room_type_id,
        $room_quantity,
        $payment_option,
        $payment_method,
        $total_amount,
        $nights,
        $downpayment_amount,
        $remaining_balance,
        $user_types,
        $num_adults,
        $num_children,
        $discount_type,
        $total_discount_percent,
        $total_discount_amount,
        $status
    );

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;

        $cartItems = json_decode($_POST['cart_items'], true);
        if ($cartItems && count($cartItems) > 0) {
            foreach ($cartItems as $item) {
                $room_type_id_item = intval($item['room_type_id']);
                $quantity = intval($item['quantity'] ?? 1);

                $roomQuery = $conn->query("SELECT room_type, price FROM room_types WHERE room_type_id = $room_type_id_item");
                if ($roomQuery && $roomQuery->num_rows > 0) {
                    $roomData = $roomQuery->fetch_assoc();
                    $room_type_name = $roomData['room_type'];
                    $price = floatval($roomData['price']);

                    $stmtRoom = $conn->prepare("
                        INSERT INTO booked_rooms (booking_id, room_type_id, room_type_name, price)
                        VALUES (?, ?, ?, ?)
                    ");

                    for ($q = 0; $q < $quantity; $q++) {
                        $stmtRoom->bind_param("iisd", $booking_id, $room_type_id_item, $room_type_name, $price);
                        $stmtRoom->execute();
                    }
                    $stmtRoom->close();
                }
            }
        }

        $totalGuests = $num_adults + $num_children;
        $stmtGuest = $conn->prepare("
            INSERT INTO guest_names (booking_id, first_name, last_name, guest_type)
            VALUES (?, ?, ?, ?)
        ");
        if ($stmtGuest) {
            for ($i = 1; $i <= $totalGuests; $i++) {
                $guest_firstname = $_POST["guest_firstname_$i"] ?? '';
                $guest_lastname = $_POST["guest_lastname_$i"] ?? '';
                $guest_type = $_POST["guest_type_$i"] ?? '';
                $stmtGuest->bind_param("isss", $booking_id, $guest_firstname, $guest_lastname, $guest_type);
                $stmtGuest->execute();
            }
            $stmtGuest->close();
        }

        if (!empty($extra_beds) && is_array($extra_beds)) {
            $bedCounts = array_count_values($extra_beds);
            foreach ($bedCounts as $bedId => $qty) {
                $bedId = intval($bedId);
                $bedQuery = $conn->query("SELECT id, item_type, price FROM beds WHERE id = $bedId");
                if ($bedQuery && $bedQuery->num_rows > 0) {
                    $bedData = $bedQuery->fetch_assoc();
                    $amenity_name = $bedData['item_type'];
                    $price = floatval($bedData['price']);
                    $amenities_fk_id = intval($bedData['id']);

                    $stmtAmenity = $conn->prepare("
                        INSERT INTO booking_amenities (booking_fk_id, amenities_fk_id, amenity_name, price, quantity, bedOrNot)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $bedOrNot = 'yes';
                    $stmtAmenity->bind_param("iisdis", $booking_id, $amenities_fk_id, $amenity_name, $price, $qty, $bedOrNot);
                    $stmtAmenity->execute();
                    $stmtAmenity->close();

                }
            }
        }

        header("Location: ../../Admin/index.php?book_room_details&id=" . $booking_id);
        exit();

    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
