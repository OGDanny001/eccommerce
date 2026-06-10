<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $order_amount = (float)($_POST['amount'] ?? 0);
    
    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Coupon code is required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code']);
        exit;
    }

    $coupon = $result->fetch_assoc();

    // Check usage limit
    if ($coupon['usage_limit'] !== null && $coupon['used_count'] >= $coupon['usage_limit']) {
        echo json_encode(['success' => false, 'message' => 'Coupon usage limit reached']);
        exit;
    }

    // Check minimum order amount
    if ($order_amount < $coupon['min_order_amount']) {
        echo json_encode(['success' => false, 'message' => 'Minimum order amount for this coupon is $' . number_format($coupon['min_order_amount'], 2)]);
        exit;
    }

    // Calculate discount
    $discount = 0;
    if ($coupon['discount_type'] === 'percentage') {
        $discount = ($coupon['discount_value'] / 100) * $order_amount;
    } else {
        $discount = $coupon['discount_value'];
    }

    // Ensure discount doesn't exceed order amount
    $discount = min($discount, $order_amount);

    echo json_encode([
        'success' => true,
        'coupon_id' => $coupon['id'],
        'code' => $coupon['code'],
        'discount' => $discount,
        'message' => 'Coupon applied successfully!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
