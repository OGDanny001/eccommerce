<?php
// ----------------------
// PROTECT THIS PAGE
// ----------------------
// Require the user to be logged in to access this page
require '../includes/auth.php';
requireLogin();

// IF ADMIN, REDIRECT TO ADMIN DASHBOARD
if (isAdmin()) {
    header('Location: /eccommerce/admin/index.php');
    exit;
}

// Get current user information
$current_user = getCurrentUser();

// Set page title
$pageTitle = "My Dashboard - LuxuryStore";
require '../includes/header.php';

// Get user's order count
$order_count = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $current_user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $order_count = $row['count'];
}
$stmt->close();

// Get total spent
$total_spent = 0;
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) as total FROM orders WHERE user_id = ? AND (status = 'paid' OR status = 'shipped' OR status = 'delivered')");
$stmt->bind_param("i", $current_user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total_spent = $row['total'];
}
$stmt->close();

// Get recent orders
$recent_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recent_orders->bind_param("i", $current_user['id']);
$recent_orders->execute();
$recent_orders_result = $recent_orders->get_result();
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
        .account-menu { list-style: none; }
        .account-menu li { margin-bottom: 0.5rem; }
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
            padding: 2.5rem;
            box-shadow: var(--shadow-sm);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .stat-card {
            padding: 1.75rem;
            border-radius: var(--radius-lg);
            color: white;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .stat-card h4 { font-size: 2.5rem; margin:0; }
        .stat-card p { margin:0; opacity:0.9; }
        .stat-card .icon {
            font-size: 2rem; margin-bottom:0.5rem;
        }
        .stat-card-orders { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-card-spent { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .stat-card-wishlist { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-card-addresses { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .quick-actions { margin-top: 2rem; padding-top: 2rem; border-top:1px solid var(--border-color); }
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
            gap: 1rem; margin-top:1rem;
        }
        .quick-action-btn {
            display:flex; align-items:center; gap:0.75rem; padding:1rem 1.5rem;
            background: var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-md);
            text-decoration:none; color:var(--text-primary); transition:all 0.2s;
        }
        .quick-action-btn:hover { border-color:var(--primary-color); box-shadow:var(--shadow-sm); }
        .orders-table { width:100%; border-collapse:collapse; margin-top:1.5rem; }
        .orders-table th, .orders-table td {
            padding:1rem; border-bottom:1px solid var(--border-color); text-align:left;
        }
        .orders-table th { color:var(--text-secondary); font-weight:600; font-size:0.9rem; }
        .order-status-badge {
            padding:0.4rem 0.8rem; border-radius:100px; font-size:0.75rem; font-weight:700;
            background: var(--bg-secondary); color: var(--text-secondary); display:inline-block;
        }
        .order-status-paid { background:#e6fffa; color:#047857; }
        .order-status-shipped { background:#eff6ff; color:#1d4ed8; }
        .order-status-delivered { background:#ecfdf5; color:#065f46; }
        .order-status-pending { background:#fffbeb; color:#92400e; }
        
        @media (max-width:768px) {
            .account-layout { grid-template-columns:1fr; }
            .account-sidebar { position:sticky; top:80px; }
        }
    </style>

    <section style="padding: 4rem 0;">
        <div class="container">
            <h2 style="margin-bottom: 2.5rem;">My Account</h2>
            <div class="account-layout">
                <aside class="account-sidebar">
                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color);">
                        <?php if ($current_user['profile_pic']): ?>
                            <img src="<?php echo htmlspecialchars($current_user['profile_pic']); ?>" alt="Profile" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                            <div style="width:64px;height:64px;border-radius:50%;background:var(--primary-color);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.75rem;">
                                <?php echo strtoupper(substr($current_user['name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <strong style="font-size:1.1rem; display:block;"><?php echo htmlspecialchars($current_user['name']); ?></strong>
                            <span style="font-size:0.9rem; color:var(--text-secondary);"><?php echo htmlspecialchars($current_user['email']); ?></span>
                        </div>
                    </div>

                    <h3 style="margin-bottom:1.25rem;">Account Menu</h3>
                    <ul class="account-menu">
                        <li><a href="/eccommerce/user/dashboard.php" class="active"><i class="fas fa-chart-bar"></i> Dashboard</a></li>
                        <li><a href="/eccommerce/user/orders.php"><i class="fas fa-box"></i> My Orders</a></li>
                        <li><a href="/eccommerce/user/profile.php"><i class="fas fa-user"></i> Profile Settings</a></li>
                        <li><a href="/eccommerce/user/addresses.php"><i class="fas fa-home"></i> Saved Addresses</a></li>
                        <li><a href="/eccommerce/wishlist.html"><i class="fas fa-heart"></i> Wishlist</a></li>
                    </ul>
                </aside>

                <main class="account-content">
                    <h3 style="margin-bottom: 2.5rem;">Welcome back, <?php echo htmlspecialchars($current_user['name']); ?>!</h3>

                    <div class="stats-grid">
                        <div class="stat-card stat-card-orders">
                            <span class="icon"><i class="fas fa-shopping-bag"></i></span>
                            <h4><?php echo $order_count; ?></h4>
                            <p>Total Orders</p>
                        </div>
                        <div class="stat-card stat-card-spent">
                            <span class="icon"><i class="fas fa-dollar-sign"></i></span>
                            <h4>$<?php echo number_format($total_spent, 2); ?></h4>
                            <p>Total Spent</p>
                        </div>
                        <div class="stat-card stat-card-wishlist">
                            <span class="icon"><i class="fas fa-heart"></i></span>
                            <h4>0</h4>
                            <p>Wishlist Items</p>
                        </div>
                        <div class="stat-card stat-card-addresses">
                            <span class="icon"><i class="fas fa-home"></i></span>
                            <h4>0</h4>
                            <p>Saved Addresses</p>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div style="margin-top: 3rem;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                            <h4 style="margin:0; font-size:1.25rem;">Recent Orders</h4>
                            <a href="/eccommerce/user/orders.php" style="color:var(--primary-color); text-decoration:none; font-weight:600;">View All →</a>
                        </div>
                        
                        <div style="overflow-x:auto;">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_orders_result->num_rows > 0): ?>
                                        <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                                        <tr>
                                            <td style="font-weight:600;">#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                            <td style="font-weight:600;">$<?php echo number_format($order['total_price'], 2); ?></td>
                                            <td><span class="order-status-badge order-status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                            <td><a href="/eccommerce/user/orders.php" style="color:var(--primary-color); text-decoration:none; font-weight:600;">View</a></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-secondary);">No orders yet. <a href="/eccommerce/products.php" style="color:var(--primary-color); font-weight:600;">Start shopping</a></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <h4 style="margin-bottom:0.5rem; font-size:1.25rem;">Quick Actions</h4>
                        <div class="quick-actions-grid">
                            <a href="/eccommerce/products.php" class="quick-action-btn">
                                <i class="fas fa-shopping-cart"></i> Shop Now
                            </a>
                            <a href="/eccommerce/user/profile.php" class="quick-action-btn">
                                <i class="fas fa-user-edit"></i> Edit Profile
                            </a>
                            <a href="/eccommerce/user/orders.php" class="quick-action-btn">
                                <i class="fas fa-history"></i> View Orders
                            </a>
                            <a href="/eccommerce/cart.php" class="quick-action-btn">
                                <i class="fas fa-cart-arrow-down"></i> Go to Cart
                            </a>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </section>

<?php require '../includes/footer.php'; ?>
