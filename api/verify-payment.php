<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = isset($_POST['reference']) ? $_POST['reference'] : '';
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    
    if (empty($reference) || empty($order_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit;
    }
    
    // Verify payment with Paystack API
    $paystack_secret_key = 'sk_test_86b2fbf97b3e9913c3cd4165e48231367bc4aaf9'; // Replace with your secret key
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/{$reference}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$paystack_secret_key}"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($result && $result['status'] && $result['data']['status'] === 'success') {
        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
