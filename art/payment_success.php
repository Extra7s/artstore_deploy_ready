<?php
session_start();
require_once "includes/db.php";
require_once "config/khalti.php";
require_once "khalti_payment.php";

$order_id = intval($_GET['order_id'] ?? 0);
$payment_id = $_GET['payment_id'] ?? null;

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
        } elseif ($payment_id) {
            // Verify payment with Khalti
            $khalti = new KhaltiPayment();
            $verification = $khalti->verifyPayment($payment_id);

            if ($verification['success'] && $verification['status'] === 'Completed') {
                // Update order status to paid
                $stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid', khalti_token = ? WHERE id = ?");
                $stmt->bind_param("si", $payment_id, $order_id);
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
    </nav>
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
                    <?php if ($payment_id): ?>
                        <p><strong>Payment ID:</strong> <?= htmlspecialchars($payment_id) ?></p>
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

</body>
</html>