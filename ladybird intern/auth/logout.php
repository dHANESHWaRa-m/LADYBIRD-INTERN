<?php
// logout.php - Handles user logout

session_start(); // Start the session

// Unset all session variables
$_SESSION = array(); // Clears all data stored in the current session

// Destroy the session cookie
// This part ensures the session ID cookie is properly removed from the user's browser.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, // Sets cookie expiration to a past time
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session data on the server
session_destroy();

// Set a logout message in the session (this will be picked up by index.php)
$_SESSION['message'] = "You have been successfully logged out.";

// Redirect the user back to the login page (index.php)
header('Location: ../public/index.php');
exit(); // Ensure no further code is executed after redirection
?>
