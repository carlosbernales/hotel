<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Fetch all occupied tables with their details including order information
$query = "SELECT t.*, o.id as order_id, o.total_amount, o.order_date, o.status as order_status, 
                o.table_id, o.table_name
          FROM table_number t 
          LEFT JOIN orders o ON t.order_id = o.id
          WHERE t.status = 'occupied' 
          ORDER BY t.table_number ASC";

// Function to get order items for a specific order
function getOrderItems($con, $orderId) {
    if (!$orderId) return [];
    
    $query = "SELECT oi.*, oia.addon_name, oia.addon_price 
              FROM order_items oi
              LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
              WHERE oi.order_id = ?
              ORDER BY oi.id ASC";
              
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $itemId = $row['id'];
        if (!isset($items[$itemId])) {
            $items[$itemId] = [
                'id' => $itemId,
                'item_name' => $row['item_name'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'addons' => []
            ];
        }
        
        // Add addon if it exists
        if (isset($row['addon_name']) && !empty($row['addon_name'])) {
            $items[$itemId]['addons'][] = [
                'name' => $row['addon_name'],
                'price' => $row['addon_price']
            ];
        }
    }
    
    return array_values($items);
}

// Execute query
$result = $con->query($query);

// Check for errors in query execution
if (!$result) {
    echo '<div class="alert alert-danger">Database query error: ' . $con->error . '</div>';
}

// For debugging only - uncomment to see raw table data
/*
echo '<pre>';
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
$result->data_seek(0); // Reset result pointer
echo '</pre>';*/
?>

<style>
    /* Custom styles for the occupied tables page */
    .card {
        border: none;
        border-radius: 10px;
        margin-top: 67px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        border-top-left-radius: 10px !important;
        border-top-right-radius: 10px !important;
        padding: 1rem 1.25rem;
    }
    
    .card-header h5 {
        font-weight: 600;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        border-bottom: 2px solid #e3e6f0;
        font-weight: 600;   
        font-size: 15px;
        letter-spacing: 0.5px;
        padding: 0.75rem 1rem;
        background-color: #f8f9fc;
        color:rgb(0, 0, 0);
        white-space: nowrap;
    }
    
    .table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-color: #e3e6f0;
        white-space: nowrap;
    }
    
    /* Ensure table number column takes minimal width */
    .table tbody td:first-child {
        width: 15%;
    }
    
    /* Ensure occupied since column takes more width */
    .table tbody td:nth-child(2) {
        width: 50%;
        white-space: normal;
    }
    
    /* Ensure actions column takes remaining width */
    .table tbody td:last-child {
        width: 35%;
    }
    
    .btn-group .btn {
        border-radius: 4px;
        margin-right: 4px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .btn-warning {
        background-color: #f6c23e;
        border-color: #f6c23e;
    }
    
    .btn-warning:hover {
        background-color: #e0b03a;
        border-color: #d4aa3a;
    }
    
    .btn-info {
        background-color: #36b9cc;
        border-color: #36b9cc;
    }
    
    .btn-info:hover {
        background-color: #2c9faf;
        border-color: #2a96a5;
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    .order-items-container {
        background-color: #f8f9fc;
        border-radius: 4px;
    }
    
    .order-items-container .table {
        background-color: white;
    }
    
    .alert {
        border: none;
        border-radius: 4px;
    }
    
    .alert-info {
        background-color: #e8f4f8;
        color: #36b9cc;
    }
</style>

<!-- Include SweetAlert for beautiful alerts and notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table mr-2"></i> Occupied Tables
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="occupiedTablesTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 15%;">Table Number</th>
                                        <th style="width: 50%;">Occupied Since</th>
                                        <th style="width: 35%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) : ?>
                                        <tr class="table-row" data-table-id="<?php echo $row['id']; ?>">
                                            <td><?php echo $row['table_number']; ?></td>
                                            <td><?php echo isset($row['occupied_at']) && !empty($row['occupied_at']) ? date('M d, Y h:i A', strtotime($row['occupied_at'])) : '<span class="text-muted">-</span>'; ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-warning mark-available" data-table-id="<?php echo $row['id']; ?>" title="Mark as Available">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <?php if (isset($row['order_id']) && !empty($row['order_id'])) : ?>
                                                        <button type="button" class="btn btn-sm btn-primary toggle-order-items" data-order-id="<?php echo $row['order_id']; ?>" title="View Items">
                                                            <i class="fas fa-list"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if (isset($row['order_id']) && !empty($row['order_id'])) : 
                                            $orderItems = getOrderItems($con, $row['order_id']);
                                        ?>
                                        <tr class="order-items-row" id="order-items-<?php echo $row['order_id']; ?>" style="display: none;">
                                            <td colspan="3" class="p-0">
                                                <div class="order-items-container p-3 bg-light">
                                                    <h5 class="mb-3">Order Items</h5>
                                                    <?php if (!empty($orderItems)) : ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Quantity</th>
                                                                        <th>Price</th>
                                                                        <th>Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($orderItems as $item) : 
                                                                        $subtotal = $item['quantity'] * $item['unit_price'];
                                                                        // Add addon prices if any
                                                                        foreach ($item['addons'] as $addon) {
                                                                            $subtotal += $addon['price'];
                                                                        }
                                                                    ?>
                                                                        <tr>
                                                                            <td>
                                                                                <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                                                                <?php if (!empty($item['addons'])) : ?>
                                                                                    <ul class="list-unstyled mb-0 mt-1 small">
                                                                                        <?php foreach ($item['addons'] as $addon) : ?>
                                                                                            <li class="text-muted">
                                                                                                + <?php echo htmlspecialchars($addon['name']); ?> 
                                                                                                <span class="text-success">₱<?php echo number_format($addon['price'], 2); ?></span>
                                                                                            </li>
                                                                                        <?php endforeach; ?>
                                                                                    </ul>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                                            <td class="text-right">₱<?php echo number_format($item['unit_price'], 2); ?></td>
                                                                            <td class="text-right font-weight-bold">₱<?php echo number_format($subtotal, 2); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php else : ?>
                                                        <div class="alert alert-info">No items found for this order.</div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i> No tables are currently occupied.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
// SweetAlert configuration is done inline with each call

$(document).ready(function() {
    console.log('Document ready, initializing table...');
    
    // Initialize DataTable
    try {
        $('#occupiedTablesTable').DataTable({
            "ordering": true,
            "responsive": true,
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
        });
        console.log('DataTable initialized successfully');
    } catch (e) {
        console.error('Error initializing DataTable:', e);
    }
    
    // Mark table as available
    // Toggle order items visibility when Items button is clicked
    $('.toggle-order-items').on('click', function() {
        const orderId = $(this).data('order-id');
        const itemsRow = $('#order-items-' + orderId);
        
        // Toggle visibility with slide animation
        if (itemsRow.is(':visible')) {
            itemsRow.slideUp(200);
            $(this).html('<i class="fas fa-list"></i> Items');
        } else {
            // Hide any other open order items first
            $('.order-items-row').slideUp(200);
            $('.toggle-order-items').html('<i class="fas fa-list"></i> Items');
            
            // Show this order's items
            itemsRow.slideDown(200);
            $(this).html('<i class="fas fa-times"></i> Close');
        }
    });
    
    // Mark table as available with SweetAlert confirmation
    $('.mark-available').on('click', function() {
        const tableId = $(this).data('table-id');
        const tableNumber = $(this).closest('tr').find('td:first').text();
        console.log('Mark as available clicked for table ID:', tableId);
        
        Swal.fire({
            title: 'Confirm Table Status Change',
            text: 'Are you sure you want to mark Table ' + tableNumber + ' as available?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, mark as available',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Confirmation accepted, sending AJAX request...');
                
                $.ajax({
                    url: 'update_table_status.php',
                    type: 'POST',
                    data: {
                        table_id: tableId,
                        status: 'available'
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX response received:', response);
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Table marked as available successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error: ' + (response.message || 'Unknown error'),
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while processing your request. Please try again.',
                            icon: 'error'
                        });
                    }
                });
            } else {
                console.log('User cancelled the operation');
            }
        });
    });
});
</script>
