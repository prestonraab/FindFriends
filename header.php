<!-- header.php -->
<?php
session_start();
$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Function to check if we're on a local environment
function is_local() {
    $whitelist = array('127.0.0.1', 'localhost');
    if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
        return true;
    }
    return false;
}

// Enforce HTTPS only on production
if (!is_local()) {
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }

    // Set security headers

    redirect_to_https();
    custom_header();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css" integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls" crossorigin="anonymous">
    <style>
        .header {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        .navbar {
            display: flex;
            justify-content: center;
            margin-top: 10px;
            background-color: #6c757d; /* New background color for better contrast */
            padding: 10px 0; /* Optional: add some padding to make it look better */
        }

        .navbar a {
            margin: 0 10px;
            color: white;
            text-decoration: none;
        }

        .user-info {
            color: white;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Find Your Friends</h1>
        <?php if ($current_user): ?>
            <div class="user-info">Logged in as: <?php echo $current_user; ?></div>
        <?php endif; ?>
    </div>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="map.php">Map</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
