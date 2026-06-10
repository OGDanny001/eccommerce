<?php
require_once __DIR__ . '/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?> - LuxuryStore</title>
    <link rel="stylesheet" href="/eccommerce/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --admin-sidebar-width: 260px;
        }
        body {
            display: flex;
            background-color: #f3f4f6;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: var(--admin-sidebar-width);
            background: #1f2937;
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            padding: 2.5rem 0;
            display: flex;
            flex-direction: column;
        }
        .admin-logo {
            padding: 0 1.5rem 2rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: block;
        }
        .admin-nav {
            flex: 1;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .admin-nav-link:hover, .admin-nav-link.active {
            background: #374151;
            color: white;
            border-left-color: var(--primary-color);
        }
        .admin-main {
            flex: 1;
            margin-left: var(--admin-sidebar-width);
            padding: 2.5rem;
            background-color: #f8fafc;
        }
        .admin-top-nav {
            background: white;
            padding: 1.25rem 2rem;
            margin: -2.5rem -2rem 2.5rem -2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .admin-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div style="padding: 0 1.5rem 2.5rem;">
            <a href="/eccommerce/admin/index.php" class="admin-logo" style="padding: 0; margin-bottom: 0.5rem;">Luxury Admin</a>
            <span style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Management Suite</span>
        </div>
        <nav class="admin-nav">
            <a href="/eccommerce/admin/index.php" class="admin-nav-link <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="/eccommerce/admin/orders.php" class="admin-nav-link <?php echo ($activePage == 'orders') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="/eccommerce/admin/products.php" class="admin-nav-link <?php echo ($activePage == 'products') ? 'active' : ''; ?>">
                <i class="fas fa-box-open"></i> Products
            </a>
            <a href="/eccommerce/admin/users.php" class="admin-nav-link <?php echo ($activePage == 'users') ? 'active' : ''; ?>">
                <i class="fas fa-user-friends"></i> Users
            </a>
            <div style="margin-top: 2rem; padding: 0 1.5rem;">
                <hr style="border: 0; border-top: 1px solid #374151;">
            </div>
            <a href="/eccommerce/index.php" class="admin-nav-link">
                <i class="fas fa-external-link-alt"></i> View Storefront
            </a>
            <a href="/eccommerce/logout.php" class="admin-nav-link" style="color: #f87171;">
                <i class="fas fa-power-off"></i> Sign Out
            </a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-top-nav">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #1e293b;"><?php echo $pageTitle; ?></h2>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="text-align: right;">
                    <div style="font-size: 0.875rem; font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                    <div style="font-size: 0.75rem; color: #64748b;">System Administrator</div>
                </div>
                <?php 
                $currUser = getCurrentUser(); 
                if ($currUser && $currUser['profile_pic']): ?>
                    <img src="<?php echo htmlspecialchars($currUser['profile_pic']); ?>" style="width: 40px; height: 40px; border-radius: 999px; object-fit: cover;" alt="Profile">
                <?php else: ?>
                    <div style="width: 40px; height: 40px; background: var(--primary-color); color: white; border-radius: 99px; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                        <?php echo substr($_SESSION['name'], 0, 1); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
