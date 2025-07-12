<?php

require_once __DIR__ . '/../auth/mailer.php'; 

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = filter_input(INPUT_POST, 'visitor_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'visitor_email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'email_subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'visitor_message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        
        $your_business_email = 'dhaneshwara001@gmail.com'; // **CHANGE THIS to your actual email address**

        $email_subject_for_you = "New Contact Form Submission: " . $subject;
        $email_body_for_you = "You have received a new message from your website contact form.\n\n"
                            . "Name: " . $name . "\n"
                            . "Email: " . $email . "\n"
                            . "Subject: " . $subject . "\n\n"
                            . "Message:\n" . $message;

        try {
           
           $mail = getMailer(); 
            $mail->addAddress($your_business_email);            
            $mail->addReplyTo($email, $name);
            $mail->isHTML(false);                               
            $mail->Subject = $email_subject_for_you;            
            $mail->Body    = $email_body_for_you;              

            $mail->send();
            $success_message = "Your message has been sent successfully!";
            $_POST = array(); 

        } catch (Exception $e) {
            
            $error_message = "Message could not be sent. Technical error: {$e->getMessage()}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Ecommerce webpage</title>
    <link rel="stylesheet" href="../assests/css/homepage.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <section id="header">
        <a href="#"><img src="logo.png" class="logo" alt=""></a>
        <div>
            <ul id="navbar">
                <li><a href="home.php"><i class="fa-solid fa-house-chimney"></i> Home</a></li>
                <li><a  href="shop.php">Shop</a></li>
                <li><a href="blog.php"> Blog</a></li>
                <li><a  href="about.php"> About</a></li>
                <li><a class="active" href="#"> Contact</a></li>
                <li id="lg-bag"><a href="cart.php
                "><i class="fa-solid fa-bag-shopping"></i> </a></li>
                <a href="#" id="close"><i class="fa-solid fa-xmark"></i></a>
            </ul>
        </div>
        <div id="mobile">
            
            <a href="cart.html"><i class="fa-solid fa-bag-shopping"></i> </a>
            <i id="bar" class="fas fa-outdent"></i>
        </div>
    </section>
<section id="page-header" class="about-header">
        
        <h2>#lets talk</h2>
       
        <p>LEAVE A MESSAGE, We love to hear from you!</p>
         

</section>

<section id="contact-details" class="section-p1">
    <div class="details">
  <span>GET IN TOUCH</span>
  <h2>Visit one of our agency locations or contact us today</h2>
  <h3>Head Office</h3>
  <div>
    <li>
      <i class="fal fa-map"></i>
      <p>56 Glassford Street Glasgow G1 1UL New York</p>
    </li>
    <li>
      <i class="far fa-envelope"></i>
      <p>contact@example.com</p>
    </li>
    <li>
      <i class="fas fa-phone-alt"></i>
      <p>contact@example.com</p>
    </li>
    <li>
      <i class="far fa-clock"></i>
      <p>Monday to Saturday: 9.00am to 16.pm</p>
    </li>
  </div>
</div>
<div class="map" >
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62412.53846936596!2d78.1102839860181!3d12.126949957534878!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bac16f95a63ed01%3A0x3f2cb64e61c93aef!2sDharmapuri%2C%20Tamil%20Nadu!5e0!3m2!1sen!2sin!4v1751964354108!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

</div>
</section>

<section id="form-details">
    <form action="contact.php" method="POST">
      <span>LEAVE A MESSAGE</span>
      <h2>We love to hear from you</h2>
      <?php if ($success_message): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

<input type="text" placeholder="Your Name" name="visitor_name" value="<?php echo htmlspecialchars($_POST['visitor_name'] ?? ''); ?>">
            <input type="email" placeholder="E-mail" name="visitor_email" value="<?php echo htmlspecialchars($_POST['visitor_email'] ?? ''); ?>">
            <input type="text" placeholder="Subject" name="email_subject" value="<?php echo htmlspecialchars($_POST['email_subject'] ?? ''); ?>">
            <textarea cols="30" rows="10" placeholder="Your Message" name="visitor_message"><?php echo htmlspecialchars($_POST['visitor_message'] ?? ''); ?></textarea>
            <button class="normal" type="submit">Submit</button>
    </form>

    <div class="people">
      <div>
        <img src="../img/people/1.png" alt="Member 1" />
        <p><span>John Doe</span>Senior Marketing Manager<br/>Phone: +44 123 456 7890<br/>Email: john@ecom.com</p>
      </div>
      <div>
        <img src="../img/people/2.png" alt="Member 2" />
        <p><span>Jane Smith</span>Product Designer<br/>Phone: +44 987 654 3210<br/>Email: jane@ecom.com</p>
      </div>
      <div>
        <img src="../img/people/3.png" alt="Member 3" />
        <p><span>Mike Lee</span>Customer Support<br/>Phone: +44 345 678 1234<br/>Email: mike@ecom.com</p>
      </div>
    </div>
  </section>




<section id="newsletter" class="section-p1 section-m1">
    <div class="newstext">
    <h4>Sign Up for newsletters</h4>
    
    <p> Get E-mail updates about our latest shop and <span>special offers</span> special offers.
    </p>
    </div>
    <div class="form">
        <input type="text" placeholder="Your email address">
<a href="index.php"> <button class="normal">Sign Up</button></a>
    </div>

</section>










<footer class="section-p1">
    <div class="col">
        <img class="logo" src="/ladybird intern/img/banner/logo.jpg" alt="">
        <h4>Contact</h4>
        <p><strong>Address:</strong>666 XYZ Nagar, ABC street, Chennai, TamilNadu</p>
        <p><strong>Phone: </strong> +91 9876543210</p>
        <p><strong>Hours: </strong> 09.00 - 20.00, Mon - Sat</p>
        <div class="follow">
            <h4> Follow us</h4>
            <div class="icon">
                <i class="fab fa-facebook-f"></i>
                <i class="fab fa-twitter"></i>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-pinterest-p"></i>
                <i class="fab fa-youtube"></i>

            </div>
        </div>

    </div>
    <div class="col">
        <h4>About</h4>
        <a href="about.php">About us</a>
        <a href="#">Delivery Information</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms & Conditions</a>
        <a href="contact.php">Contact us</a>
    </div>
    <div class="col">
        <h4>My account</h4>
        <a href="index.php">Sign In</a>
        <a href="cart.php">View Cart</a>
        <a href="#">My wishlist</a>
        <a href="#">Track my order</a>
        <a href="contact.php">Help</a>
    </div>
        <div class="col install">
        <h4>Install App</h4>
        <p>From App Store or Google Play</p>
        <div class="row">
            <img src="/ladybird intern/img/pay/app.jpg" alt="">
            <img src="/ladybird intern/img/pay/play.jpg" alt="">
        </div>
        <p>Secured Payment Gateways</p>
        <img src="/ladybird intern/img/pay/pay.png" alt="">
        
    </div>








    <div class="copyright">
    <p> Copy right</p>
</div>
</footer>
<script src="../assests/js/homepage.js"></script>

</body>
</html>
