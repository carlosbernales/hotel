function verifyEmail() {
    // Get the verification code from input fields
    const code = Array.from(document.querySelectorAll('.verification-input'))
        .map(input => input.value)
        .join('');
    
    // Get email from hidden input
    const email = document.getElementById('email').value;

    console.log('Submitting verification:', { email, code });

    // Disable verify button and show loading state
    const verifyButton = document.getElementById('verify-button');
    const originalText = verifyButton.textContent;
    verifyButton.disabled = true;
    verifyButton.textContent = 'Verifying...';

    // Send verification request
    fetch('verify_email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: email,
            code: code
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Verification response:', data);
        if (data.success) {
            // Show success message
            showMessage('success', data.message);
            
            // Redirect after a short delay if redirect URL is provided
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
            }
        } else {
            // Show error message
            showMessage('error', data.message);
            
            // Reset verification inputs
            document.querySelectorAll('.verification-input').forEach(input => {
                input.value = '';
            });
            document.querySelector('.verification-input').focus();
        }
    })
    .catch(error => {
        console.error('Verification error:', error);
        showMessage('error', 'An error occurred. Please try again.');
    })
    .finally(() => {
        // Re-enable verify button and restore original text
        verifyButton.disabled = false;
        verifyButton.textContent = originalText;
    });
}

function showMessage(type, message) {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = message;
    messageDiv.className = `alert alert-${type}`;
    messageDiv.style.display = 'block';

    // Hide message after 5 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Handle input in verification code fields
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.verification-input');
    
    inputs.forEach((input, index) => {
        // Handle input
        input.addEventListener('input', function(e) {
            if (e.target.value.length === 1) {
                // Move to next input
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value) {
                // Move to previous input
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').slice(0, 6);
            
            // Distribute pasted code across inputs
            pastedData.split('').forEach((char, i) => {
                if (i < inputs.length) {
                    inputs[i].value = char;
                }
            });

            // Focus last filled input
            const lastFilledIndex = Math.min(pastedData.length - 1, inputs.length - 1);
            inputs[lastFilledIndex].focus();
        });
    });
}); 