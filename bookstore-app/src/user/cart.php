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

// Fetch cart items from the database
$query = "SELECT c.id as cart_id, c.quantity, b.id as book_id, b.title, b.author, b.price, b.image 
          FROM cart c
          JOIN books b ON c.book_id = b.id 
          WHERE c.user_id = ?
          ORDER BY c.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

$page_title = 'Keranjang Belanja';
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">Keranjang Belanja</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 80px; margin-bottom: 20px;">Cart</div>
            <h2 style="color: #95a5a6; margin-bottom: 15px;">Keranjang Belanja Kosong</h2>
            <p style="color: #7f8c8d; margin-bottom: 30px;">Anda belum menambahkan buku ke keranjang</p>
            <a href="browse-books.php" class="btn btn-primary">Belanja Sekarang</a>
        </div>
    <?php else: ?>
        <div class="card">
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 60px; height: 80px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                            <?php if ($item['image']): ?>
                                                <img src="<?php echo BASE_URL . 'uploads/books/' . $item['image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                     style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <span style="font-size: 30px;">Book</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                            <br>
                                            <small style="color: #7f8c8d;"><?php echo htmlspecialchars($item['author']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <form method="POST" action="update-cart.php" style="display: inline;">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <input type="hidden" name="action" value="decrease">
                                            <button type="submit" class="btn" style="padding: 5px 12px; background: #95a5a6;">-</button>
                                        </form>
                                        <span style="min-width: 30px; text-align: center; font-weight: bold;"><?php echo $item['quantity']; ?></span>
                                        <form method="POST" action="update-cart.php" style="display: inline;">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <input type="hidden" name="action" value="increase">
                                            <button type="submit" class="btn" style="padding: 5px 12px; background: #3498db;">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td><strong>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></strong></td>
                                <td>
                                    <form method="POST" action="remove-from-cart.php" onsubmit="return confirm('Hapus item ini dari keranjang?');">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 8px 15px;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8f9fa;">
                            <td colspan="3" style="text-align: right; font-weight: bold; font-size: 18px;">Total:</td>
                            <td colspan="2" style="font-weight: bold; font-size: 20px; color: #e74c3c;">
                                Rp <?php echo number_format($total_price, 0, ',', '.'); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ecf0f1;">
                <a href="browse-books.php" class="btn" style="background: #95a5a6; color: white;">← Lanjut Belanja</a>
                <a href="checkout.php" class="btn btn-primary" style="padding: 12px 40px; font-size: 16px;">Checkout →</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>