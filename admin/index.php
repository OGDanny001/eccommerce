<?php
$pageTitle = "Admin Dashboard";
$activePage = "dashboard";
require_once '../includes/admin-header.php';

// Fetch stats
// 1. Total Orders
$res = $conn->query("SELECT COUNT(*) as count FROM orders");
$totalOrders = $res->fetch_assoc()['count'];

// 2. Total Revenue
$res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status = 'paid' OR status = 'shipped' OR status = 'delivered'");
$totalRevenue = $res->fetch_assoc()['total'] ?? 0;

// 3. Total Users
$res = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$totalUsers = $res->fetch_assoc()['count'];

// 4. Total Products
$res = $conn->query("SELECT COUNT(*) as count FROM products");
$totalProducts = $res->fetch_assoc()['count'];

// Latest Orders
$latestOrders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="admin-card" style="border-left: 4px solid #3b82f6;">
        <div style="color: #6b7280; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">Total Orders</div>
        <div style="font-size: 1.875rem; font-weight: 700; margin-top: 0.5rem;"><?php echo $totalOrders; ?></div>
    </div>
    <div class="admin-card" style="border-left: 4px solid #10b981;">
        <div style="color: #6b7280; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">Total Revenue</div>
        <div style="font-size: 1.875rem; font-weight: 700; margin-top: 0.5rem;">$<?php echo number_format($totalRevenue, 2); ?></div>
    </div>
    <div class="admin-card" style="border-left: 4px solid #f59e0b;">
        <div style="color: #6b7280; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">Customers</div>
        <div style="font-size: 1.875rem; font-weight: 700; margin-top: 0.5rem;"><?php echo $totalUsers; ?></div>
    </div>
    <div class="admin-card" style="border-left: 4px solid #8b5cf6;">
        <div style="color: #6b7280; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">Total Products</div>
        <div style="font-size: 1.875rem; font-weight: 700; margin-top: 0.5rem;"><?php echo $totalProducts; ?></div>
    </div>
</div>

<div class="admin-card">
    <h3 style="margin-bottom: 1.5rem;">Recent Orders</h3>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">Customer</th>
                    <th style="padding: 1rem;">Date</th>
                    <th style="padding: 1rem;">Total</th>
                    <th style="padding: 1rem;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestOrders as $order): ?>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 1rem;">#<?php echo $order['id']; ?></td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                    <td style="padding: 1rem;"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                    <td style="padding: 1rem;">$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; 
                            background: <?php echo $order['status'] == 'paid' ? '#d1fae5' : '#fee2e2'; ?>; 
                            color: <?php echo $order['status'] == 'paid' ? '#065f46' : '#991b1b'; ?>;">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
