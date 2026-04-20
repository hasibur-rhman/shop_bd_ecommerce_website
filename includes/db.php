<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("<div style='font-family:sans-serif;padding:20px;background:#fee;border:1px solid red;margin:20px;border-radius:8px;'>
    <h3>❌ Database Connection Failed!</h3>
    <p>Error: " . mysqli_connect_error() . "</p>
    <p>Please make sure:</p>
    <ul>
        <li>XAMPP is running (Apache + MySQL)</li>
        <li>You imported <strong>database.sql</strong> in phpMyAdmin</li>
        <li>Database name is <strong>ecommerce_db</strong></li>
    </ul>
    </div>");
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}
?>
