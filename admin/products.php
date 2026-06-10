<?php
$pageTitle = "Manage Products";
$activePage = "products";
require_once '../includes/admin-header.php';

// Fetch all products with category names
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
$products = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Fetch categories for the "Add/Edit" modal
$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3 style="margin: 0;">Product Inventory</h3>
        <button onclick="openModal()" class="btn btn-primary" style="padding: 0.6rem 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus"></i> Add New Product
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">Product</th>
                    <th style="padding: 1rem;">Category</th>
                    <th style="padding: 1rem;">Price</th>
                    <th style="padding: 1rem;">Stock</th>
                    <th style="padding: 1rem; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 1rem; color: #6b7280;">#<?php echo $product['id']; ?></td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 0.375rem; background: #f3f4f6;">
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?></div>
                        </div>
                    </td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                    <td style="padding: 1rem; font-weight: 700;">$<?php echo number_format($product['price'], 2); ?></td>
                    <td style="padding: 1rem;">
                        <span style="font-weight: 600; color: <?php echo $product['stock'] < 10 ? '#dc2626' : '#059669'; ?>">
                            <?php echo $product['stock']; ?>
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button onclick='editProduct(<?php echo json_encode($product); ?>)' class="btn btn-sm btn-outline" style="padding: 0.4rem 0.8rem;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="btn btn-sm btn-outline" style="padding: 0.4rem 0.8rem; color: #dc2626; border-color: #fecaca;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="productModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 600px; border-radius: 0.75rem; padding: 2rem; position: relative; max-height: 90vh; overflow-y: auto;">
        <button onclick="closeModal()" style="position: absolute; top: 1rem; right: 1rem; border: 0; background: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h3 id="modalTitle" style="margin-bottom: 2rem;">Add New Product</h3>
        
        <form id="productForm">
            <input type="hidden" name="id" id="prod-id">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Product Name</label>
                <input type="text" name="name" id="prod-name" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="prod-price" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Stock</label>
                    <input type="number" name="stock" id="prod-stock" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Category</label>
                <select name="category_id" id="prod-category" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: white;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Image URL</label>
                <input type="url" name="image" id="prod-image" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Description</label>
                <textarea name="description" id="prod-desc" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;"></textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.8rem;">Save Product</button>
                <button type="button" onclick="closeModal()" class="btn btn-outline" style="flex: 1; padding: 0.8rem;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('productModal');
const form = document.getElementById('productForm');

function openModal(isEdit = false) {
    modal.style.display = 'flex';
    if(!isEdit) {
        document.getElementById('modalTitle').textContent = 'Add New Product';
        form.reset();
        document.getElementById('prod-id').value = '';
    }
}

function closeModal() {
    modal.style.display = 'none';
}

function editProduct(product) {
    openModal(true);
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('prod-id').value = product.id;
    document.getElementById('prod-name').value = product.name;
    document.getElementById('prod-price').value = product.price;
    document.getElementById('prod-stock').value = product.stock;
    document.getElementById('prod-category').value = product.category_id;
    document.getElementById('prod-image').value = product.image;
    document.getElementById('prod-desc').value = product.description;
}

form.onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);

    try {
        const response = await fetch('/eccommerce/api/product-crud.php', {
            method: 'POST',
            body: params
        });
        const data = await response.json();
        if(data.success) {
            showNotification(data.message);
            location.reload();
        } else {
            showNotification(data.message, true);
        }
    } catch (error) {
        showNotification('Operation failed', true);
    }
};

async function deleteProduct(id) {
    if(!confirm('Are you sure you want to delete this product?')) return;
    
    try {
        const response = await fetch('/eccommerce/api/product-crud.php', {
            method: 'POST',
            body: `action=delete&id=${id}`,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        const data = await response.json();
        if(data.success) {
            showNotification(data.message);
            location.reload();
        } else {
            showNotification(data.message, true);
        }
    } catch (error) {
        showNotification('Delete failed', true);
    }
}
</script>

<?php require_once '../includes/admin-footer.php'; ?>
