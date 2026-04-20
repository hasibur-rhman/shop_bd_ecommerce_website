<?php
require_once '../includes/db.php';
if (!isLoggedIn() || !isUser()) redirect('../login.php?role=user');

$user_id = $_SESSION['user_id'];
$msg = '';

// DELETE ORDER (only pending)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id=$id AND user_id=$user_id AND status='pending'"));

    if ($check) {
        // Restore stock
        $items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id=$id");
        while ($item = mysqli_fetch_assoc($items)) {
            mysqli_query($conn, "UPDATE products SET stock=stock+" . $item['quantity'] . " WHERE id=" . $item['product_id']);
        }
        mysqli_query($conn, "DELETE FROM order_items WHERE order_id=$id");
        mysqli_query($conn, "DELETE FROM orders WHERE id=$id");
        $msg = 'success:Order #' . $id . ' deleted!';
    } else {
        $msg = 'error:Cannot delete this order (only pending orders can be deleted)';
    }
}

$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");

[$type, $text] = $msg ? explode(':', $msg, 2) : ['', ''];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — ShopBD</title>
    <?php include '../includes/style.php'; ?>
    <style>
        .order-card { background: white; border-radius: 16px; padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 16px; }
        .order-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; flex-wrap: wrap; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .order-id { font-family: 'Playfair Display',serif; font-size: 1.1rem; color: #1a1a2e; }
        .order-meta { font-size: 0.82rem; color: #aaa; margin-top: 3px; }
        .order-items { margin-bottom: 15px; }
        .order-item-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.88rem; border-bottom: 1px solid #f5f5f5; }
        .order-item-row:last-child { border-bottom: none; }
        .order-footer { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .status-timeline { display: flex; gap: 5px; align-items: center; }
        .tl-step { padding: 4px 10px; border-radius: 15px; font-size: 0.72rem; font-weight: 600; }
        .tl-done { background: #d4edda; color: #155724; }
        .tl-active { background: #1a1a2e; color: white; }
        .tl-waiting { background: #f0f0f0; color: #aaa; }
        .tl-arrow { color: #ddd; font-size: 0.7rem; }
    </style>
</head>
<body>

<nav>
    <a href="home.php" class="nav-logo">Shop<span>BD</span></a>
    <div class="nav-links">
        <a href="home.php">🏪 Shop</a>
        <a href="cart.php">🛒 Cart</a>
        <a href="orders.php" class="active">📦 My Orders</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="page-container">
    <div class="page-title">📦 My Orders</div>

    <?php if ($text): ?>
    <div class="alert alert-<?= $type ?>">
        <?= $type === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($text) ?>
    </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($orders) > 0): while ($order = mysqli_fetch_assoc($orders)):
        $items = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=" . $order['id']);

        $steps = ['pending', 'confirmed', 'delivered'];
        $statuses = ['pending' => 0, 'confirmed' => 1, 'delivered' => 2, 'cancelled' => -1];
        $current_step = $statuses[$order['status']] ?? 0;
    ?>
    <div class="order-card">
        <div class="order-header">
            <div>
                <div class="order-id">Order #<?= $order['id'] ?></div>
                <div class="order-meta">📅 <?= date('d M Y, g:i A', strtotime($order['created_at'])) ?></div>
                <?php if ($order['address']): ?>
                <div class="order-meta">📍 <?= htmlspecialchars($order['address']) ?></div>
                <?php endif; ?>
            </div>
            <div style="text-align:right;">
                <span class="badge badge-<?= $order['status'] ?>" style="font-size:0.85rem;padding:7px 14px;"><?= ucfirst($order['status']) ?></span>
                <div style="margin-top:8px;font-size:1.2rem;font-weight:700;color:#e94560;">৳<?= number_format($order['total_amount'], 0) ?></div>
            </div>
        </div>

        <!-- Status Timeline -->
        <?php if ($order['status'] !== 'cancelled'): ?>
        <div class="status-timeline" style="margin-bottom:15px;">
            <?php foreach ($steps as $i => $step): ?>
                <?php
                if ($current_step > $i) $class = 'tl-done';
                elseif ($current_step === $i) $class = 'tl-active';
                else $class = 'tl-waiting';
                $labels = ['pending'=>'⏳ Pending','confirmed'=>'✅ Confirmed','delivered'=>'📦 Delivered'];
                ?>
                <span class="tl-step <?= $class ?>"><?= $labels[$step] ?></span>
                <?php if ($i < count($steps)-1): ?><span class="tl-arrow">→</span><?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="margin-bottom:15px;"><span class="badge badge-cancelled" style="padding:6px 12px;">❌ Order Cancelled</span></div>
        <?php endif; ?>

        <!-- Items -->
        <div class="order-items">
            <?php while ($item = mysqli_fetch_assoc($items)): ?>
            <div class="order-item-row">
                <span>📦 <?= htmlspecialchars($item['name']) ?> <span style="color:#aaa;">×<?= $item['quantity'] ?></span></span>
                <span style="font-weight:600;">৳<?= number_format($item['price'] * $item['quantity'], 0) ?></span>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="order-footer">
            <div style="font-size:0.82rem;color:#aaa;">
                <?php if ($order['status'] === 'pending'): ?>
                ⏳ Waiting for admin confirmation
                <?php elseif ($order['status'] === 'confirmed'): ?>
                ✅ Confirmed — Being prepared for delivery
                <?php elseif ($order['status'] === 'delivered'): ?>
                🎉 Delivered — Thank you for shopping!
                <?php else: ?>
                ❌ This order was cancelled
                <?php endif; ?>
            </div>
            <?php if ($order['status'] === 'pending'): ?>
            <a href="?delete=<?= $order['id'] ?>" class="btn btn-danger" style="padding:8px 16px;font-size:0.82rem;"
               onclick="return confirm('Delete this order?')">🗑️ Cancel Order</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; else: ?>
    <div class="empty-state card">
        <div class="ei">📭</div>
        <h3>No orders yet</h3>
        <p>Your order history will appear here after you make a purchase.</p>
        <a href="home.php" class="btn btn-primary" style="margin-top:20px;">🛒 Start Shopping</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
