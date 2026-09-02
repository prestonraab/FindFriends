<?php

require_once __DIR__ . '/bootstrap.php';
app_bootstrap();
$currentUser = app_current_username();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find and share your location with friends.">
    <title>Find Your Friends</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css" integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        .site-header { background-color: #007BFF; color: white; padding: 10px 20px; text-align: center; }
        .navbar { display: flex; justify-content: center; align-items: center; gap: 20px; margin-top: 10px; background-color: #6c757d; padding: 10px 0; }
        .navbar a, .navbar button { color: white; text-decoration: none; background: none; border: 0; cursor: pointer; font: inherit; }
        .logout-form { margin: 0; }
        .user-info { margin-top: 10px; }
        #map { width: min(600px, 100%); height: 400px; }
        #container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .hidden { display: none; }
        .login-btn { position: absolute; top: 10px; right: 10px; padding: 10px 20px; background-color: #007BFF; color: white; border-radius: 5px; text-decoration: none; }
        .site-footer { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <?php if ($currentUser !== null): ?>
        <div class="login-btn">Logged in</div>
    <?php else: ?>
        <a href="login.php" class="login-btn">Login</a>
    <?php endif; ?>

    <main id="container">
        <div id="location" role="status" aria-live="polite"></div>
        <div id="nonSecureMessage" class="hidden">Geolocation services are not available on non-secure connections.</div>
        <button id="fetchLocation" type="button">Fetch Current Location</button>
        <button id="toggleUpdates" type="button">Start Continuous Updates</button>
        <div id="map">Map will appear here</div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <?php include __DIR__ . '/map.php'; ?>
</body>
</html>
