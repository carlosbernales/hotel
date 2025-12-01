<?php
if (!isset($_GET['booking_id'])) {
    exit("No booking ID provided.");
}
$bookingId = intval($_GET['booking_id']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Receipt Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #fff;
            color: #000;
        }

        h1 {
            font-size: 24px;
        }
    </style>
</head>

<body>
    <h1>Booking ID: <?= $bookingId ?></h1>
</body>

</html>