<?php
require_once '../includes/db.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php?role=admin');

$msg = '';

// CONFIRM ORDER
if (isset($_GET['confirm'])) {
    $id = intval($_GET['confirm']);
    mysqli_query($conn, "UPDATE orders SET status='confirmed' WHERE id=$id");
    $msg = 'success:Order #' . $id . ' confirmed!';
}

// DELIVER ORDER
if (isset($_GET['deliver'])) {
    $id = intval($_GET['deliver']);
    mysqli_query($conn, "UPDATE orders SET status='delivered' WHERE id=$id");
    $msg = 'success:Order #' . $id . ' marked as delivered!';
}

// CANCEL ORDER
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    mysqli_query($conn, "UPDATE orders SET status='cancelled' WHERE id=$id");
    $msg = 'success:Order #' . $id . ' cancelled!';
}

$filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$where = $filter ? "WHERE o.status='$filter'" : '';

$orders = mysqli_query($conn, "SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id=u.id $where ORDER BY o.created_at DESC");

[$type, $text] = $msg ? explode(':', $msg, 2) : ['', ''];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders — Admin</title>
    <?php include '../includes/style.php'; ?>
    <style>
        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
        .filter-tab { padding: 8px 18px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-decoration: none; border: 2px solid #eee; color: #888; transition: all 0.2s; }
        .filter-tab:hover, .filter-tab.active { border-color: #e94560; color: #e94560; background: rgba(233,69,96,0.05); }
        .order-detail { background: #f8f5f0; border-radius: 10px; padding: 12px; margin-top: 10px; font-size: 0.85rem; display: none; }
        .actions { display: flex; gap: 6px; flex-wrap: wrap; }
    </style>
</head>
<body>

<nav>
    <a href="dashboard.php" class="nav-logo">Shop<span>BD</span> 👑</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php" class="active">Orders</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="page-container">
    <div class="page-title">📋 Orders Management</div>

    <?php if ($text): ?>
    <div class="alert alert-<?= $type ?>">
        <?= $type === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($text) ?>
    </div>
    <?php endif; ?>

    <div class="filter-tabs">
        <a href="orders.php" class="filter-tab <?= !$filter ? 'active' : '' ?>">All Orders</a>
        <a href="orders.php?status=pending" class="filter-tab <?= $filter==='pending' ? 'active' : '' ?>">⏳ Pending</a>
        <a href="orders.php?status=confirmed" class="filter-tab <?= $filter==='confirmed' ? 'active' : '' ?>"> Confirmed</a>
        <a href="orders.php?status=delivered" class="filter-tab <?= $filter==='delivered' ? 'active' : '' ?>"> Delivered</a>
        <a href="orders.php?status=cancelled" class="filter-tab <?= $filter==='cancelled' ? 'active' : '' ?>"> Cancelled</a>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <?php if (mysqli_num_rows($orders) > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($order = mysqli_fetch_assoc($orders)):
                // Get order items
                $items = mysqli_query($conn, "SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=" . $order['id']);
            ?>
            <tr>
                <td><strong>#<?= $order['id'] ?></strong></td>
                <td>
                    <strong><?= htmlspecialchars($order['user_name']) ?></strong><br>
                    <small style="color:#aaa;"><?= htmlspecialchars($order['user_email']) ?></small>
                </td>
                <td><strong>৳<?= number_format($order['total_amount'], 0) ?></strong></td>
                <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                <td><?= date('d M Y, g:i A', strtotime($order['created_at'])) ?></td>
                <td>
                    <div class="actions">
                        <?php if ($order['status'] === 'pending'): ?>
                        <a href="?confirm=<?= $order['id'] ?><?= $filter ? '&status='.$filter : '' ?>" class="btn btn-success" style="padding:6px 12px;font-size:0.78rem;">✅ Confirm</a>
                        <a href="?cancel=<?= $order['id'] ?><?= $filter ? '&status='.$filter : '' ?>" class="btn btn-danger" style="padding:6px 12px;font-size:0.78rem;" onclick="return confirm('Cancel this order?')">❌ Cancel</a>
                        <?php elseif ($order['status'] === 'confirmed'): ?>
                        <a href="?deliver=<?= $order['id'] ?><?= $filter ? '&status='.$filter : '' ?>" class="btn btn-gold" style="padding:6px 12px;font-size:0.78rem;">📦 Deliver</a>
                        <?php else: ?>
                        <span style="color:#aaa;font-size:0.8rem;"><?= ucfirst($order['status']) ?></span>
                        <?php endif; ?>
                        <button class="btn btn-outline" style="padding:6px 12px;font-size:0.78rem;" onclick="toggleDetails(<?= $order['id'] ?>)">📄 Details</button>
                    </div>

                    <!-- Order items detail -->
                    <div id="detail-<?= $order['id'] ?>" class="order-detail">
                        <strong>🛍️ Items:</strong><br>
                        <?php while ($item = mysqli_fetch_assoc($items)): ?>
                        • <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> — ৳<?= number_format($item['price'] * $item['quantity'], 0) ?><br>
                        <?php endwhile; ?>
                        <?php if ($order['address']): ?>
                        <br><strong>📍 Address:</strong> <?= htmlspecialchars($order['address']) ?>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="ei">📭</div>
            <h3>No orders found</h3>
            <p><?= $filter ? "No $filter orders at the moment." : "No orders placed yet." ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleDetails(id) {
    const el = document.getElementById('detail-' + id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
</script>

</body>
</html>
