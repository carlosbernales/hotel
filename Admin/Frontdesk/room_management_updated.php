<?php
require_once 'includes/init.php';

// ... (rest of the PHP code remains the same) ...
?>
<!DOCTYPE html>
<!-- ... (rest of the HTML remains the same until the room type dropdown) ... -->
                                    <select class="form-control" id="room_type" name="room_type" onchange="handleRoomTypeChange(this)" required>
                                        <option value="">-- Select Room Type --</option>
                                        <?php
                                        $room_types = $con->query("SELECT room_type_id, room_type FROM room_types WHERE room_type_id IN (1, 2, 3) AND status = 'active' ORDER BY room_type");
                                        while ($type = $room_types->fetch_assoc()) {
                                            echo "<option value='" . $type['room_type_id'] . "'>" . htmlspecialchars($type['room_type']) . "</option>";
                                        }
                                        ?>
                                        <option value="other">Other (Specify Below)</option>
                                    </select>
                                    <div id="otherRoomTypeContainer" class="mt-2" style="display: none;">
                                        <label for="new_room_type">Specify Room Type *</label>
                                        <input type="text" class="form-control" id="new_room_type" name="new_room_type" placeholder="Enter custom room type">
                                    </div>
<!-- ... (rest of the HTML remains the same) ... -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to handle room type changes
        function handleRoomTypeChange(selectElement) {
            if (!selectElement) return;
            
            const form = selectElement.closest('form');
            if (!form) return;
            
            const otherContainer = form.querySelector('#otherRoomTypeContainer');
            const newRoomTypeInput = form.querySelector('#new_room_type');
            let roomTypeIdInput = form.querySelector('input[name="room_type_id"]');
            
            // Create hidden input for room_type_id if it doesn't exist
            if (!roomTypeIdInput) {
                roomTypeIdInput = document.createElement('input');
                roomTypeIdInput.type = 'hidden';
                roomTypeIdInput.name = 'room_type_id';
                selectElement.parentNode.insertBefore(roomTypeIdInput, selectElement.nextSibling);
            }
            
            if (selectElement.value === 'other') {
                if (otherContainer) otherContainer.style.display = 'block';
                if (newRoomTypeInput) newRoomTypeInput.required = true;
                roomTypeIdInput.value = 'other';
            } else {
                if (otherContainer) otherContainer.style.display = 'none';
                if (newRoomTypeInput) newRoomTypeInput.required = false;
                roomTypeIdInput.value = selectElement.value;
            }
        }
        
        // Set up event listeners for all room type selects
        document.querySelectorAll('select[name^="room_type"]').forEach(select => {
            // Set up initial state
            handleRoomTypeChange(select);
            // Add change event listener
            select.addEventListener('change', function() {
                handleRoomTypeChange(this);
            });
        });
        
        // For dynamically added modals
        $(document).on('shown.bs.modal', '.modal', function() {
            const modalSelect = this.querySelector('select[name^="room_type"]');
            if (modalSelect) {
                handleRoomTypeChange(modalSelect);
            }
        });
    });
    </script>
</body>
</html>
