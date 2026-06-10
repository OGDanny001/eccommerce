<?php
require '../config/db.php';
require '../includes/auth.php';

header('Content-Type: application/json');

// Only allow admins to access this
if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// 1. Get overall stats
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(total_price) as sum FROM orders WHERE status IN ('paid', 'shipped', 'delivered')")->fetch_assoc()['sum'] ?? 0;
$totalCustomers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$avgOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

// 2. Get monthly revenue for the last 12 months
$monthlyRevenue = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $query = "SELECT COALESCE(SUM(total_price), 0) as revenue FROM orders WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month' AND status IN ('paid', 'shipped', 'delivered')";
    $result = $conn->query($query)->fetch_assoc();
    $monthlyRevenue[] = [
        'month' => date('M', strtotime("-$i months")),
        'revenue' => (float)$result['revenue']
    ];
}

// 3. Get top 5 selling products
$topProducts = [];
$query = "SELECT p.name, p.image_url, COUNT(oi.id) as units_sold, COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue
          FROM products p
          LEFT JOIN order_items oi ON p.id = oi.product_id
          GROUP BY p.id
          ORDER BY total_revenue DESC
          LIMIT 5";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $topProducts[] = $row;
}

// 4. Get order status breakdown
$statusBreakdown = [];
$query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $statusBreakdown[$row['status']] = (int)$row['count'];
}

echo json_encode([
    'success' => true,
    'overview' => [
        'total_orders' => (int)$totalOrders,
        'total_revenue' => (float)$totalRevenue,
        'total_customers' => (int)$totalCustomers,
        'avg_order_value' => (float)$avgOrderValue
    ],
    'monthly_revenue' => $monthlyRevenue,
    'top_products' => $topProducts,
    'status_breakdown' => $statusBreakdown
]);
