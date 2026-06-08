const navbarHTML = `
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
                <a href="/index.html" class="logo">LuxuryStore</a>
                
                <ul class="nav-links" id="navLinks">
                    <li><a href="/index.html" class="${window.location.pathname.includes('index.html') || window.location.pathname.endsWith('/') ? 'active' : ''}">Home</a></li>
                    <li><a href="/products.html" class="${window.location.pathname.includes('products.html') ? 'active' : ''}">Shop</a></li>
                    <li><a href="/categories.html" class="${window.location.pathname.includes('categories.html') ? 'active' : ''}">Categories</a></li>
                    <li><a href="/deals.html" class="${window.location.pathname.includes('deals.html') ? 'active' : ''}">Deals</a></li>
                    <li><a href="/about.html" class="${window.location.pathname.includes('about.html') ? 'active' : ''}">About</a></li>
                    <li><a href="/contact.html" class="${window.location.pathname.includes('contact.html') ? 'active' : ''}">Contact</a></li>
                </ul>

                <div class="nav-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search products...">
                        <button id="searchBtn"><i class="fas fa-search"></i></button>
                    </div>
                    <a href="/wishlist.html" class="icon-btn">
                        <i class="fas fa-heart"></i>
                        <span class="badge wishlist-badge">0</span>
                    </a>
                    <a href="/cart.html" class="icon-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge cart-badge">0</span>
                    </a>
                    <a href="/user/dashboard.html" class="btn btn-sm btn-outline">My Account</a>
                    <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
                </div>
            </div>
        </div>
    </nav>
`;

function renderNavbar() {
    document.getElementById('navbar-container').innerHTML = navbarHTML;
    
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    mobileMenuBtn.addEventListener('click', () => {
        navLinks.classList.toggle('nav-links-mobile');
    });
}

renderNavbar();
