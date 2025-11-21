<?php
require 'db.php';
?>

<!-- Multiple Room Booking Modal -->
<div class="modal fade" id="multipleRoomBookingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Multiple Rooms</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <button class="btn btn-warning mb-3" onclick="backToList()">
                    <i class="fa fa-arrow-left"></i> Back to List
                </button>

                <div class="selected-rooms">
                    <div id="roomDetails"></div>
                </div>

                <div class="guest-details mt-3">
                    <div class="form-group">
                        <label>Number of Guests for <span id="currentRoomType"></span></label>
                        <input type="number" class="form-control" id="guestCount" min="1" required>
                    </div>

                    <div id="guestNameFields"></div>
                </div>

                <div class="price-summary mt-4">
                    <h6>Price Summary</h6>
                    <div id="roomPriceDetails"></div>
                    <div class="total-amount mt-2">
                        Total Amount (<span id="nightCount"></span> nights): ₱<span id="totalAmount">0.00</span>
                    </div>
                    <div class="downpayment mt-2">
                        Downpayment (50%): ₱<span id="downpayment">0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="backToList()">Close</button>
                <button type="button" class="btn btn-warning" onclick="confirmBooking()">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Summary Modal -->
<div class="modal fade" id="bookingSummaryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Booking Summary</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Guest Information -->
                <div class="summary-section">
                    <h5>Guest Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <span id="summaryGuestName"></span></p>
                            <p><strong>Contact:</strong> <span id="summaryContact"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <span id="summaryEmail"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="summary-section">
                    <h5>Booking Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Check-in:</strong> <span id="summaryCheckIn"></span></p>
                            <p><strong>Check-out:</strong> <span id="summaryCheckOut"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Number of Nights:</strong> <span id="summaryNights"></span></p>
                            <p><strong>Payment Method:</strong> <span id="summaryPaymentMethod"></span></p>
                            <p><strong>Payment Option:</strong> <span id="summaryPaymentOption"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Room Details -->
                <div class="summary-section">
                    <h5>Room Details</h5>
                    <div id="summaryRoomsList"></div>
                </div>

                <!-- Discount Information -->
                <div class="summary-section discount-section">
                    <h5>Discount Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Original Amount:</strong> ₱<span id="summaryOriginalAmount">0.00</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Discount:</strong> <span id="summaryDiscountType"></span> ₱<span id="summaryDiscountAmount">0.00</span></p>
                        </div>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="summary-section total-section">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <h4>Total Amount: ₱<span id="summaryTotalAmount">0.00</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="cancelBookingBtn">Cancel</button>
                <button type="button" class="btn btn-warning" id="editBookingBtn">Edit</button>
                <button type="button" class="btn btn-success" id="confirmFinalBookingBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-dialog.modal-lg {
    max-width: 800px;
    margin: 1.75rem auto;
}

.modal-content {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

/* Form Styles */
.form-group {
    margin-bottom: 15px;
}

.form-control {
    height: 38px;
    font-size: 14px;
}

/* Selected Rooms Section */
.selected-rooms-section {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 20px;
}

.selected-rooms-list {
    max-height: 200px;
    overflow-y: auto;
    margin: 10px 0;
}

.selected-room-item {
    background: white;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.total-amount {
    border-top: 1px solid #dee2e6;
    padding-top: 10px;
    margin-top: 10px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .modal-dialog.modal-lg {
        max-width: 95%;
        margin: 10px auto;
    }

    .row {
        margin: 0 -10px;
    }

    .col-md-6 {
        padding: 0 10px;
    }
}

/* Button Styles */
.btn {
    padding: 8px 16px;
    font-size: 14px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

/* Summary Modal Styles */
.summary-section {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.summary-section h5 {
    margin-bottom: 15px;
    color: #333;
    font-weight: 600;
}

.summary-section p {
    margin-bottom: 8px;
}

.total-section {
    background-color: #e9ecef;
    border-top: 2px solid #dee2e6;
}

#summaryRoomsList .room-item {
    padding: 10px;
    margin-bottom: 10px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.discount-section {
    border-top: 1px dashed #ccc;
    border-bottom: 1px dashed #ccc;
    padding: 10px 0;
    margin: 10px 0;
    color: #28a745;
}

.summary-section.discount-section {
    display: none;
    background-color: #f8fff8;
    border: 1px dashed #28a745;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
    margin-bottom: 15px;
}
</style>

<script>
$(document).ready(function() {
    // Initialize the modal
    $('#multipleRoomBookingModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    // Handle date changes
    $('input[name="checkIn"], input[name="checkOut"]').change(function() {
        updateTotalAmount();
    });
    
    // Handle discount selection changes
    $('#discountTypeSelect').change(function() {
        updateTotalAmount();
    });

    // Update total amount when dates change
    function updateTotalAmount() {
        const checkIn = new Date($('input[name="checkIn"]').val());
        const checkOut = new Date($('input[name="checkOut"]').val());
        
        if (checkIn && checkOut && checkOut > checkIn) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            $('#nightCount').text(nights);
            
            let originalTotal = 0;
            
            // Calculate total for all selected rooms
            $('.selected-room-item').each(function() {
                const price = parseFloat($(this).data('price'));
                originalTotal += price * nights;
            });
            
            // Get discount type
            const discountType = $('#discountTypeSelect').val();
            let discountAmount = 0;
            let finalTotal = originalTotal;
            
            // Apply 10% discount if applicable
            if (discountType) {
                discountAmount = originalTotal * 0.10; // 10% discount
                finalTotal = originalTotal - discountAmount;
                
                // Show discount information
                $('.discount-section').show();
                $('#discountLabel').text(discountType.charAt(0).toUpperCase() + discountType.slice(1) + ' (10%)');
                $('#discountAmount').text(discountAmount.toFixed(2));
            } else {
                // Hide discount section if no discount
                $('.discount-section').hide();
            }
            
            // Update displayed amounts
            $('#originalAmount').text(originalTotal.toFixed(2));
            $('#totalAmount').text(finalTotal.toFixed(2));
            $('#downpayment').text((finalTotal * 0.5).toFixed(2));
            
            return {
                originalTotal: originalTotal,
                discountAmount: discountAmount,
                finalTotal: finalTotal,
                discountType: discountType,
                nights: nights
            };
        }
    }

    // Show booking summary
    $('#showSummaryBtn').click(function() {
        if (!$('#multipleBookingForm')[0].checkValidity()) {
            $('#multipleBookingForm')[0].reportValidity();
            return;
        }

        const formData = new FormData($('#multipleBookingForm')[0]);
        
        // Calculate totals with discount
        const priceInfo = updateTotalAmount();
        
        // Add discount information
        formData.append('original_total', priceInfo.originalTotal);
        formData.append('discount_type', priceInfo.discountType || '');
        formData.append('discount_amount', priceInfo.discountAmount);
        formData.append('final_total', priceInfo.finalTotal);
        
        formData.append('rooms', JSON.stringify(getSelectedRooms()));

        $.ajax({
            type: 'POST',
            url: 'process_booking_summary.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Add discount information to response data if applicable
                    if (priceInfo.discountAmount > 0) {
                        response.data.discount = {
                            type: priceInfo.discountType,
                            amount: priceInfo.discountAmount
                        };
                        response.data.originalAmount = priceInfo.originalTotal;
                        response.data.totalAmount = priceInfo.finalTotal;
                    }
                    
                    displayBookingSummary(response.data);
                    $('#multipleRoomBookingModal').modal('hide');
                    $('#bookingSummaryModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error processing booking summary');
            }
        });
    });

    // Handle edit booking
    $('#editBookingBtn').click(function() {
        $('#bookingSummaryModal').modal('hide');
        $('#multipleRoomBookingModal').modal('show');
    });

    // Handle cancel booking
    $('#cancelBookingBtn').click(function() {
        if (confirm('Are you sure you want to cancel this booking?')) {
            $('#bookingSummaryModal').modal('hide');
            $('#multipleRoomBookingForm')[0].reset();
            $('#selectedRoomsList').empty();
            updateTotalAmount();
        }
    });

    // Function to handle final booking confirmation
    $('#confirmFinalBookingBtn').click(function() {
        // Show loading state
        Swal.fire({
            title: 'Processing Booking',
            text: 'Please wait...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Collect room data from the summary modal
        const roomsData = [];
        $('#summaryRoomsList .room-item').each(function() {
            const roomText = $(this).find('strong').first().text();
            const priceText = $(this).find('strong').last().text();
            roomsData.push({
                id: 1, // We'll get this from the room type
                type: roomText,
                price: parseFloat(priceText.replace('₱', '').replace(',', '')),
                guestCount: 1 // Default to 1 if not specified
            });
        });

        // Collect all booking data
        const bookingData = {
            firstName: $('#summaryGuestName').text().split(' ')[0],
            lastName: $('#summaryGuestName').text().split(' ')[1],
            email: $('#summaryEmail').text(),
            contact: $('#summaryContact').text(),
            checkIn: $('#summaryCheckIn').text(),
            checkOut: $('#summaryCheckOut').text(),
            paymentMethod: $('#summaryPaymentMethod').text(),
            paymentOption: $('#summaryPaymentOption').text(),
            rooms: roomsData,
            totalAmount: parseFloat($('#summaryTotalAmount').text().replace('₱', '').replace(',', '')),
            discountType: $('#summaryDiscountType').text() || 'Regular',
            discountAmount: parseFloat($('#summaryDiscountAmount').text().replace('₱', '').replace(',', '') || '0')
        };

        console.log('Sending booking data:', bookingData);

        // Send booking data to server
        $.ajax({
            url: 'process_booking.php',
            type: 'POST',
            data: bookingData,
            success: function(response) {
                Swal.close();
                console.log('Server response:', response);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Successful!',
                        text: 'Your booking has been confirmed.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Close the modal and refresh the page
                            $('#bookingSummaryModal').modal('hide');
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Failed',
                        text: response.message || 'There was an error processing your booking.',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('Booking error:', error);
                console.error('Server response:', xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: 'There was an error processing your booking. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    function displayBookingSummary(data) {
        console.log('Displaying booking summary with data:', data);
        
        // Guest Information
        $('#summaryGuestName').text(data.guest.name);
        $('#summaryContact').text(data.guest.contact);
        $('#summaryEmail').text(data.guest.email);

        // Booking Details
        $('#summaryCheckIn').text(data.booking.checkIn);
        $('#summaryCheckOut').text(data.booking.checkOut);
        $('#summaryNights').text(data.booking.nights);
        $('#summaryPaymentMethod').text(data.booking.paymentMethod);
        $('#summaryPaymentOption').text(data.booking.paymentOption);

        // Room Details
        const roomsList = $('#summaryRoomsList');
        roomsList.empty();
        data.rooms.forEach(function(room) {
            roomsList.append(`
                <div class="room-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${room.type}</strong><br>
                            ${room.beds}
                        </div>
                        <div>
                            <strong>₱${parseFloat(room.price).toFixed(2)}</strong>
                        </div>
                    </div>
                </div>
            `);
        });

        // Discount Information
        console.log('Checking for discount information in data:', data.discount);
        
        if (data.discount && data.discount.amount > 0) {
            console.log('Showing discount information:', data.discount);
            
            // For the discount section in the summary modal
            $('#summaryDiscountType').text(data.discount.type.charAt(0).toUpperCase() + data.discount.type.slice(1) + ' (10%)');
            $('#summaryDiscountAmount').text('₱' + parseFloat(data.discount.amount).toFixed(2));
            $('.discount-section').show();
            
            if (data.originalAmount) {
                const originalAmount = parseFloat(data.originalAmount);
                const totalAmount = parseFloat(data.totalAmount);
                
                // Update price summary table
                setTimeout(function() {
                    const priceSummaryTable = $('#bookingSummaryModal .price-summary table');
                    
                    if (priceSummaryTable.length) {
                        console.log('Found price summary table for adding discount info');
                        
                        // Check if discount row already exists
                        let discountRow = priceSummaryTable.find('.discount-row, tr:contains("Discount")');
                        
                        if (discountRow.length === 0) {
                            console.log('Creating discount row in price summary table');
                            
                            // Find total amount row
                            const totalAmountRow = priceSummaryTable.find('tr:contains("Total Amount")');
                            
                            if (totalAmountRow.length) {
                                // Create discount row
                                discountRow = $(`
                                    <tr class="discount-row bg-light text-success">
                                        <td><b>Discount:</b></td>
                                        <td class="text-right">${data.discount.type.charAt(0).toUpperCase() + data.discount.type.slice(1)} (10%): -₱${parseFloat(data.discount.amount).toFixed(2)}</td>
                                    </tr>
                                `);
                                
                                // Insert before total amount row
                                totalAmountRow.before(discountRow);
                                
                                // Update total amount
                                totalAmountRow.find('td:last-child').text(`₱${parseFloat(data.totalAmount).toFixed(2)}`);
                            }
                        }
                    }
                }, 300);
            }
        } else {
            console.log('No discount information in data, hiding discount section');
            $('.discount-section').hide();
        }
        
        // Set total amount
        $('#summaryTotalAmount').text('₱' + parseFloat(data.totalAmount).toFixed(2));
    }

    function getSelectedRooms() {
        const rooms = [];
        $('.selected-room-item').each(function() {
            rooms.push({
                type: $(this).data('type'),
                beds: $(this).data('beds'),
                price: $(this).data('price'),
                roomTypeId: $(this).data('room-type-id')
            });
        });
        return rooms;
    }

    // Function to add room to the list
    function addRoomToList(room) {
        const roomHtml = `
            <div class="selected-room-item" data-price="${room.price}" data-type="${room.type}" data-beds="${room.beds}" data-room-type-id="${room.roomTypeId}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${room.type}</strong><br>
                        ${room.beds}<br>
                        ₱${room.price} per night
                    </div>
                    <button type="button" class="btn btn-sm btn-danger remove-room">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#selectedRoomsList').append(roomHtml);
        updateTotalAmount();
    }

    // Handle removing rooms
    $(document).on('click', '.remove-room', function() {
        $(this).closest('.selected-room-item').remove();
        updateTotalAmount();
    });
});
</script>
