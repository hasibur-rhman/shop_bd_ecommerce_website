<?php
// Shared styles for all pages
?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
        --primary: #1a1a2e;
        --accent: #e94560;
        --gold: #f5a623;
        --light: #f8f5f0;
        --white: #ffffff;
        --border: #eaeaea;
        --sub: #888;
        --success: #27ae60;
        --danger: #e74c3c;
        --warning: #f39c12;
    }
    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--light);
        color: #2d2d2d;
        min-height: 100vh;
    }
    nav {
        background: var(--primary);
        padding: 15px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 20px rgba(0,0,0,0.3);
    }
    .nav-logo {
        font-family: 'Playfair Display', serif;
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        text-decoration: none;
    }
    .nav-logo span { color: var(--accent); }
    .nav-links { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .nav-links a {
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .nav-links a:hover { background: rgba(255,255,255,0.1); color: white; }
    .nav-links a.active { background: var(--accent); color: white; }
    .nav-links a.logout { background: rgba(233,69,96,0.15); color: var(--accent); border: 1px solid rgba(233,69,96,0.3); }
    .nav-links a.logout:hover { background: var(--accent); color: white; }
    .nav-user {
        color: rgba(255,255,255,0.5);
        font-size: 0.82rem;
        margin-right: 5px;
    }
    .page-container { max-width: 1100px; margin: 0 auto; padding: 30px 20px; }
    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        color: var(--primary);
        margin-bottom: 25px;
    }
    .card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        border: none;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.2s;
    }
    .btn:hover { opacity: 0.85; transform: translateY(-1px); }
    .btn-primary { background: var(--accent); color: white; }
    .btn-success { background: var(--success); color: white; }
    .btn-danger { background: var(--danger); color: white; }
    .btn-warning { background: var(--warning); color: white; }
    .btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--border); }
    .btn-gold { background: var(--gold); color: var(--primary); }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-confirmed { background: #d1ecf1; color: #0c5460; }
    .badge-delivered { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .alert {
        padding: 12px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    table { width: 100%; border-collapse: collapse; }
    th { background: var(--light); padding: 12px 16px; text-align: left; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--sub); font-weight: 600; }
    td { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 0.9rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fafafa; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: #555; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--border);
        border-radius: 10px;
        font-size: 0.92rem;
        font-family: 'DM Sans', sans-serif;
        outline: none;
        transition: border 0.2s;
        color: #2d2d2d;
        background: white;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--accent); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; border-radius: 16px; padding: 25px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 18px; }
    .stat-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
    .stat-icon.red { background: rgba(233,69,96,0.1); }
    .stat-icon.gold { background: rgba(245,166,35,0.1); }
    .stat-icon.green { background: rgba(39,174,96,0.1); }
    .stat-icon.blue { background: rgba(52,152,219,0.1); }
    .stat-value { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: var(--primary); }
    .stat-label { font-size: 0.8rem; color: var(--sub); margin-top: 2px; }
    .empty-state { text-align: center; padding: 60px 20px; color: var(--sub); }
    .empty-state .ei { font-size: 4rem; margin-bottom: 15px; }
    .empty-state h3 { font-size: 1.2rem; color: #aaa; margin-bottom: 8px; }
    input[type="search"] { width: 100%; }
</style>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
