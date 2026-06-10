<?php
require '../includes/auth.php';
requireAdmin(); // Admin only!

$pageTitle = "Admin - Dashboard";
require '../includes/header.php';

// Get all orders
$stmt = $conn->prepare("SELECT * FROM orders ORDER BY created_at DESC");
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
.admin-container {
    padding: 2rem 0;
}
.admin-sidebar {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
}
.admin-content {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-sm);
}
.admin-nav-item {
    display: block;
    padding: 0.8rem 1rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}
.admin-nav-item.active, .admin-nav-item:hover {
    background: var(--primary-color);
    color: white;
}
.status-badge {
    padding: 0.3rem 0.7rem;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 600;
}
.status-pending { background: #fef3c7; color: #92400e; }
.status-paid { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #ddd6fe; color: #5b21b6; }
.status-delivered { background: #d1fae5; color: #065f46; }
.admin-table {
    width: 100%;
    border-collapse: collapse;
}
.admin-table th, .admin-table td {
    padding: 0.8rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}
select {
    padding: 0.5rem;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-color);
    background: var(--bg-primary);
}
</style>

<div class="container">
    <div class="admin-container">
        <h2 style="margin-bottom: 1.5rem;">Admin Dashboard</h2>
        <div class="account-layout">
            <aside class="admin-sidebar">
                <a href="/eccommerce/admin/index.php" class="admin-nav-item active">
                    <i class="fas fa-shopping-bag"></i> Orders
                </a>
                <a href="/eccommerce/admin/products.php" class="admin-nav-item">
                    <i class="fas fa-box"></i> Products
                </a>
                <hr style="margin: 1rem 0;">
                <a href="/eccommerce/index.php" class="admin-nav-item">
                    <i class="fas fa-arrow-left"></i> Back to Shop
                </a>
            </aside>
            <main class="admin-content">
                <h3 style="margin-bottom: 1.5rem;">All Orders</h3>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table">
                            <?php if (empty($orders)): ?>
                                <tr><td colspan="6">No orders yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr id="order-row-<?= $order['id'] ?>">
                                        <td>#<?= $order['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></strong><br>
                                            <span style="color: var(--text-muted);"><?= htmlspecialchars($order['email']) ?></span>
                                        </td>
                                        <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                                        <td>$<?= number_format($order['total_price'], 2) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <select class="order-status-select" data-order-id="<?= $order['id'] ?>" style="min-width: 120px;">
                                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="paid" <?= $order['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                                <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.order-status-select').forEach(select => {
    select.addEventListener('change', async (e) => {
        const orderId = e.target.dataset.orderId;
        const newStatus = e.target.value;
        
        try {
            const response = await fetch('/eccommerce/api/update-order-status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_id=${orderId}&status=${newStatus}`
            });
            const result = await response.json();
            
            if (result.success) {
                // Update the badge color/label visually too
                const row = document.querySelector(`#order-row-${orderId}`);
                const badge = row.querySelector('.status-badge');
                badge.className = `status-badge status-${newStatus}`;
                badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                showNotification('Order status updated!');
            } else {
                showNotification('Error updating status');
            }
        } catch (error) {
            console.error(error);
            showNotification('Error updating status');
        }
    });
});
</script>

<?php require '../includes/footer.php'; ?>