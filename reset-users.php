<?php
// Reset script: logs user out and deletes all users from DB
require 'includes/auth.php';
require 'config/db.php';

// Log the user out if they are logged in
if (isLoggedIn()) {
    logoutUser();
}

// Delete all users
if ($conn) {
    $stmt = $conn->prepare("DELETE FROM users");
    $stmt->execute();
    $stmt->close();
    echo "All users deleted! <a href='index.php'>Go home</a>";
} else {
    echo "Database connection failed!";
}
?>