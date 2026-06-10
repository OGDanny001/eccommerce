<?php
$pageTitle = "Manage Orders";
$activePage = "orders";
require_once '../includes/admin-header.php';

// Fetch all orders with details
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-card">
    <h3 style="margin-bottom: 2rem;">Order Management</h3>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">Customer</th>
                    <th style="padding: 1rem;">Contact</th>
                    <th style="padding: 1rem;">Location</th>
                    <th style="padding: 1rem;">Total</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr style="border-bottom: 1px solid #f3f4f6;" id="order-row-<?php echo $order['id']; ?>">
                    <td style="padding: 1rem; font-weight: 600;">#<?php echo $order['id']; ?></td>
                    <td style="padding: 1rem;">
                        <strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong><br>
                        <span style="font-size: 0.8rem; color: #6b7280;"><?php echo htmlspecialchars($order['email']); ?></span>
                    </td>
                    <td style="padding: 1rem; font-size: 0.875rem;"><?php echo htmlspecialchars($order['phone']); ?></td>
                    <td style="padding: 1rem; font-size: 0.875rem;">
                        <?php echo htmlspecialchars($order['city'] . ', ' . $order['country']); ?>
                    </td>
                    <td style="padding: 1rem; font-weight: 700; color: #111827;">$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td style="padding: 1rem;">
                        <span class="status-badge status-<?php echo $order['status']; ?>" id="badge-<?php echo $order['id']; ?>"
                            style="padding: 0.3rem 0.7rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
                            <?php 
                                switch($order['status']) {
                                    case 'pending': echo 'background: #fef3c7; color: #92400e;'; break;
                                    case 'paid': echo 'background: #d1fae5; color: #065f46;'; break;
                                    case 'shipped': echo 'background: #dbeafe; color: #1e40af;'; break;
                                    case 'delivered': echo 'background: #dcfce7; color: #166534;'; break;
                                }
                            ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                    <td style="padding: 1rem;">
                        <select onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)" 
                                style="padding: 0.4rem; border-radius: 0.375rem; border: 1px solid #d1d5db; font-size: 0.875rem; cursor: pointer; outline: none;">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $order['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
async function updateOrderStatus(orderId, newStatus) {
    try {
        const response = await fetch('/eccommerce/api/update-order-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${orderId}&status=${newStatus}`
        });
        const data = await response.json();
        
        if (data.success) {
            const badge = document.getElementById(`badge-${orderId}`);
            badge.textContent = newStatus;
            badge.className = `status-badge status-${newStatus}`;
            
            // Dynamic styling update
            let style = '';
            switch(newStatus) {
                case 'pending': style = 'background: #fef3c7; color: #92400e;'; break;
                case 'paid': style = 'background: #d1fae5; color: #065f46;'; break;
                case 'shipped': style = 'background: #dbeafe; color: #1e40af;'; break;
                case 'delivered': style = 'background: #dcfce7; color: #166534;'; break;
            }
            badge.style.cssText = `padding: 0.3rem 0.7rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; ${style}`;
            
            showNotification(`Order #${orderId} status updated to ${newStatus}`);
        } else {
            showNotification(data.message, true);
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Failed to update order status', true);
    }
}
</script>

<?php require_once '../includes/admin-footer.php'; ?>
