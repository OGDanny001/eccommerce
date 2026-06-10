<?php
// Include auth functions to check login status
require 'includes/auth.php';

// Check if user is not logged in - if yes, show message and redirect
if (!isLoggedIn()) {
    header('Location: /eccommerce/login.php?msg=Please login or create an account to continue checkout.');
    exit;
}

$pageTitle = "Checkout - LuxuryStore";
include 'includes/header.php';
?>

<!-- Add Intl-Tel-Input CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<style>
    .iti { width: 100%; }
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background-color: var(--bg-primary);
        color: var(--text-primary);
    }
    .loading-spinner {
        display: none;
        margin-left: 10px;
        font-size: 0.8rem;
        color: var(--text-muted);
    }
</style>

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
                            <input type="text" name="first_name" placeholder="John" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" placeholder="Doe" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="john@example.com" required value="<?php echo htmlspecialchars(getCurrentUser()['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" id="phone" name="phone_input" required>
                        <input type="hidden" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Street Address</label>
                        <input type="text" name="address" placeholder="123 Main St" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Country</label>
                            <select id="country" name="country" required>
                                <option value="">Select Country</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>State / Province <span id="state-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></span></label>
                            <select id="state" name="state" required disabled>
                                <option value="">Select State</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>City <span id="city-loading" class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></span></label>
                            <select id="city" name="city" required disabled>
                                <option value="">Select City</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ZIP / Postal Code</label>
                            <input type="text" name="zip_code" placeholder="10001" required>
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
                    <!-- Summary will be loaded from JS -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
// --- Phone Input Initialization ---
const phoneInput = document.querySelector("#phone");
const iti = window.intlTelInput(phoneInput, {
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    separateDialCode: true,
    initialCountry: "auto",
    geoIpLookup: function(success, failure) {
        fetch("https://ipapi.co/json").then(res => res.json()).then(data => success(data.country_code)).catch(() => success("us"));
    }
});

// --- Country/State/City Cascading Logic ---
const countrySelect = document.querySelector("#country");
const stateSelect = document.querySelector("#state");
const citySelect = document.querySelector("#city");
const stateLoading = document.querySelector("#state-loading");
const cityLoading = document.querySelector("#city-loading");

// Load Countries
async function loadCountries() {
    try {
        const response = await fetch("https://countriesnow.space/api/v0.1/countries/positions");
        const data = await response.json();
        if (!data.error) {
            data.data.forEach(country => {
                const option = document.createElement("option");
                option.value = country.name;
                option.textContent = country.name;
                countrySelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error("Error loading countries:", error);
    }
}

// Load States
countrySelect.addEventListener("change", async () => {
    const countryName = countrySelect.value;
    stateSelect.innerHTML = '<option value="">Select State</option>';
    citySelect.innerHTML = '<option value="">Select City</option>';
    stateSelect.disabled = true;
    citySelect.disabled = true;

    if (!countryName) return;

    stateLoading.style.display = "inline";
    try {
        const response = await fetch("https://countriesnow.space/api/v0.1/countries/states", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ country: countryName })
        });
        const data = await response.json();
        if (!data.error) {
            data.data.states.forEach(state => {
                const option = document.createElement("option");
                option.value = state.name;
                option.textContent = state.name;
                stateSelect.appendChild(option);
            });
            stateSelect.disabled = false;
        }
    } catch (error) {
        console.error("Error loading states:", error);
    } finally {
        stateLoading.style.display = "none";
    }
});

// Load Cities
stateSelect.addEventListener("change", async () => {
    const countryName = countrySelect.value;
    const stateName = stateSelect.value;
    citySelect.innerHTML = '<option value="">Select City</option>';
    citySelect.disabled = true;

    if (!stateName) return;

    cityLoading.style.display = "inline";
    try {
        const response = await fetch("https://countriesnow.space/api/v0.1/countries/state/cities", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ country: countryName, state: stateName })
        });
        const data = await response.json();
        if (!data.error) {
            data.data.forEach(city => {
                const option = document.createElement("option");
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
            citySelect.disabled = false;
        }
    } catch (error) {
        // If cities fail, allow manual typing as fallback
        const option = document.createElement("option");
        option.value = "Other";
        option.textContent = "Other (Type below)";
        citySelect.appendChild(option);
        citySelect.disabled = false;
    } finally {
        cityLoading.style.display = "none";
    }
});

// --- Order Summary ---
let currentSubtotal = 0;
let appliedCoupon = null;

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
        currentSubtotal = 0;
        data.cart.forEach(item => {
            currentSubtotal += item.price * item.quantity;
            itemsHtml += `
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>${item.name} x${item.quantity}</span>
                    <span>$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            `;
        });

        updateSummaryDisplay(itemsHtml);
    } catch (error) {
        console.error("Error loading summary:", error);
    }
}

function updateSummaryDisplay(itemsHtml) {
    const shipping = currentSubtotal > 100 ? 0 : 9.99;
    const discount = appliedCoupon ? appliedCoupon.discount : 0;
    const total = currentSubtotal + shipping - discount;
    
    document.getElementById('order-summary').innerHTML = `
        <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            ${itemsHtml}
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span>Subtotal</span>
            <span>$${currentSubtotal.toFixed(2)}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span>Shipping</span>
            <span>${shipping === 0 ? 'FREE' : '$' + shipping.toFixed(2)}</span>
        </div>
        ${appliedCoupon ? `
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #059669; font-weight: 600;">
            <span>Discount (${appliedCoupon.code})</span>
            <span>-$${discount.toFixed(2)}</span>
        </div>
        ` : ''}
        <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--border-color); font-weight: 800; font-size: 1.25rem;">
            <span>Total</span>
            <span>$${total.toFixed(2)}</span>
        </div>
        
        <div style="margin-top: 2rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600;">Have a coupon?</label>
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" id="coupon-code" placeholder="Enter code" style="flex: 1; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <button onclick="applyCoupon()" class="btn btn-sm btn-secondary">Apply</button>
            </div>
            <div id="coupon-msg" style="margin-top: 0.5rem; font-size: 0.8rem;"></div>
        </div>
    `;
}

async function applyCoupon() {
    const code = document.getElementById('coupon-code').value;
    const msg = document.getElementById('coupon-msg');
    
    if(!code) return;
    
    try {
        const response = await fetch('/eccommerce/api/coupon-validate.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `code=${code}&amount=${currentSubtotal}`
        });
        const data = await response.json();
        
        if(data.success) {
            appliedCoupon = data;
            msg.style.color = '#059669';
            msg.textContent = data.message;
            loadCheckoutSummary(); // Refresh display
        } else {
            msg.style.color = '#dc2626';
            msg.textContent = data.message;
        }
    } catch (error) {
        msg.textContent = 'Error validating coupon';
    }
}
        const shipping = subtotal > 100 ? 0 : 9.99;
        const total = subtotal + shipping;
        
        summary.innerHTML = `
            <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
            ${itemsHtml}
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <span>Subtotal</span>
                <span>$${subtotal.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span>Shipping</span>
                <span>${shipping === 0 ? 'Free' : '$' + shipping.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--border-color); font-size: 1.25rem; font-weight: 700;">
                <span>Total</span>
                <span>$${total.toFixed(2)}</span>
            </div>
        `;
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

// --- Form Submission ---
document.getElementById('checkout-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Set full phone number with dial code
    document.querySelector('input[name="phone"]').value = iti.getNumber();
    
    const formData = new FormData(e.target);
    const searchParams = new URLSearchParams();
    for (const pair of formData) {
        searchParams.append(pair[0], pair[1]);
    }

    // Add coupon data if applied
    if (appliedCoupon) {
        searchParams.append('coupon_id', appliedCoupon.coupon_id);
    }

    try {
        const response = await fetch('/eccommerce/api/order-create.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: searchParams.toString()
        });
        const data = await response.json();
        
        if (data.success) {
            const handler = PaystackPop.setup({
                key: 'pk_test_0a97a5d545639b2f055c2c8b9041cdb0b32b027c',
                email: data.email,
                amount: data.amount,
                currency: 'NGN',
                ref: ''+Math.floor((Math.random()*1000000000)+1),
                metadata: { order_id: data.order_id },
                callback: function(res) { verifyPayment(res.reference, data.order_id); },
                onClose: function() { showNotification('Payment window closed.'); }
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
    loadCountries();
    loadCheckoutSummary();
});
</script>

<?php include 'includes/footer.php'; ?>
