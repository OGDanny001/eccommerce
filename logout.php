<?php
// Include the authentication helper to use logout function
require 'includes/auth.php';

// Log the user out
logoutUser();

// Redirect to homepage
header('Location: /eccommerce/index.php');
exit;
