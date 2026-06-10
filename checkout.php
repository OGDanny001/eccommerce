<?php
// Include auth functions to check login status
require 'includes/auth.php';

// Check if user is not logged in - if yes, show message and redirect
if (!isLoggedIn()) {
    // Store a temporary message to show on login page
    // Or just redirect with a query string
    header('Location: /eccommerce/login.php?msg=Please login or create an account to continue checkout.');
    exit;
}

$pageTitle = "Checkout - LuxuryStore";
include 'includes/header.php';
?>

<!-- Checkout -->
<section style="padding: 4rem 0;">
    <div class="container">
        <h2 style="margin-bottom: 2rem;">Checkout</h2>
        <div class="checkout-layout">
            <div class="checkout-form" style="background: var(--bg-primary); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm);">
                <h3 style="margin-bottom: 1.5rem;">Shipping Information</h3>
                <form id="checkout-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" placeholder="John" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" placeholder="Doe" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="john@example.com" required value="<?php echo htmlspecialchars(getCurrentUser()['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" placeholder="+1 (555) 123-4567" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" placeholder="123 Main St" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" placeholder="New York" required>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" placeholder="NY" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" placeholder="10001" required>
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <select>
                                <option>United States</option>
                                <option>Canada</option>
                                <option>United Kingdom</option>
                                <option>Nigeria</option>
                            </select>
                        </div>
                    </div>

                    <h3 style="margin: 2rem 0 1.5rem;">Payment Information</h3>
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1.5rem;">
                        <i class="fas fa-credit-card"></i> Pay with Paystack
                    </button>
                </form>
            </div>

            <div>
                <div class="order-summary" id="order-summary" style="background: var(--bg-primary); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); height: fit-content;">
                    <!-- Summary will be loaded from app.js -->
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
async function loadCheckoutSummary() {
    const summary = document.getElementById('order-summary');
    try {
        const response = await fetch('/eccommerce/api/cart-get.php');
        const data = await response.json();
        
        if (!data.success || data.cart.length === 0) {
            summary.innerHTML = `
                <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                <p style="color: var(--text-secondary);">Your cart is empty</p>
                <a href="products.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Continue Shopping</a>
            `;
            return;
        }
        
        let itemsHtml = '';
        let subtotal = 0;
        data.cart.forEach(item => {
            subtotal += item.price * item.quantity;
            itemsHtml += `
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>${item.name} x${item.quantity}</span>
                    <span>$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            `;
        });
        
        const shipping = subtotal > 100 ? 0 : 9.99;
        const total = subtotal + shipping;
        
        summary.innerHTML = `
            <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
            ${itemsHtml}
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span>Subtotal</span>
                <span>$${subtotal.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span>Shipping</span>
                <span>${shipping === 0 ? 'Free' : '$' + shipping.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 1.25rem; font-weight: 700;">
                <span>Total</span>
                <span>$${total.toFixed(2)}</span>
            </div>
        `;
        
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

document.getElementById('checkout-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Validate form
    if (!validateForm('checkout-form')) {
        return;
    }
    
    // Create order
    try {
        const response = await fetch('/eccommerce/api/order-create.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        const data = await response.json();
        
        if (data.success) {
            // Initialize Paystack
            const handler = PaystackPop.setup({
                key: 'pk_test_0a97a5d545639b2f055c2c8b9041cdb0b32b027c', // Replace with your Paystack public key
                email: data.email,
                amount: data.amount,
                currency: 'NGN', // Change to your currency
                ref: ''+Math.floor((Math.random()*1000000000)+1), // generates a pseudo-unique reference. Please replace with a reference you generated.
                metadata: {
                    order_id: data.order_id
                },
                callback: function(response) {
                    // Verify payment
                    verifyPayment(response.reference, data.order_id);
                },
                onClose: function() {
                    alert('Payment window closed.');
                }
            });
            handler.openIframe();
        } else {
            showNotification(data.message);
        }
    } catch (error) {
        console.error('Error creating order:', error);
        showNotification('Error creating order');
    }
});

async function verifyPayment(reference, orderId) {
    try {
        const response = await fetch('/eccommerce/api/verify-payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `reference=${reference}&order_id=${orderId}`
        });
        const data = await response.json();
        
        if (data.success) {
            showNotification('Payment successful!');
            window.location.href = '/eccommerce/user/orders.php';
        } else {
            showNotification('Payment verification failed: ' + data.message);
        }
    } catch (error) {
        console.error('Error verifying payment:', error);
        showNotification('Error verifying payment');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadCheckoutSummary();
});
</script>

<?php include 'includes/footer.php'; ?>
