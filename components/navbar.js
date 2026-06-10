function getNavbarHTML() {
    const isLoggedIn = currentUser !== null;
    const isAdmin = isLoggedIn && currentUser.role === 'admin';
    
    let accountLink = '';
    let navLinks = '';
    if (isLoggedIn) {
        accountLink = `
            <a href="/eccommerce/user/dashboard.php" class="btn btn-sm btn-outline">
                <i class="fas fa-user"></i> Welcome, ${currentUser.name}
            </a>
            <a href="/eccommerce/logout.php" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">
                Logout
            </a>
        `;
        navLinks = `
            <li><a href="/eccommerce/index.php" class="${window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/') ? 'active' : ''}">Home</a></li>
            <li><a href="/eccommerce/products.php" class="${window.location.pathname.includes('products.php') ? 'active' : ''}">Shop</a></li>
            ${isAdmin ? '<li><a href="/eccommerce/admin/index.php" class="' + (window.location.pathname.includes('/admin/') ? 'active' : '') + '"><i class="fas fa-cog"></i> Admin</a></li>' : ''}
            <li><a href="/eccommerce/user/dashboard.php" class="${window.location.pathname.includes('dashboard.php') ? 'active' : ''}">Dashboard</a></li>
            <li><a href="/eccommerce/user/orders.php" class="${window.location.pathname.includes('orders.php') ? 'active' : ''}">Orders</a></li>
            <li><a href="/eccommerce/user/profile.php" class="${window.location.pathname.includes('profile.php') ? 'active' : ''}">Profile</a></li>
        `;
    } else {
        accountLink = `
            <a href="/eccommerce/login.php" class="btn btn-sm btn-outline">Login</a>
            <a href="/eccommerce/register.php" class="btn btn-sm btn-primary" style="margin-left: 0.5rem;">Register</a>
        `;
        navLinks = `
            <li><a href="/eccommerce/index.php" class="${window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/') ? 'active' : ''}">Home</a></li>
            <li><a href="/eccommerce/products.php" class="${window.location.pathname.includes('products.php') ? 'active' : ''}">Shop</a></li>
            <li><a href="/eccommerce/categories.html" class="${window.location.pathname.includes('categories.html') ? 'active' : ''}">Categories</a></li>
            <li><a href="/eccommerce/deals.html" class="${window.location.pathname.includes('deals.html') ? 'active' : ''}">Deals</a></li>
            <li><a href="/eccommerce/about.html" class="${window.location.pathname.includes('about.html') ? 'active' : ''}">About</a></li>
            <li><a href="/eccommerce/contact.html" class="${window.location.pathname.includes('contact.html') ? 'active' : ''}">Contact</a></li>
        `;
    }
    
    return `
    <!-- Top Navbar -->
    <div class="navbar-top">
        <div class="container">
            <div class="navbar-top-content">
                <div><i class="fas fa-truck"></i> Free shipping on orders over $100</div>
                <div><i class="fas fa-phone"></i> Call us: +1 (555) 123-4567</div>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/eccommerce/index.php" class="logo">LuxuryStore</a>
                
                <ul class="nav-links" id="navLinks">
                    ${navLinks}
                </ul>

                <div class="nav-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search products...">
                        <button id="searchBtn"><i class="fas fa-search"></i></button>
                    </div>
                    <a href="/eccommerce/wishlist.html" class="icon-btn">
                        <i class="fas fa-heart"></i>
                        <span class="badge wishlist-badge">0</span>
                    </a>
                    <a href="/eccommerce/cart.html" class="icon-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge cart-badge">0</span>
                    </a>
                    ${accountLink}
                    <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
                </div>
            </div>
        </div>
    </nav>
`;
}

function renderNavbar() {
    document.getElementById('navbar-container').innerHTML = getNavbarHTML();
    
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    mobileMenuBtn.addEventListener('click', () => {
        navLinks.classList.toggle('nav-links-mobile');
    });
}

renderNavbar();
