<?php
// Include the database connection and auth functions
require 'config/db.php';
require 'includes/auth.php';
require 'includes/notifications.php';

// Initialize error message variable
$error = '';

// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data and clean it
  $name = trim($_POST['name']); // Trim removes whitespace from start/end
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];

  // ----------------------
  // VALIDATE FORM DATA
  // ----------------------

  // Check if name is provided
  if (empty($name)) {
    $error = 'Name is required';
  }
  // Check if email is provided
  elseif (empty($email)) {
    $error = 'Email is required';
  }
  // Check if password is provided
  elseif (empty($password)) {
    $error = 'Password is required';
  }
  // Check if passwords match
  elseif ($password !== $confirmPassword) {
    $error = 'Passwords do not match';
  }
  // Check if email is already registered
  else {
    // First, check if email exists in database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // "s" means string parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $error = 'This email is already registered';
    } else {
      // ----------------------
      // CREATE NEW USER
      // ----------------------

      // Hash the password before storing it in the database
      // We use password_hash() - this is very important for security!
      // It turns the password into a random string that can't be reversed
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // Insert new user into database
      $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $name, $email, $hashedPassword);

      if ($stmt->execute()) {
        // Get the new user's ID
        $newUserId = $conn->insert_id;

        // Auto-login the user
            loginUser($newUserId, $name, $email);

            // Send Welcome Notification (external channels)
            sendNotification(
                ['name' => $name, 'email' => $email],
                "Welcome to LuxuryStore!",
                "Hello $name, your account has been successfully created. Welcome to LuxuryStore!"
            );
            
            // Send Telegram notification for new user registration
            // Triggered at: register.php line ~69, after new user is created
            $telegramMessage = "🆕 New User Registration\n\nName: " . htmlspecialchars($name) . "\nEmail: " . htmlspecialchars($email) . "\nTime: " . date('Y-m-d H:i:s');
            sendTelegramMessage($telegramMessage);
            
            // Create database notification
            createNotification(
                $newUserId,
                "Welcome to LuxuryStore!",
                "Hello $name, your account has been successfully created. Start shopping now!"
            );
            
            // Redirect to dashboard
            header('Location: /eccommerce/user/dashboard.php');
            exit;
      } else {
        $error = 'Something went wrong, please try again.';
      }
      $stmt->close();
    }
  }
}

$pageTitle = "Register - LuxuryStore";
include 'includes/header.php';
?>

<!-- Register Content -->
<section>
  <div class="container">
    <div class="auth-container">
      <div class="auth-header">
        <h2>Create Account</h2>
        <p style="color: var(--text-secondary)">Join us today!</p>
      </div>

      <!-- Show error message if any -->
      <?php if ($error): ?>
        <div style="background: #ef4444; color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form id="register-form" method="POST" action="register.php">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" placeholder="John Doe" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" required />
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" placeholder="••••••••" required />
        </div>
        <button
          type="submit"
          class="btn btn-primary btn-lg"
          style="width: 100%">
          Create Account
        </button>
      </form>
      <div class="auth-link">
        <p style="color: var(--text-secondary)">
          Already have an account? <a href="login.php">Login</a>
        </p>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>