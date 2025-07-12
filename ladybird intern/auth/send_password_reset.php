<?php
session_start();
require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/mailer.php'; // Include mailer configuration

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Input Validation
    $email = filter_input(INPUT_POST, 'forgot_email', FILTER_SANITIZE_EMAIL);
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Please enter a valid email address.";
        header('Location: ../public/index.php?form=forgot-password');
        exit();
    }

    // 2. Generate Secure Token
    $token = bin2hex(random_bytes(32)); // More secure 64-character token
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 1800); // 30 minute expiration

    // 3. Database Operations
    $pdo = getDbConnection();
    
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Always show success message (security best practice)
        $_SESSION['message'] = "If an account exists with this email, you will receive a password reset link.";

        if ($user) {
            // Update user record
            $updateStmt = $pdo->prepare("UPDATE users SET 
                reset_token_hash = ?,
                reset_token_expires_at = ?
                WHERE email = ?");
            
            $updateStmt->execute([$token_hash, $expiry, $email]);

            // 4. Send Email
            $mail = getMailer(); // From your mailer.php
            $mail->setFrom('no-reply@yourdomain.com', 'Your App Name');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Request';
            
            // Secure reset link - use HTTPS in production!

            
            $mail->Body = <<<END
                Click <a href="http://localhost/ladybird%20intern/auth/reset_password.php?token=$token">here</a> to reset your password.
                 
            
            END;

            if (!$mail->send()) {
                error_log("Failed to send password reset email to $email");
                // Don't show mail error to user (security)
            }
        }

        header('Location: ../public/index.php?form=forgot-password');
        exit();

    } catch (PDOException $e) {
        error_log("Database error in password reset: " . $e->getMessage());
        $_SESSION['error_message'] = "A system error occurred. Please try again later.";
        header('Location: ../public/index.php?form=forgot-password');
        exit();
    }
} else {
    // Direct access prevention
    header('Location: ../public/index.php');
    exit();
}
?>