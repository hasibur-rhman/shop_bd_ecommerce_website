<?php
require_once '../includes/db.php';
if (!isLoggedIn() || !isUser()) redirect('../login.php?role=user');

$user_id = $_SESSION['user_id'];
$msg = '';

// REMOVE FROM CART
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE id=$id AND user_id=$user_id");
    $msg = 'success:Item removed from cart!';
}

// UPDATE QUANTITY
if (isset($_POST['update_qty'])) {
    $cart_id = intval($_POST['cart_id']);
    $qty = intval($_POST['quantity']);
    if ($qty > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$user_id");
    }
    redirect('cart.php');
}

// PLACE ORDER
if (isset($_POST['place_order'])) {
    $address = sanitize($_POST['address']);

    $cart_items = mysqli_query($conn, "SELECT c.*, p.price, p.stock, p.name as product_name FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");

    if (mysqli_num_rows($cart_items) > 0) {
        $total = 0;
        $items = [];
        while ($item = mysqli_fetch_assoc($cart_items)) {
            $qty = min($item['quantity'], $item['stock']);
            $total += $item['price'] * $qty;
            $items[] = $item;
        }

        // Create order
        mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, address) VALUES ($user_id, $total, '$address')");
        $order_id = mysqli_insert_id($conn);

        // Add order items & update stock
        foreach ($items as $item) {
            $qty = min($item['quantity'], $item['stock']);
            $price = $item['price'];
            $pid = $item['product_id'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, $qty, $price)");
            mysqli_query($conn, "UPDATE products SET stock=stock-$qty WHERE id=$pid");
        }

        // Clear cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");

        $msg = 'success:Order placed successfully! Order #' . $order_id;
    } else {
        $msg = 'error:Your cart is empty!';
    }
}

// Get cart items
$cart_items = mysqli_query($conn, "SELECT c.*, p.name, p.price, p.stock FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
$total = 0;
$cart_data = [];
while ($item = mysqli_fetch_assoc($cart_items)) {
    $total += $item['price'] * $item['quantity'];
    $cart_data[] = $item;
}

$cart_count = count($cart_data);

[$type, $text] = $msg ? explode(':', $msg, 2) : ['', ''];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart — ShopBD</title>
    <?php include '../includes/style.php'; ?>
    <style>
        .cart-layout { display: grid; grid-template-columns: 1fr 320px; gap: 25px; }
        .cart-item { display: flex; align-items: center; gap: 16px; padding: 18px 0; border-bottom: 1px solid #eee; }
        .cart-item:last-child { border-bottom: none; }
        .item-icon { width: 70px; height: 70px; background: #f8f5f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0; }
        .item-info { flex: 1; }
        .item-name { font-weight: 600; font-size: 0.92rem; margin-bottom: 4px; }
        .item-price { color: #e94560; font-weight: 600; }
        .qty-form { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
        .qty-input { width: 60px; padding: 6px 10px; border: 2px solid #eee; border-radius: 8px; text-align: center; font-size: 0.9rem; }
        .summary-card { position: sticky; top: 80px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem; border-bottom: 1px solid #eee; }
        .summary-total { display: flex; justify-content: space-between; padding: 15px 0 0; font-size: 1.1rem; font-weight: 700; color: #1a1a2e; }
        textarea { border: 2px solid #eee; border-radius: 10px; padding: 12px; width: 100%; resize: vertical; font-family: 'DM Sans',sans-serif; font-size: 0.9rem; outline: none; }
        textarea:focus { border-color: #e94560; }
        @media (max-width: 700px) { .cart-layout { grid-template-columns: 1fr; } .summary-card { position: static; } }
    </style>
</head>
<body>

<nav>
    <a href="home.php" class="nav-logo">Shop<span>BD</span></a>
    <div class="nav-links">
        <a href="home.php">🏪 Shop</a>
        <a href="cart.php" class="active">🛒 Cart</a>
        <a href="orders.php">📦 My Orders</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="page-container">
    <div class="page-title">🛒 Shopping Cart</div>

    <?php if ($text): ?>
    <div class="alert alert-<?= $type ?>">
        <?= $type === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($text) ?>
    </div>
    <?php endif; ?>

    <?php if ($cart_count > 0): ?>
    <div class="cart-layout">
        <div>
            <div class="card">
                <h3 style="font-family:'Playfair Display',serif;margin-bottom:5px;"><?= $cart_count ?> item<?= $cart_count > 1 ? 's' : '' ?> in cart</h3>
                <p style="color:#aaa;font-size:0.85rem;margin-bottom:15px;">Review your items before placing order</p>

                <?php foreach ($cart_data as $item):
                    $icons = ['Electronics'=>'📱','Clothing'=>'👕','Footwear'=>'👟','Accessories'=>'🎒'];
                ?>
                <div class="cart-item">
                    <div class="item-icon">📦</div>
                    <div class="item-info">
                        <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="item-price">৳<?= number_format($item['price'], 0) ?> each</div>
                        <div class="qty-form">
                            <form method="POST" style="display:flex;align-items:center;gap:8px;">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" class="qty-input" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>">
                                <button type="submit" name="update_qty" class="btn btn-outline" style="padding:6px 12px;font-size:0.78rem;">Update</button>
                            </form>
                            <a href="?remove=<?= $item['id'] ?>" class="btn btn-danger" style="padding:6px 12px;font-size:0.78rem;" onclick="return confirm('Remove this item?')">🗑️</a>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;color:#1a1a2e;font-size:1rem;">৳<?= number_format($item['price'] * $item['quantity'], 0) ?></div>
                        <div style="font-size:0.75rem;color:#aaa;">qty: <?= $item['quantity'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="summary-card">
            <div class="card">
                <h3 style="font-family:'Playfair Display',serif;margin-bottom:20px;">Order Summary</h3>
                <?php foreach ($cart_data as $item): ?>
                <div class="summary-row">
                    <span style="color:#888;"><?= htmlspecialchars(substr($item['name'], 0, 20)) ?>... ×<?= $item['quantity'] ?></span>
                    <span>৳<?= number_format($item['price'] * $item['quantity'], 0) ?></span>
                </div>
                <?php endforeach; ?>
                <div class="summary-total">
                    <span>Total</span>
                    <span style="color:#e94560;">৳<?= number_format($total, 0) ?></span>
                </div>

                <form method="POST" style="margin-top:20px;">
                    <div class="form-group" style="margin-bottom:15px;">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="3" placeholder="Enter your full delivery address..." required></textarea>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;">
                        ✅ Place Order — ৳<?= number_format($total, 0) ?>
                    </button>
                </form>
            </div>

            <a href="home.php" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:10px;">← Continue Shopping</a>
        </div>
    </div>

    <?php else: ?>
    <div class="empty-state card">
        <div class="ei">🛒</div>
        <h3>Your cart is empty</h3>
        <p>Add some amazing products to your cart!</p>
        <a href="home.php" class="btn btn-primary" style="margin-top:20px;">🏪 Start Shopping</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
