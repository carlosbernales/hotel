<?php
require 'db.php'; // This includes the database connection

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get room amenities
function getRoomAmenities($con, $room_type_id) {  // Changed $conn to $con
    $sql = "SELECT a.name, a.icon 
            FROM amenities a 
            JOIN room_type_amenities rta ON a.amenity_id = rta.amenity_id 
            WHERE rta.room_type_id = ?";
    $stmt = $con->prepare($sql);  // Changed $conn to $con
    $stmt->bind_param("i", $room_type_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get all room types with details
$sql = "SELECT * FROM room_types ORDER BY room_type_id";
$result = $con->query($sql);  // Changed $conn to $con
$roomTypes = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Booking & Reservation</li>
        </ol>
    </div><!--/.row-->

    <div class="row">
        <?php
        // If a room_id is provided, show the booking form. Otherwise, show available rooms.
        if (isset($_GET['room_id'])) {
            include 'booking_form.php';
        } else {
            include 'room_cards.php';
            include 'multiple_room_booking.php'; // Include the multiple room booking form
        }
        ?>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <p class="back-link">Casa Estela Boutique Hotel & Cafe</p>
        </div>
    </div>
</div> <!--/.main-->

<!-- Booking Confirmation Modal -->
<div id="bookingConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><b>Room Booking Confirmation</b></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert bg-success alert-dismissable" role="alert">
                            <em class="fa fa-lg fa-check-circle"></em>&nbsp; Room Successfully Booked
                        </div>
                        <table class="table table-striped table-bordered table-responsive">
                            <tbody>
                                <tr>
                                    <td><b>Customer Name</b></td>
                                    <td id="getCustomerName"></td>
                                </tr>
                                <tr>
                                    <td><b>Room Type</b></td>
                                    <td id="getRoomType"></td>
                                </tr>
                                <tr>
                                    <td><b>Room No</b></td>
                                    <td id="getRoomNo"></td>
                                </tr>
                                <tr>
                                    <td><b>Check-In</b></td>
                                    <td id="getCheckIn"></td>
                                </tr>
                                <tr>
                                    <td><b>Check-Out</b></td>
                                    <td id="getCheckOut"></td>
                                </tr>
                                <tr>
                                    <td><b>Total Amount</b></td>
                                    <td id="getTotalPrice"></td>
                                </tr>
                                <tr>
                                    <td><b>Payment Status</b></td>
                                    <td id="getPaymentStatus"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" style="border-radius: 60px;" href="index.php?reservation">
                    <i class="fa fa-check-circle"></i> OK
                </a>
            </div>
        </div>
    </div>
</div>

<!-- External Libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(document).ready(function() {
    // When form is submitted
    $('#reservationForm').submit(function(e) {
        e.preventDefault();
        
        // Show loading state
        Swal.fire({
            title: 'Processing Booking',
            text: 'Please wait while we process your reservation...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Collect form data
        let formData = $(this).serialize();
        console.log('Sending booking data:', formData);

        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: 'process_booking.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Successful!',
                        html: `
                            <div class="booking-confirmation">
                                <p>Your booking has been confirmed.</p>
                                <p>Booking Reference: <strong>${response.bookingId}</strong></p>
                                <p>Please check your email for booking details.</p>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php?reservation';
                        }
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Failed',
                        html: `
                            <div class="text-left">
                                <p class="text-danger">${response.message}</p>
                                ${response.error_details ? 
                                    `<div class="mt-3">
                                        <small class="text-muted">Error Details:</small>
                                        <pre class="bg-light p-2 mt-1" style="font-size: 12px;">${JSON.stringify(response.error_details, null, 2)}</pre>
                                    </div>` : ''
                                }
                            </div>
                        `,
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
                
                // Show network error message
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    html: `
                        <div class="text-left">
                            <p>Unable to process your booking due to a connection error.</p>
                            <p>Please check your internet connection and try again.</p>
                            <div class="mt-3">
                                <small class="text-muted">Technical Details:</small>
                                <pre class="bg-light p-2 mt-1" style="font-size: 12px;">${error}</pre>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Try Again',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});
</script>

<!-- Add this CSS to your existing styles -->
<style>
/* Add these new styles at the top of the existing style block */
.main {
    padding-top: 20px;  /* Reduce top padding */
}

.breadcrumb {
    margin-bottom: 15px;  /* Reduce bottom margin of breadcrumb */
}

/* Update existing styles */
.card {
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-body {
    padding: 15px;
}

.amenity-icons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 10px 0;
}

.amenity-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f8f9fa;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

.discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff4444;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-weight: bold;
}

.btn-primary {
    width: 100%;
    margin-top: 10px;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.booking-confirmation {
    text-align: center;
    padding: 20px;
}

.booking-confirmation p {
    margin-bottom: 15px;
}

.text-danger {
    color: #dc3545;
}

.text-muted {
    color: #6c757d;
}

.bg-light {
    background-color: #f8f9fa;
}

.mt-1 {
    margin-top: 0.25rem;
}

.mt-3 {
    margin-top: 1rem;
}

.p-2 {
    padding: 0.5rem;
}

pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    margin: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle check-in form submission
    document.querySelectorAll('.check-in-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the form data from the parent container
            const container = this.closest('.room-container');
            const form = container.querySelector('form');
            const formData = new FormData(form);
            
            // Show confirmation modal first
            Swal.fire({
                title: 'Check-in Details',
                html: `
                    <div class="booking-summary">
                        <h4>Please Confirm Check-in Details</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Guest Name:</strong></td>
                                <td>${formData.get('guest_name')}</td>
                            </tr>
                            <tr>
                                <td><strong>Contact:</strong></td>
                                <td>${formData.get('contact')}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>${formData.get('email')}</td>
                            </tr>
                            <tr>
                                <td><strong>Room Type:</strong></td>
                                <td>${formData.get('room_type')}</td>
                            </tr>
                            <tr>
                                <td><strong>Check In:</strong></td>
                                <td>${formData.get('check_in_date')}</td>
                            </tr>
                            <tr>
                                <td><strong>Check Out:</strong></td>
                                <td>${formData.get('check_out_date')}</td>
                            </tr>
                            <tr>
                                <td><strong>Nights:</strong></td>
                                <td>${formData.get('nights')}</td>
                            </tr>
                            <tr>
                                <td><strong>Guests:</strong></td>
                                <td>${formData.get('number_of_guests')}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Method:</strong></td>
                                <td>${formData.get('payment_method')}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td>₱${formData.get('total_amount')}</td>
                            </tr>
                        </table>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Confirm Check-in',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show processing message
                    Swal.fire({
                        title: 'Processing Check-in',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send AJAX request
                    fetch('process_advance_checkin.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            Swal.fire({
                                title: '<span style="color: #28a745">Check-in Successful!</span>',
                                html: `
                                    <div class="booking-summary">
                                        <h4>Booking Summary</h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td><strong>Booking ID:</strong></td>
                                                <td>#${data.data.check_in_id}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Guest Name:</strong></td>
                                                <td>${data.data.guest_name}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Contact:</strong></td>
                                                <td>${data.data.contact}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>${data.data.email}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Room Type:</strong></td>
                                                <td>${data.data.room_type}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Check In:</strong></td>
                                                <td>${data.data.check_in_date}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Check Out:</strong></td>
                                                <td>${data.data.check_out_date}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nights:</strong></td>
                                                <td>${data.data.nights}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Guests:</strong></td>
                                                <td>${data.data.guests}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Payment Method:</strong></td>
                                                <td>${data.data.payment_method}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Amount:</strong></td>
                                                <td>₱${data.data.total_amount}</td>
                                            </tr>
                                        </table>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'View Check-ins',
                                confirmButtonColor: '#28a745',
                                allowOutsideClick: false,
                                width: '600px'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Redirect to checked-in page
                                    window.location.href = 'checked_in.php';
                                }
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An unexpected error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    });
});
</script>

<style>
.booking-summary {
    text-align: left;
    padding: 15px;
    background-color: #fff;
}

.booking-summary h4 {
    color: #28a745;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
    font-size: 1.4em;
    padding-bottom: 10px;
    border-bottom: 2px solid #28a745;
}

.booking-summary table {
    width: 100%;
    margin-bottom: 0;
    border: 1px solid #dee2e6;
}

.booking-summary .table td {
    padding: 12px;
    vertical-align: middle;
    border: 1px solid #dee2e6;
}

.booking-summary .table td:first-child {
    width: 35%;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.booking-summary .table td:last-child {
    color: #333;
}

.swal2-popup {
    font-size: 14px;
    padding: 20px;
}

.swal2-title {
    font-size: 24px;
    color: #28a745 !important;
    padding-bottom: 10px;
}

.swal2-actions {
    margin-top: 20px;
}

.swal2-confirm {
    padding: 12px 25px !important;
}

.swal2-cancel {
    padding: 12px 25px !important;
}

@media print {
    .booking-summary {
        padding: 0;
    }
    
    .booking-summary h4 {
        margin-top: 20px;
    }
    
    .table {
        border-collapse: collapse;
        width: 100%;
    }
    
    .table td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    
    @page {
        margin: 0.5cm;
    }
}
</style>
