<?php
include 'db.php';
include 'header.php';
include 'sidebar.php';
?>

<!-- Add SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Messages</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Customer Messages</div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['resolveError'])) {
                        echo "<div class='alert alert-danger'>
                                <span class='glyphicon glyphicon-info-sign'></span> &nbsp; Error on Resolve !
                            </div>";
                    }
                    if (isset($_GET['resolveSuccess'])) {
                        echo "<div class='alert alert-success'>
                                <span class='glyphicon glyphicon-info-sign'></span> &nbsp; Message Successfully Resolved !
                            </div>";
                    }
                    ?>
                    <table class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%" id="messages">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Admin Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $messages_query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
                            $messages_result = mysqli_query($con, $messages_query);
                            
                            if ($messages_result && mysqli_num_rows($messages_result) > 0) {
                                $num = 0;
                                while ($message = mysqli_fetch_assoc($messages_result)) {
                                    $num++;
                                    ?>
                                    <tr>
                                        <td><?php echo $num; ?></td>
                                        <td><?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                                        <td><?php echo htmlspecialchars($message['message']); ?></td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></td>
                                        <td>
                                            <?php
                                            if ($message['status'] === 'new') {
                                                echo '<span class="label label-warning">New</span>';
                                            } else if ($message['status'] === 'read') {
                                                echo '<span class="label label-info">Read</span>';
                                            } else if ($message['status'] === 'replied') {
                                                echo '<span class="label label-success">Replied</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($message['status'] !== 'replied'): ?>
                                                <button class="btn btn-primary btn-sm" 
                                                        data-toggle="modal" 
                                                        data-target="#responseModal" 
                                                        data-id="<?php echo $message['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?>"
                                                        data-email="<?php echo htmlspecialchars($message['email']); ?>"
                                                        data-message="<?php echo htmlspecialchars($message['message']); ?>">
                                                    <i class="fa fa-reply"></i> Respond
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($message['status'] === 'replied') {
                                                echo htmlspecialchars($message['admin_response'] ?? ''); 
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No messages found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Respond to Message</h4>
            </div>
            <form id="responseForm">
                <div class="modal-body">
                    <input type="hidden" id="message_id" name="message_id">
                    <input type="hidden" id="customer_email" name="customer_email">
                    
                    <div class="form-group">
                        <label>Customer Name:</label>
                        <input type="text" class="form-control" id="customer_name" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Customer Email:</label>
                        <input type="text" class="form-control" id="display_email" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Original Message:</label>
                        <textarea class="form-control" id="original_message" readonly rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Your Response:</label>
                        <textarea class="form-control" name="admin_response" id="admin_response" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#messages').DataTable({
        "order": [[4, "desc"]], // Sort by date column descending
        "pageLength": 10
    });

    // Handle modal data
    $('#responseModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var email = button.data('email');
        var message = button.data('message');
        
        var modal = $(this);
        modal.find('#message_id').val(id);
        modal.find('#customer_name').val(name);
        modal.find('#customer_email').val(email);
        modal.find('#display_email').val(email);
        modal.find('#original_message').val(message);
        modal.find('#admin_response').val('');
    });

    // Handle form submission
    $('#responseForm').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var customerEmail = $('#customer_email').val();
        
        // Disable submit button and show loading state
        submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
        
        $.ajax({
            url: 'process_response.php',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#responseModal').modal('hide');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Response Sent!',
                        html: 'Your response has been successfully sent to:<br><strong>' + customerEmail + '</strong>',
                        showConfirmButton: true,
                        timer: 3000
                    }).then(function() {
                        location.reload(); // Reload the page to update the table
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message || 'Failed to send response'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to send response. Please try again.'
                });
            },
            complete: function() {
                // Re-enable submit button and restore text
                submitButton.prop('disabled', false).html('Send Response');
            }
        });
    });
    
    // Show success message if URL has resolveSuccess parameter
    <?php if (isset($_GET['resolveSuccess'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Response Sent!',
        text: 'Your response has been successfully sent to the customer.',
        showConfirmButton: true,
        timer: 3000
    });
    <?php endif; ?>
});
</script>

<?php include 'footer.php'; ?>