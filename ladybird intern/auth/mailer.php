<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
    
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dhaneshwara001@gmail.com'; // Use a dedicated email
        $mail->Password = 'xzyk gmzi ojsy xncy'; // Use App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Default settings
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@yourdomain.com', 'Your App Name');
        
        return $mail;
    } catch (Exception $e) {
        error_log("Mailer configuration failed: " . $e->getMessage());
        throw new Exception("Email service unavailable");
    }
}