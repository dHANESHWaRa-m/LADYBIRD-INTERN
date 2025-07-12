<?php
session_start(); 


$success_redirect_page = '../public/index.php?form=login';

require_once __DIR__ . '/../includes/db_connection.php';


// Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error_message'] = "Invalid request method.";
    // Redirect back to the form if it was not a POST request, or a general error page
    header('Location: ../public/index.php?form=forgot-password');
    exit();
}

// 1. Retrieve and sanitize POST data

$token = trim($_POST["token"] ?? '');
$password = trim($_POST["password"] ?? '');
$password_confirmation = trim($_POST["password_confirmation"] ?? '');

// 2. Basic Validation of form fields
if (empty($token) || empty($password) || empty($password_confirmation)) {
    $_SESSION['error_message'] = "All fields are required.";
    // Redirect back to the reset password form, preserving the token in the URL for continuity
    header('Location: reset_password.php?token=' . urlencode($token));
    exit();
}

if ($password !== $password_confirmation) {
    $_SESSION['error_message'] = "Passwords do not match.";
    header('Location: reset_password.php?token=' . urlencode($token));
    exit();
}

// Implement your password policy (want to check from js)
if (strlen($password) < 8) {
    $_SESSION['error_message'] = "Password must be at least 8 characters long.";
    header('Location: reset_password.php?token=' . urlencode($token));
    exit();
}

$pdo = getDbConnection();

// Hash the token from the form for database lookup 
$token_hash = hash("sha256", $token);

try {
    // 3. Find the user based on the hashed token
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token_hash = ?");
    $stmt->execute([$token_hash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no user found with that token (already used, invalid, or expired on lookup)
    if (!$user) {
        $_SESSION['error_message'] = "Invalid or expired password reset token.";
        header('Location: ../public/index.php?form=forgot-password'); // Redirect to forgot password to request new
        exit();
    }

    // 4. Double-check token expiration (critical: time could pass between form display and submission)
    $expires_at_timestamp = strtotime($user['reset_token_expires_at']);
    if ($expires_at_timestamp <= time()) {
        $_SESSION['error_message'] = "Password reset token has expired. Please request a new one.";
        // IMPORTANT: Invalidate the token here if it's expired but still found, to prevent reuse attempts
        $stmt = $pdo->prepare("UPDATE users SET reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        header('Location: ../public/index.php?form=forgot-password');
        exit();
    }

    // 5. Hash the new password before storing it
    // Use PASSWORD_DEFAULT for the strongest available algorithm (currently bcrypt)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 6. Update the user's password and INVALIDATE the reset token
    // Set reset_token_hash and reset_token_expires_at to NULL to prevent token reuse
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?");
    $stmt->execute([$hashed_password, $user['id']]);

    // 7. Set success message and redirect
    $_SESSION['success_message'] = "Your password has been reset successfully. You can now log in with your new password.";
    header('Location: ' . $success_redirect_page);
    exit();

} catch (PDOException $e) {
    // Log the database error for debugging purposes (never show to users)
    error_log("Database error during password update: " . $e->getMessage());
    $_SESSION['error_message'] = "A system error occurred during password reset. Please try again later.";
    // Fallback redirect for system errors
    header('Location: ../public/index.php?form=forgot-password');
    exit();
}

?>