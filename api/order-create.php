<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Get shipping info from POST
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';
    $zip_code = $_POST['zip_code'] ?? '';
    
    if (empty($first_name) || empty($last_name) || empty($address) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required shipping fields']);
        exit;
    }
    
    // Get cart items
    $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.image 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart_items = [];
    $subtotal = 0;
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $subtotal += $row['price'] * $row['quantity'];
    }
    
    if (empty($cart_items)) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
        exit;
    }
    
    $shipping_cost = $subtotal > 100 ? 0 : 9.99;
    $total_price = $subtotal + $shipping_cost;
    
    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, first_name, last_name, email, phone, address, country, state, city, zip_code, shipping_cost) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idsssssssssd", $user_id, $total_price, $first_name, $last_name, $email, $phone, $address, $country, $state, $city, $zip_code, $shipping_cost);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    // Create order items
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }
    
    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Return order data for Paystack
    $current_user = getCurrentUser();
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'email' => $current_user['email'],
        'amount' => $total_price * 100, // Paystack uses kobo
        'message' => 'Order created successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
