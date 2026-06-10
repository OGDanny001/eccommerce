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
            padding: 1.5rem 0;
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
            padding: 0.75rem 1.5rem;
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.3s;
        }
        .admin-nav-link:hover, .admin-nav-link.active {
            background: #374151;
            color: white;
        }
        .admin-main {
            flex: 1;
            margin-left: var(--admin-sidebar-width);
            padding: 2rem;
        }
        .admin-top-nav {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .admin-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <a href="/eccommerce/admin/index.php" class="admin-logo">Luxury Admin</a>
        <nav class="admin-nav">
            <a href="/eccommerce/admin/index.php" class="admin-nav-link <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="/eccommerce/admin/orders.php" class="admin-nav-link <?php echo ($activePage == 'orders') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Orders
            </a>
            <a href="/eccommerce/admin/products.php" class="admin-nav-link <?php echo ($activePage == 'products') ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="/eccommerce/admin/users.php" class="admin-nav-link <?php echo ($activePage == 'users') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Users
            </a>
            <div style="margin-top: 2rem; padding: 0 1.5rem;">
                <hr style="border: 0; border-top: 1px solid #374151;">
            </div>
            <a href="/eccommerce/index.php" class="admin-nav-link">
                <i class="fas fa-eye"></i> View Store
            </a>
            <a href="/eccommerce/logout.php" class="admin-nav-link" style="color: #f87171;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-top-nav">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                <i class="fas fa-user-circle fa-lg"></i>
            </div>
        </div>
