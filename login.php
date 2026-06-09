<?php
// Include database and authentication helper files
require 'config/db.php';
require 'includes/auth.php';

// Initialize error and message variables
$error = '';
$message = isset($_GET['msg']) ? $_GET['msg'] : '';

// Check if user is already logged in - if yes, redirect to dashboard
if (isLoggedIn()) {
  header('Location: /user/dashboard.php');
  exit;
}

// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data and clean it
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  // ----------------------
  // VALIDATE FORM DATA
  // ----------------------

  if (empty($email)) {
    $error = 'Email is required';
  } elseif (empty($password)) {
    $error = 'Password is required';
  } else {
    // ----------------------
    // LOOK UP USER IN DATABASE
    // ----------------------

    // Prepare SQL to get user by email
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // "s" means string parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      // User found, now check password
      $user = $result->fetch_assoc();

      // Verify password - password_verify() checks if entered password matches hashed password
      // We never store plain text passwords!
      if (password_verify($password, $user['password'])) {
        // Password is correct - log the user in!
        loginUser($user['id'], $user['name'], $user['email']);

        // Redirect to user dashboard
        header('Location: /user/dashboard.php');
        exit;
      } else {
        $error = 'Invalid email or password';
      }
    } else {
      $error = 'Invalid email or password';
    }

    $stmt->close();
  }
}

$pageTitle = "Login - LuxuryStore";
include 'includes/header.php';
?>

<!-- Login Content -->
<section>
  <div class="container">
    <div class="auth-container">
      <div class="auth-header">
        <h2>Welcome Back</h2>
        <p style="color: var(--text-secondary);">Login to your account</p>
      </div>

      <!-- Show message if any -->
      <?php if ($message): ?>
        <div style="background: #3b82f6; color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <!-- Show error message if any -->
      <?php if ($error): ?>
        <div style="background: #ef4444; color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form id="login-form" method="POST" action="login.php">
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" required />
        </div>
        <div class="form-footer">
          <label style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="checkbox" /> Remember me
          </label>
          <a
            href="forgot-password.html"
            style="color: var(--primary-color); text-decoration: none;">Forgot password?</a>
        </div>
        <button
          type="submit"
          class="btn btn-primary btn-lg"
          style="width: 100%;">
          Login
        </button>
      </form>
      <div class="auth-link">
        <p style="color: var(--text-secondary);">
          Don't have an account? <a href="register.php">Sign up</a>
        </p>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>