<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login-admin.php");
    exit();
}

require_once '../includes/config.php';
require_once '../includes/database.php';

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ADD BOOK
    if (isset($_POST['add_book'])) {
        $category_id = $_POST['category_id'];
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $publisher = trim($_POST['publisher']);
        $year = $_POST['year'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = trim($_POST['description']);
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../uploads/books/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $new_filename;
                }
            }
        }
        
        $query = "INSERT INTO books (category_id, title, author, publisher, year, price, stock, description, image) 
                  VALUES (:category_id, :title, :author, :publisher, :year, :price, :stock, :description, :image)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        
        if ($stmt->execute()) {
            $success = 'Buku berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan buku!';
        }
    }
    
    // UPDATE BOOK
    if (isset($_POST['update_book'])) {
        $id = $_POST['id'];
        $category_id = $_POST['category_id'];
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $publisher = trim($_POST['publisher']);
        $year = $_POST['year'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = trim($_POST['description']);
        
        // Handle image upload
        $image_update = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../uploads/books/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image
                    $query = "SELECT image FROM books WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $old_book = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($old_book && $old_book['image']) {
                        @unlink('../uploads/books/' . $old_book['image']);
                    }
                    
                    $image_update = ", image = :image";
                }
            }
        }
        
        $query = "UPDATE books SET category_id = :category_id, title = :title, author = :author, 
                  publisher = :publisher, year = :year, price = :price, stock = :stock, 
                  description = :description" . $image_update . " WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);
        
        if ($image_update) {
            $stmt->bindParam(':image', $new_filename);
        }
        
        if ($stmt->execute()) {
            $success = 'Buku berhasil diupdate!';
        } else {
            $error = 'Gagal mengupdate buku!';
        }
    }
    
    // DELETE BOOK
    if (isset($_POST['delete_book'])) {
        $id = $_POST['id'];
        
        // Get image filename
        $query = "SELECT image FROM books WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete book
        $query = "DELETE FROM books WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            // Delete image file
            if ($book && $book['image']) {
                @unlink('../uploads/books/' . $book['image']);
            }
            $success = 'Buku berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus buku!';
        }
    }
    
    // ADD CATEGORY
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['cat_name']);
        $description = trim($_POST['cat_description']);
        
        $query = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            $success = 'Kategori berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan kategori!';
        }
    }
    
    // DELETE CATEGORY
    if (isset($_POST['delete_category'])) {
        $id = $_POST['cat_id'];
        
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $success = 'Kategori berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus kategori!';
        }
    }
}

// Get Categories
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Books
$query = "SELECT b.*, c.name as category_name 
          FROM books b 
          LEFT JOIN categories c ON b.category_id = c.id 
          ORDER BY b.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Kelola Buku & Kategori';
include '../includes/header.php';
?>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}
.modal-content {
    background-color: #fefefe;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #888;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}
.close:hover {
    color: #000;
}
.tab-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #ddd;
}
.tab-button {
    padding: 10px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: #666;
    border-bottom: 3px solid transparent;
}
.tab-button.active {
    color: #667eea;
    border-bottom-color: #667eea;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
</style>

<div class="container" style="padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Kelola Buku & Kategori</h1>
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Tab Buttons -->
    <div class="tab-buttons">
        <button class="tab-button active" onclick="openTab('books')">Buku (<?php echo count($books); ?>)</button>
        <button class="tab-button" onclick="openTab('categories')">Kategori (<?php echo count($categories); ?>)</button>
    </div>

    <!-- BOOKS TAB -->
    <div id="books" class="tab-content active">
        <div style="margin-bottom: 20px;">
            <button onclick="document.getElementById('addBookModal').style.display='block'" class="btn btn-primary">
                Tambah Buku Baru
            </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            <?php foreach ($books as $book): ?>
                <div class="card" style="padding: 0; overflow: hidden;">
                    <div style="height: 300px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php if ($book['image']): ?>
                            <img src="<?php echo BASE_URL . 'uploads/books/' . $book['image']; ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="font-size: 80px; color: #bdc3c7;">📖</div>
                        <?php endif; ?>
                    </div>
                    <div style="padding: 20px;">
                        <span style="background: #3498db; color: white; padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: 500;">
                            <?php echo htmlspecialchars($book['category_name']); ?>
                        </span>
                        <h3 style="margin: 12px 0 8px 0; font-size: 16px; color: #2c3e50; height: 45px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 14px; margin: 0 0 12px 0;">
                            <?php echo htmlspecialchars($book['author']); ?>
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-top: 1px solid #ecf0f1; border-bottom: 1px solid #ecf0f1; margin-bottom: 15px;">
                            <div>
                                <small style="color: #7f8c8d; display: block; font-size: 12px;">Harga</small>
                                <strong style="color: #e74c3c; font-size: 18px;">Rp <?php echo number_format($book['price'], 0, ',', '.'); ?></strong>
                            </div>
                            <div style="text-align: right;">
                                <small style="color: #7f8c8d; display: block; font-size: 12px;">Stok</small>
                                <strong style="color: #2c3e50; font-size: 18px;"><?php echo $book['stock']; ?></strong>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick='editBook(<?php echo json_encode($book); ?>)' 
                                    style="flex: 1; background: #f39c12; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 13px;">
                                Edit
                            </button>
                            <form method="POST" style="flex: 1;" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                                <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                <button type="submit" name="delete_book" style="width: 100%; background: #e74c3c; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 13px;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CATEGORIES TAB -->
    <div id="categories" class="tab-content">
        <div style="margin-bottom: 20px;">
            <button onclick="document.getElementById('addCategoryModal').style.display='block'" class="btn btn-primary">
                Tambah Kategori Baru
            </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <?php foreach ($categories as $category): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p style="color: #666; margin: 10px 0;"><?php echo htmlspecialchars($category['description']); ?></p>
                    <form method="POST" style="margin-top: 15px;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                        <input type="hidden" name="cat_id" value="<?php echo $category['id']; ?>">
                        <button type="submit" name="delete_category" class="btn btn-danger" style="width: 100%;">
                            Hapus Kategori
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Book Modal -->
<div id="addBookModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addBookModal').style.display='none'">&times;</span>
        <h2>Tambah Buku Baru</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Kategori *</label>
                <select name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Judul Buku *</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Penulis *</label>
                <input type="text" name="author" required>
            </div>
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="publisher">
            </div>
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="year" min="1900" max="2099">
            </div>
            <div class="form-group">
                <label>Harga *</label>
                <input type="number" name="price" required min="0">
            </div>
            <div class="form-group">
                <label>Stok *</label>
                <input type="number" name="stock" required min="0" value="0">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Gambar Cover</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <button type="submit" name="add_book" class="btn btn-primary" style="width: 100%;">Tambah Buku</button>
        </form>
    </div>
</div>

<!-- Edit Book Modal -->
<div id="editBookModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('editBookModal').style.display='none'">&times;</span>
        <h2>Edit Buku</h2>
        <form method="POST" enctype="multipart/form-data" id="editBookForm">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Kategori *</label>
                <select name="category_id" id="edit_category_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Judul Buku *</label>
                <input type="text" name="title" id="edit_title" required>
            </div>
            <div class="form-group">
                <label>Penulis *</label>
                <input type="text" name="author" id="edit_author" required>
            </div>
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="publisher" id="edit_publisher">
            </div>
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="year" id="edit_year" min="1900" max="2099">
            </div>
            <div class="form-group">
                <label>Harga *</label>
                <input type="number" name="price" id="edit_price" required min="0">
            </div>
            <div class="form-group">
                <label>Stok *</label>
                <input type="number" name="stock" id="edit_stock" required min="0">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" id="edit_description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Gambar Cover (Kosongkan jika tidak ingin mengubah)</label>
                <input type="file" name="image" accept="image/*">
                <div id="current_image" style="margin-top: 10px;"></div>
            </div>
            <button type="submit" name="update_book" class="btn btn-primary" style="width: 100%;">Update Buku</button>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addCategoryModal').style.display='none'">&times;</span>
        <h2>Tambah Kategori Baru</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nama Kategori *</label>
                <input type="text" name="cat_name" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="cat_description" rows="3"></textarea>
            </div>
            <button type="submit" name="add_category" class="btn btn-primary" style="width: 100%;">Tambah Kategori</button>
        </form>
    </div>
</div>

<script>
function openTab(tabName) {
    var tabs = document.getElementsByClassName('tab-content');
    var buttons = document.getElementsByClassName('tab-button');
    
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
    }
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove('active');
    }
    
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

function editBook(book) {
    document.getElementById('edit_id').value = book.id;
    document.getElementById('edit_category_id').value = book.category_id;
    document.getElementById('edit_title').value = book.title;
    document.getElementById('edit_author').value = book.author;
    document.getElementById('edit_publisher').value = book.publisher || '';
    document.getElementById('edit_year').value = book.year || '';
    document.getElementById('edit_price').value = book.price;
    document.getElementById('edit_stock').value = book.stock;
    document.getElementById('edit_description').value = book.description || '';
    
    if (book.image) {
        document.getElementById('current_image').innerHTML = 
            '<img src="<?php echo BASE_URL; ?>uploads/books/' + book.image + '" style="max-width: 200px; border-radius: 5px;">';
    } else {
        document.getElementById('current_image').innerHTML = '';
    }
    
    document.getElementById('editBookModal').style.display = 'block';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
