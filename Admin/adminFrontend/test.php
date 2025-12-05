<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela SweetAlert Demo</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../Admin/adminFrontend/assets/alerts.css">
</head>

<body>
    <div class="header">
        <h1>🏨 CASA ESTELA BOUTIQUE HOTEL & CAFE</h1>
        <p>Custom SweetAlert2 Demo - Click any button to test</p>
    </div>

    <div class="button-grid">
        <button class="demo-button" onclick="showDeleteConfirmation()">
            <span class="icon">🗑️</span>
            Delete Item
            <span class="description">Confirmation with warning</span>
        </button>

        <button class="demo-button" onclick="showSuccessAlert()">
            <span class="icon">✅</span>
            Success Message
            <span class="description">Action completed successfully</span>
        </button>

        <button class="demo-button" onclick="showErrorAlert()">
            <span class="icon">❌</span>
            Error Message
            <span class="description">Something went wrong</span>
        </button>

        <button class="demo-button" onclick="showAvailabilityConfirmation()">
            <span class="icon">🔄</span>
            Change Availability
            <span class="description">Update item status</span>
        </button>

        <button class="demo-button" onclick="showPriceUpdateAlert()">
            <span class="icon">💰</span>
            Update Price
            <span class="description">Edit price with input</span>
        </button>

        <button class="demo-button" onclick="showInfoAlert()">
            <span class="icon">ℹ️</span>
            Info Message
            <span class="description">General information</span>
        </button>

        <button class="demo-button" onclick="showBookingConfirmation()">
            <span class="icon">📅</span>
            Confirm Booking
            <span class="description">Room reservation</span>
        </button>

        <button class="demo-button" onclick="showCustomHtml()">
            <span class="icon">✨</span>
            Custom HTML
            <span class="description">Rich content alert</span>
        </button>

        <button class="demo-button" onclick="showSuccessToast()">
            <span class="icon">🔔</span>
            Success Toast
            <span class="description">Top-right notification</span>
        </button>

        <button class="demo-button" onclick="showDeleteToast()">
            <span class="icon">🗑️</span>
            Delete Toast
            <span class="description">Top-right delete notification</span>
        </button>
    </div>

    <script>
        // 1. Delete Confirmation
        function showDeleteConfirmation() {
            Swal.fire({
                title: 'Delete Item?',
                html: 'Are you sure you want to delete <strong>Hand-cut Potato Fries</strong>?<br/>This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    showSuccessAlert('Item deleted successfully!');
                }
            });
        }

        // 2. Success Alert
        function showSuccessAlert(message = 'Operation completed successfully!') {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }

        // 3. Error Alert
        function showErrorAlert() {
            Swal.fire({
                title: 'Error',
                text: 'Unable to complete the operation. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }

        // 4. Availability Change
        function showAvailabilityConfirmation() {
            Swal.fire({
                title: 'Change Availability?',
                html: 'Set <strong>Mozzarella Stick</strong> as <strong>Available</strong>?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Change',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Availability status has been changed.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 2000
                    });
                }
            });
        }

        // 5. Price Update with Input
        function showPriceUpdateAlert() {
            Swal.fire({
                title: 'Update Price',
                html: `
                    <div style="text-align: left; color: #e0e0e0; margin-bottom: 10px;">
                        <label for="swal-input-price" style="display: block; margin-bottom: 5px;">New Price (₱):</label>
                        <input type="number" id="swal-input-price" class="swal2-input" 
                               value="160.00" step="0.01" 
                               style="width: 100%; margin: 0;">
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const price = document.getElementById('swal-input-price').value;
                    if (!price || price <= 0) {
                        Swal.showValidationMessage('Please enter a valid price');
                    }
                    return price;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Price Updated!',
                        text: `New price: ₱${result.value}`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // 6. Info Alert
        function showInfoAlert() {
            Swal.fire({
                title: 'Kitchen Hours',
                html: '<strong>Cafe Operating Hours:</strong><br/>Monday - Sunday: 7:00 AM - 10:00 PM',
                icon: 'info',
                confirmButtonText: 'Got it'
            });
        }

        // 7. Booking Confirmation
        function showBookingConfirmation() {
            Swal.fire({
                title: 'Confirm Reservation?',
                html: `
                    <div style="text-align: left; color: #e0e0e0;">
                        <p><strong>Room:</strong> Deluxe Suite</p>
                        <p><strong>Guest:</strong> John Doe</p>
                        <p><strong>Check-in:</strong> Dec 10, 2024</p>
                        <p><strong>Check-out:</strong> Dec 12, 2024</p>
                        <p><strong>Total:</strong> ₱4,500.00</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Confirm Booking',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Booking Confirmed!',
                        text: 'Confirmation email has been sent to the guest.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // 8. Custom HTML with styling
        function showCustomHtml() {
            Swal.fire({
                title: 'Menu Special! 🍽️',
                html: `
                    <div style="text-align: center; color: #e0e0e0;">
                        <p style="font-size: 18px; margin: 15px 0; color: #c4a962;">
                            <strong>Today's Chef Special</strong>
                        </p>
                        <p style="margin: 10px 0;">Grilled Salmon with Lemon Butter</p>
                        <p style="font-size: 24px; color: #c4a962; font-weight: bold;">₱450.00</p>
                        <p style="font-size: 14px; margin-top: 15px; opacity: 0.8;">
                            Limited time offer - Available until 9:00 PM
                        </p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Add to Menu',
                showCancelButton: true,
                cancelButtonText: 'Maybe Later'
            });
        }

        // 9. Success Toast Notification (Top Right)
        function showSuccessToast() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: 'success',
                title: 'Item added successfully!'
            });
        }

        // 10. Delete Toast Notification (Top Right)
        function showDeleteToast() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: 'error',
                title: 'Item deleted!'
            });
        }
    </script>
</body>

</html>