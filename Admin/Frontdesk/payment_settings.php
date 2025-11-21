<?php
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Handle form submission for updating payment settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        // Add your payment gateway settings here
        $success = true;
        if ($success) {
            echo "<div class='alert alert-success'>Payment settings updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating payment settings.</div>";
        }
    }
}

if (isset(
    $_FILES['gcash_qr']['name']) && $_FILES['gcash_qr']['name'] != ''
) {
    $gcashQrPath = 'uploads/' . basename($_FILES['gcash_qr']['name']);
    move_uploaded_file($_FILES['gcash_qr']['tmp_name'], $gcashQrPath);
    echo "<div class='alert alert-success'>GCash QR code uploaded successfully!</div>";
}

if (isset(
    $_FILES['maya_qr']['name']) && $_FILES['maya_qr']['name'] != ''
) {
    $mayaQrPath = 'uploads/' . basename($_FILES['maya_qr']['name']);
    move_uploaded_file($_FILES['maya_qr']['tmp_name'], $mayaQrPath);
    echo "<div class='alert alert-success'>Maya QR code uploaded successfully!</div>";
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Payment Settings</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Configure Payment Settings</div>
                <div class="panel-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Payment Gateway</label>
                            <select class="form-control" name="payment_gateway">
                                <option value="stripe">Stripe</option>
                                <option value="paypal">PayPal</option>
                                <option value="razorpay">Razorpay</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>API Key</label>
                            <input type="password" class="form-control" name="api_key" placeholder="Enter API Key">
                        </div>
                        
                        <div class="form-group">
                            <label>Secret Key</label>
                            <input type="password" class="form-control" name="secret_key" placeholder="Enter Secret Key">
                        </div>
                        
                        <div class="form-group">
                            <label>Currency</label>
                            <select class="form-control" name="currency">
                                <option value="USD">PHP</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">USD</option>
                                <option value="JPY">JPY</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Test Mode</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="test_mode" value="1">Yes
                                </label>
                                <label style="margin-left: 20px;">
                                    <input type="radio" name="test_mode" value="0" checked>No
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Upload GCash QR Code</label>
                            <input type="file" class="form-control" name="gcash_qr" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Upload Maya QR Code</label>
                            <input type="file" class="form-control" name="maya_qr" accept="image/*">
                        </div>
                        <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
