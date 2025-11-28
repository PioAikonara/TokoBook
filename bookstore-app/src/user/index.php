<?php
session_start();
include '../includes/config.php';
include '../includes/database.php';
include '../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="container">
    <h1>Welcome to the Bookstore, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <nav>
        <ul>
            <li><a href="browse-books.php">Browse Books</a></li>
            <li><a href="cart.php">View Cart</a></li>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </nav>
</div>

<?php include '../includes/footer.php'; ?>