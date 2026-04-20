<?php
require_once '../includes/db.php';
if (!isLoggedIn() || !isUser()) redirect('../login.php?role=user');

// Cart count
$user_id = $_SESSION['user_id'];
$cart_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as c FROM cart WHERE user_id=$user_id"))['c'] ?? 0;

// Search & Filter
$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$category = isset($_GET['cat']) ? sanitize($_GET['cat']) : '';

$where = "WHERE 1=1";
if ($search) $where .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
if ($category) $where .= " AND category='$category'";

$products = mysqli_query($conn, "SELECT * FROM products $where AND stock > 0 ORDER BY created_at DESC");
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM products");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop — ShopBD</title>
    <?php include '../includes/style.php'; ?>
    <style>
        .shop-header { background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; padding: 35px 30px; }
        .shop-header h2 { font-family: 'Playfair Display',serif; font-size: 1.6rem; margin-bottom: 20px; }
        .search-bar { display: flex; gap: 10px; max-width: 600px; }
        .search-bar input { flex: 1; padding: 12px 18px; border: none; border-radius: 10px; font-size: 0.92rem; outline: none; }
        .search-bar button { padding: 12px 20px; background: #e94560; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 0.9rem; font-weight: 600; }
        .cat-filter { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 15px; }
        .cat-btn { padding: 6px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-decoration: none; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.15); transition: all 0.2s; }
        .cat-btn:hover, .cat-btn.active { background: #e94560; color: white; border-color: #e94560; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: all 0.25s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 12px 35px rgba(0,0,0,0.1); }
        .product-img { width: 100%; height: 150px; background: linear-gradient(135deg, #f8f5f0, #ece9e4); display: flex; align-items: center; justify-content: center; font-size: 4rem; position: relative; }
        .cat-tag { position: absolute; top: 10px; left: 10px; background: rgba(26,26,46,0.75); color: white; padding: 3px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
        .product-body { padding: 18px; }
        .product-name { font-weight: 600; font-size: 0.92rem; color: #1a1a2e; margin-bottom: 6px; }
        .product-desc { font-size: 0.8rem; color: #aaa; margin-bottom: 10px; line-height: 1.5; }
        .product-price { font-size: 1.2rem; font-weight: 700; color: #e94560; margin-bottom: 12px; }
        .product-stock { font-size: 0.75rem; color: #aaa; margin-bottom: 12px; }
        .add-cart-btn { width: 100%; padding: 10px; background: #1a1a2e; color: white; border: none; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans',sans-serif; }
        .add-cart-btn:hover { background: #e94560; }
        .toast { position: fixed; bottom: 30px; right: 30px; background: #27ae60; color: white; padding: 14px 22px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; z-index: 1000; display: none; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .cart-badge { background: #e94560; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; }
    </style>
</head>
<body>

<nav>
    <a href="home.php" class="nav-logo">Shop<span>BD</span></a>
    <div class="nav-links">
        <span class="nav-user">Hi, <?= $_SESSION['user_name'] ?>!</span>
        <a href="home.php" class="active">🏪 Shop</a>
        <a href="cart.php">🛒 Cart <span class="cart-badge"><?= $cart_count > 0 ? $cart_count : 0 ?></span></a>
        <a href="orders.php">📦 My Orders</a>
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="shop-header">
    <h2>🛍️ Browse Products</h2>
    <form method="GET" class="search-bar">
        <input type="text" name="q" placeholder="Search for products..." value="<?= htmlspecialchars($search) ?>">
        <?php if ($category): ?>
        <input type="hidden" name="cat" value="<?= htmlspecialchars($category) ?>">
        <?php endif; ?>
        <button type="submit">🔍 Search</button>
    </form>
    <div class="cat-filter">
        <a href="home.php<?= $search ? '?q='.urlencode($search) : '' ?>" class="cat-btn <?= !$category ? 'active' : '' ?>">All</a>
        <?php
        $cats_list = mysqli_query($conn, "SELECT  DISTINCT category FROM products WHERE stock>0");
        while ($c = mysqli_fetch_assoc($cats_list)):
            $icons = ['Electronics'=>'📱','Clothing'=>'👕','Footwear'=>'👟','Accessories'=>'🎒','Other'=>'📦'];
            $ci = $icons[$c['category']] ?? '🏷️';
        ?>
        <a href="home.php?cat=<?= urlencode($c['category']) ?><?= $search ? '&q='.urlencode($search) : '' ?>"
           class="cat-btn <?= $category === $c['category'] ? 'active' : '' ?>">
            <?= $ci ?> <?= htmlspecialchars($c['category']) ?>
        </a>
        <?php endwhile; ?>
    </div>
</div>

<div class="page-container">
    <div class="product-grid">
        <?php if (mysqli_num_rows($products) > 0):
            while ($p = mysqli_fetch_assoc($products)):
                $icons = ['Electronics'=>'📱','Clothing'=>'👕','Footwear'=>'👟','Accessories'=>'🎒'];
                $icon = $icons[$p['category']] ?? '📦';
        ?>
        <div class=" product-card">
            <div class="product-img">
                <?= $icon ?>
                <div class="cat-tag"><?= htmlspecialchars($p['category']) ?></div>
            </div>
            <div class="product-body">
                <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                <div class="product-desc"><?= htmlspecialchars(substr($p['description'], 0, 60)) ?>...</div>
                <div class="product-price">৳<?= number_format($p['price'], 0) ?></div>
                <div class="product-stock">📦 In stock: <?= $p['stock'] ?> units</div>
                <button class="add-cart-btn" onclick="addToCart(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>')">
                    🛒 Add to Cart
                </button>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="empty-state" style="grid-column:1/-1;">
            <div class="ei">🔍</div>
            <h3>No products found</h3>
            <p><?= $search ? "No results for \"$search\"" : "No products available." ?></p>
            <a href="home.php" class="btn btn-primary" style="margin-top:15px;">Browse All</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="toast" id="toast">✅ Added to cart!</div>

<script>
function addToCart(productId, productName) {
    fetch('add_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(r => r.json())
    .then(data => {
        const toast = document.getElementById('toast');
        if (data.success) {
            toast.textContent = '✅ ' + productName + ' added to cart!';
            toast.style.background = '#0ba74c';
        } else {
            toast.textContent = '❌ ' + (data.message || 'Error!');
            toast.style.background = '#e74c3c';
        }
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 2500);
    });
}
</script>

</body>
</html> 