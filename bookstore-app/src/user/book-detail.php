<?php
session_start();
require_once "../includes/config.php";
require_once "../includes/database.php";

$database = new Database();
$db = $database->getConnection();

$book_id = $_GET["id"] ?? 0;

$query = "SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id WHERE b.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header("Location: browse-books.php");
    exit();
}

$page_title = htmlspecialchars($book["title"]);
include "../includes/header.php";
?>

<style>
    :root {
        --brand-blue: #4834d4;
        --brand-dark: #130f40;
        --brand-accent: #686de0;
        --soft-bg: #f8f9fc;
        --card-white: #ffffff;
        --success: #2ecc71;
    }

    body { background-color: var(--soft-bg); font-family: "Plus Jakarta Sans", sans-serif; color: var(--brand-dark); }

    .detail-container { max-width: 1100px; margin: 50px auto; padding: 0 20px; }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: var(--brand-accent);
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 30px;
        transition: 0.3s;
    }
    .back-btn:hover { color: var(--brand-blue); transform: translateX(-5px); }

    .book-master-card {
        background: var(--card-white);
        border-radius: 32px;
        padding: 50px;
        box-shadow: 0 20px 60px rgba(19, 15, 64, 0.05);
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 60px;
        border: 1px solid rgba(0,0,0,0.02);
    }

    .book-visual { position: relative; }
    .book-img-holder {
        width: 100%;
        aspect-ratio: 3/4;
        background: #f0f0f5;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
    .book-img-holder img { width: 100%; height: 100%; object-fit: cover; }

    .badge-cat {
        display: inline-block;
        padding: 8px 20px;
        background: rgba(72, 52, 212, 0.1);
        color: var(--brand-blue);
        border-radius: 100px;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
    }

    .book-title { font-size: 3rem; font-weight: 800; line-height: 1.1; margin-bottom: 10px; color: var(--brand-dark); }
    .book-author { font-size: 1.25rem; color: #999; margin-bottom: 40px; }

    .price-box {
        background: var(--soft-bg);
        padding: 30px;
        border-radius: 20px;
        margin-bottom: 40px;
    }
    .label-muted { font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 10px; display: block; }
    .price-large { font-size: 2.5rem; font-weight: 800; color: var(--brand-blue); }

    .stock-info { display: flex; align-items: center; gap: 10px; margin-bottom: 40px; }
    .stock-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--success); }
    .stock-text { font-weight: 700; color: #555; }

    .action-row { display: flex; gap: 20px; }
    .btn-cart {
        flex: 1;
        padding: 20px;
        background: var(--brand-dark);
        color: #fff;
        text-decoration: none;
        border-radius: 16px;
        text-align: center;
        font-weight: 700;
        transition: 0.3s;
        box-shadow: 0 10px 20px rgba(19, 15, 64, 0.2);
    }
    .btn-cart:hover { background: var(--brand-blue); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(72, 52, 212, 0.3); }

    .btn-disabled { background: #ccc; box-shadow: none; cursor: not-allowed; }
    .btn-login { background: #f0932b; box-shadow: 0 10px 20px rgba(240, 147, 43, 0.2); }

    .book-desc { margin-top: 60px; }
    .desc-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 20px; border-bottom: 4px solid var(--soft-bg); display: inline-block; padding-bottom: 5px; }
    .desc-text { line-height: 1.8; color: #535c68; font-size: 1.05rem; }

    .extra-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-top: 50px;
        padding: 30px;
        background: #fff;
        border: 2px solid var(--soft-bg);
        border-radius: 24px;
    }
    .info-item h5 { margin: 0 0 5px 0; color: #999; font-size: 0.75rem; text-transform: uppercase; }
    .info-item p { margin: 0; font-weight: 700; color: var(--brand-dark); }

    @media (max-width: 992px) {
        .book-master-card { grid-template-columns: 1fr; padding: 30px; }
        .book-title { font-size: 2rem; }
    }
</style>

<div class="detail-container">
    <a href="browse-books.php" class="back-btn">? Kembali ke Katalog</a>

    <?php if (isset($_SESSION["success"])): ?>
        <div style="background: rgba(46, 204, 113, 0.1); color: #27ae60; padding: 20px; border-radius: 15px; margin-bottom: 30px; font-weight: 700;">
            <?= $_SESSION["success"]; unset($_SESSION["success"]); ?>
        </div>
    <?php endif; ?>

    <div class="book-master-card">
        <div class="book-visual">
            <div class="book-img-holder">
                <img src="<?= BASE_URL . "uploads/books/" . (!empty($book["image"]) ? $book["image"] : "default.jpg") ?>" alt="<?= $page_title ?>">
            </div>
        </div>

        <div class="book-content">
            <span class="badge-cat"><?= htmlspecialchars($book["category_name"]) ?></span>
            <h1 class="book-title"><?= htmlspecialchars($book["title"]) ?></h1>
            <p class="book-author">oleh <?= htmlspecialchars($book["author"]) ?></p>

            <div class="price-box">
                <span class="label-muted">Harga Terbaik</span>
                <span class="price-large">Rp<?= number_format($book["price"], 0, ",", ".") ?></span>
            </div>

            <div class="stock-info">
                <div class="stock-dot" style="background: <?= $book["stock"] > 0 ? "var(--success)" : "#eb4d4b" ?>;"></div>
                <span class="stock-text"><?= $book["stock"] > 0 ? "Tersedia " . $book["stock"] . " buah" : "Stok Habis" ?></span>
            </div>

            <div class="action-row">
                <?php if (isset($_SESSION["user_id"]) && $book["stock"] > 0): ?>
                    <a href="add-to-cart.php?id=<?= $book["id"] ?>" class="btn-cart">TAMBAH KE KERANJANG</a>
                <?php elseif ($book["stock"] == 0): ?>
                    <a href="#" class="btn-cart btn-disabled" onclick="return false;">STOK TIDAK TERSEDIA</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn-cart btn-login">LOGIN UNTUK MEMBELI</a>
                <?php endif; ?>
            </div>

            <div class="book-desc">
                <h3 class="desc-title">Sinopsis</h3>
                <p class="desc-text"><?= nl2br(htmlspecialchars($book["description"])) ?></p>
            </div>

            <div class="extra-info-grid">
                <div class="info-item">
                    <h5>Penerbit</h5>
                    <p><?= htmlspecialchars($book["publisher"] ?? "-") ?></p>
                </div>
                <div class="info-item">
                    <h5>Tahun Terbit</h5>
                    <p><?= htmlspecialchars($book["year"] ?? "-") ?></p>
                </div>
                <div class="info-item">
                    <h5>Kategori</h5>
                    <p><?= htmlspecialchars($book["category_name"] ?? "-") ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
