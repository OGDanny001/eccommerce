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
    $coupon_id = $_POST['coupon_id'] ?? null;
    
    if (empty($first_name) || empty($last_name) || empty($address) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required shipping fields']);
        exit;
    }
    
    // Get cart items
    $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Cart prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Cart execute failed: ' . $stmt->error]);
        exit;
    }
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
    $discount_amount = 0;

    // Handle coupon if provided - wrap in try/catch in case coupons table isn't set up yet
    if ($coupon_id) {
        try {
            $c_stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ? AND status = 'active' AND expiry_date >= CURDATE()");
            $c_stmt->bind_param("i", $coupon_id);
            $c_stmt->execute();
            $c_res = $c_stmt->get_result();
            if ($coupon = $c_res->fetch_assoc()) {
                // Re-validate usage limit
                if ($coupon['usage_limit'] === null || $coupon['used_count'] < $coupon['usage_limit']) {
                    if ($subtotal >= $coupon['min_order_amount']) {
                        if ($coupon['discount_type'] === 'percentage') {
                            $discount_amount = ($coupon['discount_value'] / 100) * $subtotal;
                        } else {
                            $discount_amount = $coupon['discount_value'];
                        }
                        $discount_amount = min($discount_amount, $subtotal);
                        
                        // Increment usage count
                        $conn->query("UPDATE coupons SET used_count = used_count + 1 WHERE id = $coupon_id");
                    }
                }
            }
        } catch (Exception $e) {
            // Coupon table or columns don't exist yet, ignore discount
            $discount_amount = 0;
            $coupon_id = null;
        }
    }

    $total_price = $subtotal + $shipping_cost - $discount_amount;
    
    // Create order - FIRST TRY WITHOUT COUPON COLUMNS, then fall back
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, first_name, last_name, email, phone, address, country, state, city, zip_code, shipping_cost) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        // Maybe coupon columns DO exist - try again with them!
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, first_name, last_name, email, phone, address, country, state, city, zip_code, shipping_cost, coupon_id, discount_amount) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Order prepare failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("idsssssssssdiid", $user_id, $total_price, $first_name, $last_name, $email, $phone, $address, $country, $state, $city, $zip_code, $shipping_cost, $coupon_id, $discount_amount);
    } else {
        $stmt->bind_param("idsssssssssd", $user_id, $total_price, $first_name, $last_name, $email, $phone, $address, $country, $state, $city, $zip_code, $shipping_cost);
    }
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Order execute failed: ' . $stmt->error]);
        exit;
    }
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

    // Send notifications (don't fail the whole order if this fails)
    try {
        $current_user = getCurrentUser();
        $current_user['phone'] = $phone; // Use the phone from shipping info
        
        // Send Order Notification - ALL CHANNELS through ONE central function!
        $telegramMessage = "🛒 New Order\n\nOrder ID: #$order_id\nCustomer: " . htmlspecialchars($current_user['name']) . "\nAmount: $" . number_format($total_price, 2);
        
        sendSystemNotification(
            $user_id,
            "Order Placed Successfully",
            "Hello " . $current_user['name'] . ", your order #$order_id has been placed successfully for $" . number_format($total_price, 2) . ". We will notify you when it ships!",
            $telegramMessage,
            $current_user
        );
    } catch (Exception $e) {
        // Ignore notification errors, order is still good
    }
    
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
