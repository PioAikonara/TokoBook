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
    <title><?php echo isset($page_title) ? $page_title . ' - TokoBook' : 'TokoBook - Toko Buku Online'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>index.php">TokoBook</a>
            </div>
            <ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/browse-books.php">Buku</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/cart.php">Keranjang</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/orders.php">Pesanan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/contact.php">Kontak</a></li>
                    <li class="user-info"><?php echo htmlspecialchars($_SESSION['user_name']); ?></li>
                    <li><a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn-logout">Logout</a></li>
                <?php elseif (isset($_SESSION['admin_id'])): ?>
                    <li><a href="<?php echo BASE_URL; ?>admin/index.php">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/manage-books.php">Kelola Buku</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/manage-orders.php">Pesanan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>admin/manage-users.php">Users</a></li>
                    <li class="admin-info"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></li>
                    <li><a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/browse-books.php">Buku</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/about.php">Tentang</a></li>
                    <li><a href="<?php echo BASE_URL; ?>auth/login.php">Login</a></li>
                    <li><a href="<?php echo BASE_URL; ?>auth/register.php" class="btn-register">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>