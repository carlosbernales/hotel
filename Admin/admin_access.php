<?php
// Set a cookie that will bypass maintenance mode
setcookie('admin_access', 'true', time() + 86400, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access Granted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px 30px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4CAF50;
            margin-top: 0;
        }
        .success-icon {
            color: #4CAF50;
            font-size: 48px;
            margin-bottom: 20px;
            text-align: center;
        }
        .message {
            background-color: #e8f5e9;
            padding: 15px;
            border-left: 4px solid #4CAF50;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #388E3C;
        }
        .info {
            background-color: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Admin Access Granted</h1>
        
        <div class="message">
            <p><strong>Maintenance mode bypassed successfully!</strong></p>
            <p>You now have full access to your website even while it remains in maintenance mode for visitors.</p>
        </div>
        
        <p>A cookie has been set in your browser that will give you unrestricted access to all pages on your site for the next 24 hours.</p>
        
        <p><a href="/Admin/" class="button">Go to Admin Dashboard</a></p>
        
        <div class="info">
            <p><strong>Note:</strong> If you clear your browser cookies or use a different browser/device, you'll need to visit this page again.</p>
            <p>Your cookie will expire after 24 hours. Return to this page anytime to refresh your access.</p>
        </div>
    </div>
</body>
</html>