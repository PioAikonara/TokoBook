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

// Get orders with details
$query = "SELECT o.*, 
          (SELECT COUNT(*) FROM order_details od WHERE od.order_id = o.id) as total_items
          FROM orders o 
          WHERE o.user_id = ?
          ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Pesanan Saya';
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">Pesanan Saya</h1>
    
    <?php if (empty($orders)): ?>
        <div class="card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 80px; margin-bottom: 20px;">Orders</div>
            <h2 style="color: #95a5a6; margin-bottom: 15px;">Belum Ada Pesanan</h2>
            <p style="color: #7f8c8d; margin-bottom: 30px;">Anda belum pernah melakukan pemesanan</p>
            <a href="browse-books.php" class="btn btn-primary">Belanja Sekarang</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            $status_colors = [
                'pending' => '#f39c12',
                'paid' => '#3498db',
                'shipped' => '#9b59b6',
                'completed' => '#2ecc71',
                'cancelled' => '#e74c3c'
            ];
            $status_labels = [
                'pending' => 'Menunggu Pembayaran',
                'paid' => 'Sudah Dibayar',
                'shipped' => 'Sedang Dikirim',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ];
            $color = $status_colors[$order['status']] ?? '#95a5a6';
            $label = $status_labels[$order['status']] ?? $order['status'];
        ?>
            <div class="card" style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #ecf0f1;">
                    <div>
                        <h3 style="margin: 0 0 5px 0; color: #2c3e50;">Pesanan #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                        <p style="margin: 0; color: #7f8c8d; font-size: 14px;">
                            <?php echo date('d F Y, H:i', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <span style="background: <?php echo $color; ?>; color: white; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                        <?php echo $label; ?>
                    </span>
                </div>
                
                <?php
                // Get order items
                $query_items = "SELECT od.*, b.title, b.author, b.image 
                               FROM order_details od
                               JOIN books b ON od.book_id = b.id
                               WHERE od.order_id = ?";
                $stmt_items = $db->prepare($query_items);
                $stmt_items->execute([$order['id']]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <div style="margin-bottom: 20px;">
                    <?php foreach ($items as $item): ?>
                        <div style="display: flex; align-items: center; gap: 15px; padding: 10px 0; border-bottom: 1px solid #f1f1f1;">
                            <div style="width: 50px; height: 70px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo BASE_URL . 'uploads/books/' . $item['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <span style="font-size: 25px;">Book</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <strong style="display: block; margin-bottom: 3px;"><?php echo htmlspecialchars($item['title']); ?></strong>
                                <small style="color: #7f8c8d;">
                                    <?php echo htmlspecialchars($item['author']); ?> • 
                                    <?php echo $item['quantity']; ?>x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                </small>
                            </div>
                            <div style="text-align: right;">
                                <strong style="color: #2c3e50;">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 2px solid #ecf0f1;">
                    <div>
                        <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 14px;">Total Item: <?php echo $order['total_items']; ?></p>
                        <p style="margin: 0; font-size: 12px; color: #95a5a6;">
                            <?php echo htmlspecialchars($order['shipping_address']); ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 14px;">Total Pembayaran</p>
                        <h2 style="margin: 0; color: #e74c3c;">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></h2>
                    </div>
                </div>
                
                <?php if ($order['payment_proof']): ?>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ecf0f1;">
                        <p style="margin: 0 0 10px 0; color: #7f8c8d; font-size: 14px;">Bukti Pembayaran:</p>
                        <a href="<?php echo BASE_URL . 'uploads/payments/' . $order['payment_proof']; ?>" 
                           target="_blank" 
                           class="btn btn-secondary" 
                           style="padding: 8px 16px; font-size: 13px;">
                            Lihat Bukti Pembayaran
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if ($order['status'] == 'pending'): ?>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ecf0f1;">
                        <a href="upload-payment.php?order_id=<?php echo $order['id']; ?>" 
                           class="btn" 
                           style="background: #f39c12; color: white; padding: 10px 20px;">
                            Upload Bukti Pembayaran
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>