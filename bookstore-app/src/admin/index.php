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

// Get statistics
$stats = [];

// Total Books
$query = "SELECT COUNT(*) as total FROM books";
$stmt = $db->query($query);
$stats['total_books'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Users
$query = "SELECT COUNT(*) as total FROM users";
$stmt = $db->query($query);
$stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Orders
$query = "SELECT COUNT(*) as total FROM orders";
$stmt = $db->query($query);
$stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Revenue
$query = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
$stmt = $db->query($query);
$stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Recent Orders
$query = "SELECT o.*, u.name as user_name 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC LIMIT 5";
$stmt = $db->query($query);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Dashboard Admin';
include '../includes/header.php';
?>

<div class="container" style="padding: 40px 20px; max-width: 1400px;">
    <!-- Header Section -->
    <div style="background: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0 0 8px 0; font-size: 28px; color: #2c3e50;">Dashboard Admin</h1>
                <p style="margin: 0; color: #7f8c8d; font-size: 14px;">Kelola toko buku Anda dengan mudah</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; color: #7f8c8d; font-size: 13px;">Selamat datang</p>
                <strong style="color: #2c3e50; font-size: 16px;"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #667eea;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin: 0 0 8px 0; color: #7f8c8d; font-size: 14px; font-weight: 500;">Total Buku</p>
                    <h2 style="margin: 0; font-size: 32px; color: #2c3e50;"><?php echo $stats['total_books']; ?></h2>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    📚
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #f093fb;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin: 0 0 8px 0; color: #7f8c8d; font-size: 14px; font-weight: 500;">Total User</p>
                    <h2 style="margin: 0; font-size: 32px; color: #2c3e50;"><?php echo $stats['total_users']; ?></h2>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    👥
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #4facfe;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin: 0 0 8px 0; color: #7f8c8d; font-size: 14px; font-weight: 500;">Total Pesanan</p>
                    <h2 style="margin: 0; font-size: 32px; color: #2c3e50;"><?php echo $stats['total_orders']; ?></h2>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    📦
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #fa709a;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin: 0 0 8px 0; color: #7f8c8d; font-size: 14px; font-weight: 500;">Total Pendapatan</p>
                    <h2 style="margin: 0; font-size: 24px; color: #2c3e50;">Rp <?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?></h2>
                </div>
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    💰
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="background: white; border-radius: 12px; padding: 25px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 18px;">Aksi Cepat</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="manage-books.php" style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 15px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                📚 Kelola Buku
            </a>
            <a href="manage-orders.php" style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 15px 20px; background: #f39c12; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                📦 Kelola Pesanan
            </a>
            <a href="manage-users.php" style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 15px 20px; background: #9b59b6; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                👥 Kelola User
            </a>
        </div>
    </div>

    <!-- Recent Orders -->
    <div style="background: white; border-radius: 12px; padding: 25px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h3 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 18px;">Pesanan Terbaru</h3>
        <?php if (count($recent_orders) > 0): ?>
            <div style="overflow-x: auto;">
                <table style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">No. Pesanan</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Customer</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Total</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Status</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Tanggal</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 15px;"><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td style="padding: 15px;"><strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong></td>
                                <td style="padding: 15px;">
                                    <?php
                                    $status_colors = [
                                        'pending' => '#f39c12',
                                        'paid' => '#3498db',
                                        'shipped' => '#9b59b6',
                                        'completed' => '#2ecc71',
                                        'cancelled' => '#e74c3c'
                                    ];
                                    $status_labels = [
                                        'pending' => 'Menunggu',
                                        'paid' => 'Dibayar',
                                        'shipped' => 'Dikirim',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    $color = $status_colors[$order['status']] ?? '#95a5a6';
                                    $label = $status_labels[$order['status']] ?? $order['status'];
                                    ?>
                                    <span style="background: <?php echo $color; ?>; color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <?php echo $label; ?>
                                    </span>
                                </td>
                                <td style="padding: 15px; color: #7f8c8d;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td style="padding: 15px;">
                                    <a href="manage-orders.php?view=<?php echo $order['id']; ?>" style="color: #3498db; text-decoration: none; font-weight: 500;">Detail →</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                <a href="manage-orders.php" class="btn btn-primary" style="padding: 12px 30px;">Lihat Semua Pesanan</a>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px;">
                <div style="font-size: 60px; margin-bottom: 15px;">📦</div>
                <p style="color: #95a5a6; margin: 0;">Belum ada pesanan</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>