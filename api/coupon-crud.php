<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';
$code = strtoupper(trim($_POST['code'] ?? ''));
$discount_type = $_POST['discount_type'] ?? 'percentage';
$discount_value = $_POST['discount_value'] ?? 0;
$expiry_date = $_POST['expiry_date'] ?? '';
$usage_limit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
$min_order_amount = $_POST['min_order_amount'] ?? 0;

if ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Coupon deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete coupon']);
    }
} else {
    if (empty($id)) {
        // Create
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, expiry_date, usage_limit, min_order_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisid", $code, $discount_type, $discount_value, $expiry_date, $usage_limit, $min_order_amount);
    } else {
        // Update
        $stmt = $conn->prepare("UPDATE coupons SET code=?, discount_type=?, discount_value=?, expiry_date=?, usage_limit=?, min_order_amount=? WHERE id=?");
        $stmt->bind_param("ssisidi", $code, $discount_type, $discount_value, $expiry_date, $usage_limit, $min_order_amount, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Coupon saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
}
