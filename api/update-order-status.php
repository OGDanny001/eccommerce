<?php
require '../config/db.php';
require '../includes/auth.php';
require '../includes/notifications.php';

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
            // Get user info and order info for notification
            $u_stmt = $conn->prepare("SELECT o.email, o.first_name, o.phone, o.id as order_id FROM orders o WHERE o.id = ?");
            $u_stmt->bind_param("i", $orderId);
            $u_stmt->execute();
            $u_res = $u_stmt->get_result();
            if ($u_row = $u_res->fetch_assoc()) {
                $notif_user = [
                    'name' => $u_row['first_name'],
                    'email' => $u_row['email'],
                    'phone' => $u_row['phone']
                ];
                
                $statusMsg = "";
                switch($newStatus) {
                    case 'shipped': $statusMsg = "Your order #$orderId has been shipped!"; break;
                    case 'delivered': $statusMsg = "Your order #$orderId has been delivered! Enjoy your purchase."; break;
                    case 'paid': $statusMsg = "Payment confirmed for order #$orderId."; break;
                    default: $statusMsg = "The status of your order #$orderId has been updated to: " . ucfirst($newStatus);
                }

                sendNotification(
                    $notif_user,
                    "Order Update: " . ucfirst($newStatus),
                    "Hello " . $u_row['first_name'] . ", $statusMsg"
                );
            }

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