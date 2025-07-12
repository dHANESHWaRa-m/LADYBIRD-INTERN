<?php

session_start();
require_once __DIR__ . '/../includes/db_connection.php';

// Debugging - check if token exists
if (!isset($_GET['token']) || empty(trim($_GET['token']))) {
    error_log("Activation Error: No token provided");
    $_SESSION['error_message'] = "Activation token is missing.";
    header('Location: ../public/index.php');
    exit();
}

//Storing the token in an variable
$token = trim($_GET['token']);
$token_hash = hash("sha256", $token);

error_log("Activation Attempt - Token: $token");
error_log("Token Hash: $token_hash");

//Connectng to database
$pdo = getDbConnection();

try {
    
    // 1. Find user with this activation token
    $stmt = $pdo->prepare("SELECT * FROM users WHERE account_activation_hash = ?");
    $stmt->execute([$token_hash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("User found: " . print_r($user, true));

    if (!$user) {
        error_log("Invalid token: No user found with hash $token_hash");
        $_SESSION['error_message'] = "Invalid activation token. Please request a new one.";
        header('Location: ../public/index.php');
        exit();
    }

    // 2. Check if already activated
    if ($user['is_active']) {
        $_SESSION['message'] = "Account is already activated.";
        header('Location: ../public/index.php');
        exit();
    }

    // 3. Activate account
    $updateStmt = $pdo->prepare("UPDATE users SET 
        is_active = 1,
        account_activation_hash = NULL
        WHERE id = ?"); // Removed the extra comma here
    
    if ($updateStmt->execute([$user['id']])) {
        error_log("Successfully activated user ID: " . $user['id']);
        $_SESSION['success_message'] = "Account activated successfully! You can now log in.";
    } else {
        throw new PDOException("Failed to update activation status");
    }

} catch (PDOException $e) {
    error_log("Activation Error: " . $e->getMessage());
    $_SESSION['error_message'] = "Account activation failed. Please try again or contact support.";
}

header('Location: ../public/index.php');
exit();
?>