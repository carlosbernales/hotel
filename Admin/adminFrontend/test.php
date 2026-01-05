<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Booking Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        .receipt-header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-title { color: #0d6efd; font-weight: 800; text-transform: uppercase; }
        .table thead { background-color: #f1f4f9; }
        .total-section { background: #f8f9fa; padding: 20px; border-radius: 5px; }
        @media print {
            .no-print { display: none; }
            .receipt-container { box-shadow: none; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt-container">
        <div class="row receipt-header align-items-center">
            <div class="col-sm-6">
                <h2 class="invoice-title">Sales Report</h2>
                <p class="mb-0">Booking Reference: <strong>#EVT-99283</strong></p>
                <p>Date: Jan 05, 2026</p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h4 class="fw-bold">Starlight Venues Inc.</h4>
                <p class="text-muted small">123 Event Plaza, Suite 400<br>New York, NY 10001<br>contact@starlight.com</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="text-muted text-uppercase small font-weight-bold">Billed To:</h6>
                <p class="fw-bold mb-0">Alex Thompson</p>
                <p class="text-muted">alex.t@email.com<br>+1 (555) 000-1234</p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-muted text-uppercase small font-weight-bold">Event Details:</h6>
                <p class="fw-bold mb-0">Annual Corporate Gala</p>
                <p class="text-muted">Date: March 15, 2026<br>Guests: 150</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless mt-4">
                <thead>
                    <tr>
                        <th class="py-3">Description</th>
                        <th class="text-center py-3">Qty/Hrs</th>
                        <th class="text-end py-3">Unit Price</th>
                        <th class="text-end py-3">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-3">Grand Ballroom Rental (Full Day)</td>
                        <td class="text-center">1</td>
                        <td class="text-end">$2,500.00</td>
                        <td class="text-end fw-bold">$2,500.00</td>
                    </tr>
                    <tr>
                        <td class="py-3">Premium Catering Service (Per Head)</td>
                        <td class="text-center">150</td>
                        <td class="text-end">$65.00</td>
                        <td class="text-end fw-bold">$9,750.00</td>
                    </tr>
                    <tr>
                        <td class="py-3">AV Setup & Tech Support</td>
                        <td class="text-center">1</td>
                        <td class="text-end">$450.00</td>
                        <td class="text-end fw-bold">$450.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row mt-4 justify-content-end">
            <div class="col-md-5">
                <div class="total-section">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>$12,700.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (8%)</span>
                        <span>$1,016.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Amount</span>
                        <span class="fw-bold text-primary h5 mb-0">$13,716.00</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 text-center">
            <p class="text-muted small">Thank you for choosing Starlight Venues. Please note that cancellations made within 30 days of the event are subject to a 50% fee.</p>
            <button class="btn btn-outline-primary btn-sm no-print mt-3" onclick="window.print()">Print Receipt</button>
        </div>
    </div>
</div>

</body>
</html>