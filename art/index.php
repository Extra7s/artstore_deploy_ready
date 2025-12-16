<?php
session_start();
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ArtfyCanvas | Buy Original Art</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

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

<!-- ================= HERO SECTION ================= -->
<section class="hero">
    <div class="hero-text">
        <h1>Discover & Buy Original Art</h1>
        <p>Support independent artists. Own timeless creativity.</p>
        <a href="#gallery" class="btn">Explore Art</a>
    </div>
</section>

<!-- ================= CATEGORIES ================= -->
<section class="categories">
    <h2>Browse Categories</h2>

    <div class="category-grid">
        <?php
        $catResult = $conn->query("SELECT * FROM categories LIMIT 6");
        while ($cat = $catResult->fetch_assoc()):
        ?>
            <a href="shop.php?category=<?= $cat['id'] ?>" class="category-card">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- ================= ARTWORK GALLERY ================= -->
<section class="gallery" id="gallery">
    <h2>Featured Artworks</h2>

    <div class="art-grid">
        <?php
        $artResult = $conn->query("SELECT * FROM artworks ORDER BY id DESC LIMIT 12");

        while ($art = $artResult->fetch_assoc()):
            $imagePath = "assets/images/" . $art['image'];
            if (!file_exists($imagePath)) {
                $imagePath = "assets/images/default.jpg";
            }
        ?>
            <div class="art-card">
                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($art['title']) ?>">

                <div class="art-info">
                    <h3><?= htmlspecialchars($art['title']) ?></h3>
                    <p class="artist"><?= htmlspecialchars($art['artist']) ?></p>
                    <p class="price">Rs. <?= number_format($art['price'], 2) ?></p>
                    <a href="product.php?id=<?= $art['id'] ?>" class="btn-sm">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- ================= BEST SELLERS ================= -->
<section class="best-sellers">
    <h2>Best Sellers</h2>

    <div class="art-grid">
        <?php
        $bestResult = $conn->query("SELECT * FROM artworks ORDER BY price DESC LIMIT 6");

        while ($art = $bestResult->fetch_assoc()):
            $imagePath = "assets/images/" . $art['image'];
            if (!file_exists($imagePath)) {
                $imagePath = "assets/images/default.jpg";
            }
        ?>
            <div class="art-card">
                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($art['title']) ?>">

                <div class="art-info">
                    <h3><?= htmlspecialchars($art['title']) ?></h3>
                    <p class="artist"><?= htmlspecialchars($art['artist']) ?></p>
                    <p class="price">Rs. <?= number_format($art['price'], 2) ?></p>
                    <a href="product.php?id=<?= $art['id'] ?>" class="btn-sm">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- ================= NEW ARRIVALS ================= -->
<section class="new-arrivals">
    <h2>New Arrivals</h2>

    <div class="art-grid">
        <?php
        $newResult = $conn->query("SELECT * FROM artworks ORDER BY id DESC LIMIT 6");

        while ($art = $newResult->fetch_assoc()):
            $imagePath = "assets/images/" . $art['image'];
            if (!file_exists($imagePath)) {
                $imagePath = "assets/images/default.jpg";
            }
        ?>
            <div class="art-card">
                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($art['title']) ?>">

                <div class="art-info">
                    <h3><?= htmlspecialchars($art['title']) ?></h3>
                    <p class="artist"><?= htmlspecialchars($art['artist']) ?></p>
                    <p class="price">Rs. <?= number_format($art['price'], 2) ?></p>
                    <a href="product.php?id=<?= $art['id'] ?>" class="btn-sm">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- ================= WHY US ================= -->
<section class="why-us">
    <h2>Why Choose ArtfyCanvas?</h2>

    <div class="features">
        <div class="feature">🎨 100% Original Art</div>
        <div class="feature">💳 Secure Khalti Payment</div>
        <div class="feature">🚚 Safe & Fast Delivery</div>
        <div class="feature">⭐ Trusted Artists</div>
        <div class="feature">🔒 Quality Guarantee</div>
        <div class="feature">📞 24/7 Customer Support</div>
    </div>
</section>

<!-- ================= TESTIMONIALS ================= -->
<section class="testimonials">
    <h2>What Our Customers Say</h2>

    <div class="testimonial-grid">
        <div class="testimonial">
            <p>"Amazing collection of art! Found the perfect piece for my living room."</p>
            <cite>- Sarah Johnson</cite>
        </div>
        <div class="testimonial">
            <p>"Fast delivery and excellent quality. Highly recommend ArtfyCanvas."</p>
            <cite>- Michael Chen</cite>
        </div>
        <div class="testimonial">
            <p>"Supporting local artists has never been easier. Love the platform!"</p>
            <cite>- Priya Sharma</cite>
        </div>
    </div>
</section>
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
