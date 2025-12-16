<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$cart_items = [];
$total = 0;

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $stmt = $conn->prepare("SELECT c.quantity, a.* FROM cart c JOIN artworks a ON c.artwork_id = a.id WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $qty = $row['quantity'];
        $subtotal = $row['price'] * $qty;
        $total += $subtotal;
        $cart_items[] = ['artwork' => $row, 'qty' => $qty, 'subtotal' => $subtotal];
    }
} else {
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $art = $res->fetch_assoc();
        $subtotal = $art['price'] * $qty;
        $total += $subtotal;
        $cart_items[] = ['artwork' => $art, 'qty' => $qty, 'subtotal' => $subtotal];
    }
}

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];

    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, address, phone, payment_method, payment_status, created_at) VALUES (?, ?, ?, ?, 'khalti', 'pending', NOW())");
    $stmt->bind_param("idsss", $user_id, $total, $address, $phone);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert order items
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, artwork_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['artwork']['id'], $item['qty'], $item['artwork']['price']);
        $stmt->execute();
    }

    // Store order details in session for Khalti callback
    $_SESSION['pending_order'] = [
        'order_id' => $order_id,
        'total' => $total,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $phone
    ];

    // Redirect to Khalti payment initiation
    header("Location: payment.php?order_id=$order_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | ArtfyCanvas</title>
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
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="cart.php">Cart</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<section class="checkout-section">
    <h2>Checkout</h2>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <div class="checkout-container">
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <img src="assets/images/<?= $item['artwork']['image'] ?>" alt="<?= $item['artwork']['title'] ?>">
                    <div class="order-item-details">
                        <h4><?= $item['artwork']['title'] ?></h4>
                        <p>Artist: <?= $item['artwork']['artist'] ?></p>
                        <p>Quantity: <?= $item['qty'] ?></p>
                        <p>$<?= number_format($item['subtotal'], 2) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                <p>Total: <strong>$<?= number_format($total, 2) ?></strong></p>
            </div>
        </div>

        <div class="shipping-form">
            <h3>Shipping & Payment Information</h3>
            <form method="POST">
                <label for="customer_name">Full Name:</label>
                <input type="text" name="customer_name" id="customer_name" required value="<?= $_SESSION['user']['name'] ?? '' ?>">

                <label for="customer_email">Email:</label>
                <input type="email" name="customer_email" id="customer_email" required value="<?= $_SESSION['user']['email'] ?? '' ?>">

                <label for="address">Address:</label>
                <textarea name="address" id="address" required></textarea>

                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" required>

                <button type="submit" class="btn">Proceed to Payment</button>
            </form>
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