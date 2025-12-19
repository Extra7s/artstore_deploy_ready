<?php
session_start();
require_once "includes/db.php";
require_once "config/khalti.php";
require_once "khalti_payment.php";

$order_id = intval($_GET['order_id'] ?? 0);
$pidx = $_GET['pidx'] ?? null;
$status = $_GET['status'] ?? null;

$message = "";
$success = false;

if ($order_id && isset($_SESSION['user'])) {
    // Check if order exists and belongs to user
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $_SESSION['user']['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $order = $res->fetch_assoc();

if ($order) {
        if ($order['payment_status'] == 'paid') {
            $message = "Order #$order_id has been successfully completed!";
            $success = true;
        } elseif ($pidx && $status === 'Completed') {
            // Verify payment with Khalti
            $khalti = new KhaltiPayment();
            $verification = $khalti->verifyPayment($pidx);

            if ($verification['success'] && $verification['status'] === 'Completed') {
                // Update order status to paid
                $stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid', khalti_token = ? WHERE id = ?");
                $stmt->bind_param("si", $pidx, $order_id);
                $stmt->execute();

                // Clear cart
                $user_id = $_SESSION['user']['id'];
                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                // Clear pending order session
                unset($_SESSION['pending_order']);

                $message = "Payment successful! Your order #$order_id has been confirmed.";
                $success = true;
            } else {
                $message = "Payment verification failed. Please contact support with order ID #$order_id.";
            }
        } elseif ($status === 'Failed' || $status === 'Pending') {
            $message = "Payment was not completed. Status: $status. Please try again.";
        } else {
            $message = "Payment not completed. Please try again.";
        }
    } else {
        $message = "Order not found.";
    }
} else {
    $message = "Invalid request.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Result | ArtfyCanvas</title>
    <link rel="stylesheet" href="assets/css/style_organized.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
    </div>
</header>

<section class="result-section">
    <div class="result-container">
        <?php if ($success): ?>
            <div class="success-message">
                <h2>✅ Payment Successful!</h2>
                <p><?= $message ?></p>
                <div class="order-details">
                    <h3>Order Details</h3>
                    <p><strong>Order ID:</strong> #<?= $order_id ?></p>
                    <?php if ($pidx): ?>
                        <p><strong>Payment ID:</strong> <?= htmlspecialchars($pidx) ?></p>
                    <?php endif ?>
                </div>
                <a href="index.php" class="btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="error-message">
                <h2>❌ Payment Issue</h2>
                <p><?= $message ?></p>
                <a href="checkout.php" class="btn">Try Again</a>
            </div>
        <?php endif; ?>
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
// Mobile Menu Functions
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const menuToggle = document.querySelector('.menu-toggle');

    if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('active');
        menuToggle.classList.remove('active');
    } else {
        mobileMenu.classList.add('active');
        menuToggle.classList.add('active');
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