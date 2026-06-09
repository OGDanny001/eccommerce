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
$pageTitle = "My Dashboard - LuxuryStore";
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
      .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      .stat-card {
        padding: 1.5rem;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--primary-color), #7c3aed);
        color: white;
      }
      .stat-card h4 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
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
                <a href="dashboard.php" class="active"
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
            <h3 style="margin-bottom: 2rem">Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?>!</h3>
            <div class="stats-grid">
              <div class="stat-card">
                <h4>0</h4>
                <p>Total Orders</p>
              </div>
              <div class="stat-card">
                <h4>$0</h4>
                <p>Total Spent</p>
              </div>
              <div class="stat-card">
                <h4 id="wishlist-count">0</h4>
                <p>Wishlist Items</p>
              </div>
              <div class="stat-card">
                <h4>0</h4>
                <p>Saved Addresses</p>
              </div>
            </div>

            <h4 style="margin-bottom: 1.5rem">Recent Orders</h4>
            <div style="overflow-x: auto">
              <table style="width: 100%; border-collapse: collapse">
                <thead>
                  <tr style="border-bottom: 1px solid var(--border-color)">
                    <th
                      style="
                        text-align: left;
                        padding: 1rem;
                        color: var(--text-secondary);
                      "
                    >
                      Order ID
                    </th>
                    <th
                      style="
                        text-align: left;
                        padding: 1rem;
                        color: var(--text-secondary);
                      "
                    >
                      Date
                    </th>
                    <th
                      style="
                        text-align: left;
                        padding: 1rem;
                        color: var(--text-secondary);
                      "
                    >
                      Total
                    </th>
                    <th
                      style="
                        text-align: left;
                        padding: 1rem;
                        color: var(--text-secondary);
                      "
                    >
                      Status
                    </th>
                    <th
                      style="
                        text-align: left;
                        padding: 1rem;
                        color: var(--text-secondary);
                      "
                    >
                      Action
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr style="border-bottom: 1px solid var(--border-color)">
                    <td style="padding: 1rem" colspan="5">No orders yet</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </main>
        </div>
      </div>
    </section>

<?php require '../includes/footer.php'; ?>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const wishlistCount = document.getElementById("wishlist-count");
        if (wishlistCount) {
          wishlistCount.textContent = appState.wishlist.length;
        }
      });
    </script>
