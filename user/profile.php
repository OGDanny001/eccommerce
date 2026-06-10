<?php
require '../includes/auth.php';
requireLogin();
$currentUser = getCurrentUser();
$pageTitle = "Profile Settings - LuxuryStore";

// Handle profile updates
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    // Handle profile picture upload
    $profilePic = $currentUser['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileName = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
            $profilePic = '/eccommerce/uploads/' . $fileName;
        }
    }
    
    // Update user in DB
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_pic = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $profilePic, $currentUser['id']);
    
    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        $currentUser = getCurrentUser();
        $message = 'Profile updated successfully!';
    } else {
        $message = 'Error updating profile: ' . $conn->error;
        $messageType = 'error';
    }
}

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
      .account-menu { list-style: none; }
      .account-menu li { margin-bottom: 0.5rem; }
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
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
      }
      .profile-pic-container {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--text-muted);
      }
      .form-group { margin-bottom: 1.75rem; }
      .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-primary);
      }
      .form-group input,
      .form-group input[type="file"] {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 1rem;
        background: var(--bg-primary);
      }
      .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
      .message {
        padding: 1rem 1.5rem;
        border-radius: var(--radius-md);
        margin-bottom: 2rem;
      }
      .message-success { background: #d1fae5; color: #065f46; }
      .message-error { background: #fee2e2; color: #991b1b; }
      @media (max-width: 768px) {
        .account-layout { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
      }
    </style>

    <section style="padding: 3rem 0;">
      <div class="container">
        <h2 style="margin-bottom: 2.5rem; font-size: 2rem;">My Account</h2>
        <div class="account-layout">
          <aside class="account-sidebar">
            <h3 style="margin-bottom: 1.25rem; font-size: 1.25rem;">Account Menu</h3>
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
            <h3 style="margin-bottom: 2.5rem; font-size: 1.5rem;">Profile Settings</h3>
            
            <?php if ($message): ?>
              <div class="message message-<?php echo $messageType; ?>">
                <?php echo $message; ?>
              </div>
            <?php endif; ?>

            <form id="profile-form" method="POST" enctype="multipart/form-data">
              <div class="profile-pic-container">
                <?php if ($currentUser['profile_pic']): ?>
                    <img src="<?php echo htmlspecialchars($currentUser['profile_pic']); ?>" class="profile-pic" alt="Profile">
                <?php else: ?>
                    <div class="profile-pic">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h4 style="margin-bottom: 0.5rem; font-size: 1.125rem;">Profile Picture</h4>
                    <p style="color: var(--text-secondary); margin-bottom: 0.75rem; font-size: 0.875rem;">
                        Update your profile picture (JPG, PNG, GIF)
                    </p>
                    <input type="file" name="profile_pic" accept="image/*" id="profile-pic-input" style="border: none; padding: 0;">
                </div>
              </div>

              <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" required />
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required />
              </div>
              
              <button type="submit" class="btn btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">
                Save Changes
              </button>
            </form>
          </main>
        </div>
      </div>
    </section>

<?php require '../includes/footer.php'; ?>