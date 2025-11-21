<?php
// This script will handle the update of the room type selector in room_management.php
$file_path = 'c:/xampp/htdocs/Admin/room_management.php';
$content = file_get_contents($file_path);

// Define the new room type selector HTML
$new_selector = '                                <!-- Enhanced Room Type Selector -->
                                <div class="form-group">
                                    <label for="room_type">Room Type *</label>
                                    <select class="form-control" id="room_type" name="room_type" required>
                                        <option value="">-- Select Room Type --</option>
                                        <?php
                                        $room_types = $con->query("SELECT room_type_id, room_type FROM room_types WHERE status = \'active\' ORDER BY room_type");
                                        while ($type = $room_types->fetch_assoc()) {
                                            echo "<option value=\'' . $type[\'room_type_id\'] . '\'>" . htmlspecialchars($type[\'room_type\']) . "</option>";
                                        }
                                        ?>
                                        <option value="new" class="font-weight-bold text-primary">➕ Add New Room Type</option>
                                    </select>
                                    
                                    <!-- Hidden input that will be submitted as room_type_id -->
                                    <input type="hidden" id="room_type_id" name="room_type_id" value="">
                                    
                                    <!-- New Room Type Container -->
                                    <div id="newRoomTypeContainer" class="mt-3 p-3 border rounded" style="display: none; background-color: #f8f9fa;">
                                        <h6 class="font-weight-bold mb-3">Add New Room Type</h6>
                                        <div class="form-group">
                                            <label for="new_room_type">Room Type Name *</label>
                                            <input type="text" class="form-control" id="new_room_type" name="new_room_type" placeholder="E.g., Deluxe Suite, Family Room, etc.">
                                        </div>
                                        <div class="form-group">
                                            <label for="new_room_price">Price per Night *</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₱</span>
                                                </div>
                                                <input type="number" class="form-control" id="new_room_price" name="new_room_price" min="0" step="0.01" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_room_capacity">Capacity *</label>
                                            <select class="form-control" id="new_room_capacity" name="new_room_capacity">
                                                <option value="1">1 Person</option>
                                                <option value="2" selected>2 People</option>
                                                <option value="3">3 People</option>
                                                <option value="4">4 People</option>
                                                <option value="5">5+ People</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>';

// Define the new JavaScript for the room type selector
$new_js = '                                    <script>
                                    document.addEventListener(\'DOMContentLoaded\', function() {
                                        // Get all necessary elements
                                        const roomTypeSelect = document.getElementById(\'room_type\');
                                        const roomTypeIdInput = document.getElementById(\'room_type_id\');
                                        const newRoomTypeContainer = document.getElementById(\'newRoomTypeContainer\');
                                        const newRoomTypeInput = document.getElementById(\'new_room_type\');
                                        const newRoomPriceInput = document.getElementById(\'new_room_price\');
                                        const newRoomCapacitySelect = document.getElementById(\'new_room_capacity\');
                                        const roomForm = document.getElementById(\'saveRoomForm\');

                                        // Function to update the UI based on room type selection
                                        function updateRoomTypeUI() {
                                            if (roomTypeSelect.value === \'new\') {
                                                newRoomTypeContainer.style.display = \'block\';
                                                roomTypeIdInput.value = \'new\'; // Indicates a new room type should be created
                                                // Clear previous values
                                                if (newRoomTypeInput) newRoomTypeInput.value = \'\';
                                                if (newRoomPriceInput) newRoomPriceInput.value = \'\';
                                                if (newRoomCapacitySelect) newRoomCapacitySelect.value = \'2\';
                                                // Focus on the new room type input
                                                setTimeout(() => newRoomTypeInput.focus(), 100);
                                            } else {
                                                if (newRoomTypeContainer) newRoomTypeContainer.style.display = \'none\';
                                                if (roomTypeIdInput) roomTypeIdInput.value = roomTypeSelect.value;
                                            }
                                        }


                                        // Add event listeners
                                        if (roomTypeSelect) {
                                            roomTypeSelect.addEventListener(\'change\', updateRoomTypeUI);
                                            // Initialize the UI
                                            updateRoomTypeUI();
                                        }


                                        // Form validation
                                        if (roomForm) {
                                            roomForm.addEventListener(\'submit\', function(e) {
                                                if (roomTypeSelect.value === \'new\') {
                                                    // Validate new room type fields
                                                    if (!newRoomTypeInput || !newRoomTypeInput.value.trim()) {
                                                        e.preventDefault();
                                                        alert(\'Please enter a name for the new room type\');
                                                        if (newRoomTypeInput) newRoomTypeInput.focus();
                                                        return false;
                                                    }
                                                    
                                                    if (!newRoomPriceInput || !newRoomPriceInput.value || parseFloat(newRoomPriceInput.value) <= 0) {
                                                        e.preventDefault();
                                                        alert(\'Please enter a valid price for the new room type\');
                                                        if (newRoomPriceInput) newRoomPriceInput.focus();
                                                        return false;
                                                    }
                                                }
                                                return true;
                                            });
                                        }
                                    });
                                    </script>';

// Replace the old room type selector with the new one
$content = preg_replace(
    '/<div class="form-group">[\s\S]*?<label for="room_type">Room Type \*<\/label>[\s\S]*?<\/div>/',
    $new_selector,
    $content
);

// Replace the old JavaScript with the new one
$content = preg_replace(
    '/<script>\s*\/\/ Get the room type select and other elements[\s\S]*?<\/script>/',
    $new_js,
    $content
);

// Save the updated content back to the file
file_put_contents($file_path, $content);

echo "Room type selector has been updated successfully!";
?>
