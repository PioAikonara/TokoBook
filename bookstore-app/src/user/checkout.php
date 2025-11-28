<?php
session_start();

// Cek login user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../includes/config.php';
require_once '../includes/database.php';

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];

// Get cart items
$query = "SELECT c.*, b.title, b.price, b.stock, b.image 
          FROM cart c
          JOIN books b ON c.book_id = b.id 
          WHERE c.user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get user info
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    
    if (empty($shipping_address) || empty($phone)) {
        $_SESSION['error'] = 'Alamat pengiriman dan nomor telepon harus diisi!';
    } else {
        // Start transaction
        $db->beginTransaction();
        
        try {
            // Generate order number
            $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Create order
            $query = "INSERT INTO orders (order_number, user_id, total_amount, shipping_address, phone, notes, status, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
            $stmt = $db->prepare($query);
            $stmt->execute([$order_number, $user_id, $total, $shipping_address, $phone, $notes]);
            $order_id = $db->lastInsertId();
            
            // Add order details and update stock
            foreach ($cart_items as $item) {
                // Check stock again
                $query = "SELECT stock FROM books WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$item['book_id']]);
                $book = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($book['stock'] < $item['quantity']) {
                    throw new Exception('Stok buku ' . $item['title'] . ' tidak mencukupi!');
                }
                
                // Insert order detail
                $query = "INSERT INTO order_details (order_id, book_id, quantity, price) 
                          VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
                
                // Update stock
                $query = "UPDATE books SET stock = stock - ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$item['quantity'], $item['book_id']]);
            }
            
            // Clear cart
            $query = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$user_id]);
            
            $db->commit();
            
            $_SESSION['success'] = 'Pesanan berhasil dibuat! Silakan upload bukti pembayaran.';
            header("Location: upload-payment.php?order_id=" . $order_id);
            exit();
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal membuat pesanan: ' . $e->getMessage();
        }
    }
}

$page_title = 'Checkout';
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">Checkout</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Left Column - Form -->
        <div>
            <div class="card">
                <h2 style="margin-bottom: 20px;">Informasi Pengiriman</h2>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly style="background: #f8f9fa;">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background: #f8f9fa;">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Nomor Telepon *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="08xx-xxxx-xxxx">
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_address">Alamat Pengiriman Lengkap *</label>
                        <textarea id="shipping_address" name="shipping_address" required placeholder="Masukkan alamat lengkap termasuk kode pos" style="min-height: 120px;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Catatan (Opsional)</label>
                        <textarea id="notes" name="notes" placeholder="Catatan untuk penjual" style="min-height: 80px;"></textarea>
                    </div>
                    
                    <div style="background: #fff3cd; border-left: 4px solid #f39c12; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                        <h4 style="margin: 0 0 10px 0; color: #856404;">Informasi Pembayaran</h4>
                        <p style="margin: 5px 0; color: #856404;"><strong>Bank:</strong> BCA</p>
                        <p style="margin: 5px 0; color: #856404;"><strong>No. Rekening:</strong> 1234567890</p>
                        <p style="margin: 5px 0; color: #856404;"><strong>Atas Nama:</strong> TokoBook Indonesia</p>
                        <p style="margin: 10px 0 0 0; font-size: 13px; color: #856404;">
                            Setelah membuat pesanan, Anda akan diarahkan untuk upload bukti pembayaran.
                        </p>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <a href="cart.php" class="btn" style="background: #95a5a6; color: white; flex: 1; text-align: center;">← Kembali ke Keranjang</a>
                        <button type="submit" class="btn btn-primary" style="flex: 2;">Buat Pesanan</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div>
            <div class="card">
                <h2 style="margin-bottom: 20px;">Ringkasan Pesanan</h2>
                
                <div style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;">
                    <?php foreach ($cart_items as $item): ?>
                        <div style="display: flex; gap: 10px; padding: 15px 0; border-bottom: 1px solid #ecf0f1;">
                            <div style="width: 50px; height: 70px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo BASE_URL . 'uploads/books/' . $item['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <span style="font-size: 25px;">Buku</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <strong style="display: block; font-size: 14px; margin-bottom: 5px;"><?php echo htmlspecialchars($item['title']); ?></strong>
                                <small style="color: #7f8c8d;"><?php echo $item['quantity']; ?>x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></small>
                                <br>
                                <strong style="color: #2c3e50; font-size: 14px;">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="padding-top: 20px; border-top: 2px solid #ecf0f1;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #7f8c8d;">Subtotal</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #7f8c8d;">Ongkir</span>
                        <span style="color: #2ecc71;">Gratis</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 15px; border-top: 2px solid #ecf0f1; margin-top: 15px;">
                        <strong style="font-size: 18px;">Total</strong>
                        <strong style="font-size: 22px; color: #e74c3c;">Rp <?php echo number_format($total, 0, ',', '.'); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
