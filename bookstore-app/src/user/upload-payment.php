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
$order_id = $_GET['order_id'] ?? 0;

// Verify order belongs to user
$query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $file = $_FILES['payment_proof'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = 'payment_' . $order_id . '_' . time() . '.' . $file_ext;
            $upload_path = '../uploads/payments/' . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Update order with payment proof
                $query = "UPDATE orders SET payment_proof = ?, status = 'paid' WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$new_filename, $order_id, $user_id]);
                
                $_SESSION['success'] = 'Bukti pembayaran berhasil diupload!';
                header("Location: orders.php");
                exit();
            }
        }
    }
    $_SESSION['error'] = 'Gagal mengupload bukti pembayaran. Format file harus JPG, PNG, atau GIF.';
}

$page_title = 'Upload Bukti Pembayaran';
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="margin-bottom: 30px;">Upload Bukti Pembayaran</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #2c3e50;">Informasi Pesanan</h3>
                <p style="margin: 5px 0;"><strong>No. Pesanan:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                <p style="margin: 5px 0;"><strong>Total Pembayaran:</strong> <span style="color: #e74c3c; font-size: 20px; font-weight: bold;">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span></p>
                <p style="margin: 5px 0;"><strong>Status:</strong> <span style="background: #f39c12; color: white; padding: 4px 12px; border-radius: 15px; font-size: 13px;">Menunggu Pembayaran</span></p>
            </div>
            
            <div style="background: #fff3cd; border-left: 4px solid #f39c12; padding: 15px; margin-bottom: 25px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #856404;">Informasi Transfer</h4>
                <p style="margin: 5px 0; color: #856404;"><strong>Bank:</strong> BCA</p>
                <p style="margin: 5px 0; color: #856404;"><strong>No. Rekening:</strong> 1234567890</p>
                <p style="margin: 5px 0; color: #856404;"><strong>Atas Nama:</strong> TokoBook Indonesia</p>
                <p style="margin: 10px 0 0 0; font-size: 13px; color: #856404;">
                    Silakan transfer sesuai jumlah total pembayaran dan upload bukti transfernya di bawah ini.
                </p>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="payment_proof">Bukti Pembayaran (JPG, PNG, GIF)</label>
                    <input type="file" 
                           id="payment_proof" 
                           name="payment_proof" 
                           accept="image/*" 
                           required
                           style="padding: 10px; border: 2px dashed #ddd; border-radius: 8px; background: #f8f9fa;">
                    <small style="display: block; margin-top: 8px; color: #7f8c8d;">Upload screenshot atau foto bukti transfer Anda (Max 5MB)</small>
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <a href="orders.php" class="btn" style="background: #95a5a6; color: white; flex: 1; text-align: center;">Batal</a>
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Upload Bukti Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
