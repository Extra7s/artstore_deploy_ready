<?php
session_start();
require_once "includes/db.php";

$total = 0;
$cart_items = [];

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
} elseif (!empty($_SESSION['cart'])) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart | ArtfyCanvas</title>
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

<section class="cart-section">
    <h2>Your Cart</h2>

    <?php if(empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="assets/images/<?= $item['artwork']['image'] ?>" alt="<?= $item['artwork']['title'] ?>">
                    <div class="item-details">
                        <h3><?= $item['artwork']['title'] ?></h3>
                        <p>Artist: <?= $item['artwork']['artist'] ?></p>
                        <div class="quantity-controls">
                            <form method="POST" action="actions/update_cart.php" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $item['artwork']['id'] ?>">
                                <button type="submit" name="action" value="decrease">-</button>
                                <input type="number" name="qty" value="<?= $item['qty'] ?>" min="1" readonly>
                                <button type="submit" name="action" value="increase">+</button>
                            </form>
                        </div>
                        <p>Price: $<?= number_format($item['subtotal'], 2) ?></p>
                    </div>
                    <button class="remove-btn" onclick="window.location.href='actions/remove_from_cart.php?id=<?= $item['artwork']['id'] ?>'">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="cart-total">
            <h3>Total: <span class="total-price">$<?= number_format($total, 2) ?></span></h3>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
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
