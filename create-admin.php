<?php
/**
 * ONE-TIME USE FILE TO CREATE YOUR FIRST ADMIN USER!
 * DELETE THIS FILE AFTER USING IT FOR SECURITY!
 */
require 'config/db.php';

// If the script gets here, proceed to create admin user
$name = "Store Admin";
$email = "admin@yourstore.com"; // CHANGE THIS EMAIL!
$password = "admin123"; // CHANGE THIS PASSWORD!

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        echo "<h1>Admin Created!</h1>";
        echo "<p>You can now login as admin with: <strong>{$email}</strong> and password <strong>{$password}</strong></p>";
        echo "<p><strong>DELETE THIS FILE NOW</strong> for security!</p>";
    } else {
        echo "<h1>Error!</h1>";
        echo "<p>Email might already be used.</p>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>