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
    <link rel="stylesheet" href="assets/css/style.css">
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

<section class="product-section">
    <div class="product-container">
        <div class="product-image">
            <?php
            $imagePath = "assets/images/" . $art['image'];
            if (!file_exists($imagePath)) {
                $imagePath = "assets/images/default.jpg";
            }
            ?>
            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($art['title']) ?>">
        </div>
        <div class="product-details">
            <h2><?= htmlspecialchars($art['title']) ?></h2>
            <p class="artist">By <?= htmlspecialchars($art['artist']) ?></p>
            <p class="category">Category: <?= htmlspecialchars($art['category_name']) ?></p>
            <p class="description"><?= htmlspecialchars($art['description']) ?></p>
            <p class="price">Rs. <?= number_format($art['price'], 2) ?></p>
            <form action="actions/add_to_cart.php" method="POST">
                <input type="hidden" name="artwork_id" value="<?= $art['id'] ?>">
                <input type="number" name="qty" value="1" min="1" required>
                <button type="submit" class="btn">Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="recommendations">
        <h3>You Might Also Like</h3>
        <div class="art-grid">
            <?php while ($rec = $recResult->fetch_assoc()): 
                $recImagePath = "assets/images/" . $rec['image'];
                if (!file_exists($recImagePath)) {
                    $recImagePath = "assets/images/default.jpg";
                }
            ?>
                <div class="art-card">
                    <img src="<?= $recImagePath ?>" alt="<?= htmlspecialchars($rec['title']) ?>">
                    <div class="art-info">
                        <h3><?= htmlspecialchars($rec['title']) ?></h3>
                        <p class="artist"><?= htmlspecialchars($rec['artist']) ?></p>
                        <p class="price">Rs. <?= number_format($rec['price'], 2) ?></p>
                        <a href="product.php?id=<?= $rec['id'] ?>" class="btn-sm">View Details</a>
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

</body>
</html>
