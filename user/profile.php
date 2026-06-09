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
$pageTitle = "Profile Settings - LuxuryStore";
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
      .form-group {
        margin-bottom: 1.5rem;
      }
      .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
      }
      .form-group input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 1rem;
      }
      .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
      }
      @media (max-width: 768px) {
        .account-layout {
          grid-template-columns: 1fr;
        }
        .account-sidebar {
          position: sticky;
          top: 80px;
        }
        .form-row {
          grid-template-columns: 1fr;
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
                <a href="profile.php" class="active"
                  ><i class="fas fa-user"></i> Profile Settings</a
                >
              </li>
              <li>
                <a href="addresses.php"
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
            <h3 style="margin-bottom: 2rem">Profile Settings</h3>

            <form
              id="profile-form"
              onsubmit="
                event.preventDefault();
                showNotification('Profile updated successfully!');
              "
            >
              <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" required />
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input
                  type="email"
                  id="email"
                  value="<?php echo htmlspecialchars($currentUser['email']); ?>"
                  required
                />
              </div>
              <button type="submit" class="btn btn-primary">
                Save Changes
              </button>
            </form>

            <hr
              style="
                border: none;
                border-top: 1px solid var(--border-color);
                margin: 2.5rem 0;
              "
            />

            <h4 style="margin-bottom: 1.5rem">Change Password</h4>
            <form
              id="password-form"
              onsubmit="
                event.preventDefault();
                showNotification('Password changed successfully!');
              "
            >
              <div class="form-group">
                <label for="current-password">Current Password</label>
                <input
                  type="password"
                  id="current-password"
                  placeholder="••••••••"
                  required
                />
              </div>
              <div class="form-group">
                <label for="new-password">New Password</label>
                <input
                  type="password"
                  id="new-password"
                  placeholder="••••••••"
                  required
                  minlength="8"
                />
              </div>
              <div class="form-group">
                <label for="confirm-password">Confirm New Password</label>
                <input
                  type="password"
                  id="confirm-password"
                  placeholder="••••••••"
                  required
                  minlength="8"
                />
              </div>
              <button type="submit" class="btn btn-primary">
                Update Password
              </button>
            </form>
          </main>
        </div>
      </div>
    </section>

<?php require '../includes/footer.php'; ?>
