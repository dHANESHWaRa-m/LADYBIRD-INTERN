<?php
session_start();

$message = '';
$error_message = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login/Signup Form</title>
    <link rel="stylesheet" href="../assests/css/stylenew.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="formbox login">
            <form action="../auth/login.php" method="POST" class="login-form">
                <h1>Login</h1>
                <?php
                // Display error message only if specifically for login form or no form param
                if (!empty($error_message) && (!isset($_GET['form']) || $_GET['form'] === 'login' || $_GET['form'] === '')):
                ?>
                    <p class="form-error-message"><?php echo htmlspecialchars($error_message); ?></p>
                <?php
                endif;
                // Display success message only if specifically for login form or no form param
                if (!empty($message) && (!isset($_GET['form']) || $_GET['form'] === 'login' || $_GET['form'] === '')):
                ?>
                    <p class="form-success-message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="text" id="user" name="email" placeholder="Email" required />
                        <i class="bx bxs-envelope"></i>
                    </div>
                    <span class="error-text" id="userError"></span>
                </div>
                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="password" id="pass" name="password" placeholder="Password" required />
                        <i class="bx bxs-lock-alt"></i>
                    </div>
                    <span class="error-text" id="passError"></span>
                </div>

                <div class="forget">
                    <a href="#" id="forgotPasswordLink">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <p>or login with social platforms</p>

                <div class="social-login-btn">
                    <a href="#" class="google-btn">
                        <i class="bx bxl-google"></i>
                        <span>Sign in with Google</span>
                    </a>
                </div>
            </form>

            <form action="../auth/send_password_reset.php" method="POST" class="forget-password-form">
                <h1>Reset Password</h1>
                <p>Enter your email address and we'll send you a link to reset your password.</p>

                <?php
                // Display error message only if specifically for forgot-password form
                if (!empty($error_message) && isset($_GET['form']) && $_GET['form'] === 'forgot-password'):
                ?>
                    <p class="form-error-message"><?php echo htmlspecialchars($error_message); ?></p>
                <?php
                endif;
                // Display success message only if specifically for forgot-password form
                if (!empty($message) && isset($_GET['form']) && $_GET['form'] === 'forgot-password'):
                ?>
                    <p class="form-success-message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="email" id="forgotEmail" name="forgot_email" placeholder="Enter your email" required />
                        <i class="bx bxs-envelope"></i>
                    </div>
                    <span class="error-text" id="forgotEmailError"></span>
                </div>

                <button type="submit" class="btn">Send Reset Link</button>
                <div class="back-to-login">
                    <a href="#" id="backToLogin">
                        <i class="bx bx-arrow-back"></i>
                        Back to Login
                    </a>
                </div>
            </form>
        </div>

        <div class="formbox register">
            <form action="../auth/register.php" method="POST">
                <h1>Registration</h1>
                <?php
                // Display error message only if specifically for register form
                if (!empty($error_message) && isset($_GET['form']) && $_GET['form'] === 'register'):
                ?>
                    <p class="form-error-message"><?php echo htmlspecialchars($error_message); ?></p>
                <?php
                endif;
                // Display success message only if specifically for register form
                if (!empty($message) && isset($_GET['form']) && $_GET['form'] === 'register'):
                ?>
                    <p class="form-success-message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="text" id="regUser" name="username" placeholder="Username" required />
                        <i class="bx bxs-user"></i>
                    </div>
                    <span class="error-text" id="regUserError"></span>
                </div>
                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="email" id="regEmail" name="email" placeholder="Email" required />
                        <i class="bx bxs-envelope"></i>
                    </div>
                    <span class="error-text" id="regEmailError"></span>
                </div>
                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="password" id="regPass" name="password" placeholder="Password" required />
                        <i class="bx bxs-lock-alt"></i>
                    </div>
                    <span class="error-text" id="regPassError"></span>
                </div>

                <button type="submit" class="btn">Register</button>
                <p>or register with social platforms</p>

                <div class="social-login-btn">
                    <a href="#" class="google-btn">
                        <i class="bx bxl-google"></i>
                        <span>Sign up with Google</span>
                    </a>
                </div>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script src="../assests/js/script.js"></script>
</body>
</html>