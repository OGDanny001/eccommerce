<?php
// ----------------------
// PROTECT THIS PAGE
// ----------------------
// Require the user to be logged in to access this page
require '../includes/auth.php';
requireLogin();

// Get current user information
$currentUser = getCurrentUser();

// Set page title
$pageTitle = "Saved Addresses - LuxuryStore";
require '../includes/header.php';
?>

    <style>
      .account-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
      }
      .account-sidebar {
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        height: fit-content;
      }
      .account-menu {
        list-style: none;
      }
      .account-menu li {
        margin-bottom: 0.5rem;
      }
      .account-menu a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.2s;
      }
      .account-menu a:hover,
      .account-menu a.active {
        background: var(--primary-color);
        color: white;
      }
      .account-content {
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: var(--shadow-sm);
      }
      .address-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
      }
      .address-card.primary {
        border-color: var(--primary-color);
      }
      .address-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
      }
      .address-actions button {
        background: none;
        border: none;
        color: var(--primary-color);
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
      }
      .address-actions button:hover {
        text-decoration: underline;
      }
      @media (max-width: 768px) {
        .account-layout {
          grid-template-columns: 1fr;
        }
        .account-sidebar {
          position: sticky;
          top: 80px;
        }
      }
    </style>

    <section>
      <div class="container">
        <h2 style="margin-bottom: 2rem">My Account</h2>
        <div class="account-layout">
          <aside class="account-sidebar">
            <h3 style="margin-bottom: 1rem">Account Menu</h3>
            <ul class="account-menu">
              <li>
                <a href="dashboard.php"
                  ><i class="fas fa-chart-bar"></i> Dashboard</a
                >
              </li>
              <li>
                <a href="orders.php"><i class="fas fa-box"></i> My Orders</a>
              </li>
              <li>
                <a href="profile.php"
                  ><i class="fas fa-user"></i> Profile Settings</a
                >
              </li>
              <li>
                <a href="addresses.php" class="active"
                  ><i class="fas fa-home"></i> Saved Addresses</a
                >
              </li>
              <li>
                <a href="../wishlist.html"
                  ><i class="fas fa-heart"></i> Wishlist</a
                >
              </li>
            </ul>
          </aside>

          <main class="account-content">
            <div
              style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
              "
            >
              <h3>Saved Addresses</h3>
              <button
                class="btn btn-primary"
                onclick="
                  showNotification('Add new address modal would open here!')
                "
              >
                <i class="fas fa-plus"></i> Add New Address
              </button>
            </div>

            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-home" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                <p style="color: var(--text-secondary);">No saved addresses yet</p>
            </div>
          </main>
        </div>
      </div>
    </section>

<?php require '../includes/footer.php'; ?>
