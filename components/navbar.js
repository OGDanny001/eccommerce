function getNavbarHTML() {
    const isLoggedIn = currentUser !== null;
    
    let accountLink = '';
    if (isLoggedIn) {
        accountLink = `
            <a href="/php/user/dashboard.php" class="btn btn-sm btn-outline">
                <i class="fas fa-user"></i> ${currentUser.name}
            </a>
            <a href="/php/logout.php" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">
                Logout
            </a>
        `;
    } else {
        accountLink = `
            <a href="/php/login.php" class="btn btn-sm btn-outline">Login</a>
            <a href="/php/register.php" class="btn btn-sm btn-primary" style="margin-left: 0.5rem;">Sign Up</a>
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
                <a href="/php/index.php" class="logo">LuxuryStore</a>
                
                <ul class="nav-links" id="navLinks">
                    <li><a href="/php/index.php" class="${window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/') ? 'active' : ''}">Home</a></li>
                    <li><a href="/php/products.php" class="${window.location.pathname.includes('products.php') ? 'active' : ''}">Shop</a></li>
                    <li><a href="/php/categories.html" class="${window.location.pathname.includes('categories.html') ? 'active' : ''}">Categories</a></li>
                    <li><a href="/php/deals.html" class="${window.location.pathname.includes('deals.html') ? 'active' : ''}">Deals</a></li>
                    <li><a href="/php/about.html" class="${window.location.pathname.includes('about.html') ? 'active' : ''}">About</a></li>
                    <li><a href="/php/contact.html" class="${window.location.pathname.includes('contact.html') ? 'active' : ''}">Contact</a></li>
                </ul>

                <div class="nav-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search products...">
                        <button id="searchBtn"><i class="fas fa-search"></i></button>
                    </div>
                    <a href="/php/wishlist.html" class="icon-btn">
                        <i class="fas fa-heart"></i>
                        <span class="badge wishlist-badge">0</span>
                    </a>
                    <a href="/php/cart.html" class="icon-btn">
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
