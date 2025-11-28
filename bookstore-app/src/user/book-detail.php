<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

$database = new Database();
$db = $database->getConnection();

$book_id = $_GET['id'] ?? 0;

// Get book details
$query = "SELECT b.*, c.name as category_name 
          FROM books b 
          LEFT JOIN categories c ON b.category_id = c.id 
          WHERE b.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header("Location: browse-books.php");
    exit();
}

$page_title = htmlspecialchars($book['title']);
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 20px;">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <a href="browse-books.php" style="display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none;">
        ← Kembali ke Daftar Buku
    </a>
    
    <div class="card">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px;">
            <!-- Book Image -->
            <div>
                <div style="background: #f8f9fa; border-radius: 10px; padding: 20px; display: flex; align-items: center; justify-content: center; min-height: 500px;">
                    <?php if ($book['image']): ?>
                        <img src="<?php echo BASE_URL . 'uploads/books/' . $book['image']; ?>" 
                             alt="<?php echo htmlspecialchars($book['title']); ?>"
                             style="max-width: 100%; max-height: 500px; object-fit: contain; border-radius: 10px;">
                    <?php else: ?>
                        <span style="font-size: 150px;">Book</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Book Details -->
            <div>
                <span style="display: inline-block; background: #3498db; color: white; padding: 5px 15px; border-radius: 20px; font-size: 13px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($book['category_name']); ?>
                </span>
                
                <h1 style="margin: 0 0 15px 0; font-size: 32px; color: #2c3e50;">
                    <?php echo htmlspecialchars($book['title']); ?>
                </h1>
                
                <p style="color: #7f8c8d; font-size: 18px; margin-bottom: 25px;">
                    <strong>Penulis:</strong> <?php echo htmlspecialchars($book['author']); ?>
                </p>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
                    <p style="margin: 0 0 10px 0; color: #7f8c8d;">Harga</p>
                    <h2 style="margin: 0; color: #e74c3c; font-size: 36px;">
                        Rp <?php echo number_format($book['price'], 0, ',', '.'); ?>
                    </h2>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <p style="margin: 0 0 5px 0; color: #7f8c8d;">Stok Tersedia</p>
                    <p style="margin: 0; font-size: 20px; font-weight: bold; color: <?php echo $book['stock'] > 0 ? '#2ecc71' : '#e74c3c'; ?>;">
                        <?php echo $book['stock']; ?> buah
                    </p>
                </div>
                
                <?php if (isset($_SESSION['user_id']) && $book['stock'] > 0): ?>
                    <a href="add-to-cart.php?id=<?php echo $book['id']; ?>" 
                       class="btn btn-primary" 
                       style="display: inline-block; padding: 15px 40px; font-size: 18px; text-decoration: none;">
                        Tambah ke Keranjang
                    </a>
                <?php elseif ($book['stock'] == 0): ?>
                    <button class="btn" disabled style="background: #95a5a6; padding: 15px 40px; font-size: 18px;">
                        Stok Habis
                    </button>
                <?php else: ?>
                    <a href="../auth/login.php" 
                       class="btn" 
                       style="background: #f39c12; color: white; padding: 15px 40px; font-size: 18px; text-decoration: none;">
                        Login untuk Membeli
                    </a>
                <?php endif; ?>
                
                <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #ecf0f1;">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">Deskripsi Buku</h3>
                    <p style="line-height: 1.8; color: #555; white-space: pre-line;">
                        <?php echo htmlspecialchars($book['description']); ?>
                    </p>
                </div>
                
                <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-left: 4px solid #f39c12; border-radius: 5px;">
                    <h4 style="margin: 0 0 10px 0; color: #856404;">Informasi Tambahan</h4>
                    <p style="margin: 5px 0; color: #856404;"><strong>Penerbit:</strong> <?php echo htmlspecialchars($book['publisher'] ?? '-'); ?></p>
                    <p style="margin: 5px 0; color: #856404;"><strong>Tahun Terbit:</strong> <?php echo htmlspecialchars($book['year'] ?? '-'); ?></p>
                    <p style="margin: 5px 0; color: #856404;"><strong>Kategori:</strong> <?php echo htmlspecialchars($book['category_name'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
