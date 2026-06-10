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
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) as total FROM orders WHERE user_id = ? AND status = 'paid'");
$stmt->bind_param("i", $current_user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total_spent = $row['total'];
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
        .stat-card h4 {
            font-size: 2.5rem;
            margin: 0;
        }
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        .stat-card .icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-card-orders {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .stat-card-spent {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }
        .stat-card-wishlist {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }
        .stat-card-addresses {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }
        .quick-actions {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.2s;
        }
        .quick-action-btn:hover {
            border-color: var(--primary-color);
            box-shadow: var(--shadow-sm);
        }
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
                            <a href="/eccommerce/user/dashboard.php" class="active">
                                <i class="fas fa-chart-bar"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="/eccommerce/user/orders.php">
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
                    <h3 style="margin-bottom: 2rem;">Welcome back, <?php echo htmlspecialchars($current_user['name']); ?>!</h3>

                    <div class="stats-grid">
                        <div class="stat-card stat-card-orders">
                            <div class="icon"><i class="fas fa-box"></i></div>
                            <h4><?php echo $order_count; ?></h4>
                            <p>Total Orders</p>
                        </div>
                        <div class="stat-card stat-card-spent">
                            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                            <h4>$<?php echo number_format($total_spent, 2); ?></h4>
                            <p>Total Spent</p>
                        </div>
                        <div class="stat-card stat-card-wishlist">
                            <div class="icon"><i class="fas fa-heart"></i></div>
                            <h4 id="wishlist-count">0</h4>
                            <p>Wishlist Items</p>
                        </div>
                        <div class="stat-card stat-card-addresses">
                            <div class="icon"><i class="fas fa-home"></i></div>
                            <h4>0</h4>
                            <p>Saved Addresses</p>
                        </div>
                    </div>

                    <div class="quick-actions">
                        <h4 style="margin-bottom: 0.5rem;">Quick Actions</h4>
                        <div class="quick-actions-grid">
                            <a href="/eccommerce/products.php" class="quick-action-btn">
                                <i class="fas fa-shopping-bag"></i> Shop Now
                            </a>
                            <a href="/eccommerce/user/profile.php" class="quick-action-btn">
                                <i class="fas fa-user-cog"></i> Edit Profile
                            </a>
                            <a href="/eccommerce/user/orders.php" class="quick-action-btn">
                                <i class="fas fa-history"></i> View Orders
                            </a>
                            <a href="/eccommerce/cart.html" class="quick-action-btn">
                                <i class="fas fa-shopping-cart"></i> Go to Cart
                            </a>
                        </div>
                    </div>

                    <h4 style="margin: 2.5rem 0 1.5rem 0;">Recent Orders</h4>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary);">Order ID</th>
                                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary);">Date</th>
                                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary);">Total</th>
                                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary);">Status</th>
                                    <th style="text-align: left; padding: 1rem; color: var(--text-secondary);">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($order_count === 0): ?>
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td style="padding: 1rem;" colspan="5">No orders yet. <a href="/eccommerce/products.php" style="color: var(--primary-color); text-decoration: none;">Start shopping now!</a></td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                                    $stmt->bind_param("i", $current_user['id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()):
                                    ?>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td style="padding: 1rem;">#<?php echo $row['id']; ?></td>
                                            <td style="padding: 1rem;"><?php echo date('F j, Y', strtotime($row['created_at'])); ?></td>
                                            <td style="padding: 1rem;">$<?php echo number_format($row['total_price'], 2); ?></td>
                                            <td style="padding: 1rem;">
                                                <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-weight: 600; font-size: 0.875rem; background: <?php 
                                                    echo $row['status'] === 'pending' ? '#fef3c7' : 
                                                         ($row['status'] === 'paid' ? '#d1fae5' : 
                                                         ($row['status'] === 'shipped' ? '#dbeafe' : '#d1fae5')); 
                                                ?>; color: <?php 
                                                    echo $row['status'] === 'pending' ? '#92400e' : 
                                                         ($row['status'] === 'paid' ? '#065f46' : 
                                                         ($row['status'] === 'shipped' ? '#1e40af' : '#065f46')); 
                                                ?>;">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td style="padding: 1rem;">
                                                <a href="/eccommerce/user/orders.php" style="color: var(--primary-color); text-decoration: none;">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; $stmt->close(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </main>
            </div>
        </div>
    </section>

<?php require '../includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const wishlistCount = document.getElementById('wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = wishlist.length;
    }
});
</script>
