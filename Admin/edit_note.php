<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: manage_package_options.php');
    exit();
}

$note_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_type = $_POST['note_type'];
    $note_text = $_POST['note_text'];

    if (empty($note_type) || empty($note_text)) {
        $_SESSION['error_message'] = "All fields are required.";
    } else {
        $query = "UPDATE package_notes SET note_type = ?, note_text = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssi", $note_type, $note_text, $note_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Note updated successfully!";
            header('Location: manage_package_options.php');
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating note: " . $con->error;
        }
    }
}

// Fetch existing note
$query = "SELECT * FROM package_notes WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $note_id);
$stmt->execute();
$result = $stmt->get_result();
$note = $result->fetch_assoc();

if (!$note) {
    $_SESSION['error_message'] = "Note not found.";
    header('Location: manage_package_options.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Note - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .form-section {
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #DAA520;
        }
        
        .btn-submit {
            background: #DAA520;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .btn-submit:hover {
            background: #b8860b;
            color: white;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
    
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li><a href="manage_package_options.php">Package Options</a></li>
                <li class="active">Edit Note</li>
            </ol>
        </div>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="form-section">
                    <div class="section-header">
                        <h2>Edit Package Note</h2>
                    </div>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Package Type</label>
                            <select class="form-control" name="note_type" required>
                                <option value="30PAX" <?php echo $note['note_type'] === '30PAX' ? 'selected' : ''; ?>>30 PAX</option>
                                <option value="50PAX" <?php echo $note['note_type'] === '50PAX' ? 'selected' : ''; ?>>50 PAX</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Note Text</label>
                            <textarea class="form-control" name="note_text" rows="4" required><?php echo htmlspecialchars($note['note_text']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <a href="manage_package_options.php" class="btn btn-cancel">Cancel</a>
                            <button type="submit" class="btn btn-submit">Update Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html> 