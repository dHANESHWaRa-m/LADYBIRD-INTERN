<?php
// dashboard.php - Example of a protected page for logged-in users

// Start PHP session
session_start();

// Check if the user is NOT logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['error_message'] = "Please log in to access this page.";
    header('Location: index.php'); // Redirect to login page if not logged in
    exit();
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear message after displaying
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- You can link your style.css here if you want consistent styling -->
    <link rel="stylesheet" href="style.css" />
    <style>
        /* This style block could also be moved to style.css for better organization */
        body {
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .dashboard-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }
        h1 {
            color: #28a745; /* Green for success */
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .dashboard-actions { /* New class for button grouping */
            display: flex;
            justify-content: center;
            gap: 15px; /* Space between buttons */
            margin-top: 25px;
        }
        .action-btn { /* General style for action buttons */
            background-color: #007bff; /* Blue for general actions */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .action-btn:hover {
            background-color: #0056b3;
        }
        .logout-btn { /* Red for logout */
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .message {
            color: green;
            margin-bottom: 15px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome to your Dashboard!</h1>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <p>Hello, **<?php echo htmlspecialchars($username); ?>**!</p>
        <p>Your User ID: <?php echo htmlspecialchars($user_id); ?></p>
        <p>You are now logged in.</p>
        <div class="dashboard-actions">
            <a href="home.html" class="action-btn">Go to Home</a> <!-- New button/link to home.html -->
            <a href="logout.php" class="action-btn logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>
