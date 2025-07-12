<?php
function getDbConnection() {
    // These variables are defined within the function scope.
    // Ensure these match your XAMPP MySQL/MariaDB setup.
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "user_login_data"; // This should be your user database name

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        $_SESSION['error_message'] = "Database connection error. Please try again later.";
        header('Location: ../public/index.php'); 
        exit();
    }
}
?>