<?php
// Set secure session configurations
ini_set('session.cookie_httponly', 1); // Prevent JavaScript from accessing session cookies
ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Use secure cookies if using HTTPS
ini_set('session.use_strict_mode', 1); // Use strict mode for sessions to prevent session fixation
ini_set('session.use_trans_sid', 0); // Disable transparent session ID in URLs
ini_set('session.cookie_samesite', 'Strict'); // SameSite cookie attribute to prevent CSRF

// Set custom session name
session_name('SMARTECH_SESSION');

// Start the session
session_start();

// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) { // Regenerate every 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Bind session to user's IP and User-Agent to prevent hijacking
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['ip_address'])) {
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
} elseif ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

