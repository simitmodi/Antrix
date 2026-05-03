<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Delete remember me cookie if present
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, "/");
}

// Start a new session just for the flash message
session_start();
$_SESSION['flash_success'] = "You have been successfully logged out.";

header('Location: login.php');
exit;
?>
