<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Backend Validation
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($username) < 4) {
        $error = 'Username minimal 4 karakter!';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();

            // Check if email or username already exists
            $query = "SELECT id FROM users WHERE email = :email OR username = :username";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['email'] === $email) {
                        $error = 'Email sudah terdaftar!';
                    } else {
                        $error = 'Username sudah digunakan!';
                    }
                }
            } else {
                // Insert new user
                $query = "INSERT INTO users (name, username, email, password, phone, address) 
                          VALUES (:name, :username, :email, :password, :phone, :address)";
                $stmt = $db->prepare($query);
                
                // Using PASSWORD_DEFAULT (standard bcrypt in PHP)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);

                if ($stmt->execute()) {
                    $success = 'Registrasi berhasil! Mengalihkan ke halaman login...';
                    header("refresh:2;url=login.php");
                } else {
                    $error = 'Registrasi gagal. Silakan coba lagi.';
                }
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TokoBook</title>
    <!-- Modern UI using Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Daftar Akun</h2>
                <p class="text-gray-500 mt-2 text-sm">Bergabunglah dengan komunitas pembaca kami</p>
            </div>
            
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4" id="registerForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="John Doe"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Username</label>
                        <input type="text" name="username" required minlength="4" placeholder="johndoe123"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" required placeholder="email@contoh.com"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Password</label>
                        <input type="password" id="password" name="password" required minlength="8" placeholder="••••••••"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Konfirmasi</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">No. Telepon <span class="text-gray-400 font-normal text-[10px]">(Opsional)</span></label>
                    <input type="tel" name="phone" placeholder="0812XXX"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                
                <input type="hidden" name="address" value="">

                <button type="submit" 
                        class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all transform active:scale-[0.98]">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-gray-600 text-sm">
                    Sudah punya akun? 
                    <a href="login.php" class="text-indigo-600 font-semibold hover:text-indigo-800 transition-colors">Masuk</a>
                </p>
                <a href="../index.php" class="inline-block mt-4 text-xs text-gray-400 hover:text-gray-600">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <!-- Frontend Validation -->
    <script>
        document.getElementById('registerForm').addEventListener('submit', (e) => {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Konfirmasi password tidak cocok!');
            } else if (password.length < 8) {
                e.preventDefault();
                alert('Password minimal harus 8 karakter!');
            }
        });
    </script>
</body>
</html>