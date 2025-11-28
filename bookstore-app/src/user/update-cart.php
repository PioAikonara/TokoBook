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
    $action = $_POST['action'] ?? '';
    
    if ($cart_id > 0 && in_array($action, ['increase', 'decrease'])) {
        // Get current quantity
        $query = "SELECT quantity FROM cart WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cart) {
            $new_quantity = $cart['quantity'];
            
            if ($action == 'increase') {
                $new_quantity++;
            } elseif ($action == 'decrease') {
                $new_quantity--;
            }
            
            // If quantity is 0 or less, delete the item
            if ($new_quantity <= 0) {
                $query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$cart_id, $_SESSION['user_id']]);
            } else {
                // Update quantity
                $query = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$new_quantity, $cart_id, $_SESSION['user_id']]);
            }
        }
    }
}

header("Location: cart.php");
exit();
