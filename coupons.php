<?php
require 'includes/auth.php';
$pageTitle = "Get Discount Coupons - LuxuryStore";
include 'includes/header.php';

// Fetch public active coupons
$query = "SELECT * FROM coupons WHERE status = 'active' AND expiry_date >= CURDATE() AND (usage_limit IS NULL OR used_count < usage_limit) ORDER BY created_at DESC";
$coupons = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<section style="padding: 5rem 0; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 4rem;">
            <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: #1e293b;">Exclusive Offers & Coupons</h2>
            <p style="color: #64748b; font-size: 1.1rem;">Grab these special discount codes and apply them at checkout to save big on your luxury purchases!</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
            <?php if (count($coupons) > 0): ?>
                <?php foreach ($coupons as $coupon): ?>
                    <div style="background: white; border-radius: 1rem; padding: 2rem; border: 2px dashed #e2e8f0; position: relative; transition: transform 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="background: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; display: inline-block; font-weight: 700; font-size: 1.5rem; margin-bottom: 1.5rem;">
                            <?php echo $coupon['discount_type'] == 'percentage' ? $coupon['discount_value'].'%' : '$'.$coupon['discount_value']; ?> OFF
                        </div>
                        <h3 style="margin-bottom: 1rem; color: #1e293b;">Special Discount</h3>
                        <p style="color: #64748b; margin-bottom: 1.5rem; font-size: 0.95rem;">
                            <?php if ($coupon['min_order_amount'] > 0): ?>
                                Valid on orders over <strong>$<?php echo number_format($coupon['min_order_amount'], 2); ?></strong>.
                            <?php else: ?>
                                Valid on all orders.
                            <?php endif; ?>
                            Expires on <?php echo date('M j, Y', strtotime($coupon['expiry_date'])); ?>.
                        </p>
                        
                        <div style="background: #f1f5f9; padding: 1rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                            <code style="font-family: 'Courier New', Courier, monospace; font-size: 1.25rem; font-weight: 800; color: #1e293b;"><?php echo $coupon['code']; ?></code>
                            <button onclick="copyCode('<?php echo $coupon['code']; ?>', this)" style="background: white; border: 1px solid #cbd5e1; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; font-weight: 600; transition: all 0.2s;">
                                Copy Code
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 4rem; background: white; border-radius: 1rem; border: 1px solid #e2e8f0;">
                    <i class="fas fa-ticket-alt" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1.5rem;"></i>
                    <h3 style="color: #1e293b;">No active coupons at the moment</h3>
                    <p style="color: #64748b;">Check back later for exclusive deals and seasonal offers!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function copyCode(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const originalText = btn.textContent;
        btn.textContent = 'Copied!';
        btn.style.background = '#d1fae5';
        btn.style.color = '#065f46';
        btn.style.borderColor = '#10b981';
        
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = 'white';
            btn.style.color = '#1e293b';
            btn.style.borderColor = '#cbd5e1';
        }, 2000);
    });
}
</script>

<?php include 'includes/footer.php'; ?>
