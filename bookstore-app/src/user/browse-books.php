<?php
session_start();
require_once "../includes/config.php";
require_once "../includes/database.php";

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$category = isset($_GET["category"]) ? (int)$_GET["category"] : 0;

$query = "SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id WHERE 1=1";
if ($search) {
    $query .= " AND (b.title LIKE :search OR b.author LIKE :search OR b.description LIKE :search)";
}
if ($category > 0) {
    $query .= " AND b.category_id = :category";
}
$query .= " ORDER BY b.created_at DESC";

$stmt = $db->prepare($query);
if ($search) {
    $searchParam = "%$search%";
    $stmt->bindParam(":search", $searchParam);
}
if ($category > 0) {
    $stmt->bindParam(":category", $category);
}
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$queryCategories = "SELECT * FROM categories ORDER BY name";
$stmtCategories = $db->prepare($queryCategories);
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Katalog Buku";
include "../includes/header.php";
?>

<style>
    :root {
        --brand-blue: #4834d4;
        --brand-dark: #130f40;
        --brand-accent: #686de0;
        --soft-bg: #f8f9fc;
        --card-white: #ffffff;
    }

    body { background-color: var(--soft-bg); font-family: "Plus Jakarta Sans", sans-serif; }

    .browse-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

    /* Modern Filter Bar */
    .filter-panel {
        background: var(--card-white);
        padding: 30px;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        margin-bottom: 40px;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .filter-form { display: grid; grid-template-columns: 1fr 200px 150px; gap: 15px; }

    .input-gram {
        padding: 15px 25px;
        border: 2px solid #f0f0f0;
        border-radius: 100px;
        font-size: 0.95rem;
        transition: 0.3s;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }

    .input-gram:focus { border-color: var(--brand-blue); background: #fff; }

    .btn-gram-search {
        background: var(--brand-blue);
        color: #fff;
        border: none;
        padding: 15px 30px;
        border-radius: 100px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-gram-search:hover { background: var(--brand-dark); transform: scale(1.02); }

    /* Category Pill Header */
    .cat-header { margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap; }
    .cat-pill-fixed {
        padding: 10px 20px;
        background: #fff;
        border: 1px solid #efefef;
        border-radius: 100px;
        text-decoration: none;
        color: #666;
        font-weight: 600;
        font-size: 0.85rem;
        transition: 0.3s;
    }
    .cat-pill-fixed.active { background: var(--brand-blue); color: #fff; border-color: var(--brand-blue); }

    /* Book Grid */
    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 30px;
    }

    .catalog-card {
        background: var(--card-white);
        border-radius: 20px;
        padding: 15px;
        text-decoration: none;
        color: inherit;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
    }

    .catalog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(72, 52, 212, 0.08);
        border-color: rgba(72, 52, 212, 0.1);
    }

    .catalog-img {
        width: 100%;
        aspect-ratio: 3/4;
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 15px;
        background: #f0f0f0;
    }

    .catalog-img img { width: 100%; height: 100%; object-fit: cover; }

    .catalog-info { padding: 5px; flex-grow: 1; }
    .catalog-cat {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--brand-accent);
        font-weight: 800;
        margin-bottom: 8px;
        display: block;
    }

    .catalog-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 5px;
        line-height: 1.3;
        color: var(--brand-dark);
        height: 40px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .catalog-author { font-size: 0.8rem; color: #999; margin-bottom: 15px; display: block; }

    .catalog-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px dashed #eee;
    }

    .catalog-price { font-size: 1.1rem; font-weight: 800; color: var(--brand-blue); }
    .catalog-stock { font-size: 0.75rem; color: #bbb; }

    .btn-buy-simple {
        width: 40px;
        height: 40px;
        background: var(--brand-dark);
        color: #fff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-weight: 800;
        transition: 0.3s;
    }
    .btn-buy-simple:hover { background: var(--brand-blue); transform: rotate(90deg); }

    @media (max-width: 768px) {
        .filter-form { grid-template-columns: 1fr; }
        .catalog-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
    }
</style>

<div class="browse-wrapper">
    <div style="margin-bottom: 40px;">
        <span style="text-transform: uppercase; letter-spacing: 3px; font-size: 0.7rem; font-weight: 800; color: var(--brand-accent);">Explore Our Library</span>
        <h1 style="font-size: 3rem; margin: 10px 0; color: var(--brand-dark);">Dunia dalam Genggaman</h1>
    </div>

    <!-- Category Pills -->
    <div class="cat-header">
        <a href="browse-books.php" class="cat-pill-fixed <?= $category == 0 ? "active" : "" ?>">Semua Buku</a>
        <?php foreach ($categories as $cat): ?>
            <a href="browse-books.php?category=<?= $cat["id"] ?>" class="cat-pill-fixed <?= $category == $cat["id"] ? "active" : "" ?>">
                <?= htmlspecialchars($cat["name"]) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Search Panel -->
    <div class="filter-panel">
        <form method="GET" action="" class="filter-form">
            <input type="text" name="search" class="input-gram" placeholder="Cari buku atau penulis favoritmu..." value="<?= htmlspecialchars($search) ?>">
            <select name="category" class="input-gram" style="border-radius: 100px;">
                <option value="0">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat["id"] ?>" <?= $category == $cat["id"] ? "selected" : "" ?>><?= htmlspecialchars($cat["name"]) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-gram-search">CARI</button>
        </form>
    </div>

    <?php if (count($books) > 0): ?>
        <div class="catalog-grid">
            <?php foreach ($books as $book): ?>
                <div class="catalog-card">
                    <a href="book-detail.php?id=<?= $book["id"] ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div class="catalog-img">
                            <img src="<?= BASE_URL . "uploads/books/" . (!empty($book["image"]) ? $book["image"] : "default.jpg") ?>" alt="<?= htmlspecialchars($book["title"]) ?>">
                        </div>
                        <div class="catalog-info">
                            <span class="catalog-cat"><?= htmlspecialchars($book["category_name"]) ?></span>
                            <h3 class="catalog-title"><?= htmlspecialchars($book["title"]) ?></h3>
                            <span class="catalog-author"><?= htmlspecialchars($book["author"]) ?></span>
                        </div>
                    </a>
                    <div class="catalog-footer">
                        <div>
                            <span class="catalog-price">Rp<?= number_format($book["price"], 0,",",".") ?></span>
                            <div class="catalog-stock">Stok: <?= $book["stock"] ?></div>
                        </div>
                        <?php if ($book["stock"] > 0 && isset($_SESSION["user_id"])): ?>
                            <a href="add-to-cart.php?id=<?= $book["id"] ?>" class="btn-buy-simple">+</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 100px 20px; background: #fff; border-radius: 30px;">
            <h2 style="color: #ccc;">Ups! Buku tidak ditemukan.</h2>
            <a href="browse-books.php" style="color: var(--brand-blue); font-weight: 700;">Kembali ke Katalog Lengkap</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
