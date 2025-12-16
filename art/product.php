<?php
session_start();
require_once "includes/db.php";

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT artworks.*, categories.name as category_name FROM artworks JOIN categories ON artworks.category_id = categories.id WHERE artworks.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$artResult = $stmt->get_result();
$art = $artResult->fetch_assoc();

if (!$art) {
    die("Artwork not found.");
}

// Recommendations: similar category
$stmt = $conn->prepare("SELECT * FROM artworks WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 4");
$stmt->bind_param("ii", $art['category_id'], $id);
$stmt->execute();
$recResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($art['title']) ?> | ArtfyCanvas</title>
    <meta name="description" content="<?= htmlspecialchars(substr($art['description'], 0, 160)) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <div class="logo">
        <i class="fas fa-palette"></i>
        ArtfyCanvas
    </div>
    <nav>
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="shop.php"><i class="fas fa-store"></i> Shop</a>
        <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
        <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </nav>

    <!-- Mobile Menu Toggle -->
    <div class="menu-toggle" onclick="toggleMobileMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="close-menu" onclick="toggleMobileMenu()">&times;</div>
        <a href="index.php" onclick="toggleMobileMenu()"><i class="fas fa-home"></i> Home</a>
        <a href="shop.php" onclick="toggleMobileMenu()"><i class="fas fa-store"></i> Shop</a>
        <a href="about.php" onclick="toggleMobileMenu()"><i class="fas fa-info-circle"></i> About</a>
        <a href="contact.php" onclick="toggleMobileMenu()"><i class="fas fa-envelope"></i> Contact</a>

        <?php if(isset($_SESSION['user'])): ?>
            <a href="cart.php" onclick="toggleMobileMenu()"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="logout.php" onclick="toggleMobileMenu()"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php" onclick="toggleMobileMenu()"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </div>
</header>

<section class="product-section">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Home</a> >
        <a href="shop.php">Shop</a> >
        <span><?= htmlspecialchars($art['title']) ?></span>
    </div>

    <div class="product-container">
        <div class="product-gallery">
            <div class="product-image">
                <?php
                $imagePath = "assets/images/" . $art['image'];
                if (!file_exists($imagePath)) {
                    $imagePath = "assets/images/default.jpg";
                }
                ?>
                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($art['title']) ?>" id="main-image">
            </div>
        </div>

        <div class="product-details">
            <div class="product-header">
                <h1><?= htmlspecialchars($art['title']) ?></h1>
                <div class="product-meta">
                    <span class="artist-info">
                        <i class="fas fa-user"></i>
                        By <strong><?= htmlspecialchars($art['artist']) ?></strong>
                    </span>
                    <span class="category-badge">
                        <i class="fas fa-tag"></i>
                        <?= htmlspecialchars($art['category_name']) ?>
                    </span>
                </div>
            </div>

            <div class="product-price">
                <span class="current-price">Rs. <?= number_format($art['price'], 2) ?></span>
            </div>

            <div class="product-description">
                <h3>About This Artwork</h3>
                <p><?= htmlspecialchars($art['description']) ?></p>
            </div>

            <div class="product-specifications">
                <h3>Specifications</h3>
                <div class="specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">Artist:</span>
                        <span class="spec-value"><?= htmlspecialchars($art['artist']) ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Category:</span>
                        <span class="spec-value"><?= htmlspecialchars($art['category_name']) ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Medium:</span>
                        <span class="spec-value">Oil on Canvas</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Year:</span>
                        <span class="spec-value">2024</span>
                    </div>
                </div>
            </div>

            <div class="purchase-section">
                <div class="quantity-selector">
                    <label for="quantity">Quantity:</label>
                    <div class="quantity-controls">
                        <button type="button" class="qty-btn" onclick="changeQuantity(-1)">-</button>
                        <input type="number" id="quantity" name="qty" value="1" min="1" max="10" readonly>
                        <button type="button" class="qty-btn" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>

                <form action="actions/add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="artwork_id" value="<?= $art['id'] ?>">
                    <input type="hidden" name="qty" value="1" id="hidden-qty">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                    </button>
                </form>

                <div class="action-buttons">
                    <button class="btn-secondary" onclick="toggleWishlist()">
                        <i class="far fa-heart"></i>
                        Add to Wishlist
                    </button>
                    <button class="btn-secondary" onclick="shareProduct()">
                        <i class="fas fa-share"></i>
                        Share
                    </button>
                </div>
            </div>

            <div class="product-features">
                <div class="feature">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Free Shipping</span>
                </div>
                <div class="feature">
                    <i class="fas fa-undo"></i>
                    <span>30-Day Returns</span>
                </div>
                <div class="feature">
                    <i class="fas fa-certificate"></i>
                    <span>Certificate of Authenticity</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="related-products">
        <div class="section-header">
            <h2>You Might Also Like</h2>
            <p>Discover more beautiful artworks from our collection</p>
        </div>
        <div class="art-grid">
            <?php while ($rec = $recResult->fetch_assoc()):
                $recImagePath = "assets/images/" . $rec['image'];
                if (!file_exists($recImagePath)) {
                    $recImagePath = "assets/images/default.jpg";
                }
            ?>
                <div class="art-card">
                    <div class="art-image">
                        <img src="<?= $recImagePath ?>" alt="<?= htmlspecialchars($rec['title']) ?>">
                        <div class="art-overlay">
                            <a href="product.php?id=<?= $rec['id'] ?>" class="view-details-btn">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                    <div class="art-info">
                        <h3>
                            <a href="product.php?id=<?= $rec['id'] ?>">
                                <?= htmlspecialchars($rec['title']) ?>
                            </a>
                        </h3>
                        <p class="artist">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($rec['artist']) ?>
                        </p>
                        <div class="art-footer">
                            <p class="price">Rs. <?= number_format($rec['price'], 2) ?></p>
                            <form action="actions/add_to_cart.php" method="POST" class="quick-add">
                                <input type="hidden" name="artwork_id" value="<?= $rec['id'] ?>">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="quick-add-btn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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

<script>
function changeQuantity(delta) {
    const qtyInput = document.getElementById('quantity');
    const hiddenQty = document.getElementById('hidden-qty');
    let currentQty = parseInt(qtyInput.value);
    currentQty += delta;
    if (currentQty >= 1 && currentQty <= 10) {
        qtyInput.value = currentQty;
        hiddenQty.value = currentQty;
    }
}

function toggleWishlist() {
    // Placeholder for wishlist functionality
    alert('Wishlist feature coming soon!');
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?= htmlspecialchars($art['title']) ?>',
            text: 'Check out this beautiful artwork by <?= htmlspecialchars($art['artist']) ?>',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

// Update hidden quantity when input changes
document.getElementById('quantity').addEventListener('change', function() {
    document.getElementById('hidden-qty').value = this.value;
});

// Mobile Menu Functions
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const menuToggle = document.querySelector('.menu-toggle');

    if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('active');
        menuToggle.innerHTML = '<span></span><span></span><span></span>';
    } else {
        mobileMenu.classList.add('active');
        menuToggle.innerHTML = '<span style="transform: rotate(45deg) translate(5px, 5px);"></span><span style="opacity: 0;"></span><span style="transform: rotate(-45deg) translate(7px, -6px);"></span>';
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const mobileMenu = document.getElementById('mobileMenu');
    const menuToggle = document.querySelector('.menu-toggle');
    const navbar = document.querySelector('.navbar');

    if (!navbar.contains(event.target) && mobileMenu.classList.contains('active')) {
        toggleMobileMenu();
    }
});

// Close mobile menu on window resize if desktop size
window.addEventListener('resize', function() {
    const mobileMenu = document.getElementById('mobileMenu');
    if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
        toggleMobileMenu();
    }
});
</script>

</body>
</html>
