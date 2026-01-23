<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela Custom Alerts</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #d4af37 0%, #c5a028 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .demo-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .demo-section h2 {
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-error {
            background: #ef4444;
            color: white;
        }

        .btn-error:hover {
            background: #dc2626;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-info {
            background: #3b82f6;
            color: white;
        }

        .btn-info:hover {
            background: #2563eb;
        }

        .btn-gold {
            background: #d4af37;
            color: white;
        }

        .btn-gold:hover {
            background: #c5a028;
        }

        /* Alert Styles */
        .ce-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 350px;
            max-width: 450px;
            padding: 16px 20px;
            border-radius: 8px;
            border-left: 4px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: start;
            gap: 12px;
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
        }

        .ce-alert-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #6b7280;
            line-height: 1;
            padding: 0;
            width: 20px;
            height: 20px;
        }

        .ce-alert-close:hover {
            color: #1f2937;
        }

        .ce-alert-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
        }

        .ce-alert-content {
            flex: 1;
            padding-right: 20px;
        }

        .ce-alert-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 4px;
            color: #1f2937;
        }

        .ce-alert-message {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
        }

        .ce-alert-success {
            background: #ecfdf5;
            border-color: #10b981;
        }

        .ce-alert-error {
            background: #fef2f2;
            border-color: #ef4444;
        }

        .ce-alert-warning {
            background: #fffbeb;
            border-color: #f59e0b;
        }

        .ce-alert-info {
            background: #eff6ff;
            border-color: #3b82f6;
        }

        /* Modal Overlay */
        .ce-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
            animation: fadeIn 0.2s ease-out;
        }

        /* Modal Styles */
        .ce-modal {
            background: white;
            border-radius: 12px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: scaleIn 0.2s ease-out;
        }

        .ce-modal-content {
            padding: 30px;
            text-align: center;
        }

        .ce-modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .ce-modal-icon svg {
            width: 40px;
            height: 40px;
        }

        .ce-modal-icon-success {
            background: #d1fae5;
        }

        .ce-modal-icon-error {
            background: #fee2e2;
        }

        .ce-modal-icon-warning {
            background: #fef3c7;
        }

        .ce-modal-icon-info {
            background: #dbeafe;
        }

        .ce-modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .ce-modal-message {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .ce-modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .ce-modal-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ce-modal-btn-primary {
            background: #d4af37;
            color: white;
        }

        .ce-modal-btn-primary:hover {
            background: #c5a028;
        }

        .ce-modal-btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .ce-modal-btn-secondary:hover {
            background: #d1d5db;
        }

        /* Casa Estela Themed Confirmation */
        .ce-confirm {
            background: #1f2937;
            border-top: 4px solid #d4af37;
        }

        .ce-confirm .ce-modal-title {
            color: #d4af37;
        }

        .ce-confirm .ce-modal-message {
            color: #d1d5db;
        }

        .ce-confirm .ce-modal-icon {
            background: #d4af37;
        }

        .ce-confirm .ce-modal-btn-secondary {
            background: #374151;
            color: #d1d5db;
        }

        .ce-confirm .ce-modal-btn-secondary:hover {
            background: #4b5563;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .ce-alert-closing {
            animation: slideOutRight 0.3s ease-out forwards;
        }

        /* SVG Icons */
        .icon-success {
            fill: #10b981;
        }

        .icon-error {
            fill: #ef4444;
        }

        .icon-warning {
            fill: #f59e0b;
        }

        .icon-info {
            fill: #3b82f6;
        }

        .icon-white {
            fill: white;
        }

        .code-section {
            background: #1f2937;
            padding: 20px;
            border-radius: 8px;
            color: #d1d5db;
            overflow-x: auto;
        }

        .code-section pre {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .code-title {
            color: #d4af37;
            font-weight: 700;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>
    <div class="demo-container">
        <div class="header">
            <h1>CASA ESTELA BOUTIQUE HOTEL & CAFE</h1>
            <p>Custom Alert & Modal System - Native JS & PHP</p>
        </div>

        <div class="demo-section">
            <h2>Inline Alerts</h2>
            <div class="button-group">
                <button class="btn btn-success" onclick="showAlert('success')">Success Alert</button>
                <button class="btn btn-error" onclick="showAlert('error')">Error Alert</button>
                <button class="btn btn-warning" onclick="showAlert('warning')">Warning Alert</button>
                <button class="btn btn-info" onclick="showAlert('info')">Info Alert</button>
            </div>
        </div>

        <div class="demo-section">
            <h2>Modal Dialogs</h2>
            <div class="button-group">
                <button class="btn btn-success" onclick="showModal('success')">Success Modal</button>
                <button class="btn btn-error" onclick="showModal('error')">Error Modal</button>
                <button class="btn btn-warning" onclick="showModal('warning')">Warning Modal</button>
                <button class="btn btn-gold" onclick="showConfirm()">Casa Estela Confirmation</button>
            </div>
        </div>

        <div class="demo-section">
            <div class="code-title">JavaScript Usage</div>
            <div class="code-section">
                <pre>// Show Alert
                    CasaEstelaAlert.show('success', 'Booking Confirmed!', 'Your room has been successfully booked.');

                    // Show Modal
                    CasaEstelaModal.show('success', 'Success!', 'Operation completed successfully.');

                    // Show Confirmation
                    CasaEstelaModal.confirm('Delete Booking?', 'Are you sure you want to cancel this reservation?',
                    function() {
                    // Confirmed action
                    console.log('Confirmed!');
                    },
                    function() {
                    // Cancelled action
                    console.log('Cancelled!');
                    }
                    );</pre>
            </div>
        </div>

        <div class="demo-section">
            <div class="code-title">PHP Usage Example</div>
            <div class="code-section">
                <pre>&lt;?php
                    // In your PHP file after a successful booking
                    if ($bookingSuccess) {
                    echo "&lt;script&gt;
                    document.addEventListener('DOMContentLoaded', function() {
                    CasaEstelaAlert.show('success', 'Booking Confirmed!',
                    'Room #' . $roomNumber . ' has been booked successfully.');
                    });
                    &lt;/script&gt;";
                    }

                    // After a failed operation
                    if ($error) {
                    echo "&lt;script&gt;
                    document.addEventListener('DOMContentLoaded', function() {
                    CasaEstelaModal.show('error', 'Booking Failed', '" . addslashes($errorMessage) . "');
                    });
                    &lt;/script&gt;";
                    }

                    // Before deleting a record
                    echo "&lt;button onclick=\"confirmDelete(" . $bookingId . ")\"&gt;Delete Booking&lt;/button&gt;";
                    ?&gt;

                    &lt;script&gt;
                    function confirmDelete(bookingId) {
                    CasaEstelaModal.confirm(
                    'Delete Booking?',
                    'This action cannot be undone.',
                    function() {
                    window.location.href = 'delete.php?id=' + bookingId;
                    }
                    );
                    }
                    &lt;/script&gt;</pre>
            </div>
        </div>
    </div>

    <script>
        // Casa Estela Alert System
        const CasaEstelaAlert = {
            show: function (type, title, message, duration = 5000) {
                const icons = {
                    success: '<svg class="icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    error: '<svg class="icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    warning: '<svg class="icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                    info: '<svg class="icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                };

                const alert = document.createElement('div');
                alert.className = `ce-alert ce-alert-${type}`;
                alert.innerHTML = `
                    <div class="ce-alert-icon">${icons[type]}</div>
                    <div class="ce-alert-content">
                        <div class="ce-alert-title">${title}</div>
                        <div class="ce-alert-message">${message}</div>
                    </div>
                    <button class="ce-alert-close" onclick="this.parentElement.classList.add('ce-alert-closing'); setTimeout(() => this.parentElement.remove(), 300)">×</button>
                `;

                document.body.appendChild(alert);

                if (duration > 0) {
                    setTimeout(() => {
                        alert.classList.add('ce-alert-closing');
                        setTimeout(() => alert.remove(), 300);
                    }, duration);
                }
            }
        };

        // Casa Estela Modal System
        const CasaEstelaModal = {
            show: function (type, title, message, onConfirm = null, showCancel = false) {
                const icons = {
                    success: '<svg class="icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    error: '<svg class="icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    warning: '<svg class="icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                    info: '<svg class="icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                };

                const overlay = document.createElement('div');
                overlay.className = 'ce-overlay';
                overlay.innerHTML = `
                    <div class="ce-modal">
                        <div class="ce-modal-content">
                            <div class="ce-modal-icon ce-modal-icon-${type}">
                                ${icons[type]}
                            </div>
                            <div class="ce-modal-title">${title}</div>
                            <div class="ce-modal-message">${message}</div>
                            <div class="ce-modal-buttons">
                                ${showCancel ? '<button class="ce-modal-btn ce-modal-btn-secondary" onclick="CasaEstelaModal.close(this)">Cancel</button>' : ''}
                                <button class="ce-modal-btn ce-modal-btn-primary" onclick="CasaEstelaModal.handleConfirm(this)">${showCancel ? 'Confirm' : 'OK'}</button>
                            </div>
                        </div>
                    </div>
                `;

                overlay.querySelector('.ce-modal-btn-primary').ceConfirmCallback = onConfirm;
                document.body.appendChild(overlay);

                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) {
                        CasaEstelaModal.close(overlay);
                    }
                });
            },

            confirm: function (title, message, onConfirm, onCancel = null) {
                const overlay = document.createElement('div');
                overlay.className = 'ce-overlay';
                overlay.innerHTML = `
                    <div class="ce-modal ce-confirm">
                        <div class="ce-modal-content">
                            <div class="ce-modal-icon">
                                <svg class="icon-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ce-modal-title">${title}</div>
                            <div class="ce-modal-message">${message}</div>
                            <div class="ce-modal-buttons">
                                <button class="ce-modal-btn ce-modal-btn-secondary" onclick="CasaEstelaModal.handleCancel(this)">Cancel</button>
                                <button class="ce-modal-btn ce-modal-btn-primary" onclick="CasaEstelaModal.handleConfirm(this)">Confirm</button>
                            </div>
                        </div>
                    </div>
                `;

                overlay.querySelector('.ce-modal-btn-primary').ceConfirmCallback = onConfirm;
                overlay.querySelector('.ce-modal-btn-secondary').ceCancelCallback = onCancel;
                document.body.appendChild(overlay);
            },

            handleConfirm: function (btn) {
                if (btn.ceConfirmCallback && typeof btn.ceConfirmCallback === 'function') {
                    btn.ceConfirmCallback();
                }
                this.close(btn);
            },

            handleCancel: function (btn) {
                if (btn.ceCancelCallback && typeof btn.ceCancelCallback === 'function') {
                    btn.ceCancelCallback();
                }
                this.close(btn);
            },

            close: function (element) {
                const overlay = element.closest ? element.closest('.ce-overlay') : element;
                if (overlay) {
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.remove(), 200);
                }
            }
        };

        // Demo Functions
        function showAlert(type) {
            const messages = {
                success: { title: 'Booking Confirmed!', message: 'Your room has been successfully booked for January 25-27, 2026.' },
                error: { title: 'Booking Failed', message: 'Unable to process your booking. Please check your payment details.' },
                warning: { title: 'Limited Availability', message: 'Only 2 rooms left for your selected dates.' },
                info: { title: 'New Feature Available', message: 'You can now book cafe reservations directly from the dashboard.' }
            };

            CasaEstelaAlert.show(type, messages[type].title, messages[type].message);
        }

        function showModal(type) {
            const messages = {
                success: { title: 'Booking Successful!', message: 'Your reservation has been confirmed. A confirmation email has been sent to your inbox.' },
                error: { title: 'Payment Failed', message: 'We could not process your payment. Please try again or use a different payment method.' },
                warning: { title: 'Check-out Reminder', message: 'Your check-out time is approaching. Please settle your bill at the front desk.' }
            };

            CasaEstelaModal.show(type, messages[type].title, messages[type].message);
        }

        function showConfirm() {
            CasaEstelaModal.confirm(
                'Delete Booking?',
                'Are you sure you want to cancel this reservation? This action cannot be undone.',
                function () {
                    CasaEstelaAlert.show('success', 'Deleted!', 'The booking has been successfully deleted.');
                },
                function () {
                    CasaEstelaAlert.show('info', 'Cancelled', 'The booking was not deleted.');
                }
            );
        }
    </script>
</body>

</html>