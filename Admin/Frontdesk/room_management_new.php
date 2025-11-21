<?php
require_once 'includes/init.php';

// ... (rest of your existing PHP code) ...
?>

<!-- ... (rest of your HTML) ... -->

<select class="form-control" id="room_type" name="room_type_id" required onchange="toggleOtherRoomType()">
    <option value="">-- Select Room Type --</option>
    <?php
    $room_types = $con->query("SELECT room_type_id, room_type FROM room_types WHERE room_type_id IN (1, 2, 3) AND status = 'active' ORDER BY room_type");
    while ($type = $room_types->fetch_assoc()) {
        echo "<option value='" . $type['room_type_id'] . "'>" . htmlspecialchars($type['room_type']) . "</option>";
    }
    ?>
    <option value="other">Other (Specify below)</option>
</select>
<div id="otherRoomTypeContainer" style="display: none; margin-top: 10px;">
    <label for="new_room_type">New Room Type Name *</label>
    <input type="text" class="form-control" id="new_room_type" name="new_room_type" placeholder="Enter new room type name">
</div>

<!-- ... (rest of your HTML) ... -->

<script>
function toggleOtherRoomType() {
    const roomTypeSelect = document.getElementById('room_type');
    const otherContainer = document.getElementById('otherRoomTypeContainer');
    const newRoomTypeInput = document.getElementById('new_room_type');
    
    if (roomTypeSelect.value === 'other') {
        otherContainer.style.display = 'block';
        newRoomTypeInput.required = true;
    } else {
        otherContainer.style.display = 'none';
        newRoomTypeInput.required = false;
    }
}

// Initialize the other room type container when the page loads
document.addEventListener('DOMContentLoaded', function() {
    toggleOtherRoomType();
    // ... rest of your existing JavaScript ...
});
</script>
