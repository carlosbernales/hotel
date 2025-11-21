<?php
require_once 'db.php';  // Include the database connection file
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Add Employee</li>
        </ol>
    </div><!--/.row-->

    <!-- <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add Employee</h1>
        </div>
    </div> -->
    <!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Employee Detail:</div>
                <div class="panel-body">
                    <div class="emp-response"></div>
                    <form role="form" id="addEmployee" data-toggle="validator">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>Staff</label>
                                <select class="form-control" id="staff_type" name="staff_type_id" required data-error="Select Staff Type">
                                    <option selected disabled>Select Staff Type</option>
                                    <?php
                                    $query = "SELECT * FROM staff_type";
                                    $result = mysqli_query($con, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($staff = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . $staff['staff_type_id'] . '">' . $staff['staff_type'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label>Shift Timing</label>
                                <div class="input-group">
                                    <input type="time" class="form-control" id="shift_start" name="shift_start" required>
                                    <div class="input-group-append input-group-prepend">
                                        <span class="input-group-text">to</span>
                                    </div>
                                    <input type="time" class="form-control" id="shift_end" name="shift_end" required>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label>First Name</label>
                                <input type="text" class="form-control" placeholder="First Name" id="first_name" name="first_name" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label>Last Name</label>
                                <input type="text" class="form-control" placeholder="Last Name" id="last_name" name="last_name">
                            </div>

                            <div class="form-group col-lg-6">
                                <label>Contact Number</label>
                                <input type="number" class="form-control" placeholder="Contact Number" id="contact_no" name="contact_no" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label>Residential Address</label>
                                <input type="text" class="form-control" placeholder="Residential Address" id="address" name="address" required>
                                <div class="help-block with-errors"></div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-lg btn-success" style="border-radius:5px;">
                            <i class="fas fa-save"></i> Submit
                        </button>
                        <button type="reset" class="btn btn-lg btn-danger" style="border-radius:5px;">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </form>
                </div>
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-sm-12">
        <p class="back-link">Casa Estella Boutique Hotel & Cafe</p>
        </div>
    </div>

</div>    <!--/.main-->

<script>
$(document).ready(function() {
    // Handle form submission
    $('#addEmployee').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        $('.emp-response').empty();
        $('.help-block').empty();
        $('.form-group').removeClass('has-error');
        
        // Get all form values
        var formData = {
            action: 'add_employee',
            staff_type_id: $('#staff_type').val(),
            shift_start: $('#shift_start').val(),
            shift_end: $('#shift_end').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            contact_no: $('#contact_no').val(),
            address: $('#address').val()
        };

        // Debug logs for form data
        console.log('Form Data:', formData);

        // Client-side validation
        var errors = [];
        var requiredFields = {
            'staff_type_id': 'Staff Type',
            'shift_start': 'Shift Start Time',
            'shift_end': 'Shift End Time',
            'first_name': 'First Name',
            'contact_no': 'Contact Number',
            'address': 'Address'
        };

        Object.keys(requiredFields).forEach(function(field) {
            if (!formData[field]) {
                console.log('Missing field:', field, 'Current value:', formData[field]);
                errors.push(requiredFields[field] + ' is required');
                $('#' + field).closest('.form-group').addClass('has-error')
                    .find('.help-block').text(requiredFields[field] + ' is required');
            }
        });

        // Validate shift times
        if (formData.shift_start && formData.shift_end) {
            var start = new Date('1970-01-01T' + formData.shift_start);
            var end = new Date('1970-01-01T' + formData.shift_end);
            if (end <= start) {
                errors.push('Shift end time must be after start time');
                $('#shift_end').closest('.form-group').addClass('has-error')
                    .find('.help-block').text('End time must be after start time');
            }
        }

        if (errors.length > 0) {
            console.log('Validation errors:', errors);
            $('.emp-response').html('<div class="alert alert-danger"><ul><li>' + errors.join('</li><li>') + '</li></ul></div>');
            return;
        }

        // Show loading state
        $('.emp-response').html('<div class="alert alert-info">Adding employee...</div>');
        
        // Send AJAX request
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                if (response.status === 'success') {
                    $('.emp-response').html('<div class="alert alert-success">' + response.message + '</div>');
                    // Reset form
                    $('#addEmployee')[0].reset();
                    // Remove any error styling
                    $('.form-group').removeClass('has-error');
                    $('.help-block').empty();
                    // Redirect to staff management page after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'index.php?staff_mang';
                    }, 2000);
                } else {
                    var errorMessage = response.message || 'An error occurred while adding the employee.';
                    console.log('Error response:', errorMessage);
                    $('.emp-response').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    
                    // If the error message contains field names, highlight those fields
                    var fieldNames = Object.keys(requiredFields).map(function(key) {
                        return requiredFields[key].toLowerCase();
                    });
                    
                    fieldNames.forEach(function(fieldName) {
                        if (errorMessage.toLowerCase().includes(fieldName.toLowerCase())) {
                            var fieldId = Object.keys(requiredFields).find(function(key) {
                                return requiredFields[key].toLowerCase() === fieldName.toLowerCase();
                            });
                            if (fieldId) {
                                $('#' + fieldId).closest('.form-group').addClass('has-error');
                            }
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    xhr: xhr
                });
                
                let errorMessage = 'An error occurred while adding the employee.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    console.log('Raw response:', xhr.responseText);
                    errorMessage = 'Server returned an invalid response. Please try again.';
                }
                
                $('.emp-response').html('<div class="alert alert-danger">' + errorMessage + '</div>');
            }
        });
    });
});
</script>




