// =============================================
// GLOBAL APP STATE
// =============================================
const appState = {
    cart: [],
    wishlist: JSON.parse(localStorage.getItem('wishlist')) || [],
    products: [], // Will be populated by PHP from database
    currentSliderIndex: 0
};

// =============================================
// HELPER: GET STARS HTML
// =============================================
function getStarsHtml(rating) {
    let stars = '';
    for(let i=1; i<=5; i++) {
        stars += `<i class="fas fa-star"></i>`;
    }
    return stars;
}

// =============================================
// UPDATE BADGES
// =============================================
function updateBadges() {
    const cartBadge = document.querySelector('.cart-badge');
    const wishlistBadge = document.querySelector('.wishlist-badge');
    
    if (cartBadge) cartBadge.textContent = appState.cart.reduce((sum, item) => sum + item.quantity, 0);
    if (wishlistBadge) wishlistBadge.textContent = appState.wishlist.length;
}

// =============================================
// CART FUNCTIONS (DATABASE-BACKED)
// =============================================
async function addToCart(productId, quantity = 1) {
  try {
    const response = await fetch('/eccommerce/api/cart-add.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `product_id=${productId}&quantity=${quantity}`
    });
    const data = await response.json();
    if (data.success) {
      await loadCart();
      showNotification('Item added to cart!');
    } else {
      showNotification(data.message);
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    showNotification('Error adding to cart');
  }
}

async function removeFromCart(productId) {
  try {
    const response = await fetch('/eccommerce/api/cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `product_id=${productId}`
    });
    const data = await response.json();
    if (data.success) {
      await loadCart();
      if (document.getElementById('cart-items')) renderCart();
      showNotification('Item removed from cart');
    } else {
      showNotification(data.message);
    }
  } catch (error) {
    console.error('Error removing from cart:', error);
    showNotification('Error removing from cart');
  }
}

async function updateQuantity(productId, change) {
  const item = appState.cart.find(i => i.product_id === productId);
  if (item) {
    const newQuantity = item.quantity + change;
    try {
      const response = await fetch('/eccommerce/api/cart-update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&quantity=${newQuantity}`
      });
      const data = await response.json();
      if (data.success) {
        await loadCart();
        if (document.getElementById('cart-items')) renderCart();
      } else {
        showNotification(data.message);
      }
    } catch (error) {
      console.error('Error updating quantity:', error);
      showNotification('Error updating quantity');
    }
  }
}

async function loadCart() {
  try {
    const response = await fetch('/eccommerce/api/cart-get.php');
    const data = await response.json();
    if (data.success) {
      appState.cart = data.cart;
      updateBadges();
    }
  } catch (error) {
    console.error('Error loading cart:', error);
  }
}

// =============================================
// WISHLIST FUNCTIONS
// =============================================
function toggleWishlist(productId) {
    const product = appState.products.find(p => p.id === productId);
    if (!product) return;
    
    const index = appState.wishlist.findIndex(item => item.id === productId);
    if (index > -1) {
        appState.wishlist.splice(index, 1);
        showNotification(`${product.name} removed from wishlist`);
    } else {
        appState.wishlist.push(product);
        showNotification(`${product.name} added to wishlist!`);
    }
    saveWishlist();
    updateBadges();
    if (document.getElementById('wishlist-grid')) renderWishlist();
}

function saveWishlist() {
    localStorage.setItem('wishlist', JSON.stringify(appState.wishlist));
}

// =============================================
// TOAST NOTIFICATIONS
// =============================================
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'toast-notification';
    notification.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// =============================================
// HERO SLIDER
// =============================================
const slides = [
    {
        title: "Premium Products",
        subtitle: "For Modern Life",
        text: "Discover our collection of high-quality products designed with you in mind. Shop now and enjoy exclusive offers!",
        cta: "Shop Now",
        ctaLink: "/eccommerce/products.php"
    },
    {
        title: "Summer Sale 2024",
        subtitle: "Up to 50% OFF",
        text: "Don't miss out on our biggest sale of the year! Limited time only.",
        cta: "View Deals",
        ctaLink: "/eccommerce/deals.html"
    },
    {
        title: "New Arrivals",
        subtitle: "Latest Trends",
        text: "Check out our brand new collection just in time for the season.",
        cta: "Explore Now",
        ctaLink: "/eccommerce/categories.html"
    }
];

function renderSlider() {
    const sliderContainer = document.getElementById('slider-container');
    if (!sliderContainer) return;
    
    sliderContainer.innerHTML = slides.map((slide, index) => `
        <div class="hero-slide ${index === 0 ? 'active' : ''}">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1>${slide.title}<br><span style="opacity:0.85;font-size:0.7em">${slide.subtitle}</span></h1>
                        <p>${slide.text}</p>
                        <div style="display:flex;gap:1rem;">
                            <a href="${slide.ctaLink}" class="btn btn-primary btn-lg">${slide.cta}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    sliderContainer.innerHTML += `
        <div class="slider-controls">
            <button class="slider-btn" id="prevSlide"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-btn" id="nextSlide"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="slider-dots" id="slider-dots">
            ${slides.map((_, i) => `<div class="slider-dot ${i === 0 ? 'active' : ''}" data-index="${i}"></div>`).join('')}
        </div>
    `;
    
    document.getElementById('prevSlide').addEventListener('click', () => changeSlide(-1));
    document.getElementById('nextSlide').addEventListener('click', () => changeSlide(1));
    
    document.querySelectorAll('.slider-dot').forEach(dot => {
        dot.addEventListener('click', (e) => {
            appState.currentSliderIndex = parseInt(e.target.dataset.index);
            updateSlider();
        });
    });
    
    setInterval(() => changeSlide(1), 5000);
}

function changeSlide(direction) {
    appState.currentSliderIndex += direction;
    if (appState.currentSliderIndex >= slides.length) appState.currentSliderIndex = 0;
    if (appState.currentSliderIndex < 0) appState.currentSliderIndex = slides.length - 1;
    updateSlider();
}

function updateSlider() {
    document.querySelectorAll('.hero-slide').forEach((slide, i) => {
        slide.classList.toggle('active', i === appState.currentSliderIndex);
    });
    document.querySelectorAll('.slider-dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === appState.currentSliderIndex);
    });
}

// =============================================
// PRODUCT CARD RENDER
// =============================================
function renderProductCard(product) {
    return `
        <div class="product-card" onclick="window.location.href='product.php?id=${product.id}'">
            <div class="product-image">
                ${product.badge ? `<span class="product-badge">${product.badge}</span>` : ''}
                <div class="product-actions" onclick="event.stopPropagation()">
                    <button onclick="toggleWishlist(${product.id})"><i class="fas fa-heart"></i></button>
                </div>
                ${product.image ? `<img src="${product.image}" alt="${product.name}" style="width:100%;height:200px;object-fit:cover;">` : `<i class="fas fa-${product.icon}"></i>`}
            </div>
            <div class="product-info">
                <div class="product-category">${product.category}</div>
                <h4 class="product-title">${product.name}</h4>
                <div class="product-rating">${getStarsHtml(product.rating)}<span style="color: var(--text-muted);">(${product.ratingCount})</span></div>
                <div class="product-price">
                    <span class="price-current">$${product.price}</span>
                    ${product.oldPrice ? `<span class="price-old">$${product.oldPrice}</span>` : ''}
                </div>
                <button onclick="event.stopPropagation(); addToCart(${product.id})" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
        </div>
    `;
}

// =============================================
// RENDER WISHLIST
// =============================================
function renderWishlist() {
    const grid = document.getElementById('wishlist-grid');
    const empty = document.getElementById('wishlist-empty');
    if (!grid || !empty) return;
    
    if (appState.wishlist.length === 0) {
        grid.style.display = 'none';
        empty.style.display = 'block';
        return;
    }
    
    grid.style.display = 'grid';
    empty.style.display = 'none';
    grid.innerHTML = appState.wishlist.map(product => renderProductCard(product)).join('');
}

// =============================================
// RENDER CART
// =============================================
function renderCart() {
    const itemsContainer = document.getElementById('cart-items');
    const summaryContainer = document.getElementById('cart-summary');
    if (!itemsContainer || !summaryContainer) return;
    
    if (appState.cart.length === 0) {
        itemsContainer.innerHTML = `
            <div style="text-align:center;padding:4rem;">
                <i class="fas fa-shopping-cart" style="font-size:5rem;color:var(--text-muted);margin-bottom:1.5rem;"></i>
                <h3>Your cart is empty</h3>
                <p style="color:var(--text-secondary);margin-bottom:2rem;">Add some products to your cart!</p>
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        `;
        summaryContainer.style.display = 'none';
        return;
    }
    
    summaryContainer.style.display = 'block';
    
    itemsContainer.innerHTML = appState.cart.map(item => `
        <div class="cart-item">
            <div class="cart-item-image">
                ${item.image ? `<img src="${item.image}" alt="${item.name}" style="width:80px;height:80px;object-fit:cover;">` : `<i class="fas fa-box"></i>`}
            </div>
            <div class="cart-item-details">
                <h4 style="margin-bottom:0.5rem;">${item.name}</h4>
                <p style="color:var(--text-secondary);margin-bottom:1rem;">$${item.price}</p>
                <div class="qty-selector">
                    <button class="qty-btn" onclick="updateQuantity(${item.product_id}, -1)"><i class="fas fa-minus"></i></button>
                    <span style="padding:0 1rem;font-weight:600;">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.product_id}, 1)"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div style="text-align:right;">
                <p style="font-weight:700;font-size:1.25rem;margin-bottom:1rem;">$${(item.price * item.quantity).toFixed(2)}</p>
                <button onclick="removeFromCart(${item.product_id})" style="background:none;border:none;color:var(--text-muted);cursor:pointer;">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        </div>
    `).join('');
    
    const subtotal = appState.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal > 100 ? 0 : 9.99;
    const total = subtotal + shipping;
    
    summaryContainer.innerHTML = `
        <h3 style="margin-bottom:1.5rem;">Order Summary</h3>
        <div class="summary-row">
            <span>Subtotal</span>
            <span>$${subtotal.toFixed(2)}</span>
        </div>
        <div class="summary-row">
            <span>Shipping</span>
            <span>${shipping === 0 ? 'Free' : '$' + shipping.toFixed(2)}</span>
        </div>
        <div class="summary-row summary-total">
            <span>Total</span>
            <span>$${total.toFixed(2)}</span>
        </div>
        <a href="checkout.php" class="btn btn-primary" style="width:100%;margin-top:1.5rem;">
            <i class="fas fa-credit-card"></i> Proceed to Checkout
        </a>
        <a href="products.php" class="btn btn-secondary" style="width:100%;margin-top:1rem;">
            <i class="fas fa-shopping-bag"></i> Continue Shopping
        </a>
    `;
}

// =============================================
// SEARCH FUNCTIONALITY
// =============================================
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    if (searchInput && searchBtn) {
        const handleSearch = () => {
            const query = searchInput.value.toLowerCase().trim();
            if (window.location.pathname.includes('products.php')) {
                filterProducts(query);
            } else if (query) {
                localStorage.setItem('searchQuery', query);
                window.location.href = 'products.php';
            }
        };
        searchBtn.addEventListener('click', handleSearch);
        searchInput.addEventListener('keypress', (e) => e.key === 'Enter' && handleSearch());
    }
}

function filterProducts(query) {
    const grid = document.querySelector('.product-grid');
    if (!grid) return;
    
    if (!query) {
        grid.innerHTML = appState.products.map(p => renderProductCard(p)).join('');
        return;
    }
    
    const filtered = appState.products.filter(p => 
        p.name.toLowerCase().includes(query) || 
        p.category.toLowerCase().includes(query)
    );
    
    if (filtered.length === 0) {
        grid.innerHTML = `
            <div style="grid-column:1/-1;text-align:center;padding:3rem;">
                <h3>No products found</h3>
                <p style="color:var(--text-secondary);">Try a different search term</p>
            </div>
        `;
    } else {
        grid.innerHTML = filtered.map(p => renderProductCard(p)).join('');
    }
}

// =============================================
// FORM VALIDATION
// =============================================
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#dc2626';
        } else {
            input.style.borderColor = 'var(--border-color)';
        }
    });
    
    const emailInputs = form.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        if (input.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
            isValid = false;
            input.style.borderColor = '#dc2626';
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields correctly');
    }
    
    return isValid;
}

// =============================================
// INITIALIZE APP
// =============================================
document.addEventListener('DOMContentLoaded', async () => {
    await loadCart();
    updateBadges();
    renderSlider();
    initSearch();
    
    if (document.getElementById('wishlist-grid')) renderWishlist();
    if (document.getElementById('cart-items')) renderCart();
    
    if (window.location.pathname.includes('products.php')) {
        const savedQuery = localStorage.getItem('searchQuery');
        if (savedQuery) {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = savedQuery;
            filterProducts(savedQuery);
            localStorage.removeItem('searchQuery');
        }
    }
});

// Add CSS animations to head
const style = document.createElement('style');
style.textContent = `
    .toast-notification {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: var(--primary-color);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        animation: slideUp 0.3s ease;
        display: flex;
        gap: 0.75rem;
        align-items: center;
        font-weight: 500;
    }
    
    @keyframes slideUp {
        from { transform: translateY(100px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(100px); opacity: 0; }
    }
    
    .nav-links-mobile {
        display: flex !important;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        flex-direction: column;
        background: var(--bg-primary);
        box-shadow: var(--shadow-md);
        padding: 1rem;
        z-index: 999;
    }
    
    .nav-links-mobile li {
        margin-bottom: 0.75rem;
    }
    
    .nav-links-mobile a {
        padding: 0.5rem 0;
        display: block;
    }
`;
document.head.appendChild(style);
