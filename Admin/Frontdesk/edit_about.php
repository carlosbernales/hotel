<?php
require_once 'manage_about.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($title) || empty($description)) {
        $error = "Both title and description are required.";
    } else {
        // Check if content exists
        $existing = getAboutContent();
        
        if ($existing) {
            // Update existing content
            if (updateAboutContent($title, $description)) {
                $success = "Content updated successfully!";
            } else {
                $error = "Error updating content.";
            }
        } else {
            // Insert new content
            if (insertAboutContent($title, $description)) {
                $success = "Content added successfully!";
            } else {
                $error = "Error adding content.";
            }
        }
    }
}

// Get current content
$content = getAboutContent();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Content</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container { margin-top: 50px; }
        .form-group label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Edit About Content</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" 
                       class="form-control" 
                       id="title" 
                       name="title" 
                       value="<?php echo htmlspecialchars($content['title'] ?? ''); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" 
                          id="description" 
                          name="description" 
                          rows="6" 
                          required><?php echo htmlspecialchars($content['description'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="about.php" class="btn btn-secondary">View About Page</a>
        </form>
    </div>
</body>
</html> 