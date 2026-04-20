<?php
require_once 'includes/db.php';

if (isLoggedIn()) {
    if (isAdmin()) redirect('admin/dashboard.php');
    else redirect('user/home.php');
}

$role = isset($_GET['role']) ? $_GET['role'] : 'user';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            redirect('admin/dashboard.php');
        } else {
            redirect('user/home.php');
        }
    } else {
        $error = 'Invalid email or password!';
    }
}

$isAdmin = $role === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isAdmin ? 'Admin' : 'User' ?> Login — ShopBD</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #1a1a2e;
            --accent: <?= $isAdmin ? '#e94560' : '#f5a623' ?>;
            --bg: #f8f5f0;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-wrap {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            margin: 20px;
        }

        .left-panel {
            background: var(--accent);
            width: 45%;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 250px; height: 250px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            bottom: -50px; right: -50px;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            top: -30px; left: -30px;
        }

        .panel-icon { font-size: 4rem; margin-bottom: 20px; z-index: 1; position: relative; }

        .panel-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: white;
            font-weight: 700;
            margin-bottom: 12px;
            z-index: 1; position: relative;
        }

        .panel-desc {
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
            line-height: 1.7;
            z-index: 1; position: relative;
        }

        .credentials {
            margin-top: 30px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 16px 20px;
            z-index: 1; position: relative;
        }

        .credentials p {
            color: rgba(255,255,255,0.9);
            font-size: 0.8rem;
            margin-bottom: 6px;
        }
        .credentials strong { color: white; }

        .right-panel {
            background: white;
            flex: 1;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #999;
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 35px;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--accent); }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .form-sub {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 35px;
        }

        .error-msg {
            background: #fff0f3;
            border: 1px solid #ffc0cb;
            border-radius: 10px;
            padding: 12px 16px;
            color: #c0392b;
            font-size: 0.88rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
            outline: none;
            color: var(--primary);
        }

        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(var(--accent-rgb, 233,69,96),0.08);
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            margin-top: 5px;
        }

        .submit-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .switch-role {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #aaa;
        }

        .switch-role a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .left-panel { display: none; }
            .right-panel { padding: 40px 25px; }
        }
    </style>
</head>
<body>

<div class="page-wrap">
    <div class="left-panel">
        <div class="panel-icon"><?= $isAdmin ? '👑' : '🛒' ?></div>
        <div class="panel-title"><?= $isAdmin ? 'Admin Panel' : 'Welcome Back!' ?></div>
        <div class="panel-desc">
            <?= $isAdmin
                ? 'Manage your store, add products, confirm orders and control everything from here.'
                : 'Browse thousands of products, add to cart and order with ease.' ?>
        </div>
        <div class="credentials">
            <p>📧 <strong>Email:</strong> <?= $isAdmin ? 'admin@gmail.com' : 'user@gmail.com' ?></p>
            <p>🔑 <strong>Password:</strong> <?= $isAdmin ? 'admin' : 'user' ?></p>
        </div>
    </div>

    <div class="right-panel">
        <a href="index.php" class="back-link">← Back to Home</a>

        <div class="form-title">Sign In</div>
        <div class="form-sub">Login as <?= $isAdmin ? 'Administrator' : 'Customer' ?></div>

        <?php if ($error): ?>
        <div class="error-msg">❌ <?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email"
                    value="<?= $isAdmin ? 'admin@gmail.com' : 'user@gmail.com' ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password"
                    value="<?= $isAdmin ? 'admin' : 'user' ?>" required>
            </div>
            <button type="submit" class="submit-btn">
                <?= $isAdmin ? '🔐 Admin Login' : '🛒 Login & Shop' ?>
            </button>
        </form>

        <div class="switch-role">
            <?php if ($isAdmin): ?>
                Not admin? <a href="login.php?role=user">Login as User</a>
            <?php else: ?>
                Are you admin? <a href="login.php?role=admin">Login as Admin</a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
