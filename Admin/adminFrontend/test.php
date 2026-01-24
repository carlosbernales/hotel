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

        .cea-demo-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .cea-header {
            background: linear-gradient(135deg, #d4af37 0%, #c5a028 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cea-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .cea-demo-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cea-demo-section h2 {
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .cea-button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .cea-demo-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cea-demo-btn-success {
            background: #10b981;
            color: white;
        }

        .cea-demo-btn-success:hover {
            background: #059669;
        }

        .cea-demo-btn-error {
            background: #ef4444;
            color: white;
        }

        .cea-demo-btn-error:hover {
            background: #dc2626;
        }

        .cea-demo-btn-warning {
            background: #f59e0b;
            color: white;
        }

        .cea-demo-btn-warning:hover {
            background: #d97706;
        }

        .cea-demo-btn-info {
            background: #3b82f6;
            color: white;
        }

        .cea-demo-btn-info:hover {
            background: #2563eb;
        }

        .cea-demo-btn-gold {
            background: #d4af37;
            color: white;
        }

        .cea-demo-btn-gold:hover {
            background: #c5a028;
        }

        /* Alert Styles */
        .cea-inline-alert {
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
            animation: ceaSlideInRight 0.3s ease-out;
        }

        .cea-inline-alert-close {
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

        .cea-inline-alert-close:hover {
            color: #1f2937;
        }

        .cea-inline-alert-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
        }

        .cea-inline-alert-content {
            flex: 1;
            padding-right: 20px;
        }

        .cea-inline-alert-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 4px;
            color: #1f2937;
        }

        .cea-inline-alert-message {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
        }

        .cea-inline-alert-success {
            background: #ecfdf5;
            border-color: #10b981;
        }

        .cea-inline-alert-error {
            background: #fef2f2;
            border-color: #ef4444;
        }

        .cea-inline-alert-warning {
            background: #fffbeb;
            border-color: #f59e0b;
        }

        .cea-inline-alert-info {
            background: #eff6ff;
            border-color: #3b82f6;
        }

        /* Modal Overlay */
        .cea-modal-overlay {
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
            animation: ceaFadeIn 0.2s ease-out;
        }

        /* Modal Styles */
        .cea-modal-dialog {
            background: white;
            border-radius: 12px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: ceaScaleIn 0.2s ease-out;
        }

        .cea-modal-body {
            padding: 30px;
            text-align: center;
        }

        .cea-modal-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .cea-modal-icon-wrapper svg {
            width: 40px;
            height: 40px;
        }

        .cea-modal-icon-wrapper-success {
            background: #d1fae5;
        }

        .cea-modal-icon-wrapper-error {
            background: #fee2e2;
        }

        .cea-modal-icon-wrapper-warning {
            background: #fef3c7;
        }

        .cea-modal-icon-wrapper-info {
            background: #dbeafe;
        }

        .cea-modal-heading {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .cea-modal-text {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .cea-modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .cea-modal-button {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cea-modal-button-primary {
            background: #d4af37;
            color: white;
        }

        .cea-modal-button-primary:hover {
            background: #c5a028;
        }

        .cea-modal-button-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .cea-modal-button-secondary:hover {
            background: #d1d5db;
        }

        /* Casa Estela Themed Confirmation */
        .cea-modal-confirm {
            background: #1f2937;
            border-top: 4px solid #d4af37;
        }

        .cea-modal-confirm .cea-modal-heading {
            color: #d4af37;
        }

        .cea-modal-confirm .cea-modal-text {
            color: #d1d5db;
        }

        .cea-modal-confirm .cea-modal-icon-wrapper {
            background: #d4af37;
        }

        .cea-modal-confirm .cea-modal-button-secondary {
            background: #374151;
            color: #d1d5db;
        }

        .cea-modal-confirm .cea-modal-button-secondary:hover {
            background: #4b5563;
        }

        /* Animations */
        @keyframes ceaFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes ceaScaleIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes ceaSlideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes ceaSlideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .cea-inline-alert-closing {
            animation: ceaSlideOutRight 0.3s ease-out forwards;
        }

        /* SVG Icons */
        .cea-icon-success {
            fill: #10b981;
        }

        .cea-icon-error {
            fill: #ef4444;
        }

        .cea-icon-warning {
            fill: #f59e0b;
        }

        .cea-icon-info {
            fill: #3b82f6;
        }

        .cea-icon-white {
            fill: white;
        }
    </style>
</head>

<body>
    <div class="cea-demo-container">
        <div class="cea-header">
            <h1>CASA ESTELA BOUTIQUE HOTEL & CAFE</h1>
            <p>Custom Alert & Modal System - Native JS & PHP</p>
        </div>

        <div class="cea-demo-section">
            <h2>Inline Alerts</h2>
            <div class="cea-button-group">
                <button class="cea-demo-btn cea-demo-btn-success" onclick="showAlert('success')">Success Alert</button>
                <button class="cea-demo-btn cea-demo-btn-error" onclick="showAlert('error')">Error Alert</button>
                <button class="cea-demo-btn cea-demo-btn-warning" onclick="showAlert('warning')">Warning Alert</button>
                <button class="cea-demo-btn cea-demo-btn-info" onclick="showAlert('info')">Info Alert</button>
            </div>
        </div>

        <div class="cea-demo-section">
            <h2>Modal Dialogs</h2>
            <div class="cea-button-group">
                <button class="cea-demo-btn cea-demo-btn-success" onclick="showModal('success')">Success Modal</button>
                <button class="cea-demo-btn cea-demo-btn-error" onclick="showModal('error')">Error Modal</button>
                <button class="cea-demo-btn cea-demo-btn-warning" onclick="showModal('warning')">Warning Modal</button>
                <button class="cea-demo-btn cea-demo-btn-gold" onclick="showConfirm()">Casa Estela Confirmation</button>
            </div>
        </div>
    </div>

    <script>
        // Casa Estela Alert System
        const CasaEstelaAlert = {
            show: function (type, title, message, duration = 5000) {
                const icons = {
                    success: '<svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    error: '<svg class="cea-icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    warning: '<svg class="cea-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                    info: '<svg class="cea-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                };

                const alert = document.createElement('div');
                alert.className = `cea-inline-alert cea-inline-alert-${type}`;
                alert.innerHTML = `
                    <div class="cea-inline-alert-icon">${icons[type]}</div>
                    <div class="cea-inline-alert-content">
                        <div class="cea-inline-alert-title">${title}</div>
                        <div class="cea-inline-alert-message">${message}</div>
                    </div>
                    <button class="cea-inline-alert-close" onclick="this.parentElement.classList.add('cea-inline-alert-closing'); setTimeout(() => this.parentElement.remove(), 300)">×</button>
                `;

                document.body.appendChild(alert);

                if (duration > 0) {
                    setTimeout(() => {
                        alert.classList.add('cea-inline-alert-closing');
                        setTimeout(() => alert.remove(), 300);
                    }, duration);
                }
            }
        };

        // Casa Estela Modal System
        const CasaEstelaModal = {
            show: function (type, title, message, onConfirm = null, showCancel = false) {
                const icons = {
                    success: '<svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    error: '<svg class="cea-icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    warning: '<svg class="cea-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                    info: '<svg class="cea-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                };

                const overlay = document.createElement('div');
                overlay.className = 'cea-modal-overlay';
                overlay.innerHTML = `
                    <div class="cea-modal-dialog">
                        <div class="cea-modal-body">
                            <div class="cea-modal-icon-wrapper cea-modal-icon-wrapper-${type}">
                                ${icons[type]}
                            </div>
                            <div class="cea-modal-heading">${title}</div>
                            <div class="cea-modal-text">${message}</div>
                            <div class="cea-modal-actions">
                                ${showCancel ? '<button class="cea-modal-button cea-modal-button-secondary" onclick="CasaEstelaModal.close(this)">Cancel</button>' : ''}
                                <button class="cea-modal-button cea-modal-button-primary" onclick="CasaEstelaModal.handleConfirm(this)">${showCancel ? 'Confirm' : 'OK'}</button>
                            </div>
                        </div>
                    </div>
                `;

                overlay.querySelector('.cea-modal-button-primary').ceConfirmCallback = onConfirm;
                document.body.appendChild(overlay);

                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) {
                        CasaEstelaModal.close(overlay);
                    }
                });
            },

            confirm: function (title, message, onConfirm, onCancel = null) {
                const overlay = document.createElement('div');
                overlay.className = 'cea-modal-overlay';
                overlay.innerHTML = `
                    <div class="cea-modal-dialog cea-modal-confirm">
                        <div class="cea-modal-body">
                            <div class="cea-modal-icon-wrapper">
                                <svg class="cea-icon-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="cea-modal-heading">${title}</div>
                            <div class="cea-modal-text">${message}</div>
                            <div class="cea-modal-actions">
                                <button class="cea-modal-button cea-modal-button-secondary" onclick="CasaEstelaModal.handleCancel(this)">Cancel</button>
                                <button class="cea-modal-button cea-modal-button-primary" onclick="CasaEstelaModal.handleConfirm(this)">Confirm</button>
                            </div>
                        </div>
                    </div>
                `;

                overlay.querySelector('.cea-modal-button-primary').ceConfirmCallback = onConfirm;
                overlay.querySelector('.cea-modal-button-secondary').ceCancelCallback = onCancel;
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
                const overlay = element.closest ? element.closest('.cea-modal-overlay') : element;
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