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
    // ADD USER
    if (isset($_POST['add_user'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        
        // Check if email exists
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (name, email, password, phone, address) 
                      VALUES (:name, :email, :password, :phone, :address)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            
            if ($stmt->execute()) {
                $success = 'User berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan user!';
            }
        }
    }
    
    // ADD ADMIN
    if (isset($_POST['add_admin'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        // Check if username exists
        $query = "SELECT id FROM admin WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = 'Username sudah ada!';
        } else {
            $query = "INSERT INTO admin (username, email, password) 
                      VALUES (:username, :email, MD5(:password))";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            
            if ($stmt->execute()) {
                $success = 'Admin berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan admin!';
            }
        }
    }
    
    // DELETE USER
    if (isset($_POST['delete_user'])) {
        $id = $_POST['id'];
        
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $success = 'User berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus user!';
        }
    }
    
    // DELETE ADMIN
    if (isset($_POST['delete_admin'])) {
        $id = $_POST['id'];
        
        // Prevent deleting self
        if ($id == $_SESSION['admin_id']) {
            $error = 'Tidak bisa menghapus akun sendiri!';
        } else {
            $query = "DELETE FROM admin WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $success = 'Admin berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus admin!';
            }
        }
    }
}

// Get Users
$query = "SELECT u.*, COUNT(o.id) as total_orders 
          FROM users u 
          LEFT JOIN orders o ON u.id = o.user_id 
          GROUP BY u.id 
          ORDER BY u.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Admins
$query = "SELECT * FROM admin ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Kelola User & Admin';
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
    background-color: rgba(0,0,0,0.6);
    animation: fadeIn 0.2s;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.modal-content {
    background-color: #fefefe;
    margin: 50px auto;
    padding: 30px;
    border: none;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    animation: slideIn 0.3s;
}
@keyframes slideIn {
    from { transform: translateY(-30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.close {
    color: #95a5a6;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.2s;
}
.close:hover {
    color: #e74c3c;
}
.tab-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #ecf0f1;
}
.tab-button {
    padding: 12px 24px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    color: #7f8c8d;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}
.tab-button.active {
    color: #667eea;
    border-bottom-color: #667eea;
}
.tab-button:hover {
    color: #667eea;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
</style>

<div class="container" style="padding: 40px 20px; max-width: 1400px;">
    <!-- Header -->
    <div style="background: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0 0 8px 0; font-size: 28px; color: #2c3e50;">Kelola User & Admin</h1>
                <p style="margin: 0; color: #7f8c8d; font-size: 14px;">Kelola semua user dan admin aplikasi</p>
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

    <!-- Tab Buttons -->
    <div class="tab-buttons">
        <button class="tab-button active" onclick="openTab('users')">User (<?php echo count($users); ?>)</button>
        <button class="tab-button" onclick="openTab('admins')">Admin (<?php echo count($admins); ?>)</button>
    </div>

    <!-- USERS TAB -->
    <div id="users" class="tab-content active">
        <div style="margin-bottom: 20px;">
            <button onclick="document.getElementById('addUserModal').style.display='block'" class="btn btn-primary" style="padding: 12px 24px; font-weight: 500;">
                + Tambah User Baru
            </button>
        </div>

        <div style="background: white; border-radius: 12px; padding: 25px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="overflow-x: auto;">
                <table style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">ID</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Nama</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Email</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Telepon</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Total Pesanan</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Terdaftar</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 15px;"><strong style="color: #667eea;"><?php echo $user['id']; ?></strong></td>
                                <td style="padding: 15px;"><strong style="color: #2c3e50;"><?php echo htmlspecialchars($user['name']); ?></strong></td>
                                <td style="padding: 15px; color: #7f8c8d;"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td style="padding: 15px; color: #7f8c8d;"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td style="padding: 15px;">
                                    <span style="background: #e8f5e9; color: #2ecc71; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <?php echo $user['total_orders']; ?> pesanan
                                    </span>
                                </td>
                                <td style="padding: 15px; color: #7f8c8d;"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td style="padding: 15px;">
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick='viewUser(<?php echo json_encode($user); ?>)' 
                                                style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                            Lihat
                                        </button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ADMINS TAB -->
    <div id="admins" class="tab-content">
        <div style="margin-bottom: 20px;">
            <button onclick="document.getElementById('addAdminModal').style.display='block'" class="btn btn-primary" style="padding: 12px 24px; font-weight: 500;">
                + Tambah Admin Baru
            </button>
        </div>

        <div style="background: white; border-radius: 12px; padding: 25px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="overflow-x: auto;">
                <table style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">ID</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Username</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Email</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Terdaftar</th>
                            <th style="background: #f8f9fa; color: #2c3e50; padding: 15px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 15px;"><strong style="color: #667eea;"><?php echo $admin['id']; ?></strong></td>
                                <td style="padding: 15px;">
                                    <strong style="color: #2c3e50;"><?php echo htmlspecialchars($admin['username']); ?></strong>
                                    <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                        <span style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; margin-left: 8px;">YOU</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; color: #7f8c8d;"><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td style="padding: 15px; color: #7f8c8d;"><?php echo date('d/m/Y', strtotime($admin['created_at'])); ?></td>
                                <td style="padding: 15px;">
                                    <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus admin ini?');">
                                            <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                            <button type="submit" name="delete_admin" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #95a5a6; font-size: 14px;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addUserModal').style.display='none'">&times;</span>
        <h2 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 24px;">Tambah User Baru</h2>
        <form method="POST">
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">Nama Lengkap *</label>
                <input type="text" name="name" required style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px;">
            </div>
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">Email *</label>
                <input type="email" name="email" required style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px;">
            </div>
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">Password *</label>
                <input type="password" name="password" required minlength="6" style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px;">
            </div>
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">No. Telepon</label>
                <input type="tel" name="phone" style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px;">
            </div>
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">Alamat</label>
                <textarea name="address" rows="3" style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px; resize: vertical;"></textarea>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 500; font-size: 15px; margin-top: 10px;">Tambah User</button>
        </form>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addAdminModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addAdminModal').style.display='none'">&times;</span>
        <h2 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 24px;">Tambah Admin Baru</h2>
        <form method="POST">
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">Username *</label>
                <input type="text" name="username" required style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px;">
            </div>
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">Email *</label>
                <input type="email" name="email" required style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px;">
            </div>
            <div class="form-group">
                <label style="color: #2c3e50; font-weight: 500; margin-bottom: 8px; display: block;">Password *</label>
                <input type="password" name="password" required minlength="6" style="padding: 12px; border: 2px solid #ecf0f1; border-radius: 8px; width: 100%; font-size: 14px;">
            </div>
            <button type="submit" name="add_admin" class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 500; font-size: 15px; margin-top: 10px;">Tambah Admin</button>
        </form>
    </div>
</div>

<!-- View User Modal -->
<div id="viewUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('viewUserModal').style.display='none'">&times;</span>
        <h2 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 24px;">Detail User</h2>
        <div id="userDetails"></div>
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

function viewUser(user) {
    var html = '<div style="background: #f8f9fa; border-radius: 8px; padding: 20px; margin-top: 10px;">';
    html += '<div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">';
    html += '<div style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">ID</div>';
    html += '<div style="color: #2c3e50; font-size: 16px; font-weight: 500;">' + user.id + '</div>';
    html += '</div>';
    html += '<div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">';
    html += '<div style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">Nama</div>';
    html += '<div style="color: #2c3e50; font-size: 16px; font-weight: 500;">' + user.name + '</div>';
    html += '</div>';
    html += '<div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">';
    html += '<div style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">Email</div>';
    html += '<div style="color: #2c3e50; font-size: 16px;">' + user.email + '</div>';
    html += '</div>';
    html += '<div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">';
    html += '<div style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">Telepon</div>';
    html += '<div style="color: #2c3e50; font-size: 16px;">' + (user.phone || '-') + '</div>';
    html += '</div>';
    html += '<div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">';
    html += '<div style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">Alamat</div>';
    html += '<div style="color: #2c3e50; font-size: 16px;">' + (user.address || '-') + '</div>';
    html += '</div>';
    html += '<div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ecf0f1;">';
    html += '<div style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">Total Pesanan</div>';
    html += '<div><span style="background: #e8f5e9; color: #2ecc71; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500;">' + user.total_orders + ' pesanan</span></div>';
    html += '</div>';
    html += '<div style="margin-bottom: 0;">';
    html += '<div style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">Terdaftar</div>';
    html += '<div style="color: #2c3e50; font-size: 16px;">' + new Date(user.created_at).toLocaleDateString('id-ID') + '</div>';
    html += '</div>';
    html += '</div>';
    
    document.getElementById('userDetails').innerHTML = html;
    document.getElementById('viewUserModal').style.display = 'block';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
