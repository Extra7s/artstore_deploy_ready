<?php
include 'admin_guard.php';

if(isset($_POST['add'])){
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $stmt = $conn->prepare("INSERT INTO artworks(title, artist, price, description, image, category_id) VALUES (?, ?, ?, ?, ?, NULL)");
    $stmt->bind_param("ssdss", $title, $artist, $price, $description, $image);
    $stmt->execute();
}

if(isset($_GET['del'])){
    $del_id = intval($_GET['del']);
    $stmt = $conn->prepare("DELETE FROM artworks WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
}
$stmt = $conn->prepare("SELECT * FROM artworks");
$stmt->execute();
$r = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">ArtfyCanvas - Admin</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="messages.php">Messages</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<section class="admin-section">
    <h2>Manage Products</h2>

    <div class="admin-content">
        <form method="POST" class="admin-form">
        <input name="title" placeholder="Title" required>
        <input name="artist" placeholder="Artist" required>
        <input name="price" placeholder="Price" type="number" step="0.01" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input name="image" placeholder="image.jpg" required>
        <button name="add">Add Product</button>
        </form>
    </div>

    <div class="admin-content">
        <?php while($p = $r->fetch_assoc()){ ?>
        <div class="admin-item">
            <h3><?= htmlspecialchars($p['title']) ?> by <?= htmlspecialchars($p['artist']) ?> - $<?= $p['price'] ?></h3>
            <div class="admin-actions">
                <a href="?del=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

</body>
</html>
