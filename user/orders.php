<?php
// ----------------------
// PROTECT THIS PAGE
// ----------------------
// Require the user to be logged in to access this page
require '../includes/auth.php';
requireLogin();

// Get current user information
$current_user = getCurrentUser();

// Set page title
$pageTitle = "My Orders - LuxuryStore";
require '../includes/header.php';

// Get user's orders
$orders = [];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $current_user['id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // Get order items
    $order_items = [];
    $stmt_items = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt_items->bind_param("i", $row['id']);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();
    while ($item = $result_items->fetch_assoc()) {
        $order_items[] = $item;
    }
    $stmt_items->close();
    
    $row['items'] = $order_items;
    $orders[] = $row;
}
$stmt->close();
?>

    <style>
        .account-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
        }
        .account-sidebar {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            height: fit-content;
        }
        .account-menu {
            list-style: none;
        }
        .account-menu li {
            margin-bottom: 0.5rem;
        }
        .account-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.2s;
        }
        .account-menu a:hover,
        .account-menu a.active {
            background: var(--primary-color);
            color: white;
        }
        .account-content {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .order-card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-shipped { background: #dbeafe; color: #1e40af; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        @media (max-width: 768px) {
            .account-layout {
                grid-template-columns: 1fr;
            }
            .account-sidebar {
                position: sticky;
                top: 80px;
            }
        }
    </style>

    <section style="padding: 4rem 0;">
        <div class="container">
            <h2 style="margin-bottom: 2rem;">My Account</h2>
            <div class="account-layout">
                <aside class="account-sidebar">
                    <h3 style="margin-bottom: 1rem;">Account Menu</h3>
                    <ul class="account-menu">
                        <li>
                            <a href="/eccommerce/user/dashboard.php">
                                <i class="fas fa-chart-bar"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="/eccommerce/user/orders.php" class="active">
                                <i class="fas fa-box"></i> My Orders
                            </a>
                        </li>
                        <li>
                            <a href="/eccommerce/user/profile.php">
                                <i class="fas fa-user"></i> Profile Settings
                            </a>
                        </li>
                        <li>
                            <a href="/eccommerce/user/addresses.php">
                                <i class="fas fa-home"></i> Saved Addresses
                            </a>
                        </li>
                        <li>
                            <a href="/eccommerce/wishlist.html">
                                <i class="fas fa-heart"></i> Wishlist
                            </a>
                        </li>
                    </ul>
                </aside>

                <main class="account-content">
                    <h3 style="margin-bottom: 2rem;">My Orders</h3>
                    
                    <?php if (empty($orders)): ?>
                        <div style="text-align: center; padding: 4rem;">
                            <i class="fas fa-box" style="font-size: 5rem; color: var(--text-muted); margin-bottom: 1.5rem;"></i>
                            <h3>No orders yet</h3>
                            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Start shopping now!</p>
                            <a href="/eccommerce/products.php" class="btn btn-primary">Shop Now</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div>
                                        <h4>Order #<?php echo $order['id']; ?></h4>
                                        <p style="color: var(--text-secondary); font-size: 0.875rem;"><?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></p>
                                    </div>
                                    <span class="order-status status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                
                                <?php foreach ($order['items'] as $item): ?>
                                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius-sm);">
                                        <?php else: ?>
                                            <div style="width: 80px; height: 80px; background: var(--bg-secondary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-box" style="font-size: 2rem; color: var(--text-muted);"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div style="flex: 1;">
                                            <h5 style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($item['name']); ?></h5>
                                            <p style="color: var(--text-secondary); font-size: 0.875rem;">Quantity: <?php echo $item['quantity']; ?></p>
                                            <p style="color: var(--text-primary); font-weight: 600;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div style="text-align: right; font-size: 1.25rem; font-weight: 700;">
                                    Total: $<?php echo number_format($order['total_price'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>

<?php require '../includes/footer.php'; ?>
