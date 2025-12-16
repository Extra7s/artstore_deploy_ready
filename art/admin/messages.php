<?php
include 'admin_guard.php';

$stmt = $conn->prepare("SELECT * FROM messages ORDER BY created_at DESC");
$stmt->execute();
$r = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Messages - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<section class="admin-section">
    <h2>Contact Messages</h2>

    <div class="admin-content">
        <?php while($m = $r->fetch_assoc()){ ?>
        <div class="admin-item">
            <h3>From: <?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['email']) ?>)</h3>
            <p><strong>Date:</strong> <?= $m['created_at'] ?></p>
            <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($m['message'])) ?></p>
        </div>
        <?php } ?>
    </div>
</section>

<div style="text-align: center; margin: 20px;">
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

</body>
</html>