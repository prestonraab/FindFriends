<?php
// Start the session
session_start();

// Unset all of the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Optional: Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], 
        $params["domain"], 
        true, // Ensure cookie is only sent over HTTPS
        $params["httponly"]
    );
}

// Redirect to a secure page
header("Location: https://whisperconnection.com/index.php");
exit;
?>
