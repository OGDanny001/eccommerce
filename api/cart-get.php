<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first', 'cart' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items with product details
$stmt = $conn->prepare("SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
while ($row = $result->fetch_assoc()) {
    $cart[] = [
        'id' => $row['id'],
        'product_id' => $row['product_id'],
        'name' => $row['name'],
        'price' => (float)$row['price'],
        'image' => $row['image'],
        'quantity' => $row['quantity']
    ];
}

echo json_encode(['success' => true, 'cart' => $cart]);
