<?php
require_once '../includes/db.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php?role=admin');

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='user'"))['c'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='pending'"))['c'];
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as s FROM orders WHERE status IN ('confirmed','delivered')"))['s'] ?? 0;

// Recent Orders
$orders = mysqli_query($conn, "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — ShopBD</title>
    <?php include '../includes/style.php'; ?>
</head>
<body>

<nav>
    <a href="dashboard.php" class="nav-logo">Shop<span>BD</span> 👑</a>
    <div class="nav-links">
        <span class="nav-user">Hi, <?= $_SESSION['user_name'] ?>!</span>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="page-container">
    <div class="page-title">📊 Admin Dashboard</div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon gold">📦</div>
            <div>
                <div class="stat-value"><?= $total_products ?></div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">🛒</div>
            <div>
                <div class="stat-value"><?= $total_orders ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">👥</div>
            <div>
                <div class="stat-value"><?= $total_users ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">⏳</div>
            <div>
                <div class="stat-value"><?= $pending_orders ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:25px;">
        <a href="products.php" class="btn btn-primary">➕ Add Product</a>
        <a href="orders.php" class="btn btn-gold">📋 View All Orders</a>
    </div>

    <div class="card">
        <h2 style="font-family:'Playfair Display',serif;margin-bottom:20px;font-size:1.2rem;">🕐 Recent Orders</h2>
        <?php if (mysqli_num_rows($orders) > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
            <tr>
                <td><strong>#<?= $order['id'] ?></strong></td>
                <td><?= htmlspecialchars($order['user_name']) ?></td>
                <td><strong>৳<?= number_format($order['total_amount'], 0) ?></strong></td>
                <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                <td>
                    <?php if ($order['status'] === 'pending'): ?>
                    <a href="orders.php?confirm=<?= $order['id'] ?>" class="btn btn-success" style="padding:6px 14px;font-size:0.8rem;">✅ Confirm</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="ei">📭</div>
            <h3>No orders yet</h3>
        </div>
        <?php endif; ?>
    </div>

    <div class="card" style="background:linear-gradient(135deg,#1a1a2e,#16213e);color:white;">
        <h3 style="font-family:'Playfair Display',serif;margin-bottom:8px;">💰 Total Revenue</h3>
        <div style="font-size:2.5rem;font-weight:700;font-family:'Playfair Display',serif;color:#f5a623;">৳<?= number_format($revenue, 0) ?></div>
        <div style="color:rgba(255,255,255,0.5);font-size:0.85rem;margin-top:5px;">From confirmed & delivered orders</div>
    </div>
</div>

</body>
</html>
