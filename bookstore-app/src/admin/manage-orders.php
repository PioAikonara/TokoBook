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

// Handle Update Order Status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE orders SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $order_id);
    
    if ($stmt->execute()) {
        $success = 'Status pesanan berhasil diupdate!';
    } else {
        $error = 'Gagal mengupdate status pesanan!';
    }
}

// Get filter
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$query = "SELECT o.*, u.name as user_name, u.email as user_email 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE 1=1";

if ($filter_status) {
    $query .= " AND o.status = :status";
}

if ($search) {
    $query .= " AND (o.order_number LIKE :search OR u.name LIKE :search)";
}

$query .= " ORDER BY o.created_at DESC";

$stmt = $db->prepare($query);

if ($filter_status) {
    $stmt->bindParam(':status', $filter_status);
}

if ($search) {
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam);
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get status counts
$counts = [];
$statuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
foreach ($statuses as $status) {
    $query = "SELECT COUNT(*) as total FROM orders WHERE status = :status";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->execute();
    $counts[$status] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

$page_title = 'Kelola Pesanan';
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
    margin: 30px auto;
    padding: 20px;
    border: 1px solid #888;
    border-radius: 10px;
    width: 90%;
    max-width: 800px;
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
.status-filter {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.status-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 13px;
    cursor: pointer;
    border: 2px solid;
    text-decoration: none;
    display: inline-block;
}
</style>

<div class="container" style="padding: 40px 20px; max-width: 1400px;">
    <!-- Header -->
    <div style="background: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0 0 8px 0; font-size: 28px; color: #2c3e50;">Kelola Pesanan</h1>
                <p style="margin: 0; color: #7f8c8d; font-size: 14px;">Kelola semua pesanan dari customer</p>
            </div>
            <a href="index.php" class="btn" style="background: #95a5a6; color: white;">← Kembali</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Filter & Search -->
    <div style="background: white; border-radius: 12px; padding: 25px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <div style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50;">Filter Status</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="manage-orders.php" class="status-badge" style="background: <?php echo !$filter_status ? '#667eea' : 'white'; ?>; color: <?php echo !$filter_status ? 'white' : '#667eea'; ?>; border-color: #667eea;">
                    Semua (<?php echo array_sum($counts); ?>)
                </a>
                <a href="?status=pending" class="status-badge" style="background: <?php echo $filter_status == 'pending' ? '#f39c12' : 'white'; ?>; color: <?php echo $filter_status == 'pending' ? 'white' : '#f39c12'; ?>; border-color: #f39c12;">
                    Menunggu (<?php echo $counts['pending']; ?>)
                </a>
                <a href="?status=paid" class="status-badge" style="background: <?php echo $filter_status == 'paid' ? '#3498db' : 'white'; ?>; color: <?php echo $filter_status == 'paid' ? 'white' : '#3498db'; ?>; border-color: #3498db;">
                    Dibayar (<?php echo $counts['paid']; ?>)
                </a>
                <a href="?status=shipped" class="status-badge" style="background: <?php echo $filter_status == 'shipped' ? '#9b59b6' : 'white'; ?>; color: <?php echo $filter_status == 'shipped' ? 'white' : '#9b59b6'; ?>; border-color: #9b59b6;">
                    Dikirim (<?php echo $counts['shipped']; ?>)
                </a>
                <a href="?status=completed" class="status-badge" style="background: <?php echo $filter_status == 'completed' ? '#2ecc71' : 'white'; ?>; color: <?php echo $filter_status == 'completed' ? 'white' : '#2ecc71'; ?>; border-color: #2ecc71;">
                    Selesai (<?php echo $counts['completed']; ?>)
                </a>
                <a href="?status=cancelled" class="status-badge" style="background: <?php echo $filter_status == 'cancelled' ? '#e74c3c' : 'white'; ?>; color: <?php echo $filter_status == 'cancelled' ? 'white' : '#e74c3c'; ?>; border-color: #e74c3c;">
                    Dibatalkan (<?php echo $counts['cancelled']; ?>)
                </a>
            </div>
        </div>
        
        <div style="border-top: 1px solid #ecf0f1; padding-top: 20px;">
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <?php if ($filter_status): ?>
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="Cari no. pesanan atau customer..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; min-width: 250px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 25px;">Cari</button>
                <?php if ($search || $filter_status): ?>
                    <a href="manage-orders.php" class="btn" style="background: #95a5a6; color: white; padding: 10px 25px;">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div style="background: white; border-radius: 12px; padding: 25px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <?php if (count($orders) > 0): ?>
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
                        <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 15px;"><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td style="padding: 15px;">
                                    <div><?php echo htmlspecialchars($order['user_name']); ?></div>
                                    <small style="color: #7f8c8d;"><?php echo htmlspecialchars($order['user_email']); ?></small>
                                </td>
                                <td style="padding: 15px;"><strong style="color: #2c3e50;">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong></td>
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
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick='viewOrder(<?php echo $order['id']; ?>)' 
                                                style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                            Detail
                                        </button>
                                        <button onclick='updateStatus(<?php echo json_encode($order); ?>)' 
                                                style="background: #f39c12; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                            Status
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px;">
                <div style="font-size: 60px; margin-bottom: 15px;">📦</div>
                <p style="color: #95a5a6; margin: 0; font-size: 16px;">Tidak ada pesanan ditemukan</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Order Detail Modal -->
<div id="viewOrderModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('viewOrderModal').style.display='none'">&times;</span>
        <h2>Detail Pesanan</h2>
        <div id="orderDetails">Loading...</div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('updateStatusModal').style.display='none'">&times;</span>
        <h2>Update Status Pesanan</h2>
        <form method="POST" id="updateStatusForm">
            <input type="hidden" name="order_id" id="status_order_id">
            <div class="form-group">
                <label>No. Pesanan</label>
                <input type="text" id="status_order_number" readonly style="background: #f5f5f5;">
            </div>
            <div class="form-group">
                <label>Status Saat Ini</label>
                <input type="text" id="status_current" readonly style="background: #f5f5f5;">
            </div>
            <div class="form-group">
                <label>Status Baru *</label>
                <select name="status" id="status_new" required>
                    <option value="pending">Menunggu Pembayaran</option>
                    <option value="paid">Dibayar</option>
                    <option value="shipped">Dikirim</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <button type="submit" name="update_status" class="btn btn-primary" style="width: 100%;">Update Status</button>
        </form>
    </div>
</div>

<script>
function viewOrder(orderId) {
    document.getElementById('orderDetails').innerHTML = 'Loading...';
    document.getElementById('viewOrderModal').style.display = 'block';
    
    // Fetch order details via AJAX
    fetch('get-order-details.php?id=' + orderId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('orderDetails').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('orderDetails').innerHTML = 'Error loading order details';
        });
}

function updateStatus(order) {
    const statusLabels = {
        'pending': 'Menunggu Pembayaran',
        'paid': 'Dibayar',
        'shipped': 'Dikirim',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan'
    };
    
    document.getElementById('status_order_id').value = order.id;
    document.getElementById('status_order_number').value = order.order_number;
    document.getElementById('status_current').value = statusLabels[order.status] || order.status;
    document.getElementById('status_new').value = order.status;
    document.getElementById('updateStatusModal').style.display = 'block';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
