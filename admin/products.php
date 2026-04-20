<?php
require_once '../includes/db.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php?role=admin');

$msg = '';

// ADD PRODUCT
if (isset($_POST['add_product'])) {
    $name = sanitize($_POST['name']);
    $desc = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = sanitize($_POST['category']);

    $query = "INSERT INTO products (name, description, price, stock, category) VALUES ('$name', '$desc', $price, $stock, '$category')";
    if (mysqli_query($conn, $query)) {
        $msg = 'success:Product added successfully!';
    } else {
        $msg = 'error:Failed to add product!';
    }
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM order_items WHERE product_id=$id");
    mysqli_query($conn, "DELETE FROM cart WHERE product_id=$id");
    if (mysqli_query($conn, "DELETE FROM products WHERE id=$id")) {
        $msg = 'success:Product deleted!';
    }
}

$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$where = $search ? "WHERE name LIKE '%$search%' OR category LIKE '%$search%'" : '';
$products = mysqli_query($conn, "SELECT * FROM products $where ORDER BY created_at DESC");

[$type, $text] = $msg ? explode(':', $msg, 2) : ['', ''];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products — Admin</title>
    <?php include '../includes/style.php'; ?>
    <style>
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
        .product-card { background: white; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-3px); }
        .product-img { width: 100%; height: 140px; background: linear-gradient(135deg, #f8f5f0, #eee); display: flex; align-items: center; justify-content: center; font-size: 3rem; }
        .product-body { padding: 16px; }
        .product-name { font-weight: 600; font-size: 0.92rem; margin-bottom: 6px; color: #1a1a2e; }
        .product-cat { font-size: 0.75rem; color: #aaa; margin-bottom: 8px; }
        .product-price { font-size: 1.1rem; font-weight: 700; color: #e9459c; margin-bottom: 8px; }
        .product-stock { font-size: 0.78rem; color: #888; margin-bottom: 12px; }
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 20px; padding: 35px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto; }
        .modal-title { font-family: 'Playfair Display',serif; font-size: 1.5rem; margin-bottom: 25px; }
    </style>
</head>
<body>

<nav>
    <a href="dashboard.php" class="nav-logo">Shop<span>BD</span> 👑</a>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php" class="active">Products</a>
        <a href="orders.php">Orders</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="page-container">
    <div class="page-title">📦 Products Management</div>

    <?php if ($text): ?>
    <div class="alert alert-<?= $type === 'success' ? 'success' : 'error' ?>"><?= $type === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($text) ?></div>
    <?php endif; ?>

    <div style="display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap;margin-bottom:20px;">
        <form method="GET" style="flex:1;max-width:400px;">
            <input type="search" name="q" placeholder="🔍 Search products..." value="<?= htmlspecialchars($search) ?>" style="padding:12px 18px;border:2px solid #eee;border-radius:10px;font-size:0.9rem;width:100%;outline:none;" oninput="this.form.submit()">
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">➕ Add New Product</button>
    </div>

    <div class="product-grid">
        <?php if (mysqli_num_rows($products) > 0):
            while ($p = mysqli_fetch_assoc($products)):
                $icons = ['Electronics'=>'📱','Clothing'=>'👕','Footwear'=>'👟','Accessories'=>'🎒'];
                $icon = $icons[$p['category']] ?? '📦';
        ?>
        <div class="product-card">
            <div class="product-img"><?= $icon ?></div>
            <div class="product-body">
                <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                <div class="product-cat">🏷️ <?= htmlspecialchars($p['category']) ?></div>
                <div class="product-price">৳<?= number_format($p['price'], 0) ?></div>
                <div class="product-stock">📊 Stock: <?= $p['stock'] ?> units</div>
                <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger" style="width:100%;justify-content:center;"
                   onclick="return confirm('Delete this product?')">🗑️ Delete</a>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="empty-state" style="grid-column:1/-1;">
            <div class="ei">📭</div>
            <h3>No products found</h3>
            <p>Add your first product to get started</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div class="modal-title">➕ Add New Product</div>
            <button onclick="document.getElementById('addModal').classList.remove('active')" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#aaa;">✕</button>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" placeholder="e.g. Samsung Galaxy A54" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Product description..." style="width:100%;padding:12px 16px;border:2px solid #eee;border-radius:10px;font-family:'DM Sans',sans-serif;resize:vertical;outline:none;"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price (৳)</label>
                    <input type="number" name="price" placeholder="0.00" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" placeholder="0" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option value="Electronics">Electronics</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Footwear">Footwear</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div style="display:flex;gap:12px;">
                <button type="submit" name="add_product" class="btn btn-primary" style="flex:1;justify-content:center;">✅ Add Product</button>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('active')" style="flex:1;justify-content:center;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
// Close modal on outside click
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});
<?php if ($msg): ?>
// Auto open modal on error
<?php if (strpos($msg, 'error') === 0): ?>
document.getElementById('addModal').classList.add('active');
<?php endif; ?>
<?php endif; ?>
</script>

</body>
</html>
