<?php
include 'admin_guard.php';
$stmt = $conn->prepare("
SELECT o.*, u.name FROM orders o
LEFT JOIN users u ON o.user_id = u.id
ORDER BY o.id DESC");
$stmt->execute();
$r = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders - Admin</title>
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
    <h2>Orders</h2>

    <div class="admin-content">
        <?php while($o = $r->fetch_assoc()){ ?>
        <div class="admin-item">
            <h3>Order #<?= $o['id'] ?> - <?= htmlspecialchars($o['name']) ?> - $<?= $o['total'] ?> - <?= $o['status'] ?></h3>
            <p>Date: <?= $o['created_at'] ?></p>
        </div>
        <?php } ?>
    </div>
</section>

</body>
</html>
