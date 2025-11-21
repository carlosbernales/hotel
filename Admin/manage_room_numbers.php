<?php
require_once 'includes/init.php';

// Get all room types for dropdown
$room_types = [];
$result = $con->query("SELECT id, room_type FROM room_types WHERE is_deleted = 0 ORDER BY room_type");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $room_types[] = $row;
    }
}

// Get room numbers with their details
$room_numbers = [];
$result = $con->query("SELECT rn.*, rt.room_type 
                   FROM room_numbers rn 
                   JOIN room_types rt ON rn.room_type_id = rt.id 
                   ORDER BY rt.room_type, rn.room_number");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $room_numbers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room Numbers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .room-number-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .room-number-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .room-number-badge {
            font-size: 0.9rem;
            padding: 0.35em 0.65em;
        }
        .status-available { background-color: #198754; color: white; }
        .status-occupied { background-color: #dc3545; color: white; }
        .status-maintenance { background-color: #ffc107; color: #000; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Manage Room Numbers</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomNumberModal">
                        <i class="fas fa-plus"></i> Add Room Number
                    </button>
                </div>
            </div>
        </div>

        <!-- Room Numbers Grid -->
        <div class="row">
            <?php foreach ($room_numbers as $room): ?>
            <div class="col-md-4 mb-4">
                <div class="room-number-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0">Room #<?php echo htmlspecialchars($room['room_number']); ?></h4>
                        <span class="badge room-number-badge status-<?php echo strtolower($room['status']); ?>">
                            <?php echo ucfirst($room['status']); ?>
                        </span>
                    </div>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($room['room_type']); ?></p>
                    <?php if (!empty($room['floor'])): ?>
                    <p class="mb-2"><i class="fas fa-layer-group"></i> Floor: <?php echo htmlspecialchars($room['floor']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($room['description'])): ?>
                    <p class="mb-3"><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                    <?php endif; ?>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-primary me-2 edit-room-number" 
                                data-id="<?php echo $room['id']; ?>"
                                data-room-number="<?php echo htmlspecialchars($room['room_number']); ?>"
                                data-room-type="<?php echo $room['room_type_id']; ?>"
                                data-floor="<?php echo htmlspecialchars($room['floor']); ?>"
                                data-description="<?php echo htmlspecialchars($room['description']); ?>"
                                data-status="<?php echo $room['status']; ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-room-number" data-id="<?php echo $room['id']; ?>">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add/Edit Room Number Modal -->
    <div class="modal fade" id="roomNumberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Room Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="roomNumberForm">
                    <div class="modal-body">
                        <input type="hidden" id="roomNumberId" name="id" value="">
                        
                        <div class="mb-3">
                            <label for="roomNumber" class="form-label">Room Number *</label>
                            <input type="text" class="form-control" id="roomNumber" name="room_number" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="roomType" class="form-label">Room Type *</label>
                            <select class="form-select" id="roomType" name="room_type_id" required>
                                <option value="">Select Room Type</option>
                                <?php foreach ($room_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['room_type']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="floor" class="form-label">Floor</label>
                            <input type="text" class="form-control" id="floor" name="floor">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize Select2
        $('#roomType').select2({
            dropdownParent: $('#roomNumberModal')
        });

        // Show add modal
        $('[data-bs-target="#addRoomNumberModal"]').on('click', function() {
            $('#modalTitle').text('Add Room Number');
            $('#roomNumberForm')[0].reset();
            $('#roomNumberId').val('');
            $('#roomNumberModal').modal('show');
        });

        // Show edit modal
        $(document).on('click', '.edit-room-number', function() {
            const id = $(this).data('id');
            const roomNumber = $(this).data('room-number');
            const roomType = $(this).data('room-type');
            const floor = $(this).data('floor');
            const description = $(this).data('description');
            const status = $(this).data('status');

            $('#modalTitle').text('Edit Room Number');
            $('#roomNumberId').val(id);
            $('#roomNumber').val(roomNumber);
            $('#roomType').val(roomType).trigger('change');
            $('#floor').val(floor);
            $('#description').val(description);
            $('#status').val(status);
            
            $('#roomNumberModal').modal('show');
        });

        // Handle form submission
        $('#roomNumberForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            const isEdit = $('#roomNumberId').val() !== '';
            
            $.ajax({
                url: 'process_room_management.php',
                type: 'POST',
                data: formData + '&action=' + (isEdit ? 'edit_room_number' : 'add_room_number'),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#roomNumberModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'An error occurred. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Handle delete
        $(document).on('click', '.delete-room-number', function() {
            if (confirm('Are you sure you want to delete this room number? This action cannot be undone.')) {
                const id = $(this).data('id');
                
                $.ajax({
                    url: 'process_room_management.php',
                    type: 'POST',
                    data: {
                        action: 'delete_room_number',
                        room_id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message || 'An error occurred. Please try again.');
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
