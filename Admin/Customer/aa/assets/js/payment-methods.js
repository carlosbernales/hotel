// Function to fetch payment methods from the server
function fetchPaymentMethods() {
    fetch('get_payment_methods.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const paymentMethodSelect = document.getElementById('payment_method');
                if (!paymentMethodSelect) {
                    console.error('Payment method select element not found');
                    return;
                }

                // Clear existing options except the first one
                while (paymentMethodSelect.options.length > 1) {
                    paymentMethodSelect.remove(1);
                }

                // Add payment methods from server
                data.methods.forEach(method => {
                    if (method.is_active) {
                        const option = document.createElement('option');
                        option.value = method.name;
                        option.textContent = method.display_name;
                        option.dataset.accountName = method.account_name;
                        option.dataset.accountNumber = method.account_number;
                        option.dataset.qrCode = method.qr_code_image;
                        paymentMethodSelect.appendChild(option);
                    }
                });

                // Update payment details based on the selected method
                updatePaymentMethodDetails();
            } else {
                console.error('Failed to fetch payment methods:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching payment methods:', error);
        });
}

// Function to update payment method details based on selected option
function updatePaymentMethodDetails() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const accountNameElement = document.getElementById('account_name');
    const accountNumberElement = document.getElementById('account_number');
    const qrCodeElement = document.getElementById('qr_code_image');
    const paymentDetailsContainer = document.getElementById('payment_details_container');
    
    if (!paymentMethodSelect || !accountNameElement || !accountNumberElement || !qrCodeElement || !paymentDetailsContainer) {
        return;
    }
    
    const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
    
    if (paymentMethodSelect.value === '') {
        // No payment method selected, hide details
        paymentDetailsContainer.style.display = 'none';
        return;
    }
    
    // Show payment details container
    paymentDetailsContainer.style.display = 'block';
    
    // Update account details
    accountNameElement.textContent = selectedOption.dataset.accountName || '';
    accountNumberElement.textContent = selectedOption.dataset.accountNumber || '';
    
    // Update QR code image
    if (selectedOption.dataset.qrCode) {
        qrCodeElement.src = 'uploads/payment_qr_codes/' + selectedOption.dataset.qrCode;
        qrCodeElement.style.display = 'block';
    } else {
        qrCodeElement.style.display = 'none';
    }
}

// Initialize payment methods when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    fetchPaymentMethods();
    
    // Add event listener for payment method change
    const paymentMethodSelect = document.getElementById('payment_method');
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', updatePaymentMethodDetails);
    }
});
