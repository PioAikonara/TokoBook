<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized');
}

require_once '../includes/config.php';
require_once '../includes/database.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id'])) {
    die('Order ID required');
}

$order_id = $_GET['id'];

// Get order details
$query = "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE o.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Order not found');
}

// Get order items
$query = "SELECT od.*, b.title as book_title, b.author as book_author, b.image as book_image 
          FROM order_details od 
          LEFT JOIN books b ON od.book_id = b.id 
          WHERE od.order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$status_labels = [
    'pending' => 'Menunggu Pembayaran',
    'paid' => 'Dibayar',
    'shipped' => 'Dikirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];

$status_colors = [
    'pending' => '#f39c12',
    'paid' => '#3498db',
    'shipped' => '#9b59b6',
    'completed' => '#2ecc71',
    'cancelled' => '#e74c3c'
];
?>

<div style="padding: 10px;">
    <h3>Informasi Pesanan</h3>
    <table style="width: 100%; margin-bottom: 20px;">
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">No. Pesanan</td>
            <td style="padding: 10px;"><?php echo htmlspecialchars($order['order_number']); ?></td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">Tanggal Pesanan</td>
            <td style="padding: 10px;"><?php echo date('d F Y, H:i', strtotime($order['created_at'])); ?> WIB</td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">Status</td>
            <td style="padding: 10px;">
                <span style="background: <?php echo $status_colors[$order['status']]; ?>; color: white; padding: 5px 15px; border-radius: 5px;">
                    <?php echo $status_labels[$order['status']]; ?>
                </span>
            </td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">Total Pembayaran</td>
            <td style="padding: 10px; font-size: 20px; font-weight: bold; color: #e74c3c;">
                Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
            </td>
        </tr>
    </table>

    <h3>Informasi Customer</h3>
    <table style="width: 100%; margin-bottom: 20px;">
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">Nama</td>
            <td style="padding: 10px;"><?php echo htmlspecialchars($order['user_name']); ?></td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">Email</td>
            <td style="padding: 10px;"><?php echo htmlspecialchars($order['user_email']); ?></td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">Telepon</td>
            <td style="padding: 10px;"><?php echo htmlspecialchars($order['user_phone'] ?? '-'); ?></td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="padding: 10px; background: #f8f9fa; font-weight: bold;">Alamat Pengiriman</td>
            <td style="padding: 10px;"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></td>
        </tr>
    </table>

    <h3>Item Pesanan</h3>
    <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
        <thead>
            <tr style="background: #34495e; color: white;">
                <th style="padding: 10px; text-align: left;">Buku</th>
                <th style="padding: 10px; text-align: center;">Qty</th>
                <th style="padding: 10px; text-align: right;">Harga</th>
                <th style="padding: 10px; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <?php if ($item['book_image']): ?>
                                <img src="<?php echo BASE_URL . 'uploads/books/' . $item['book_image']; ?>" 
                                     style="width: 50px; height: 70px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                <div style="width: 50px; height: 70px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;">Book</div>
                            <?php endif; ?>
                            <div>
                                <strong><?php echo htmlspecialchars($item['book_title']); ?></strong><br>
                                <small style="color: #666;">by <?php echo htmlspecialchars($item['book_author']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 10px; text-align: center;"><?php echo $item['quantity']; ?></td>
                    <td style="padding: 10px; text-align: right;">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                    <td style="padding: 10px; text-align: right; font-weight: bold;">
                        Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background: #f8f9fa;">
                <td colspan="3" style="padding: 15px; text-align: right; font-weight: bold; font-size: 18px;">TOTAL:</td>
                <td style="padding: 15px; text-align: right; font-weight: bold; font-size: 18px; color: #e74c3c;">
                    Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <?php if ($order['payment_proof']): ?>
        <h3>Bukti Pembayaran</h3>
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <img src="<?php echo BASE_URL . 'uploads/payments/' . $order['payment_proof']; ?>" 
                 style="max-width: 100%; max-height: 400px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        </div>
    <?php endif; ?>
</div>
