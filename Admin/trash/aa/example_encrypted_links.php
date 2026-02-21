<?php
require_once 'includes/url_encrypt.php';

// Example of creating encrypted links
$profileLink = getEncryptedURL('profile', ['id' => 123, 'view' => 'settings']);
$bookingLink = getEncryptedURL('booking', ['room' => 'deluxe', 'checkin' => '2025-01-01', 'nights' => 3]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypted URL Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Encrypted URL Example</h1>
        <div class="card mt-4">
            <div class="card-header">
                <h2>Example Encrypted Links</h2>
            </div>
            <div class="card-body">
                <h3>Profile Link</h3>
                <p>Original: profile.php?id=123&view=settings</p>
                <p>Encrypted: <a href="/e/<?php echo $profileLink; ?>">/e/<?php echo $profileLink; ?></a></p>
                
                <h3 class="mt-4">Booking Link</h3>
                <p>Original: booking.php?room=deluxe&checkin=2025-01-01&nights=3</p>
                <p>Encrypted: <a href="/e/<?php echo $bookingLink; ?>">/e/<?php echo $bookingLink; ?></a></p>
                
                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
