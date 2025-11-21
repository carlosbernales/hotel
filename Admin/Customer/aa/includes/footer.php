<?php
// Get user ID from session if available
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<!-- Include Chat Component -->
<div id="chatContainer" data-user-id="<?php echo htmlspecialchars($userId); ?>">
    <?php include 'includes/chat.php'; ?>
</div>

<!-- Include Chat JavaScript -->
<script src="assets/js/chat.js"></script>

</body>
</html> 