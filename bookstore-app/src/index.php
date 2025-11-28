<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';

$database = new Database();
$db = $database->getConnection();

// Get featured books
$query = "SELECT b.*, c.name as category_name FROM books b 
          LEFT JOIN categories c ON b.category_id = c.id 
          WHERE b.stock > 0 
          ORDER BY b.created_at DESC LIMIT 8";
$stmt = $db->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Beranda';
include 'includes/header.php';
?>

<style>
    .hero-minimal {
        background: #2c3e50;
        color: white;
        padding: 120px 20px 80px;
        position: relative;
        overflow: hidden;
    }
    
    .hero-minimal::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(52, 73, 94, 0.8) 0%, rgba(44, 62, 80, 0.95) 100%);
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }
    
    .hero-minimal h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        line-height: 1.2;
        letter-spacing: -1px;
    }
    
    .hero-minimal p {
        font-size: 1.25rem;
        margin-bottom: 2.5rem;
        opacity: 0.9;
        font-weight: 300;
    }
    
    .hero-btn {
        display: inline-block;
        padding: 16px 48px;
        background: white;
        color: #2c3e50;
        text-decoration: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .hero-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .section-header h2 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .section-header p {
        color: #7f8c8d;
        font-size: 1.1rem;
    }

    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    
    .book-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e8e8e8;
    }
    
    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-color: #d0d0d0;
    }
    
    .book-image {
        height: 320px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    
    .book-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .book-info {
        padding: 20px;
    }
    
    .book-category {
        display: inline-block;
        background: #ecf0f1;
        color: #34495e;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 12px;
    }
    
    .book-title {
        font-size: 17px;
        color: #2c3e50;
        margin: 0 0 8px 0;
        font-weight: 600;
        height: 48px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .book-author {
        color: #95a5a6;
        font-size: 14px;
        margin: 0 0 15px 0;
    }
    
    .book-price {
        color: #2c3e50;
        font-size: 22px;
        font-weight: 700;
        margin: 15px 0;
    }
    
    .book-btn {
        display: block;
        text-align: center;
        padding: 12px;
        background: #2c3e50;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .book-btn:hover {
        background: #34495e;
    }

    .view-all-btn {
        display: inline-block;
        padding: 14px 40px;
        background: #2c3e50;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .view-all-btn:hover {
        background: #34495e;
        transform: translateY(-2px);
    }

    .features-section {
        background: #f8f9fa;
        padding: 80px 20px;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .feature-card {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid #e8e8e8;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .feature-icon {
        font-size: 60px;
        margin-bottom: 20px;
    }
    
    .feature-card h3 {
        color: #2c3e50;
        font-size: 20px;
        margin-bottom: 12px;
        font-weight: 600;
    }
    
    .feature-card p {
        color: #7f8c8d;
        font-size: 15px;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .hero-minimal h1 {
            font-size: 2.5rem;
        }
        
        .hero-minimal p {
            font-size: 1.1rem;
        }
        
        .section-header h2 {
            font-size: 2rem;
        }
        
        .book-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-minimal">
    <div class="hero-content">
        <h1>Temukan Dunia dalam Setiap Halaman</h1>
        <p>Ribuan buku berkualitas siap menemani perjalanan literasi Anda</p>
        <a href="user/browse-books.php" class="hero-btn">Jelajahi Koleksi</a>
    </div>
</section>

<!-- Featured Books -->
<section style="padding: 80px 20px;">
    <div class="container">
        <div class="section-header">
            <h2>Buku Terbaru</h2>
            <p>Koleksi terbaru yang kami rekomendasikan untuk Anda</p>
        </div>
        
        <?php if (count($books) > 0): ?>
            <div class="book-grid">
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <div class="book-image">
                            <?php if ($book['image']): ?>
                                <img src="<?php echo BASE_URL . 'uploads/books/' . $book['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <?php else: ?>
                                <span style="font-size: 80px; color: #bdc3c7;">📖</span>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <span class="book-category"><?php echo htmlspecialchars($book['category_name']); ?></span>
                            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author"><?php echo htmlspecialchars($book['author']); ?></p>
                            <div class="book-price">Rp <?php echo number_format($book['price'], 0, ',', '.'); ?></div>
                            <a href="user/browse-books.php" class="book-btn">Lihat Detail</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #95a5a6; font-size: 18px;">Belum ada buku tersedia.</p>
        <?php endif; ?>
        
        <div style="text-align: center;">
            <a href="user/browse-books.php" class="view-all-btn">Lihat Semua Buku</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <h2>Mengapa Memilih TokoBook?</h2>
            <p>Kemudahan dan kenyamanan berbelanja buku online</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📚</div>
                <h3>Koleksi Lengkap</h3>
                <p>Ribuan judul buku dari berbagai kategori dan genre pilihan</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Harga Terbaik</h3>
                <p>Harga kompetitif dengan berbagai promo menarik setiap bulan</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>Pengiriman Cepat</h3>
                <p>Pengiriman ke seluruh Indonesia dengan layanan terpercaya</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Transaksi Aman</h3>
                <p>Sistem pembayaran yang aman dan terpercaya untuk kenyamanan Anda</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
