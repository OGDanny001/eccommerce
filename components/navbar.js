function getNavbarHTML() {
    const isLoggedIn = currentUser !== null;
    const isAdmin = isLoggedIn && currentUser.role === 'admin';
    
    let accountLink = '';
    let navLinks = '';
    let notificationBell = '';

    if (isAdmin) {
        // ADMIN NAVBAR
        accountLink = `
            <a href="/eccommerce/admin/index.php" class="btn btn-sm btn-outline" style="display:flex;align-items:center;gap:0.5rem;">
                ${currentUser.profile_pic 
                    ? `<img src="${currentUser.profile_pic}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">` 
                    : `<i class="fas fa-user-shield"></i>`}
                Admin: ${currentUser.name}
            </a>
            <a href="/eccommerce/logout.php" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">
                Logout
            </a>
        `;
        navLinks = `
            <li><a href="/eccommerce/admin/index.php" class="${window.location.pathname.includes('/admin/index.php') ? 'active' : ''}">Dashboard</a></li>
            <li><a href="/eccommerce/admin/orders.php" class="${window.location.pathname.includes('/admin/orders.php') ? 'active' : ''}">Orders</a></li>
            <li><a href="/eccommerce/admin/products.php" class="${window.location.pathname.includes('/admin/products.php') ? 'active' : ''}">Products</a></li>
            <li><a href="/eccommerce/admin/users.php" class="${window.location.pathname.includes('/admin/users.php') ? 'active' : ''}">Users</a></li>
            <li style="margin-left: 1rem;"><a href="/eccommerce/index.php" style="color: var(--primary-color); font-weight: 700;"><i class="fas fa-eye"></i> View Shop</a></li>
        `;
    } else if (isLoggedIn) {
        // CUSTOMER NAVBAR
        const unreadCount = notificationData ? notificationData.unread_count : 0;
        const recentNotifications = notificationData ? notificationData.recent_notifications : [];
        
        let notificationsDropdown = '';
        if (recentNotifications.length > 0) {
            notificationsDropdown = recentNotifications.map(n => `
                <a href="javascript:void(0)" class="notification-item ${n.is_read ? 'read' : 'unread'}" data-id="${n.id}" onclick="markNotificationRead(${n.id})">
                    <div style="font-weight: 600;">${n.title}</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">${n.message}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">${new Date(n.created_at).toLocaleDateString()}</div>
                </a>
            `).join('');
        } else {
            notificationsDropdown = '<div style="padding: 1rem; text-align: center; color: var(--text-secondary);">No notifications yet</div>';
        }
        
        notificationBell = `
            <div class="notification-dropdown" style="position: relative;">
                <button class="icon-btn notification-btn" id="notificationBtn" style="position: relative;">
                    <i class="fas fa-bell"></i>
                    ${unreadCount > 0 ? `<span class="badge notification-badge" style="background:#ef4444;">${unreadCount}</span>` : ''}
                </button>
                <div class="notification-dropdown-menu" id="notificationDropdown" style="display: none; position: absolute; top: 100%; right: 0; width: 350px; background: var(--bg-primary); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); z-index: 1000; margin-top: 0.5rem;">
                    <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <strong>Notifications</strong>
                        <button onclick="markAllNotificationsRead()" style="color: var(--primary-color); background:none; border:none; cursor:pointer; font-size: 0.875rem;">Mark all as read</button>
                    </div>
                    <div style="max-height: 400px; overflow-y: auto;">
                        ${notificationsDropdown}
                    </div>
                    <div style="padding: 1rem; border-top: 1px solid var(--border-color); text-align: center;">
                        <a href="/eccommerce/user/dashboard.php#notifications" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">View all notifications</a>
                    </div>
                </div>
            </div>
        `;
        
        accountLink = `
            <a href="/eccommerce/user/dashboard.php" class="btn btn-sm btn-outline" style="display:flex;align-items:center;gap:0.5rem;">
                ${currentUser.profile_pic 
                    ? `<img src="${currentUser.profile_pic}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">` 
                    : `<i class="fas fa-user"></i>`}
                Welcome, ${currentUser.name}
            </a>
            <a href="/eccommerce/logout.php" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">
                Logout
            </a>
        `;
        navLinks = `
            <li><a href="/eccommerce/index.php" class="${window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/') ? 'active' : ''}">Home</a></li>
            <li><a href="/eccommerce/products.php" class="${window.location.pathname.includes('products.php') ? 'active' : ''}">Shop</a></li>
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
                        <input type="text" id="searchInput" placeholder="Search products..." />
                        <button id="searchBtn"><i class="fas fa-search"></i></button>
                    </div>
                    ${notificationBell}
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
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('nav-links-mobile');
        });
    }
    
    // Notification dropdown toggle
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            notificationDropdown.style.display = 'none';
        });
    }
}

// Notification functions
async function markNotificationRead(id) {
    try {
        await fetch('/eccommerce/api/notifications.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=mark_read&notification_id=' + id
        });
        // Refresh page to update UI
        window.location.reload();
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

async function markAllNotificationsRead() {
    try {
        await fetch('/eccommerce/api/notifications.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=mark_all_read'
        });
        // Refresh page to update UI
        window.location.reload();
    } catch (error) {
        console.error('Error marking notifications as read:', error);
    }
}

renderNavbar();
