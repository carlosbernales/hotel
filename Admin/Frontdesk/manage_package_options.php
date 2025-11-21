<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create tables if they don't exist
$create_tables = [
    "CREATE TABLE IF NOT EXISTS package_menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS package_max_guests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        capacity INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS package_durations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        hours INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS package_notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note_type ENUM('30PAX', '50PAX') NOT NULL,
        note_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($create_tables as $query) {
    $con->query($query);
}

// Fetch existing options
$menu_items = $con->query("SELECT * FROM package_menu_items ORDER BY item_name");
$max_guests = $con->query("SELECT * FROM package_max_guests ORDER BY capacity");
$durations = $con->query("SELECT * FROM package_durations ORDER BY hours");
$notes_30pax = $con->query("SELECT * FROM package_notes WHERE note_type = '30PAX' ORDER BY created_at DESC");
$notes_50pax = $con->query("SELECT * FROM package_notes WHERE note_type = '50PAX' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Package Options - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .options-section {
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #DAA520;
        }
        
        .option-card {
            border: 1px solid #DAA520;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .option-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .btn-add-option {
            background: #DAA520;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-add-option:hover {
            background: #b8860b;
            color: white;
            text-decoration: none;
        }
        
        .options-list {
            margin-top: 20px;
        }
        
        .option-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .option-item:last-child {
            border-bottom: none;
        }
        
        .option-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit-option, .btn-delete-option {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }
        
        .btn-edit-option {
            background: #28a745;
        }
        
        .btn-delete-option {
            background: #dc3545;
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
                <li><a href="event_management.php">Event Management</a></li>
                <li class="active">Package Options</li>
            </ol>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

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
                <div class="options-section">
                    <div class="section-header">
                        <h2>Package Options Management</h2>
                    </div>

                    <!-- Menu Items Section -->
                    <div class="option-card">
                        <div class="option-title d-flex justify-content-between align-items-center">
                            <span>Menu Items</span>
                            <button type="button" class="btn-add-option" data-toggle="modal" data-target="#addMenuItemModal">
                                <i class="fa fa-plus"></i> Add Menu Item
                            </button>
                        </div>
                        <div class="options-list">
                            <?php if ($menu_items->num_rows > 0): ?>
                                <?php while ($item = $menu_items->fetch_assoc()): ?>
                                    <div class="option-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                        </div>
                                        <div class="option-actions">
                                            <button class="btn-edit-option" onclick="editMenuItem(<?php echo $item['id']; ?>)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn-delete-option" onclick="deleteMenuItem(<?php echo $item['id']; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted">No menu items added yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Max Guests Section -->
                    <div class="option-card">
                        <div class="option-title d-flex justify-content-between align-items-center">
                            <span>Maximum Guests Options</span>
                            <button type="button" class="btn-add-option" data-toggle="modal" data-target="#addMaxGuestsModal">
                                <i class="fa fa-plus"></i> Add Max Guests Option
                            </button>
                        </div>
                        <div class="options-list">
                            <?php if ($max_guests->num_rows > 0): ?>
                                <?php while ($capacity = $max_guests->fetch_assoc()): ?>
                                    <div class="option-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($capacity['capacity']); ?> PAX</strong>
                                        </div>
                                        <div class="option-actions">
                                            <button class="btn-edit-option" onclick="editMaxGuests(<?php echo $capacity['id']; ?>)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn-delete-option" onclick="deleteMaxGuests(<?php echo $capacity['id']; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted">No maximum guests options added yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Duration Section -->
                    <div class="option-card">
                        <div class="option-title d-flex justify-content-between align-items-center">
                            <span>Duration Options</span>
                            <button type="button" class="btn-add-option" data-toggle="modal" data-target="#addDurationModal">
                                <i class="fa fa-plus"></i> Add Duration Option
                            </button>
                        </div>
                        <div class="options-list">
                            <?php if ($durations->num_rows > 0): ?>
                                <?php while ($duration = $durations->fetch_assoc()): ?>
                                    <div class="option-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($duration['hours']); ?> Hours</strong>
                                        </div>
                                        <div class="option-actions">
                                            <button class="btn-edit-option" onclick="editDuration(<?php echo $duration['id']; ?>)">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn-delete-option" onclick="deleteDuration(<?php echo $duration['id']; ?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted">No duration options added yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="option-card">
                        <div class="option-title d-flex justify-content-between align-items-center">
                            <span>Package Notes</span>
                            <button type="button" class="btn-add-option" data-toggle="modal" data-target="#addNoteModal">
                                <i class="fa fa-plus"></i> Add Note
                            </button>
                        </div>
                        
                        <!-- 30 PAX Notes -->
                        <div class="notes-section mb-4">
                            <h5 class="mb-3">30 PAX Notes</h5>
                            <div class="options-list">
                                <?php if ($notes_30pax->num_rows > 0): ?>
                                    <?php while ($note = $notes_30pax->fetch_assoc()): ?>
                                        <div class="option-item">
                                            <div>
                                                <strong><?php echo nl2br(htmlspecialchars($note['note_text'])); ?></strong>
                                            </div>
                                            <div class="option-actions">
                                                <button class="btn-edit-option" onclick="editNote(<?php echo $note['id']; ?>)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn-delete-option" onclick="deleteNote(<?php echo $note['id']; ?>)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-muted">No notes added for 30 PAX packages yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 50 PAX Notes -->
                        <div class="notes-section">
                            <h5 class="mb-3">50 PAX Notes</h5>
                            <div class="options-list">
                                <?php if ($notes_50pax->num_rows > 0): ?>
                                    <?php while ($note = $notes_50pax->fetch_assoc()): ?>
                                        <div class="option-item">
                                            <div>
                                                <strong><?php echo nl2br(htmlspecialchars($note['note_text'])); ?></strong>
                                            </div>
                                            <div class="option-actions">
                                                <button class="btn-edit-option" onclick="editNote(<?php echo $note['id']; ?>)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn-delete-option" onclick="deleteNote(<?php echo $note['id']; ?>)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-muted">No notes added for 50 PAX packages yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Menu Item Modal -->
    <div class="modal fade" id="addMenuItemModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Menu Item</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="add_menu_item.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Item Name</label>
                            <input type="text" class="form-control" name="item_name" placeholder="e.g., Chicken Cordon Bleu, Caesar Salad" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Max Guests Modal -->
    <div class="modal fade" id="addMaxGuestsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Maximum Guests Option</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="add_max_guests.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Capacity (PAX)</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Capacity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Duration Modal -->
    <div class="modal fade" id="addDurationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Duration Option</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="add_duration.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Hours</label>
                            <input type="number" class="form-control" name="hours" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Duration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Package Note</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="add_note.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Package Type</label>
                            <select class="form-control" name="note_type" required>
                                <option value="30PAX">30 PAX</option>
                                <option value="50PAX">50 PAX</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Note Text</label>
                            <textarea class="form-control" name="note_text" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    function editMenuItem(id) {
        // Implement edit functionality
        window.location.href = 'edit_menu_item.php?id=' + id;
    }

    function deleteMenuItem(id) {
        if (confirm('Are you sure you want to delete this menu item?')) {
            window.location.href = 'delete_menu_item.php?id=' + id;
        }
    }

    function editMaxGuests(id) {
        window.location.href = 'edit_max_guests.php?id=' + id;
    }

    function deleteMaxGuests(id) {
        if (confirm('Are you sure you want to delete this maximum guests option?')) {
            window.location.href = 'delete_max_guests.php?id=' + id;
        }
    }

    function editDuration(id) {
        window.location.href = 'edit_duration.php?id=' + id;
    }

    function deleteDuration(id) {
        if (confirm('Are you sure you want to delete this duration option?')) {
            window.location.href = 'delete_duration.php?id=' + id;
        }
    }

    function editNote(id) {
        window.location.href = 'edit_note.php?id=' + id;
    }

    function deleteNote(id) {
        if (confirm('Are you sure you want to delete this note?')) {
            window.location.href = 'delete_note.php?id=' + id;
        }
    }
    </script>
</body>
</html> 