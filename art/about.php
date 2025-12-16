<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | ArtfyCanvas</title>
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

<section class="about-hero">
    <div class="hero-content">
        <h1>About ArtfyCanvas</h1>
        <p>Connecting Artists with Art Lovers Worldwide</p>
    </div>
</section>

<section class="about-section">
    <div class="about-intro">
        <h2>Our Story</h2>
        <p>Founded in 2020, ArtfyCanvas emerged from a simple belief: everyone deserves to experience the transformative power of original art. What started as a small online gallery has grown into a thriving community of artists, collectors, and art enthusiasts from around the globe.</p>
        <p>We curate exceptional artworks from emerging and established artists, ensuring each piece tells a unique story and brings lasting beauty to your space.</p>
    </div>
    
    <div class="stats-section">
        <div class="stat">
            <h3>500+</h3>
            <p>Artists Featured</p>
        </div>
        <div class="stat">
            <h3>10,000+</h3>
            <p>Artworks Sold</p>
        </div>
        <div class="stat">
            <h3>50+</h3>
            <p>Countries Reached</p>
        </div>
        <div class="stat">
            <h3>4.9/5</h3>
            <p>Customer Rating</p>
        </div>
    </div>
</section>

<section class="mission-vision">
    <div class="container">
        <div class="mission">
            <h2>Our Mission</h2>
            <p>To democratize art ownership by making original artworks accessible to everyone, while providing a platform for emerging and established artists to showcase their creativity and reach a global audience.</p>
        </div>
        <div class="vision">
            <h2>Our Vision</h2>
            <p>A world where art is an integral part of every home and workplace, fostering creativity, cultural appreciation, and emotional connection through beautiful, meaningful artworks.</p>
        </div>
    </div>
</section>

<section class="why-choose-us">
    <h2>Why Choose ArtfyCanvas?</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🎨</div>
            <h3>Original Artworks</h3>
            <p>Every piece is handcrafted by talented artists, ensuring authenticity and uniqueness.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👨‍🎨</div>
            <h3>Support Local Artists</h3>
            <p>Directly support independent artists and help them build sustainable creative careers.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🚚</div>
            <h3>Worldwide Shipping</h3>
            <p>Careful packaging and global shipping to ensure your artwork arrives safely.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💳</div>
            <h3>Secure Payments</h3>
            <p>Safe and secure payment processing with multiple payment options.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Authenticity Guarantee</h3>
            <p>Every artwork comes with a certificate of authenticity and provenance.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📞</div>
            <h3>24/7 Customer Support</h3>
            <p>Dedicated support team ready to assist with any questions or concerns.</p>
        </div>
    </div>
</section>

<section class="team-section">
    <h2>Meet Our Team</h2>
    <p>The passionate individuals behind ArtfyCanvas who make it all possible.</p>
    <div class="team">
        <div class="team-member">
            <div class="member-avatar">👩‍🎨</div>
            <h4>Sarah Johnson</h4>
            <p class="member-role">Founder & Chief Art Curator</p>
            <p class="member-bio">With over 15 years in the art world, Sarah curates our collection and ensures every piece meets our standards of excellence.</p>
        </div>
        <div class="team-member">
            <div class="member-avatar">👨‍💼</div>
            <h4>Mike Chen</h4>
            <p class="member-role">Operations Manager</p>
            <p class="member-bio">Mike oversees our logistics and ensures smooth operations from artist onboarding to customer delivery.</p>
        </div>
        <div class="team-member">
            <div class="member-avatar">👩‍💻</div>
            <h4>Emily Davis</h4>
            <p class="member-role">Lead Developer</p>
            <p class="member-bio">Emily builds and maintains our platform, ensuring a seamless experience for artists and collectors alike.</p>
        </div>
        <div class="team-member">
            <div class="member-avatar">🎨</div>
            <h4>Alex Rodriguez</h4>
            <p class="member-role">Artist Relations</p>
            <p class="member-bio">Alex works directly with artists to help them showcase their work and grow their careers on our platform.</p>
        </div>
    </div>
</section>

<section class="testimonials">
    <h2>What Our Customers Say</h2>
    <div class="testimonial-grid">
        <div class="testimonial">
            <p>"ArtfyCanvas helped me discover incredible artists I never would have found otherwise. The quality and customer service are outstanding."</p>
            <cite>- Jennifer M., New York</cite>
        </div>
        <div class="testimonial">
            <p>"As an artist, ArtfyCanvas has been instrumental in connecting me with collectors worldwide. Their platform is professional and supportive."</p>
            <cite>- David K., London</cite>
        </div>
        <div class="testimonial">
            <p>"The authenticity guarantee gives me peace of mind when purchasing art. I've bought several pieces and each one has been perfect."</p>
            <cite>- Maria S., Barcelona</cite>
        </div>
    </div>
</section>

<section class="cta-section">
    <h2>Ready to Discover Your Next Favorite Artwork?</h2>
    <p>Join thousands of art lovers who have found their perfect piece through ArtfyCanvas.</p>
    <a href="shop.php" class="btn">Browse Artworks</a>
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