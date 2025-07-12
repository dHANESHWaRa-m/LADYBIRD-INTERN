document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const container = document.querySelector('.container');
    const registerBtn = document.querySelector('.register-btn');
    const loginBtn = document.querySelector('.login-btn');
    // Select the forms directly within their respective formbox
    const loginForm = document.querySelector('.formbox.login form');
    const forgotPasswordForm = document.querySelector('.forget-password-form');
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    const backToLogin = document.getElementById('backToLogin');
    // Select the actual form *inside* the .formbox.register
    const registerForm = document.querySelector('.formbox.register form');

    let messageTimeoutId; // To store the ID of the setTimeout, allowing it to be cleared
    let currentForm = 'login'; // Track current form state (used for client-side validation persistence)

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
        document.querySelectorAll("input.error, select.error").forEach(input => {
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

    // Function to show/hide forms for USER-INITIATED TRANSITIONS (e.g., button clicks)
    // This function applies a slight delay for animation smoothness.
    function showCorrectFormAnimated(formType) {
        clearMessages(); // Always clear messages when user actively switches forms
        currentForm = formType; // Update current form state

        // Hide all forms initially to prepare for transition
        loginForm.style.display = 'none';
        forgotPasswordForm.style.display = 'none';
        if (registerForm) { // Check if registerForm exists before trying to hide it
            registerForm.style.display = 'none';
        }

        // Set container class immediately for animation
        if (formType === 'register') {
            container.classList.add('active');
        } else {
            container.classList.remove('active');
        }

        // Use a timeout to match the CSS transition duration for the .formbox
        // (Assuming your CSS transition for .formbox is around 0.6s or 600ms)
        setTimeout(() => {
            // Show the target form after the animation
            if (formType === 'login') {
                loginForm.style.display = 'flex';
            } else if (formType === 'forgot-password') {
                forgotPasswordForm.style.display = 'flex';
            } else if (formType === 'register' && registerForm) { // Check if registerForm exists
                registerForm.style.display = 'flex';
            }
            displayMessages(); // Display any relevant PHP messages that might have been redirected
        }, 600);
    }

    // Initialize forms on page load based on URL parameters (for redirects from PHP)
    // This function will set the initial display IMMEDIATELY without animation delay
    function initializeFormsOnLoad() {
        const urlParams = new URLSearchParams(window.location.search);
        const formParam = urlParams.get('form');

        // Hide all forms initially before deciding which to show, preventing FOUC (Flash Of Unstyled Content)
        loginForm.style.display = 'none';
        forgotPasswordForm.style.display = 'none';
        if (registerForm) {
            registerForm.style.display = 'none';
        }

        // Immediately set the container state and show the correct form
        if (formParam === 'forgot-password') {
            container.classList.remove('active'); // Forgot password form usually appears on the left (login side)
            forgotPasswordForm.style.display = 'flex';
            currentForm = 'forgot-password';
        } else if (formParam === 'register') {
            container.classList.add('active'); // Activate register side
            if (registerForm) {
                registerForm.style.display = 'flex';
            }
            currentForm = 'register';
        } else {
            // Default: show login form (e.g., initial load or simple redirect without 'form' param)
            container.classList.remove('active'); // Ensure login side
            loginForm.style.display = 'flex';
            currentForm = 'login';
        }

        // After setting the initial form display, clear any old messages
        // and then display new ones that might have come from PHP via session
        clearMessages();
        displayMessages();

        // Clean up URL parameters after initial load so they don't persist on refresh
        const url = new URL(window.location);
        if (formParam) { // Only remove if a 'form' parameter was present
            url.searchParams.delete('form');
            window.history.replaceState({}, document.title, url.pathname);
        }
    }

    // Event Listeners for form switching buttons
    if (registerBtn) {
        // Corrected: This button should toggle to the register form, not login
        registerBtn.addEventListener('click', () => showCorrectFormAnimated('register'));
    }

    if (loginBtn) {
        // Corrected: This button should toggle to the login form
        loginBtn.addEventListener('click', () => showCorrectFormAnimated('login'));
    }

    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default link behavior
            showCorrectFormAnimated('forgot-password');
        });
    }

    if (backToLogin) {
        backToLogin.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default link behavior
            showCorrectFormAnimated('login');
        });
    }

    // Add event listeners to input fields to clear client-side errors and global messages on input/focus
    document.querySelectorAll('.inputbox input, .inputbox select').forEach(input => {
        input.addEventListener('input', () => {
            const errorTextSpan = input.closest('.inputbox').querySelector('.error-text');
            if (errorTextSpan) errorTextSpan.textContent = ''; // Clear client-side error text
            input.classList.remove('error'); // Remove error class from input itself
            clearMessages(); // Also clear any global PHP messages on user input
        });
        input.addEventListener('focus', () => {
            const errorTextSpan = input.closest('.inputbox').querySelector('.error-text');
            if (errorTextSpan) errorTextSpan.textContent = '';
            input.classList.remove('error');
            clearMessages(); // Also clear global messages on input focus
        });
    });

    // Run initialization on page load
    initializeFormsOnLoad();

    // ========== FORM VALIDATIONS (Client-Side) ========== //

    // Login Form Validation
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const emailInput = document.getElementById('user');
            const passwordInput = document.getElementById('pass');
            const emailError = document.getElementById('userError');
            const passError = document.getElementById('passError');

            let isValid = true;

            // Reset client-side errors for this specific form before re-validating
            if (emailInput) emailInput.classList.remove('error');
            if (passwordInput) passwordInput.classList.remove('error');
            if (emailError) emailError.textContent = '';
            if (passError) passError.textContent = '';

            clearMessages(); // Clear any existing PHP messages before displaying client-side errors

            // Email validation
            if (emailInput) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    emailInput.classList.add('error');
                    if (emailError) emailError.textContent = 'Please enter a valid email address.';
                    isValid = false;
                }
            }

            // Password validation (client-side)
            if (passwordInput) {
                const passwordValue = passwordInput.value;
                if (passwordValue.length === 0) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password cannot be empty.';
                    isValid = false;
                } else if (passwordValue.length < 8) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password must be at least 8 characters.';
                    isValid = false;
                } else if (!/[A-Z]/.test(passwordValue)) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password needs at least one uppercase letter.';
                    isValid = false;
                } else if (!/[a-z]/.test(passwordValue)) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password needs at least one lowercase letter.';
                    isValid = false;
                } else if (!/[0-9]/.test(passwordValue)) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password needs at least one number.';
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault(); // Prevent form submission if validation fails
                // Stay on login form - don't change currentForm
                displayMessages(); // Ensure messages are shown and timer starts for client-side errors
            }
        });
    }

    // Forgot Password Form Validation
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            const emailInput = document.getElementById('forgotEmail');
            const emailError = document.getElementById('forgotEmailError');

            let isValid = true;

            // Reset client-side errors for this specific form
            if (emailInput) emailInput.classList.remove('error');
            if (emailError) emailError.textContent = '';
            clearMessages(); // Clear any existing PHP messages

            // Email validation
            if (emailInput) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    emailInput.classList.add('error');
                    if (emailError) emailError.textContent = 'Please enter a valid email address.';
                    isValid = false;
                }
            }
            if (!isValid) {
                e.preventDefault();
                // Stay on forgot password form - don't change currentForm
                displayMessages(); // Ensure messages are shown and timer starts
            }
        });
    }

    // Registration Form Validation (with full password validation)
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const usernameInput = document.getElementById('regUser');
            const emailInput = document.getElementById('regEmail');
            const passwordInput = document.getElementById('regPass');

            const userError = document.getElementById('regUserError');
            const emailError = document.getElementById('regEmailError');
            const passError = document.getElementById('regPassError');

            let isValid = true;

            // Reset client-side errors for this specific form
            if (usernameInput) usernameInput.classList.remove('error');
            if (emailInput) emailInput.classList.remove('error');
            if (passwordInput) passwordInput.classList.remove('error');
            if (userError) userError.textContent = '';
            if (emailError) emailError.textContent = '';
            if (passError) passError.textContent = '';

            clearMessages(); // Clear any existing PHP messages before displaying client-side errors

            // Username validation
            if (usernameInput) {
                const nameRegex = /^[A-Za-z0-9_]{3,20}$/;
                if (!nameRegex.test(usernameInput.value.trim())) {
                    usernameInput.classList.add('error');
                    if (userError) userError.textContent = 'Username must be 3-20 characters (letters, numbers, or underscores).';
                    isValid = false;
                }
            }

            // Email validation
            if (emailInput) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    emailInput.classList.add('error');
                    if (emailError) emailError.textContent = 'Please enter a valid email address.';
                    isValid = false;
                }
            }

            // Password validation (matching PHP rules)
            if (passwordInput) {
                const passwordValue = passwordInput.value;
                if (passwordValue.length === 0) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password cannot be empty.';
                    isValid = false;
                } else if (passwordValue.length < 8) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password must be at least 8 characters.';
                    isValid = false;
                } else if (!/[A-Z]/.test(passwordValue)) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password needs at least one uppercase letter.';
                    isValid = false;
                } else if (!/[a-z]/.test(passwordValue)) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password needs at least one lowercase letter.';
                    isValid = false;
                } else if (!/[0-9]/.test(passwordValue)) {
                    passwordInput.classList.add('error');
                    if (passError) passError.textContent = 'Password needs at least one number.';
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
                // IMPORTANT: Stay on register form - don't change currentForm
                // Make sure we're showing the register form
                if (currentForm !== 'register') {
                    currentForm = 'register';
                    container.classList.add('active');
                    loginForm.style.display = 'none';
                    forgotPasswordForm.style.display = 'none';
                    registerForm.style.display = 'flex';
                }
                displayMessages(); // Ensure messages are shown and timer starts
            }
        });
    }
});