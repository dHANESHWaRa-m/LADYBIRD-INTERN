<?php
session_start();

// --- Function to connect to the database (reusable) ---
require_once __DIR__ . '/../includes/db_connection.php'; // Ensure this path is correct for your setup

// --- Process Login Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get Input
    $email_input = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? ''; // Use null coalescing to prevent undefined index notice if 'password' isn't set

    // Basic Validation for empty fields
    if (empty($email_input) || empty($password)) {
        $_SESSION['error_message'] = "Please enter both email and password.";
        header('Location: ../public/index.php?form=login');
        exit();
    }

    // Further validate email format (client-side already does this, but good for server-side too)
    if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Please enter a valid email address.";
        header('Location: ../public/index.php?form=login');
        exit();
    }

    $pdo = getDbConnection(); // Get your PDO connection

    try {
        // 2. Fetch User from Database by Email
        $stmt = $pdo->prepare("SELECT id, username, email, password_hash, user_type, is_active FROM users WHERE email = :email");
        $stmt->execute(['email' => $email_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array for easier access

        // --- Start of refined error handling ---
        if (!$user) {
            // User not found in database (email not registered)
            $_SESSION['error_message'] = "Email is not registered.";
            header('Location: ../public/index.php?form=login');
            exit();
        }

        // User found, now verify password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, now check if active
            if ($user['is_active']) {
                // Login Successful!
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['loggedin'] = true;
                $_SESSION['user_type'] = $user['user_type']; // Store the actual user_type

                // Set a welcome message in the session
                $_SESSION['message'] = "Welcome, " . htmlspecialchars($user['username']) . "!";

                // Conditional redirection based on user_type
                if ($_SESSION['user_type'] === 'admin') {
                    header('Location: ../public/admin_dashboard.php'); // Redirect to admin dashboard
                } else {
                    header('Location: ../public/home.php'); // Redirect to regular user home page
                }
                exit(); // Ensure no further code is executed after redirection
            } else {
                // Account is not active
                $_SESSION['error_message'] = "Your account is not active. Please contact support.";
                header('Location: ../public/index.php?form=login');
                exit();
            }
        } else {
            // Password incorrect for the found user
            $_SESSION['error_message'] = "Incorrect password.";
            header('Location: ../public/index.php?form=login');
            exit();
        }
        // --- End of refined error handling ---

    } catch (PDOException $e) {
        // Log the actual error for debugging, but show a generic message to the user
        error_log("Login database error: " . $e->getMessage()); // Logs to your PHP error log
        $_SESSION['error_message'] = "Login failed due to a server error. Please try again later.";
        header('Location: ../public/index.php?form=login');
        exit();
    }
} else {
    // If accessed directly without POST request, redirect to login page with an optional message
    $_SESSION['error_message'] = "Access denied. Please use the login form.";
    header('Location: ../public/index.php?form=login');
    exit();
}
?>