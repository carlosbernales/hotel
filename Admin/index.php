<?php
require_once "db.php";

// Check if user is logged in before any output
if (!isset($_SESSION['user_id'])) {
    header('Location:../Admin/Customer/aa');
    exit();
}

// Handle event booking redirect before any output
if (isset($_GET['event_booking'])) {
    header('Location: event_booking.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$userQuery = "SELECT * FROM userss WHERE id = '$user_id'";
$result = mysqli_query($con, $userQuery);
$user = mysqli_fetch_assoc($result);

// Include header and sidebar after all possible redirects

?>
<?php
// Default page is dashboard
$page = 'dashboard.php';

// Handle room booking first
if (isset($_GET['room_id'])) {
    $page = "booking_form.php";
}
// Then handle other pages
elseif (isset($_GET['room_mang'])) {
    $page = "room_mang.php";
} elseif (isset($_GET['dashboard'])) {
    $page = "dashboard.php";
} elseif (isset($_GET['UserInfo'])) {
    $page = "UserInfo.php";
} elseif (isset($_GET['Order'])) {
    $page = "Order.php";
} elseif (isset($_GET['BookingList'])) {
    $page = "BookingList.php";
} elseif (isset($_GET['roomcards'])) {
    $page = "room_cards.php";
} elseif (isset($_GET['table_booking'])) {
    $page = "table_booking.php";
} elseif (isset($_GET['table_packages'])) {
    $page = "table_packages.php";
} elseif (isset($_GET['booking_settings'])) {
    $page = "booking_settings.php";
} elseif (isset($_GET['booking_status'])) {
    $page = "booking_status.php";
} elseif (isset($_GET['staff_mang'])) {
    $page = "staff_mang.php";
} elseif (isset($_GET['add_emp'])) {
    $page = "add_emp.php";
} elseif (isset($_GET['complain'])) {
    $page = "Feedback.php";
} elseif (isset($_GET['statistics']) || isset($_GET['sales_report'])) {
    $page = "sales_report.php";
} elseif (isset($_GET['payment_settings'])) {
    $page = "payment_settings.php";
} elseif (isset($_GET['Paymentss'])) {
    $page = "Paymentss.php";
} elseif (isset($_GET['room_types'])) {
    $page = "room_types.php";
} elseif (isset($_GET['emp_history'])) {
    $page = "emp_history.php";
} elseif (isset($_GET['room_settings'])) {
    $page = "room_settings.php";
} elseif (isset($_GET['table_settings'])) {
    $page = "table_settings.php";
} elseif (isset($_GET['event_settings'])) {
    $page = "event_settings.php";


} elseif (isset($_GET['room_management'])) {
    $page = "adminFrontend/room_management.php";

} elseif (isset($_GET['table_management'])) {
    $page = "adminFrontend/table_management.php";

} elseif (isset($_GET['cafe_management'])) {
    $page = "adminFrontend/cafe_management.php";

} elseif (isset($_GET['event_management'])) {
    $page = "adminFrontend/event_management.php";

} elseif (isset($_GET['room_booking'])) {
    $page = "adminFrontend/room_booking.php";

    ///ROOM BOOKINGS
} elseif (isset($_GET['pending_room_bookings_list'])) {
    $page = "adminFrontend/pending_room_bookings_list.php";

} elseif (isset($_GET['accepted_room_bookings_list'])) {
    $page = "adminFrontend/accepted_room_bookings_list.php";

} elseif (isset($_GET['room_booking_list'])) {
    $page = "adminFrontend/room_booking_list.php";

} elseif (isset($_GET['finished_room_bookings_list'])) {
    $page = "adminFrontend/finished_room_bookings_list.php";
    //////////////////

} elseif (isset($_GET['book_room_details'])) {
    $page = "adminFrontend/book_room_details.php";

} elseif (isset($_GET['accepted_room_bookDetails'])) {
    $page = "adminFrontend/accepted_room_bookDetails.php";

} elseif (isset($_GET['details-pend'])) {
    $page = "adminFrontend/pending_room_bookDetails.php";



} elseif (isset($_GET['checkInDetails_room_booking'])) {
    $page = "adminFrontend/checkInDetails_room_booking.php";

} elseif (isset($_GET['room_booking_receipt'])) {
    $page = "adminFrontend/room_booking_receipt.php";

} elseif (isset($_GET['room_booking_receipt_accp'])) {
    $page = "adminFrontend/room_booking_receipt_accepted.php";


} elseif (isset($_GET['amenity_list'])) {
    $page = "adminFrontend/amenity_list.php";

} elseif (isset($_GET['table-booking'])) {
    $page = "adminFrontend/table_booking.php";

} elseif (isset($_GET['advance-orders'])) {
    $page = "adminFrontend/advance_orders.php";

} elseif (isset($_GET['table-booking-acptd'])) {
    $page = "adminFrontend/table_booking_accepted.php";

} elseif (isset($_GET['add-order-existing-order'])) {
    $page = "adminFrontend/table_order_addOrder.php";

} elseif (isset($_GET['event-booking'])) {
    $page = "adminFrontend/event_booking.php";

} elseif (isset($_GET['event-acp-list'])) {
    $page = "adminFrontend/event_accepted_booking.php";

} elseif (isset($_GET['event-ongoing'])) {
    $page = "adminFrontend/event_ongoing_booking.php";

} elseif (isset($_GET['event-complete-list'])) {
    $page = "adminFrontend/event_completed_booking.php";

} elseif (isset($_GET['admin-dashboard'])) {
    $page = "adminFrontend/dashboard.php";

} elseif (isset($_GET['facilities'])) {
    $page = "adminFrontend/facilities_management.php";

} elseif (isset($_GET['contact_management'])) {
    $page = "adminFrontend/contact_management.php";

} elseif (isset($_GET['messages'])) {
    $page = "message.php";

} elseif (isset($_GET['rejected-room-bookings'])) {
    $page = "adminFrontend/rejected_room_bookings_list.php";

} elseif (isset($_GET['sales-report'])) {
    $page = "adminFrontend/sales_report.php";

} elseif (isset($_GET['generate-report-event'])) {
    $page = "adminFrontend/sales_report_event.php";

} elseif (isset($_GET['generate-report-room'])) {
    $page = "adminFrontend/sales_report_room.php";

} elseif (isset($_GET['generate-report-table'])) {
    $page = "adminFrontend/sales_report_table.php";

} elseif (isset($_GET['users'])) {
    $page = "adminFrontend/users.php";

} elseif (isset($_GET['feedbacks'])) {
    $page = "adminFrontend/feedback.php";



    ///THIS IS TEST
} elseif (isset($_GET['test'])) {
    $page = "adminFrontend/test.php";
    ///THIS IS TEST


} elseif (isset($_GET['advance_checkin'])) {
    include 'advance_checkin_page.php';
}

// Include the selected page
include_once $page;
