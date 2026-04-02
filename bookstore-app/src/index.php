<?php
session_start();
require_once "includes/config.php";
require_once "includes/database.php";

$database = new Database();
$db = $database->getConnection();

$cat_query = "SELECT c.*, (SELECT image FROM books WHERE category_id = c.id LIMIT 1) as cat_image FROM categories c LIMIT 6";
$cat_stmt = $db->prepare($cat_query);
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

$best_query = "SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id ORDER BY RAND() LIMIT 5";
$best_stmt = $db->prepare($best_query);
$best_stmt->execute();
$best_sellers = $best_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Beranda";
include "includes/header.php";
?>

<style>
    @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap");

    :root {
        --primary: #4834d4;
        --secondary: #686de0;
        --dark: #130f40;
        --light: #f9f9ff;
        --white: #ffffff;
    }

    body {
        font-family: "Plus Jakarta Sans", sans-serif;
        background-color: var(--light);
        margin: 0;
        color: var(--dark);
    }

    .hero-creative {
        padding: 100px 20px;
        background: var(--white);
        display: flex;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        gap: 50px;
    }

    .hero-text { flex: 1; }
    .hero-text h1 {
        font-size: 4.5rem;
        line-height: 1;
        font-weight: 800;
        margin-bottom: 20px;
        background: linear-gradient(to right, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-text p {
        font-size: 1.25rem;
        color: #535c68;
        margin-bottom: 30px;
        max-width: 500px;
    }

    .btn-main {
        padding: 18px 40px;
        background: var(--dark);
        color: var(--white);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        display: inline-block;
        transition: 0.3s;
    }

    .btn-main:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(19, 15, 64, 0.2); }

    .hero-visual {
        flex: 1;
        position: relative;
        display: flex;
        justify-content: center;
    }

    .floating-card {
        width: 300px;
        height: 400px;
        background: var(--primary);
        border-radius: 30px;
        transform: rotate(-10deg);
        position: relative;
        z-index: 1;
        overflow: hidden;
        box-shadow: 20px 20px 60px rgba(0,0,0,0.1);
    }

    /* Bento Grid Features */
    .bento-container {
        max-width: 1200px;
        margin: 50px auto;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(2, 300px);
        gap: 20px;
        padding: 0 20px;
    }

    .bento-item {
        background: var(--white);
        border-radius: 32px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        transition: 0.3s;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .bento-item:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }

    .bento-big { grid-column: span 2; grid-row: span 2; background: var(--dark); color: var(--white); }
    .bento-wide { grid-column: span 2; }

    .cat-pill {
        display: inline-block;
        padding: 8px 20px;
        background: #f0f0f0;
        border-radius: 100px;
        margin: 5px;
        text-decoration: none;
        color: var(--dark);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .book-card-alt {
        display: flex;
        align-items: center;
        gap: 20px;
        text-decoration: none;
        color: inherit;
        margin-bottom: 20px;
    }

    .book-card-alt img { width: 80px; height: 110px; border-radius: 12px; object-fit: cover; }
    .book-card-alt h4 { margin: 0; font-size: 1rem; }
    .book-card-alt p { margin: 5px 0 0; color: var(--primary); font-weight: 800; }

    .section-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--secondary);
        font-weight: 800;
        margin-bottom: 15px;
        display: block;
    }

    @media (max-width: 900px) {
        .bento-container { grid-template-columns: 1fr; grid-template-rows: auto; }
        .hero-creative { flex-direction: column; padding: 60px 20px; text-align: center; }
        .hero-text h1 { font-size: 3rem; }
    }
</style>

<section class="hero-creative">
    <div class="hero-text">
        <span class="section-label">Est. 2026</span>
        <h1>Curated.<br>Unlimited.</h1>
        <p>Bukan sekadar toko, TokoBook adalah perjalanan imajinasi yang dikurasi khusus untuk Anda.</p>
        <a href="user/browse-books.php" class="btn-main">Mulai Petualangan</a>
    </div>
    <div class="hero-visual">
        <div class="floating-card">
            <div style="padding: 40px; color: white;">
                <h2 style="font-size: 2.5rem; margin: 0;">Featured<br>Story</h2>
                <div style="margin-top: 20px; width: 50px; height: 4px; background: white;"></div>
            </div>
        </div>
        <div style="position:absolute; bottom: -20px; right: 20px; width: 150px; height: 150px; background: var(--secondary); border-radius: 50%; z-index: 0; opacity: 0.2;"></div>
    </div>
</section>

<div class="bento-container">
    <!-- BEST SELLERS -->
    <div class="bento-item bento-big">
        <span class="section-label" style="color: #686de0;">Top Picks</span>
        <h2 style="font-size: 2.5rem; margin-bottom: 40px;">Buku Terlaris <br>Bulan Ini</h2>
        
        <?php foreach(array_slice($best_sellers, 0, 3) as $book): ?>
        <a href="user/book-detail.php?id=<?= $book["id"] ?>" class="book-card-alt">
            <img src="uploads/books/<?= !empty($book["image"]) ? $book["image"] : "default.jpg" ?>" alt="book">
            <div>
                <h4><?= htmlspecialchars($book["title"]) ?></h4>
                <p>Rp<?= number_format($book["price"], 0,",",".") ?></p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- CATEGORIES -->
    <div class="bento-item bento-wide">
        <span class="section-label">Discovery</span>
        <h3>Jelajahi Berdasarkan Genre</h3>
        <div style="margin-top: 20px;">
            <?php foreach($categories as $cat): ?>
            <a href="user/browse-books.php?category=<?= $cat["id"] ?>" class="cat-pill"><?= htmlspecialchars($cat["name"]) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SMALL FEATURE 1 -->
    <div class="bento-item">
        <span class="section-label">Service</span>
        <h3 style="font-size: 1.2rem;">Pengiriman Express</h3>
        <p style="font-size: 0.9rem; color: #888;">Sampai di rumah Anda sebelum kopi Anda dingin.</p>
    </div>

    <!-- SMALL FEATURE 2 -->
    <div class="bento-item">
        <span class="section-label">Trust</span>
        <h3 style="font-size: 1.2rem;">100% Original</h3>
        <p style="font-size: 0.9rem; color: #888;">Hanya buku asli dari penerbit terpercaya.</p>
    </div>
</div>

<div style="text-align: center; padding: 100px 20px;">
    <h2 style="font-size: 3rem; margin-bottom: 20px;">Siap Menemukan Dunia Baru?</h2>
    <a href="user/browse-books.php" style="color: var(--primary); font-weight: 800; text-decoration: none; font-size: 1.5rem; border-bottom: 4px solid var(--primary);">Buka Katalog Sekarang ?</a>
</div>

<?php include "includes/footer.php"; ?>
