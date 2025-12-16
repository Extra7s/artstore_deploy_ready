<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | ArtfyCanvas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
<?php session_start(); ?>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <div class="logo">ArtfyCanvas</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="cart.php">Cart</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<section class="contact-section">
    <h2>Contact Us</h2>
    <p>We'd love to hear from you! Reach out with any questions, feedback, or inquiries about our artworks, artists, or services.</p>
    
    <div class="contact-grid">
        <div>
            <h3>Get In Touch</h3>
            <form method="POST" action="send_message.php">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>

                <button type="submit" class="btn">Send Message</button>
            </form>
            <?php if (isset($_GET['sent'])): ?>
                <p class="message success">Message sent successfully!</p>
            <?php elseif (isset($_GET['error'])): ?>
                <p class="message error">Error sending message. Please try again.</p>
            <?php endif; ?>
        </div>
        
        <div>
            <h3>Contact Information</h3>
            <div class="contact-info">
                <p><strong>Email:</strong> info@artfycanvas.com</p>
                <p><strong>Phone:</strong> +1 (123) 456-7890</p>
                <p><strong>Address:</strong> 123 Art Street, Creative City, CC 12345</p>
                <p><strong>Business Hours:</strong></p>
                <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                <p>Saturday: 10:00 AM - 4:00 PM</p>
                <p>Sunday: Closed</p>
            </div>
            
            <h3>Follow Us</h3>
            <div class="social-links">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Instagram">📷</a>
                <a href="#" title="Twitter">🐦</a>
            </div>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>ArtfyCanvas</h3>
            <p>Discover and buy original art from talented artists around the world.</p>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <a href="index.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </div>
        <div class="footer-section">
            <h3>Support</h3>
            <a href="#">FAQ</a>
            <a href="#">Shipping</a>
            <a href="#">Returns</a>
            <a href="#">Terms of Service</a>
        </div>
        <div class="footer-section">
            <h3>Follow Us</h3>
            <a href="#">Facebook</a>
            <a href="#">Instagram</a>
            <a href="#">Twitter</a>
        </div>
    </div>
    <p>© <?= date("Y") ?> ArtfyCanvas. All Rights Reserved.</p>
</footer>

</body>
</html>