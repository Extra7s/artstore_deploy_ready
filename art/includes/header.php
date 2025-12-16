<nav class="navbar">
<a href="index.php" class="logo">ArtStore</a>

<div class="nav-links">
<a href="shop.php">Shop</a>
<a href="cart.php">Cart</a>

<?php if(isset($_SESSION['user'])){ ?>
<span>Hello, <?= $_SESSION['user']['name'] ?></span>
<a href="logout.php">Logout</a>
<?php if($_SESSION['user']['role']=='admin'){ ?>
<a href="admin/dashboard.php">Admin</a>
<?php } ?>
<?php } else { ?>
<a href="login.php">Login</a>
<a href="signup.php">Sign Up</a>
<?php } ?>
</div>
</nav>
