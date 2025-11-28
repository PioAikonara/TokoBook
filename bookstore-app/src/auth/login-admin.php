<?php
session_start();

// Jika sudah login, redirect
if (isset($_SESSION['admin_id'])) {
    header("Location: ../admin/index.php");
    exit();
}

require_once '../includes/config.php';
require_once '../includes/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        $database = new Database();
        $db = $database->getConnection();

        // Cek admin (gunakan MD5 karena di SQL pakai MD5)
        $query = "SELECT id, username, email FROM admin WHERE username = :username AND password = MD5(:password)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Login berhasil
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            
            header("Location: ../admin/index.php");
            exit();
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - TokoBook</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .login-container {
            max-width: 440px;
            width: 100%;
            margin: 20px;
            padding: 48px 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }

        .admin-badge-container {
            text-align: center;
            margin-bottom: 24px;
        }

        .admin-badge {
            background: #2c3e50;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header .brand {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .login-header h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .alert {
            padding: 14px 16px;
            margin-bottom: 24px;
            border-radius: 8px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .alert-error {
            background: #fff5f5;
            color: #c53030;
            border-left-color: #e53e3e;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background: #fafafa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2c3e50;
            background: white;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #34495e;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .back-home {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            font-size: 14px;
            color: #7f8c8d;
        }

        .back-home a {
            color: #7f8c8d;
            text-decoration: none;
            transition: color 0.3s;
            margin: 0 5px;
        }

        .back-home a:hover {
            color: #2c3e50;
        }

        .default-info {
            margin-top: 24px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            border-left: 4px solid #2c3e50;
            line-height: 1.6;
        }

        .default-info strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 8px;
        }

        .default-info code {
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #2c3e50;
            font-weight: 600;
            border: 1px solid #e0e0e0;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
            }

            .login-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="admin-badge-container">
            <span class="admin-badge">ADMIN AREA</span>
        </div>
        
        <div class="login-header">
            <div class="brand">Admin</div>
            <h2>Login Administrator</h2>
            <p>Akses khusus untuk admin</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Masukkan username admin"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                       placeholder="Masukkan password">
            </div>

            <button type="submit" class="btn-login">Masuk sebagai Admin</button>
        </form>

        <div class="back-home">
            <a href="../index.php">← Kembali ke Beranda</a> | 
            <a href="login.php">Login sebagai User</a>
        </div>
        
</body>
</html>