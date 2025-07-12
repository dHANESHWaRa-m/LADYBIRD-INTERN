<?php
session_start();
require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/mailer.php'; // Include mailer configuration

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get and Sanitize Input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING); 
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Get raw password for hashing and validation

    $errors = []; // Array to store all validation errors

    // --- Server-Side Validation ---

    // Username Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match("/^[A-Za-z0-9_]{3,20}$/", $username)) {
        $errors[] = "Username must be 3-20 characters (letters, numbers, or underscores).";
    }

    // Email Validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        // Check if Email already exists in the database
        $pdo = getDbConnection(); // Get database connection
        $stmt_check_email = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt_check_email->execute(['email' => $email]);
        if ($stmt_check_email->fetchColumn() > 0) {
            $errors[] = "This email is already registered. Please use a different email or log in.";
        }
    }

    // Password Validation (must be consistent with client-side)
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    // You can add special character validation here if desired:
    // elseif (!preg_match("/[^a-zA-Z0-9]/", $password)) {
    //     $errors[] = "Password must contain at least one special character.";
    // }

    // If there are any validation errors, set session message and redirect
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors); // Join all errors with <br> for display
        header("Location: ../public/index.php?form=register"); // Redirect back to register form
        exit();
    }

    // If no validation errors, proceed with registration
    $activation_token = bin2hex(random_bytes(16));
    $activation_hash = hash("sha256", $activation_token);
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert User Data with activation hash
        $stmt_insert = $pdo->prepare("INSERT INTO users 
            (username, email, password_hash, account_activation_hash) 
            VALUES (:username, :email, :password_hash, :activation_hash)");
        
        $stmt_insert->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $password_hash,
            'activation_hash' => $activation_hash
        ]);

        // Send activation email
        $mail = getMailer();
        $mail->setFrom('no-reply@yourdomain.com', 'Your App Name'); // Change 'Your App Name'
        $mail->addAddress($email);
        $mail->Subject = 'Activate Your Account';
        
        // Corrected email body for account activation
        $mail->Body = <<<END
            Hello $username,
            
            Thank you for registering! Please click the link below to activate your account:
            <a href="http://localhost/ladybird%20intern/auth/activate_account.php?token=$activation_token">Activate Account</a>
            
            If you did not register for this service, please ignore this email.
            
            Regards,
            Your App Team
            END;

        if ($mail->send()) {
            $_SESSION['message'] = "Registration successful! Please check your email to activate your account.";
        } else {
            error_log("Failed to send activation email to $email: " . $mail->ErrorInfo); // Log specific error
            $_SESSION['message'] = "Registration successful, but we couldn't send the activation email. Please contact support.";
        }

        header('Location: ../public/index.php?form=login'); // Redirect to login form on success
        exit();

    } catch (PDOException $e) {
        error_log("Registration DB error: " . $e->getMessage()); // Log detailed DB error
        $_SESSION['error_message'] = "Registration failed due to a server error. Please try again. (Error Code: " . $e->getCode() . ")";
        header('Location: ../public/index.php?form=register'); // Stay on register form for server errors
        exit();
    }
} else {
    // If somehow accessed directly without POST request
    header('Location: ../public/index.php');
    exit();
}
?>