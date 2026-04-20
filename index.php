<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopBD - Your Online Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1c1c37;
            --accent: #e94560;
            --gold: #f5a623;
            --light: #f8f5f0;
            --card: #ffffff;
            --text: #2d2d2d;
            --sub: #777;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--light);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* HERO SECTION */
        .hero {
            background: var(--primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(233,69,96,0.15) 0%, transparent 70%);
            top: -100px; right: -100px;
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(245,166,35,0.1) 0%, transparent 70%);
            bottom: -80px; left: -80px;
            border-radius: 50%;
            animation: pulse 4s ease-in-out infinite 2s;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .hero-content {
            text-align: center;
            z-index: 2;
            padding: 20px;
        }

        .logo-badge {
            background: var(--accent);
            color: white;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 6px 18px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 24px;
            animation: fadeDown 0.8s ease;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(3rem, 8vw, 7rem);
            font-weight: 900;
            color: white;
            line-height: 1.05;
            margin-bottom: 20px;
            animation: fadeDown 0.8s ease 0.1s both;
        }

        .hero h1 span {
            color: var(--accent);
        }

        .hero p {
            color: rgba(255,255,255,0.6);
            font-size: 1.1rem;
            font-weight: 300;
            margin-bottom: 50px;
            animation: fadeDown 0.8s ease 0.2s both;
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* LOGIN OPTIONS */
        .login-options {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp 0.8s ease 0.3s both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 40px 50px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            min-width: 220px;
            backdrop-filter: blur(10px);
        }

        .login-card:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.3);
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .login-card.admin-card:hover {
            border-color: var(--accent);
            box-shadow: 0 20px 60px rgba(233,69,96,0.2);
        }

        .login-card.user-card:hover {
            border-color: var(--gold);
            box-shadow: 0 20px 60px rgba(245,166,35,0.2);
        }

        .card-icon {
            width: 70px; height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .admin-icon { background: rgba(233,69,96,0.2); border: 2px solid var(--accent); }
        .user-icon { background: rgba(245,166,35,0.2); border: 2px solid var(--gold); }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .card-desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            text-align: center;
            line-height: 1.5;
        }

        .card-btn {
            padding: 10px 28px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        .admin-btn { background: var(--accent); color: white; }
        .user-btn { background: var(--gold); color: var(--primary); }

        /* FEATURES */
        .features {
            background: white;
            padding: 80px 40px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .feature-item {
            text-align: center;
            padding: 30px 20px;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .feature-item h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .feature-item p {
            font-size: 0.85rem;
            color: var(--sub);
            line-height: 1.6;
        }

        footer {
            background: var(--primary);
            color: rgba(255,255,255,0.5);
            text-align: center;
            padding: 20px;
            font-size: 0.85rem;
        }

        /* Floating dots */
        .dot {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>

<section class="hero">
    <!-- Floating dots -->
    <div class="dot" style="width:150px;height:150px;top:15%;left:5%;animation-delay:0s;"></div>
    <div class="dot" style="width:80px;height:80px;top:60%;left:80%;animation-delay:2s;"></div>
    <div class="dot" style="width:50px;height:50px;top:30%;left:75%;animation-delay:4s;"></div>
    <div class="dot" style="width:100px;height:100px;top:75%;left:15%;animation-delay:1s;"></div>

    <div class="hero-content">
        <div class="logo-badge">🛍️ Welcome to ShopBD</div>
        <h1>Shop <span>Smart</span>,<br>Live Better.</h1>
        <p>Your one-stop online store. Log in to explore amazing products.</p>

        <div class="login-options">
            <a href="login.php?role=admin" class="login-card admin-card">
                <div class="card-icon admin-icon">👑</div>
                <div class="card-title">Admin</div>
                <div class="card-desc">Manage products, orders, and the store dashboard</div>
                <div class="card-btn admin-btn">ADMIN LOGIN</div>
            </a>

            <a href="login.php?role=user" class="login-card user-card">
                <div class="card-icon user-icon">🛒</div>
                <div class="card-title">Customer</div>
                <div class="card-desc">Browse products, add to cart, and place orders</div>
                <div class="card-btn user-btn">USER LOGIN</div>
            </a>
        </div>
    </div>
</section>

<section class="features">
    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon">📦</div>
            <h3>Wide Selection</h3>
            <p>Electronics, clothing, accessories and more</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔒</div>
            <h3>Secure Orders</h3>
            <p>Safe and reliable order management</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">⚡</div>
            <h3>Fast & Simple</h3>
            <p>Easy to use shopping experience</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🎯</div>
            <h3>Best Prices</h3>
            <p>Competitive pricing on all products</p>
        </div>
    </div>
</section>

<footer>
    <p>&copy; 2024 ShopBD — Built with XAMPP (PHP + MySQL)</p>
</footer>

</body>
</html>
