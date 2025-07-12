document.addEventListener('DOMContentLoaded', function() {
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('passwordError');
    const passwordConfirmationError = document.getElementById('passwordConfirmationError');

    let messageTimeoutId; // To store the ID of the setTimeout, allowing it to be cleared

    // Function to clear all types of form messages and input errors
    function clearMessages() {
        clearTimeout(messageTimeoutId); // Stop any active message disappearance timer

        // Hide and clear text content for PHP-generated general messages
        document.querySelectorAll(".form-error-message, .form-success-message").forEach(msg => {
            msg.style.display = 'none';
            msg.textContent = '';
        });

        // Clear text content for client-side validation error messages (e.g., "error-text" spans)
        document.querySelectorAll(".error-text").forEach(text => {
            text.textContent = '';
        });

        // Remove 'error' class from input fields
        document.querySelectorAll("input.error").forEach(input => {
            input.classList.remove('error');
        });
    }

    // Function to display messages (PHP-generated or client-side) and start auto-hide timer
    function displayMessages() {
        let anyVisible = false;
        document.querySelectorAll(".form-error-message, .form-success-message").forEach(msg => {
            // Check if the message element has any content to display
            if (msg.textContent.trim() !== '') {
                msg.style.display = 'block'; // Make the message visible
                anyVisible = true;
            }
        });

        // If any message is now visible, set a timeout to clear them
        if (anyVisible) {
            clearTimeout(messageTimeoutId); // Clear any previous timeout
            messageTimeoutId = setTimeout(() => {
                clearMessages(); // Call the function to hide and clear messages
            }, 5000); // 5000 milliseconds = 5 seconds
        }
    }

    // Initial call to display messages on load
    clearMessages(); // Clear any old messages
    displayMessages(); // Display new ones

    // Add event listeners to input fields to clear client-side errors and global messages on input/focus
    document.querySelectorAll('#resetPasswordForm input').forEach(input => {
        input.addEventListener('input', () => {
            const errorTextSpan = input.closest('.inputbox').querySelector('.error-text');
            if (errorTextSpan) errorTextSpan.textContent = '';
            input.classList.remove('error');
            clearMessages();
        });
        input.addEventListener('focus', () => {
            const errorTextSpan = input.closest('.inputbox').querySelector('.error-text');
            if (errorTextSpan) errorTextSpan.textContent = '';
            input.classList.remove('error');
            clearMessages();
        });
    });

    // Client-side validation for password reset form
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', function(e) {
            let isValid = true;

            // Reset errors
            passwordInput.classList.remove('error');
            passwordConfirmationInput.classList.remove('error');
            passwordError.textContent = '';
            passwordConfirmationError.textContent = '';
            clearMessages(); // Clear existing PHP messages

            // Password validation (same rules as registration)
            const passwordValue = passwordInput.value;
            if (passwordValue.length === 0) {
                passwordInput.classList.add('error');
                passwordError.textContent = 'Password cannot be empty.';
                isValid = false;
            } else if (passwordValue.length < 8) {
                passwordInput.classList.add('error');
                passwordError.textContent = 'Password must be at least 8 characters.';
                isValid = false;
            } else if (!/[A-Z]/.test(passwordValue)) {
                passwordInput.classList.add('error');
                passwordError.textContent = 'Password needs at least one uppercase letter.';
                isValid = false;
            } else if (!/[a-z]/.test(passwordValue)) {
                passwordInput.classList.add('error');
                passwordError.textContent = 'Password needs at least one lowercase letter.';
                isValid = false;
            } else if (!/[0-9]/.test(passwordValue)) {
                passwordInput.classList.add('error');
                passwordError.textContent = 'Password needs at least one number.';
                isValid = false;
            }

            // Confirm password validation
            if (passwordInput.value !== passwordConfirmationInput.value) {
                passwordConfirmationInput.classList.add('error');
                passwordConfirmationError.textContent = 'Passwords do not match.';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission
                displayMessages(); // Show client-side error messages
            }
        });
    }
});