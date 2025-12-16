<?php
session_start();
require_once "includes/db.php";

$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM artworks WHERE 1";
$params = [];
$types = '';

if ($category > 0) {
    $query .= " AND category_id = ?";
    $params[] = $category;
    $types .= 'i';
}
if ($search) {
    $query .= " AND (title LIKE ? OR artist LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}
$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop | ArtfyCanvas</title>
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

<section class="shop-section">
    <h2>Shop Artworks</h2>

    <!-- Filters -->
    <div class="filters">
        <form method="GET" action="shop.php">
            <select name="category">
                <option value="">All Categories</option>
                <?php
                $catResult = $conn->query("SELECT * FROM categories");
                while ($cat = $catResult->fetch_assoc()):
                ?>
                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="search" placeholder="Search by title or artist" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn">Filter</button>
        </form>
    </div>

    <div class="art-grid">
        <?php while ($art = $result->fetch_assoc()): 
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
