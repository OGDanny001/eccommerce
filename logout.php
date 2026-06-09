<?php
// Include the authentication helper to use logout function
require 'includes/auth.php';

// Log the user out
logoutUser();

// Redirect to homepage
header('Location: /php/index.php');
exit;
