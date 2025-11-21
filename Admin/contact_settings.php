<?php
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Information Settings - Casa Estela</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .icon-preview {
            font-size: 20px;
            margin-right: 10px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        .status-active {
            background-color: #28a745;
            color: white;
        }
        .status-inactive {
            background-color: #dc3545;
            color: white;
        }
        .table {
            width: 90%;
            margin: 0 auto;
        }
        .table th {
            text-align: center;
        }
        .table td {
            text-align: center;
            vertical-align: middle;
        }
        main {
            padding: 20px;
        }
        .page-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .add-button {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .btn-warning {
            background-color: #DAA520;
            border-color: #DAA520;
            color: white;
        }
        .btn-warning:hover {
            background-color: #CD9B1D;
            border-color: #CD9B1D;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="page-title">
                    <h1>Contact Information Settings</h1>
                </div>
                <div class="text-center mb-4">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addContactModal">
                        + Add New Contact
                    </button>
                </div>

                <!-- Contact Info Table -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Display Text</th>
                                <th>Link</th>
                                <th>External</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM contact_info ORDER BY display_order ASC";
                            $result = mysqli_query($con, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusClass = $row['active'] ? 'status-active' : 'status-inactive';
                                $statusText = $row['active'] ? 'Active' : 'Inactive';
                                
                                echo "<tr>
                                    <td>{$row['display_text']}</td>
                                    <td>{$row['link']}</td>
                                    <td>" . ($row['is_external'] ? 'Yes' : 'No') . "</td>
                                    <td><span class='badge bg-" . ($row['active'] ? 'success' : 'danger') . "'>{$statusText}</span></td>
                                    <td>
                                        <button type='button' class='btn btn-warning btn-sm edit-contact' data-bs-toggle='modal' data-bs-target='#editContactModal' data-id='{$row['id']}'>
                                            <i class='fas fa-edit'></i>
                                        </button>
                                        <button class='btn btn-" . ($row['active'] ? 'danger' : 'success') . " btn-sm toggle-status' data-id='{$row['id']}'>
                                            " . ($row['active'] ? 'Disable' : 'Enable') . "
                                        </button>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Add Contact Modal -->
                <div class="modal" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addContactModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addContactModalLabel">Add New Contact Information</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addContactForm">
                                    <div class="mb-3">
                                        <label class="form-label">Display Text</label>
                                        <input type="text" class="form-control" name="display_text" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link</label>
                                        <input type="text" class="form-control" name="link">
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="is_external" value="1">
                                            <label class="form-check-label">Open in New Tab</label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="icon_class" value="fa fa-link">
                                    <input type="hidden" name="display_order" value="99">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-warning" id="saveContact">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Contact Modal -->
                <div class="modal" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editContactModalLabel">Edit Contact Information</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editContactForm">
                                    <input type="hidden" name="id">
                                    <div class="mb-3">
                                        <label class="form-label">Display Text</label>
                                        <input type="text" class="form-control" name="display_text" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link</label>
                                        <input type="text" class="form-control" name="link" required>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="is_external" value="1">
                                            <label class="form-check-label">Open in New Tab</label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="icon_class" value="fa fa-link">
                                    <input type="hidden" name="display_order" value="99">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-warning" id="updateContact">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize modals
            var addModal = new bootstrap.Modal(document.getElementById('addContactModal'));
            var editModal = new bootstrap.Modal(document.getElementById('editContactModal'));
            
            // Add new contact button click
            $('.btn-warning[data-bs-target="#addContactModal"]').click(function(e) {
                e.preventDefault();
                addModal.show();
            });

            // Add new contact save
            $('#saveContact').click(function() {
                $.ajax({
                    url: 'ajax/contact_settings.php',
                    method: 'POST',
                    data: $('#addContactForm').serialize() + '&action=add',
                    success: function(response) {
                        if (response.success) {
                            addModal.hide();
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error: ' + error);
                    }
                });
            });

            // Edit contact button click
            $('.edit-contact').click(function() {
                const id = $(this).data('id');
                
                // Clear previous form data
                $('#editContactForm')[0].reset();
                
                $.ajax({
                    url: 'ajax/contact_settings.php',
                    method: 'POST',
                    data: { 
                        action: 'get', 
                        id: id 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#editContactForm [name="id"]').val(data.id);
                            $('#editContactForm [name="display_text"]').val(data.display_text);
                            $('#editContactForm [name="link"]').val(data.link);
                            $('#editContactForm [name="is_external"]').prop('checked', data.is_external == 1);
                            $('#editContactForm [name="icon_class"]').val(data.icon_class);
                            $('#editContactForm [name="display_order"]').val(data.display_order);
                            
                            // Show the modal
                            $('#editContactModal').modal('show');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error loading contact information: ' + error);
                    }
                });
            });

            // Update contact
            $('#updateContact').click(function() {
                const formData = $('#editContactForm').serialize();
                $.ajax({
                    url: 'ajax/contact_settings.php',
                    method: 'POST',
                    data: formData + '&action=update',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editContactModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating contact: ' + error);
                    }
                });
            });

            // Toggle status
            $('.toggle-status').click(function() {
                const id = $(this).data('id');
                const isCurrentlyActive = $(this).hasClass('btn-danger');
                const confirmMessage = isCurrentlyActive ? 
                    'Are you sure you want to disable this contact?' : 
                    'Are you sure you want to enable this contact?';
                
                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: 'ajax/contact_settings.php',
                        method: 'POST',
                        data: { 
                            action: 'toggle', 
                            id: id 
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error toggling status: ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html> 