<?php
require_once "includes/db.php";

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,'user')");
    if ($stmt->execute([$name, $email, $password])) {
        $message = "Account created successfully! <a href='login.php'>Login here</a>";
    } else {
        $message = "Error creating account. Email may already exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | ArtfyCanvas</title>
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
        <a href="login.php">Login</a>
    </nav>
</header>

<section class="auth-section">
    <div class="auth-container">
        <h2>Create Your Account</h2>
        <?php if ($message): ?>
            <p class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>"><?= $message ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="name">Name:</label>
            <input name="name" id="name" placeholder="Full Name" required>

            <label for="email">Email:</label>
            <input name="email" type="email" id="email" placeholder="Email" required>

            <label for="password">Password:</label>
            <input name="password" type="password" id="password" placeholder="Password" required>

            <button type="submit" class="btn">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
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
