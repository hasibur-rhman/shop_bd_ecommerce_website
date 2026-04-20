<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isLoggedIn() || !isUser()) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    // Check product exists and has stock
    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id AND stock>0"));

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not available']);
        exit();
    }

    // Check if already in cart
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id"));

    if ($existing) {
        mysqli_query($conn, "UPDATE cart SET quantity=quantity+1 WHERE user_id=$user_id AND product_id=$product_id");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
