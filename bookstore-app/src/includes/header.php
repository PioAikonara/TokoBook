<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - TokoBook" : "TokoBook - Toko Buku Online"; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-blue: #4834d4;
            --brand-dark: #130f40;
            --brand-accent: #686de0;
            --soft-bg: #f8f9fc;
            --nav-white: rgba(255, 255, 255, 0.95);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: var(--soft-bg);
            color: var(--brand-dark);
        }

        /* Modern Navigation Styles */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: var(--nav-white);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo a {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--brand-blue);
            text-decoration: none;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links li a {
            text-decoration: none;
            color: var(--brand-dark);
            font-weight: 600;
            font-size: 0.95rem;
            padding: 10px 18px;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: block;
        }

        /* Visual Feedback on Click */
        .nav-links li a:active {
            transform: scale(0.92);
            background: rgba(72, 52, 212, 0.08);
        }

        .nav-links li a:hover {
            color: var(--brand-blue);
            background: rgba(72, 52, 212, 0.05);
        }

        /* Interactive Underline */
        .nav-links li a::after {
            content: "";
            position: absolute;
            bottom: 6px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--brand-blue);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .nav-links li a:hover::after {
            width: 20px;
        }

        /* Identity Badges */
        .user-badge, .admin-badge {
            font-size: 0.85rem;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 50px;
            margin: 0 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .user-badge { background: rgba(72, 52, 212, 0.1); color: var(--brand-blue); border: 1px solid rgba(72, 52, 212, 0.1); }
        .admin-badge { background: #fff3e0; color: #f57c00; border: 1px solid #ffe0b2; }

        /* Logout Button */
        .btn-logout {
            border: 1.5px solid #fee2e2 !important;
            color: #ef4444 !important;
        }
        .btn-logout:hover {
            background: #fff1f2 !important;
            border-color: #fca5a5 !important;
        }

        /* Register/Primary Action */
        .btn-register-nav {
            background: var(--brand-blue) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(72, 52, 212, 0.15);
        }
        .btn-register-nav:hover {
            box-shadow: 0 8px 20px rgba(72, 52, 212, 0.25);
            transform: translateY(-2px);
        }
        .btn-register-nav:active {
            transform: translateY(0) scale(0.95) !important;
        }

        main { min-height: 80vh; padding-top: 20px; }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="nav-container">
            <div class="nav-logo">
                <a href="<?php echo BASE_URL; ?>index.php">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    TokoBook
                </a>
            </div>
            <ul class="nav-links">
                <?php if (isset($_SESSION["user_id"])): ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/browse-books.php">Katalog</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/cart.php">Keranjang</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/orders.php">Pesanan</a></li>
                    <div class="user-badge" style="display: flex; align-items: center; gap: 6px;">
                        <span>👋</span>
                        <span><?php echo explode(" ", htmlspecialchars($_SESSION["user_name"]))[0]; ?></span>
                    </div>
                    <li><a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn-logout">Logout</a></li>
                <?php elseif (isset($_SESSION["admin_id"])): ?>
                    <li><a href="<?php echo BASE_URL; ?>admin/index.php">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/manage-books.php">Buku</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/manage-orders.php">Pesanan</a></li>
                    <div class="admin-badge" style="display: flex; align-items: center; gap: 6px;">
                        <span>🛠️</span>
                        <span>Admin</span>
                    </div>
                    <li><a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/browse-books.php">Buku</a></li>
                    <li><a href="<?php echo BASE_URL; ?>about.php">Tentang</a></li>
                    <li><a href="<?php echo BASE_URL; ?>auth/login.php">Login</a></li>
                    <li><a href="<?php echo BASE_URL; ?>auth/register.php" class="btn-register-nav">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>