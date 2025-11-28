<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

$database = new Database();
$db = $database->getConnection();

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build query
$query = "SELECT b.*, c.name as category_name 
          FROM books b 
          LEFT JOIN categories c ON b.category_id = c.id 
          WHERE 1=1";

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
    $stmt->bindParam(':search', $searchParam);
}

if ($category > 0) {
    $stmt->bindParam(':category', $category);
}

$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$queryCategories = "SELECT * FROM categories ORDER BY name";
$stmtCategories = $db->prepare($queryCategories);
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Jelajahi Buku';
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">Jelajahi Buku</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error" style="padding: 15px; margin-bottom: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Search and Filter -->
    <div class="search-filter" style="margin-bottom: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Cari judul, penulis..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   style="flex: 1; min-width: 250px; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            
            <select name="category" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="0">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" style="padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Cari
            </button>
            
            <?php if ($search || $category): ?>
                <a href="browse-books.php" style="padding: 12px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px;">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Books Grid -->
    <?php if (count($books) > 0): ?>
        <div class="books-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
            <?php foreach ($books as $book): ?>
                <div class="book-card" style="border: 1px solid #ddd; padding: 20px; border-radius: 10px; transition: 0.3s; background: white;">
                    <div style="height: 300px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <?php if ($book['image']): ?>
                            <img src="<?php echo BASE_URL . 'uploads/books/' . $book['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>"
                                 style="max-width: 100%; max-height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-size: 80px;">Book</span>
                        <?php endif; ?>
                    </div>
                    
                    <span style="display: inline-block; background: #3498db; color: white; padding: 3px 10px; border-radius: 3px; font-size: 11px; margin-bottom: 10px;">
                        <?php echo htmlspecialchars($book['category_name']); ?>
                    </span>
                    
                    <h3 style="margin: 10px 0; font-size: 18px;">
                        <?php echo htmlspecialchars($book['title']); ?>
                    </h3>
                    
                    <p style="color: #7f8c8d; margin: 5px 0;">
                        <?php echo htmlspecialchars($book['author']); ?>
                    </p>
                    
                    <p style="color: #e74c3c; font-size: 20px; font-weight: bold; margin: 15px 0;">
                        Rp <?php echo number_format($book['price'], 0, ',', '.'); ?>
                    </p>
                    
                    <p style="color: #95a5a6; font-size: 13px; margin: 5px 0;">
                        Stok: <?php echo $book['stock']; ?> buah
                    </p>
                    
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <a href="book-detail.php?id=<?php echo $book['id']; ?>" 
                           style="flex: 1; text-align: center; padding: 10px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">
                            Detail
                        </a>
                        
                        <?php if (isset($_SESSION['user_id']) && $book['stock'] > 0): ?>
                            <a href="add-to-cart.php?id=<?php echo $book['id']; ?>" 
                               style="flex: 1; text-align: center; padding: 10px; background: #2ecc71; color: white; text-decoration: none; border-radius: 5px;">
                                Beli
                            </a>
                        <?php elseif ($book['stock'] == 0): ?>
                            <span style="flex: 1; text-align: center; padding: 10px; background: #95a5a6; color: white; border-radius: 5px;">
                                Habis
                            </span>
                        <?php else: ?>
                            <a href="../auth/login.php" 
                               style="flex: 1; text-align: center; padding: 10px; background: #f39c12; color: white; text-decoration: none; border-radius: 5px;">
                                Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px;">
            <p style="font-size: 18px; color: #95a5a6;">Tidak ada buku ditemukan.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
