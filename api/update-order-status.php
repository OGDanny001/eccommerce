<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

// Require admin only!
if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $newStatus = isset($_POST['status']) ? $_POST['status'] : '';

    $allowedStatuses = ['pending', 'paid', 'shipped', 'delivered'];
    
    if (in_array($newStatus, $allowedStatuses) && $orderId > 0) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    }
}
?>