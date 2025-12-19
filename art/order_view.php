<?php
session_start();
require_once "includes/db.php";

// Ensure user logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$order_id = intval($_GET['id'] ?? 0);
$message = null;

if (!$order_id) {
    header('Location: my_orders.php');
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $del_id = intval($_POST['delete_order']);
    // Verify ownership
    $chk = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
    $chk->bind_param("ii", $del_id, $user_id);
    $chk->execute();
    $res = $chk->get_result();
    if ($row = $res->fetch_assoc()) {
        // Allow delete only for completed or cancelled orders
        if (in_array($row['status'], ['completed','cancelled'])) {
            $conn->begin_transaction();
            try {
                $delItems = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
                $delItems->bind_param("i", $del_id);
                $delItems->execute();

                $delOrder = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $delOrder->bind_param("i", $del_id);
                $delOrder->execute();

                $conn->commit();
                $message = ['type' => 'success', 'text' => 'Order deleted successfully.'];
            } catch (Exception $e) {
                $conn->rollback();
                $message = ['type' => 'error', 'text' => 'Failed to delete order.'];
            }
        } else {
            $message = ['type' => 'error', 'text' => 'Only completed or cancelled orders can be deleted.'];
        }
    } else {
        $message = ['type' => 'error', 'text' => 'Order not found.'];
    }
}

// Fetch order details (unless just deleted)
$stmt = $conn->prepare("SELECT o.* FROM orders o WHERE o.id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$orderRes = $stmt->get_result();
$order = $orderRes->fetch_assoc();

if (!$order) {
    // If order missing (deleted or not found)
    if ($message && $message['type'] === 'success') {
        // show delete success and link back
    } else {
        header('Location: my_orders.php');
        exit();
    }
}

// Fetch items if order exists
$items = [];
if ($order) {
    $it = $conn->prepare("SELECT oi.*, a.title, a.image FROM order_items oi JOIN artworks a ON oi.artwork_id = a.id WHERE oi.order_id = ?");
    $it->bind_param("i", $order_id);
    $it->execute();
    $resIt = $it->get_result();
    while ($r = $resIt->fetch_assoc()) $items[] = $r;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Order #<?= htmlspecialchars($order_id) ?> — Order Details</title>
    <link rel="stylesheet" href="assets/css/style_organized.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="main-section">
    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-receipt"></i> Order Details</h2>
            <p>Order #<?= htmlspecialchars($order_id) ?></p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $message['type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>

        <?php if (!$order): ?>
            <p>This order was removed. <a href="my_orders.php">Back to orders</a></p>
        <?php else: ?>

        <div class="order-view">
            <div class="order-main">
                <div class="order-box">
                    <h3>Shipping Information</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name'] ?? $_SESSION['user']['name']) ?></p>
                    <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['address'])) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                    <?php if ($order['khalti_token']): ?>
                        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['khalti_token']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="order-box">
                    <h3>Payment & Status</h3>
                    <p><strong>Method:</strong> <?= htmlspecialchars($order['payment_method'] ?: '—') ?></p>
                    <p><strong>Payment Status:</strong> <?= ucfirst(htmlspecialchars($order['payment_status'])) ?></p>
                    <p><strong>Order Status:</strong> <?= ucfirst(htmlspecialchars($order['status'])) ?></p>
                    <p><strong>Placed:</strong> <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
                </div>

                <div class="order-box full">
                    <h3>Items (<?= count($items) ?>)</h3>
                    <?php if (count($items) === 0): ?>
                        <p>No items found for this order.</p>
                    <?php else: ?>
                        <div class="items-list">
                            <?php foreach ($items as $it): 
                                $img = 'assets/images/' . ($it['image'] ?: 'default.jpg');
                            ?>
                                <div class="item-row">
                                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($it['title']) ?>" class="item-image">
                                    <div class="item-details">
                                        <h5><?= htmlspecialchars($it['title']) ?></h5>
                                        <p class="muted">Quantity: <?= $it['quantity'] ?> × Rs. <?= number_format($it['price'], 2) ?></p>
                                        <p class="item-subtotal">Subtotal: Rs. <?= number_format($it['price'] * $it['quantity'], 2) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="order-box totals">
                    <h3>Totals</h3>
                    <p><span>Items total:</span> <strong>Rs. <?= number_format($order['total'], 2) ?></strong></p>
                </div>

                <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="my_orders.php" class="btn btn-outline">Back</a>
                    <?php if ($order['status'] === 'pending'): ?>
                        <form method="POST" onsubmit="return confirm('Confirm cancel order?');" style="display:inline-block;">
                            <input type="hidden" name="cancel_order_id" value="<?= $order['id'] ?>">
                            <button type="submit" class="btn btn-danger">Cancel Order</button>
                        </form>
                    <?php endif; ?>

                    <?php if (in_array($order['status'], ['completed','cancelled'])): ?>
                        <form method="POST" onsubmit="return confirm('Permanently delete this order from your history?');" style="display:inline-block;">
                            <input type="hidden" name="delete_order" value="<?= $order['id'] ?>">
                            <button type="submit" class="btn btn-danger">Delete Order</button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
