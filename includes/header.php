<?php
// Include auth functions to check login status
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/notifications.php';

// Get current user if logged in
$currentUser = isLoggedIn() ? getCurrentUser() : null;

// Get notification data if user is logged in
$notificationData = null;
if ($currentUser) {
    $notificationData = [
        'unread_count' => getUnreadNotificationCount($currentUser['id']),
        'recent_notifications' => getUserNotifications($currentUser['id'], 5)
    ];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - LuxuryStore' : 'LuxuryStore - Premium Shopping'; ?></title>
    <link rel="stylesheet" href="/eccommerce/assets/css/styles.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght=300;400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <script>
        // Pass user information to JavaScript
        const currentUser = <?php echo $currentUser ? json_encode($currentUser) : 'null'; ?>;
        const notificationData = <?php echo $notificationData ? json_encode($notificationData) : 'null'; ?>;
    </script>
  </head>
  <body>
    <div id="navbar-container"></div>
