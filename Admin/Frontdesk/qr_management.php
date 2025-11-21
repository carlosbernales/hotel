<?php
include 'db.php';
include 'header.php';
include 'sidebar.php';
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;"></a></li>
            <li class="active">QR Code Management</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Payment QR Codes</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">GCash QR Code</div>
                <div class="panel-body">
                    <form id="gcashForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Upload GCash QR Code</label>
                            <input type="file" class="form-control" name="gcash_qr" accept="image/*" required>
                            <small class="text-muted">Upload a clear image of your GCash QR code</small>
                        </div>
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" class="form-control" name="gcash_name" required>
                        </div>
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" class="form-control" name="gcash_number" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update GCash QR</button>
                    </form>
                    <div class="current-qr mt-3">
                        <h4>Current GCash QR Code</h4>
                        <img id="currentGcashQR" src="" alt="Current GCash QR" class="img-responsive" style="max-width: 200px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Maya QR Code</div>
                <div class="panel-body">
                    <form id="mayaForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Upload Maya QR Code</label>
                            <input type="file" class="form-control" name="maya_qr" accept="image/*" required>
                            <small class="text-muted">Upload a clear image of your Maya QR code</small>
                        </div>
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" class="form-control" name="maya_name" required>
                        </div>
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" class="form-control" name="maya_number" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Maya QR</button>
                    </form>
                    <div class="current-qr mt-3">
                        <h4>Current Maya QR Code</h4>
                        <img id="currentMayaQR" src="" alt="Current Maya QR" class="img-responsive" style="max-width: 200px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mt-3 {
    margin-top: 20px;
}
.current-qr {
    border-top: 1px solid #ddd;
    padding-top: 15px;
}
.panel-body {
    padding: 20px;
}
.form-group {
    margin-bottom: 15px;
}
.text-muted {
    color: #6c757d;
    font-size: 12px;
}
</style>

<script>
document.getElementById('gcashForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('type', 'gcash');
    
    updateQRCode(formData);
});

document.getElementById('mayaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('type', 'maya');
    
    updateQRCode(formData);
});

function updateQRCode(formData) {
    fetch('process_qr_code.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('QR Code updated successfully');
            location.reload();
        } else {
            alert('Error updating QR Code: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating QR Code');
    });
}

// Load current QR codes on page load
window.addEventListener('load', function() {
    fetch('get_qr_codes.php')
    .then(response => response.json())
    .then(data => {
        if(data.gcash_qr) {
            document.getElementById('currentGcashQR').src = data.gcash_qr;
            document.querySelector('[name="gcash_name"]').value = data.gcash_name || '';
            document.querySelector('[name="gcash_number"]').value = data.gcash_number || '';
        }
        if(data.maya_qr) {
            document.getElementById('currentMayaQR').src = data.maya_qr;
            document.querySelector('[name="maya_name"]').value = data.maya_name || '';
            document.querySelector('[name="maya_number"]').value = data.maya_number || '';
        }
    });
});
</script> 