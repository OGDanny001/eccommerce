<?php
require '../includes/auth.php';
requireAdmin();

$pageTitle = "Admin - Products";
require '../includes/header.php';

// Get all products and categories
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
.admin-container { padding: 2rem 0; }
.admin-sidebar { background: var(--bg-primary); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); }
.admin-content { background: var(--bg-primary); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); }
.admin-nav-item { display: block; padding: 0.8rem 1rem; border-radius: var(--radius-md); text-decoration: none; color: var(--text-primary); margin-bottom: 0.5rem; }
.admin-nav-item.active, .admin-nav-item:hover { background: var(--primary-color); color: white; }
.admin-table { width: 100%; border-collapse: collapse; }
.admin-table th, .admin-table td { padding: 0.8rem; text-align: left; border-bottom: 1px solid var(--border-color); }
</style>

<div class="container">
    <div class="admin-container">
        <h2 style="margin-bottom: 1.5rem;">Admin Dashboard</h2>
        <div class="account-layout">
            <aside class="admin-sidebar">
                <a href="/eccommerce/admin/index.php" class="admin-nav-item">
                    <i class="fas fa-shopping-bag"></i> Orders
                </a>
                <a href="/eccommerce/admin/products.php" class="admin-nav-item active">
                    <i class="fas fa-box"></i> Products
                </a>
                <hr style="margin: 1rem 0;">
                <a href="/eccommerce/index.php" class="admin-nav-item">
                    <i class="fas fa-arrow-left"></i> Back to Shop
                </a>
            </aside>
            <main class="admin-content">
                <h3 style="margin-bottom: 1.5rem;">Manage Products</h3>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <?php if ($product['image']): ?>
                                                <img src="<?= htmlspecialchars($product['image']) ?>" style="width:50px; height:50px; object-fit: cover; border-radius: var(--radius-md);">
                                            <?php endif; ?>
                                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>