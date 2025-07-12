<?php
session_start(); 


if (!isset($_GET["token"]) || empty($_GET["token"])) {
    $_SESSION['error_message'] = "Password reset token is missing.";
    header('Location: ../public/index.php?form=forgot-password'); // Redirect back to forgot password form
    exit();
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);


require_once __DIR__ . '/../includes/db_connection.php';
$pdo = getDbConnection();

$user = null; 
try {
    // Check if user exists and token matches
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token_hash = ?");
    $stmt->execute([$token_hash]); // Use the hashed token for lookup
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

    // Check if token exists
    if (!$user) {
        $_SESSION['error_message'] = "Invalid or expired password reset token.";
        header('Location: ../public/index.php?form=forgot-password'); // Redirect back to forgot password form
        exit();
    }

    // Check if token has expired
    // Convert the database timestamp to a Unix timestamp for comparison
    $expires_at_timestamp = strtotime($user['reset_token_expires_at']);

    if ($expires_at_timestamp <= time()) {
        $_SESSION['error_message'] = "Password reset token has expired. Please request a new one.";
        header('Location: ../public/index.php?form=forgot-password');
        exit();
    }

    // If we reach this point, the token is valid and not expired.
    // Display the password reset form.

} catch (PDOException $e) {
    error_log("Database error in password reset token validation: " . $e->getMessage());
    $_SESSION['error_message'] = "A system error occurred during token validation. Please try again later.";
    header('Location: ../public/index.php?form=forgot-password');
    exit();
}

// Prepare messages for display (similar to index.php)
$message = '';
$error_message_display = ''; // Use a different variable name to avoid conflict with $_SESSION['error_message']

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message_display = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <link rel="stylesheet" href="../assests/css/reset_password.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="formbox login"> <h1>Reset Your Password</h1>
            <p>Please enter your new password below.</p>

            <?php if (!empty($error_message_display)): ?>
                <p class="form-error-message"><?php echo htmlspecialchars($error_message_display); ?></p>
            <?php endif; ?>
            <?php if (!empty($message)): ?>
                <p class="form-success-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form action="update_password.php" method="post" id="resetPasswordForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="New Password" required />
                        <i class="bx bxs-lock-alt"></i>
                    </div>
                    <span class="error-text" id="passwordError"></span>
                </div>

                <div class="inputbox">
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password" required />
                        <i class="bx bxs-lock-alt"></i>
                    </div>
                    <span class="error-text" id="passwordConfirmationError"></span>
                </div>
                
                <button type="submit" class="btn">Reset Password</button>
            </form>
        </div>
    </div>

    <script src="../assests/js/reset_password.js"></script>
</body>
</html>