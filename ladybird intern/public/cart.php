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
                <li><a  href="contact.php"> Contact</a></li>
                <li id="lg-bag"><a class="active" href="cart.php
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

<section id="cart" class="section-p1">
    <table width="100%">
        <thead>
            <tr>
                <td>Remove</td>
                <td>Image</td>
                <td>Product</td>
                <td>Price</td>
                <td>Quantity</td>
                <td>Subtotal</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="#"><i class="far fa-times-circle"></i></a></td>
                <td><img src="../img/products/f1.jpg" alt="Cartoon Astronaut T-Shirt"></td>
                <td>Cartoon Astronaut T-Shirts</td>
                <td>Rs 1000</td>
                <td><input type="number" value="1"></td>
                <td>Rs 1000</td>
            </tr>
            <tr>
                <td><a href="#"><i class="far fa-times-circle"></i></a></td>
                <td><img src="../img/products/f2.jpg" alt="Cartoon Astronaut T-Shirt"></td>
                <td>Cartoon Astronaut T-Shirts</td>
                <td>Rs 1000</td>
                <td><input type="number" value="1"></td>
                <td>Rs 1000</td>
            </tr>
            <tr>
                <td><a href="#"><i class="far fa-times-circle"></i></a></td>
                <td><img src="../img/products/f3.jpg" alt="Cartoon Astronaut T-Shirt"></td>
                <td>Cartoon Astronaut T-Shirts</td>
                <td>Rs 1000</td>
                <td><input type="number" value="1"></td>
                <td>Rs 1000</td>
            </tr>
        </tbody>
    </table>
</section>

<section id="cart-add" class="section-p1">
    <div id="coupon">
        <h3>Apply Coupon</h3>
        <div>
            <input type="text" placeholder="Enter Your Coupon">
            <button class="normal">Apply</button>
        </div>
    </div>

    <div id="subtotal">
        <h3>Cart Totals</h3>
        <table>
            <tr>
                <td>Cart Subtotal</td>
                <td>Rs 1000</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td>Free</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>Rs 1000</strong></td>
            </tr>
        </table>
        <button class="normal">Proceed to checkout</button>
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
