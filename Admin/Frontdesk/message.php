<?php
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch messages with sender and receiver information
$sql = "SELECT m.*, 
        sender.name as sender_name,
        CASE 
            WHEN m.receiver_id = 0 THEN 'General'
            WHEN m.receiver_id = -1 THEN 'All Customers'
            ELSE receiver.name 
        END as receiver_name
        FROM messages m 
        LEFT JOIN users sender ON m.sender_id = sender.id
        LEFT JOIN users receiver ON m.receiver_id = receiver.id
        ORDER BY m.created_at DESC";

try {
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messages - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .message-table {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .btn-send-message {
            margin-bottom: 20px;
            padding: 12px 24px;
            font-size: 16px;
            background-color: #28a745;
            border-color: #28a745;
            font-weight: bold;
        }
        .btn-send-message:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-send-final {
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            background-color: #28a745;
            border-color: #28a745;
            transition: all 0.3s ease;
        }
        .btn-send-final:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .unread {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .modal-footer {
            justify-content: space-between;
            padding: 15px 20px;
        }
        .modal-content {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .modal-header {
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
            padding: 15px 20px;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
            padding: 15px 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            height: auto;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        textarea.form-control {
            min-height: 120px;
        }
        .modal-dialog {
            max-width: 600px;
        }
        label {
            font-weight: 600;
            color: #495057;
        }
        label i {
            margin-right: 5px;
            color: #28a745;
        }
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
        .modal-content {
            position: relative;
            background-color: #fff;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 6px;
            box-shadow: 0 3px 9px rgba(0,0,0,.5);
        }
        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            padding: 15px;
            text-align: right;
            border-top: 1px solid #e5e5e5;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            padding: 10px 16px;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .modal-dialog {
            margin: 30px auto;
        }
        .modal-content {
            border-radius: 6px;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }
        .modal-body {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            height: auto;
            padding: 8px 12px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: bold;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .close {
            font-size: 1.5rem;
            opacity: .5;
        }
        textarea.form-control {
            min-height: 100px;
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
                <li class="active">Messages</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="panel-title">Messages</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-send-message" data-toggle="modal" data-target="#sendMessageModal">
                                    <i class="fa fa-paper-plane"></i> Send New Message
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover message-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr class="<?php echo (!$row['is_read']) ? 'unread' : ''; ?>">
                                                <td><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($row['sender_name'] ?? 'Unknown User'); ?></td>
                                                <td><?php echo htmlspecialchars($row['receiver_name'] ?? 'Unknown User'); ?></td>
                                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($row['message'] ?? '', 0, 100)) . (strlen($row['message'] ?? '') > 100 ? '...' : ''); ?></td>
                                                <td class="action-buttons">
                                                    <?php if (!$row['is_read']): ?>
                                                        <button class="btn btn-xs btn-primary mark-as-read" data-message-id="<?php echo $row['id']; ?>">
                                                            <i class="fa fa-check"></i> Mark Read
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-xs btn-danger delete-message" data-message-id="<?php echo $row['id']; ?>">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No messages found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sendMessageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send New Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="sendMessageForm">
                        <!-- Quick Templates -->
                        <div class="form-group">
                            <label>Quick Templates:</label>
                            <select class="form-control" id="message_template" name="message_template">
                                <option value="">Select a template or compose custom message</option>
                                <option value="maintenance">Maintenance Announcement</option>
                                <option value="event">Event Announcement</option>
                                <option value="reminder">Payment Reminder</option>
                                <option value="welcome">Welcome Message</option>
                                <option value="checkout">Check-out Reminder</option>
                                <option value="emergency">Emergency Notice</option>
                            </select>
                        </div>

                        <!-- Recipients -->
                        <div class="form-group">
                            <label>Send To:</label>
                            <select class="form-control" id="recipient_type" name="recipient_type" required>
                                <option value="">Select Recipient</option>
                                <option value="general">General</option>
                                <option value="frontdesk">Front Desk</option>
                                <option value="cashier">Cashier</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>

                        <!-- Customer Selection (Hidden by default) -->
                        <div class="form-group" id="customerSelectGroup" style="display: none;">
                            <label>Search Customer:</label>
                            <input type="text" class="form-control" id="customerSearch" placeholder="Search by name or email...">
                            
                            <label style="margin-top: 10px;">Select Customer:</label>
                            <select class="form-control" id="customer_id" name="customer_id" size="5" style="margin-top: 5px;">
                                <option value="">Select a Customer</option>
                                <option value="all">All Customers</option>
                                <?php
                                // Fetch all users from the database
                                $customerQuery = "SELECT id, name, email FROM users";
                                $customerResult = $con->query($customerQuery);
                                if ($customerResult && $customerResult->num_rows > 0) {
                                    while ($customer = $customerResult->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($customer['id']) . '" data-search="' . 
                                             htmlspecialchars(strtolower($customer['name'] . ' ' . $customer['email'])) . '">' . 
                                             htmlspecialchars($customer['name']) . ' (' . htmlspecialchars($customer['email']) . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Send Button -->
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-success" style="width: 200px; margin: 10px 0;">
                                <i class="fa fa-paper-plane"></i> Send Message
                            </button>
                        </div>

                        <!-- Subject -->
                        <div class="form-group">
                            <label>Subject:</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <!-- Message -->
                        <div class="form-group">
                            <label>Message:</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mark message as read
            $('.mark-as-read').click(function() {
                const messageId = $(this).data('message-id');
                const button = $(this);
                
                $.ajax({
                    url: 'mark_message_read.php',
                    type: 'POST',
                    data: { message_id: messageId },
                    success: function(response) {
                        button.fadeOut();
                    },
                    error: function() {
                        alert('Error marking message as read');
                    }
                });
            });

            // Delete message
            $('.delete-message').click(function() {
                if (confirm('Are you sure you want to delete this message?')) {
                    const messageId = $(this).data('message-id');
                    const messageCard = $(this).closest('.message-card');
                    
    $.ajax({
                        url: 'delete_message.php',
                        type: 'POST',
        data: { message_id: messageId },
        success: function(response) {
                            messageCard.fadeOut();
        },
        error: function() {
                            alert('Error deleting message');
        }
    });
}
            });

            // Show/hide customer selection based on recipient type
            $('#recipient_type').change(function() {
                if ($(this).val() === 'customer') {
                    $('#customerSelectGroup').show();
                    $('#customer_id').prop('required', true);
                } else {
                    $('#customerSelectGroup').hide();
                    $('#customer_id').prop('required', false);
                }
            });

            // Handle message template selection
            $('#message_template').change(function() {
                const templateType = $(this).val();
                const recipientType = $('#recipient_type').val();
                const templates = {
                    maintenance: {
                        subject: "Scheduled Maintenance Notice",
                        messages: {
                            general: "Dear valued guests,\n\nThis is to inform you that we will be conducting scheduled maintenance work on [DATE] from [TIME]. During this period, there might be temporary interruptions to some services.\n\nWe apologize for any inconvenience this may cause.\n\nBest regards,\nManagement",
                            frontdesk: "Dear Front Desk Staff,\n\nPlease be informed that maintenance work is scheduled for [DATE] from [TIME]. Please notify any guests who may be affected and coordinate with the maintenance team.\n\nKindly manage guest concerns during this period.\n\nThank you for your cooperation.",
                            cashier: "Dear Cashier,\n\nThis is to inform you that maintenance work is scheduled for [DATE] from [TIME]. Please note that this may affect payment system operations during this period.\n\nPlease plan accordingly.\n\nBest regards,\nManagement",
                            customer: "Dear valued guest,\n\nWe wish to inform you that maintenance work is scheduled in your area on [DATE] from [TIME]. We will ensure minimal disruption to your stay.\n\nWe appreciate your understanding."
                        }
                    },
                    event: {
                        subject: "Upcoming Event Announcement",
                        messages: {
                            general: "Dear all,\n\nWe are excited to announce an upcoming event at Casa Estela! Join us for [EVENT NAME] on [DATE] at [TIME].\n\nWe look forward to your participation!\n\nBest regards,\nCasa Estela Management",
                            frontdesk: "Dear Front Desk Staff,\n\nPlease be informed of our upcoming event: [EVENT NAME] on [DATE] at [TIME]. Please assist in guest inquiries and coordination.\n\nKindly prepare the necessary arrangements for check-ins and guest assistance during the event.",
                            cashier: "Dear Cashier,\n\nWe have an upcoming event: [EVENT NAME] on [DATE] at [TIME]. Please prepare for potential increased transaction volume.\n\nKindly ensure all payment systems are ready.",
                            customer: "Dear valued guest,\n\nWe are delighted to invite you to our special event: [EVENT NAME] on [DATE] at [TIME].\n\nWe would be honored to have you join us for this occasion."
                        }
                    },
                    reminder: {
                        subject: "Payment Reminder",
                        messages: {
                            general: "Important payment reminder for all departments.",
                            frontdesk: "Dear Front Desk Staff,\n\nPlease remind checking-out guests about pending payments for [DETAILS].\n\nEnsure all accounts are settled before check-out.",
                            cashier: "Dear Cashier,\n\nKindly process pending payments for [DETAILS].\n\nPlease update the system accordingly once processed.",
                            customer: "Dear valued guest,\n\nThis is a friendly reminder about your upcoming payment due on [DATE].\n\nPlease settle this at your earliest convenience to avoid any service interruptions.\n\nThank you for choosing Casa Estela."
                        }
                    },
                    welcome: {
                        subject: "Welcome to Casa Estela",
                        messages: {
                            general: "Welcome announcement for all staff.",
                            frontdesk: "Dear Front Desk Staff,\n\nPlease prepare for the arrival of [GUEST NAME]. Ensure all welcome amenities are ready.\n\nKindly provide our standard welcome briefing upon check-in.",
                            cashier: "Dear Cashier,\n\nNew guest check-in alert. Please prepare for initial payment processing.\n\nEnsure all systems are ready for new guest transactions.",
                            customer: "Dear valued guest,\n\nWelcome to Casa Estela! We're delighted to have you with us.\n\nYour comfort is our priority. Our front desk is available 24/7 for any assistance you may need.\n\nEnjoy your stay!"
                        }
                    },
                    checkout: {
                        subject: "Check-out Reminder",
                        messages: {
                            general: "Check-out reminder for all departments.",
                            frontdesk: "Dear Front Desk Staff,\n\nPlease prepare for guest check-out in Room [ROOM] tomorrow at 12:00 PM.\n\nEnsure all services are properly closed and keys are collected.",
                            cashier: "Dear Cashier,\n\nPlease prepare final billing for Room [ROOM] check-out tomorrow.\n\nEnsure all charges are properly posted and ready for settlement.",
                            customer: "Dear valued guest,\n\nThis is a reminder that your check-out time is scheduled for tomorrow at 12:00 PM.\n\nPlease ensure all belongings are collected and room keys are returned to the front desk.\n\nThank you for choosing Casa Estela!"
                        }
                    },
                    emergency: {
                        subject: "Important: Emergency Notice",
                        messages: {
                            general: "ATTENTION ALL GUESTS AND STAFF:\n\nThis is an important emergency notice regarding [EMERGENCY SITUATION]. Please follow safety protocols and remain calm.\n\nFurther instructions will be provided as needed.",
                            frontdesk: "Dear Front Desk Staff,\n\nEMERGENCY ALERT: [EMERGENCY SITUATION]\n\nPlease implement emergency protocols immediately. Coordinate with security and ensure guest safety.\n\nProvide regular updates to management.",
                            cashier: "Dear Cashier,\n\nEMERGENCY SITUATION: [EMERGENCY SITUATION]\n\nPlease secure all financial systems and documents.\n\nFollow emergency protocols as trained.",
                            customer: "Dear valued guest,\n\nIMPORTANT SAFETY NOTICE: [EMERGENCY SITUATION]\n\nPlease remain calm and follow staff instructions. Your safety is our priority.\n\nUpdates will be provided regularly."
                        }
                    }
                };

                if (templateType && templates[templateType]) {
                    const template = templates[templateType];
                    $('#subject').val(template.subject);
                    
                    // Get the appropriate message based on recipient type
                    const recipientMessage = template.messages[recipientType] || template.messages['general'];
                    $('#message').val(recipientMessage);
                }
            });

            // Update message when recipient type changes
            $('#recipient_type').change(function() {
                const templateType = $('#message_template').val();
                if (templateType) {
                    $('#message_template').trigger('change');
                }
            });

            // Handle message form submission
            $('#sendMessageForm').on('submit', function(e) {
                e.preventDefault();
                
                // Disable submit button to prevent double submission
                const submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true);
                
                const formData = {
                    recipient_type: $('#recipient_type').val(),
                    subject: $('#subject').val(),
                    message: $('#message').val()
                };

                // Add customer_id if recipient type is customer
                if (formData.recipient_type === 'customer' && $('#customer_id').length) {
                    formData.customer_id = $('#customer_id').val();
                }

                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Message sent successfully!');
                            $('#sendMessageModal').modal('hide');
                            location.reload(); // Reload to show new message
                        } else {
                            alert('Error: ' + (response.message || 'Unknown error occurred'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error details:', xhr.responseText);
                        alert('Error sending message. Please try again.');
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitButton.prop('disabled', false);
                    }
                });
            });

            // Add this to your existing JavaScript
            $('#customerSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                $('#customer_id option').each(function() {
                    const $option = $(this);
                    // Skip the first two options (Select a Customer and All Customers)
                    if ($option.index() < 2) return true;
                    
                    const searchText = $option.data('search');
                    if (searchText && searchText.includes(searchTerm)) {
                        $option.show();
                    } else {
                        $option.hide();
                    }
                });
            });

            // Make the dropdown stay open while typing in the search box
            $('#customerSearch').click(function(e) {
                e.stopPropagation();
            });

            // Clear search when customer selection is hidden
            $('#recipient_type').change(function() {
                if ($(this).val() !== 'customer') {
                    $('#customerSearch').val('');
                    $('#customer_id option').show();
                }
            });
        });
    </script> 
</body>
</html> 