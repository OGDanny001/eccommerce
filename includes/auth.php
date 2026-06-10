<?php
// This is the authentication helper file.
// It handles session management and login checks.

// Start a session - this lets us store user data across pages
// Sessions let us remember a user even when they navigate to different pages
session_start();

// Include database connection to access users table
require_once __DIR__ . '/../config/db.php';

/**
 * Check if a user is currently logged in
 * Returns true if user is logged in, false otherwise
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Require a user to be logged in to access the page
 * If not logged in, redirect to login page
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: /eccommerce/login.php');
        exit; // Always exit after header redirect to stop script execution
    }
    // Also check if user still exists in DB
    $user = getCurrentUser();
    if (!$user) {
        logoutUser();
        header('Location: /eccommerce/login.php');
        exit;
    }
}

/**
 * Get the current logged in user's information
 * Returns an array with user data or null if not logged in
 */
function getCurrentUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    global $conn;
    $userId = $_SESSION['user_id'];

    // Prepare SQL to get user from database using their ID
    $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId); // "i" means integer parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user;
}

/**
 * Log the user in
 * Stores user information in session
 */
function loginUser($userId, $name, $email)
{
    // Store user info in session to remember them
    $_SESSION['user_id'] = $userId;
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
}

/**
 * Log the user out
 * Destroys the session
 */
function logoutUser()
{
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
}

/**
 * Check if current user is admin
 */
function isAdmin()
{
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

/**
 * Require admin role, else redirect
 */
function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: /eccommerce/index.php');
        exit;
    }
}
