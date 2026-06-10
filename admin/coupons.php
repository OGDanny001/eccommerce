<?php
$pageTitle = "Manage Coupons";
$activePage = "coupons";
require_once '../includes/admin-header.php';

// Fetch all coupons
$coupons = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3 style="margin: 0;">Discount Coupons</h3>
        <button onclick="openCouponModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Coupon
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                    <th style="padding: 1rem;">Code</th>
                    <th style="padding: 1rem;">Discount</th>
                    <th style="padding: 1rem;">Limits</th>
                    <th style="padding: 1rem;">Expiry</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 1rem;"><strong style="color: var(--primary-color);"><?php echo htmlspecialchars($coupon['code']); ?></strong></td>
                    <td style="padding: 1rem;">
                        <?php echo $coupon['discount_type'] == 'percentage' ? $coupon['discount_value'].'%' : '$'.$coupon['discount_value']; ?>
                    </td>
                    <td style="padding: 1rem; font-size: 0.875rem; color: #64748b;">
                        Used: <?php echo $coupon['used_count']; ?> / <?php echo $coupon['usage_limit'] ?? '∞'; ?><br>
                        Min Order: $<?php echo number_format($coupon['min_order_amount'], 2); ?>
                    </td>
                    <td style="padding: 1rem; color: <?php echo strtotime($coupon['expiry_date']) < time() ? '#ef4444' : '#1e293b'; ?>">
                        <?php echo date('M j, Y', strtotime($coupon['expiry_date'])); ?>
                    </td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
                            background: <?php echo $coupon['status'] == 'active' ? '#d1fae5' : '#fee2e2'; ?>; 
                            color: <?php echo $coupon['status'] == 'active' ? '#065f46' : '#991b1b'; ?>;">
                            <?php echo $coupon['status']; ?>
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button onclick='editCoupon(<?php echo json_encode($coupon); ?>)' class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteCoupon(<?php echo $coupon['id']; ?>)" class="btn btn-sm btn-outline" style="color: #ef4444; border-color: #fecaca;"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Coupon Modal -->
<div id="couponModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 500px; border-radius: 0.75rem; padding: 2rem;">
        <h3 id="modalTitle" style="margin-bottom: 1.5rem;">Create Coupon</h3>
        <form id="couponForm">
            <input type="hidden" name="id" id="coupon-id">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Coupon Code</label>
                <input type="text" name="code" id="coupon-code" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; text-transform: uppercase;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Type</label>
                    <select name="discount_type" id="coupon-type" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount ($)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Value</label>
                    <input type="number" step="0.01" name="discount_value" id="coupon-value" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Expiry Date</label>
                    <input type="date" name="expiry_date" id="coupon-expiry" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Usage Limit</label>
                    <input type="number" name="usage_limit" id="coupon-limit" placeholder="Unlimited" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Min Order Amount ($)</label>
                <input type="number" step="0.01" name="min_order_amount" id="coupon-min" value="0.00" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Coupon</button>
                <button type="button" onclick="closeCouponModal()" class="btn btn-outline" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCouponModal() {
    document.getElementById('couponModal').style.display = 'flex';
    document.getElementById('couponForm').reset();
    document.getElementById('coupon-id').value = '';
    document.getElementById('modalTitle').textContent = 'Create Coupon';
}
function closeCouponModal() {
    document.getElementById('couponModal').style.display = 'none';
}
function editCoupon(coupon) {
    openCouponModal();
    document.getElementById('modalTitle').textContent = 'Edit Coupon';
    document.getElementById('coupon-id').value = coupon.id;
    document.getElementById('coupon-code').value = coupon.code;
    document.getElementById('coupon-type').value = coupon.discount_type;
    document.getElementById('coupon-value').value = coupon.discount_value;
    document.getElementById('coupon-expiry').value = coupon.expiry_date;
    document.getElementById('coupon-limit').value = coupon.usage_limit;
    document.getElementById('coupon-min').value = coupon.min_order_amount;
}

document.getElementById('couponForm').onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);
    
    try {
        const res = await fetch('/eccommerce/api/coupon-crud.php', {
            method: 'POST',
            body: params
        });
        const data = await res.json();
        if(data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (err) { alert('Operation failed'); }
};

async function deleteCoupon(id) {
    if(!confirm('Delete this coupon?')) return;
    try {
        const res = await fetch('/eccommerce/api/coupon-crud.php', {
            method: 'POST',
            body: `action=delete&id=${id}`,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        const data = await res.json();
        if(data.success) location.reload();
    } catch (err) { alert('Delete failed'); }
}
</script>

<?php require_once '../includes/admin-footer.php'; ?>
