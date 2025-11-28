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

$book_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

if ($book_id > 0) {
    // Check if book exists and has stock
    $query = "SELECT * FROM books WHERE id = ? AND stock > 0";
    $stmt = $db->prepare($query);
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($book) {
        // Check if already in cart
        $query = "SELECT * FROM cart WHERE user_id = ? AND book_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id, $book_id]);
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cart_item) {
            // Update quantity
            $new_quantity = $cart_item['quantity'] + 1;
            
            // Check stock
            if ($new_quantity <= $book['stock']) {
                $query = "UPDATE cart SET quantity = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$new_quantity, $cart_item['id']]);
                $_SESSION['success'] = 'Jumlah buku di keranjang berhasil ditambah!';
            } else {
                $_SESSION['error'] = 'Stok tidak mencukupi!';
            }
        } else {
            // Add new item to cart
            $query = "INSERT INTO cart (user_id, book_id, quantity, created_at) VALUES (?, ?, 1, NOW())";
            $stmt = $db->prepare($query);
            $stmt->execute([$user_id, $book_id]);
            $_SESSION['success'] = 'Buku berhasil ditambahkan ke keranjang!';
        }
    } else {
        $_SESSION['error'] = 'Buku tidak tersedia!';
    }
}

header("Location: browse-books.php");
exit();
