<?php
session_start();
require_once "includes/db.php";
require_once "config/khalti.php";

if (!isset($_SESSION['user']) || !isset($_SESSION['pending_order'])) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET['order_id'] ?? 0);
$pending_order = $_SESSION['pending_order'];

if ($order_id != $pending_order['order_id']) {
    header("Location: checkout.php");
    exit;
}

// Verify order exists
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user']['id']);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();

if (!$order) {
    header("Location: checkout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment | ArtfyCanvas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
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
        <a href="cart.php">Cart</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<section class="payment-section">
    <div class="payment-container">
        <h2>Complete Your Payment</h2>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <p><strong>Order ID:</strong> #<?= $order_id ?></p>
            <p><strong>Total Amount:</strong> NPR <?= number_format($pending_order['total'] * 130, 2) ?> (≈ $<?= number_format($pending_order['total'], 2) ?>)</p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($pending_order['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($pending_order['customer_email']) ?></p>
        </div>

        <div class="payment-methods">
            <h3>Choose Payment Method</h3>

            <div class="payment-option">
                <button id="khalti-payment-button" class="btn khalti-btn">
                    <img src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-logo.png" alt="Khalti" class="khalti-logo">
                    Pay with Khalti
                </button>
            </div>

            <div class="payment-info">
                <p><strong>Note:</strong> You will be redirected to Khalti's secure payment page to complete your transaction.</p>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const khaltiBtn = document.getElementById('khalti-payment-button');

    // Khalti Checkout Configuration
    const config = {
        publicKey: '<?= KHALTI_PUBLIC_KEY ?>',
        productIdentity: '<?= $order_id ?>',
        productName: 'Art Purchase - Order #<?= $order_id ?>',
        productUrl: '<?= WEBSITE_URL ?>product.php?id=1', // Generic product URL
        amount: <?= $pending_order['total'] * 100 ?>, // Amount in paisa
        eventHandler: {
            onSuccess: function(payload) {
                // Payment successful
                console.log('Payment successful:', payload);

                // Verify payment on server
                fetch('khalti_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=verify&pidx=' + payload.pidx
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'Completed') {
                        // Payment verified, redirect to success page
                        window.location.href = 'payment_success.php?order_id=<?= $order_id ?>&payment_id=' + payload.idx;
                    } else {
                        alert('Payment verification failed. Please contact support.');
                        window.location.href = 'checkout.php';
                    }
                })
                .catch(error => {
                    console.error('Verification error:', error);
                    alert('Payment verification failed. Please try again.');
                    window.location.href = 'checkout.php';
                });
            },
            onError: function(error) {
                console.log('Payment failed:', error);
                alert('Payment failed. Please try again.');
            },
            onClose: function() {
                console.log('Payment widget closed');
            }
        },
        paymentPreference: ["KHALTI", "EBANKING", "MOBILE_BANKING", "CONNECT_IPS", "SCT"],
    };

    const checkout = new KhaltiCheckout(config);

    khaltiBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkout.show({amount: <?= $pending_order['total'] * 100 ?>});
    });
});
</script>

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