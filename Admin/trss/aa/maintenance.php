<?php
// Ensure maintenance config is available
if (!isset($maintenanceConfig)) {
    die('Configuration not loaded');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Maintenance - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('assets/images/bg.jpg');
            background-size: cover;
            background-position: center;
        }
        .maintenance-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .maintenance-icon {
            font-size: 4rem;
            color: #d4af37;
            margin-bottom: 1rem;
        }
        .maintenance-title {
            color: #333;
            margin-bottom: 1rem;
        }
        .maintenance-text {
            color: #666;
            margin-bottom: 2rem;
        }
        .countdown {
            font-size: 1.2rem;
            color: #d4af37;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">🛠️</div>
        <h1 class="maintenance-title">We'll Be Right Back!</h1>
        <p class="maintenance-text">
            <?php echo htmlspecialchars($maintenanceConfig->getMessage()); ?>
        </p>
        <?php if ($maintenanceConfig->getEndTime()): ?>
        <div class="countdown">
            <p>Estimated completion time:</p>
            <p id="countdown"><?php echo htmlspecialchars($maintenanceConfig->getEndTime()); ?></p>
        </div>
        <script>
            // Add countdown timer if end time is set
            function updateCountdown() {
                const endTime = new Date('<?php echo $maintenanceConfig->getEndTime(); ?>').getTime();
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    document.getElementById('countdown').innerHTML = 'Maintenance period has ended. Please refresh the page.';
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('countdown').innerHTML = 
                    hours + "h " + minutes + "m " + seconds + "s ";
            }

            setInterval(updateCountdown, 1000);
            updateCountdown();
        </script>
        <?php endif; ?>
        <p class="text-muted">
            - Casa Estela Team
        </p>
    </div>
</body>
</html> 