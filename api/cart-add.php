<?php
require '../config/db.php';
require '../includes/auth.php';
require '../includes/notifications.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $user_id = $_SESSION['user_id'];

    // Check if product already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $row['id']);
        $update_stmt->execute();
    } else {
        // Insert new item
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
    }

    // Get product name for notification
    $p_stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $p_stmt->bind_param("i", $product_id);
    $p_stmt->execute();
    $p_res = $p_stmt->get_result();
    $product_name = "A product";
    if ($p_row = $p_res->fetch_assoc()) {
        $product_name = $p_row['name'];
    }

    // Send Cart Notification
    $currentUser = getCurrentUser();
    sendNotification(
        $currentUser,
        "Item Added to Cart",
        "Hello " . $currentUser['name'] . ", you just added <b>$product_name</b> to your cart. Happy shopping!"
    );

    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
