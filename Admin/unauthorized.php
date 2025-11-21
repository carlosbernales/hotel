<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .unauthorized-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
        }
        .unauthorized-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .unauthorized-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
        }
        .unauthorized-message {
            color: #666;
            margin-bottom: 2rem;
        }
        .btn-back {
            background-color: #d4af37;
            border: none;
            color: white;
            padding: 0.5rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #b6960a;
            color: white;
        }
    </style>
</head>
<body>
    <div class="unauthorized-container">
        <i class="fa fa-exclamation-triangle unauthorized-icon"></i>
        <h1 class="unauthorized-title">Unauthorized Access</h1>
        <p class="unauthorized-message">
            Sorry, you don't have permission to access this page. 
            Please contact the administrator if you believe this is an error.
        </p>
        <a href="login.php" class="btn btn-back">
            <i class="fa fa-arrow-left"></i> Back to Login
        </a>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html> 