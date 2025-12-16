<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='admin'){
    header("Location: ../login.php"); exit;
}
include '../includes/db.php';

$products = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM artworks"))[0] ?? 0;
$orders = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM orders"))[0] ?? 0;
$users = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM users WHERE role='user'"))[0] ?? 0;
$messages = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM messages"))[0] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | ArtfyCanvas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <h2>Admin Dashboard</h2>

    <div class="admin-stats">
        <div class="stat-card">
            <h3>Products</h3>
            <p><?= $products ?></p>
        </div>
        <div class="stat-card">
            <h3>Orders</h3>
            <p><?= $orders ?></p>
        </div>
        <div class="stat-card">
            <h3>Users</h3>
            <p><?= $users ?></p>
        </div>
        <div class="stat-card">
            <h3>Messages</h3>
            <p><?= $messages ?></p>
        </div>
    </div>

    <div class="admin-content">
        <canvas id="stats" height="100"></canvas>
    </div>
</section>

<script>
new Chart(document.getElementById('stats'),{
 type:'bar',
 data:{
  labels:['Products','Orders','Users','Messages'],
  datasets:[{
    data:[<?= $products ?>,<?= $orders ?>,<?= $users ?>,<?= $messages ?>],
    backgroundColor:['#000','#555','#999','#ccc']
  }]
 }
});
</script>

</body>
</html>
