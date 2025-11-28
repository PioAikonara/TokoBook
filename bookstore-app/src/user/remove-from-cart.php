<?php
session_start();

// Cek login user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../includes/config.php';
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $cart_id = $_POST['cart_id'] ?? 0;
    
    if ($cart_id > 0) {
        $query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
    }
}

header("Location: cart.php");
exit();
