<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geolocation with Leaflet</title>
    <?php include 'header.php'; ?>
    <link rel="icon" href="https://whisperconnection.com/favicon.ico" type="image/x-icon">
    <style>
        #map {
            width: 600px;
            height: 400px;
        }
        .hidden {
            display: none;
        }
        .login-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
</head>
<body>
    <?php if (isset($_SESSION['token'])): ?>
        <a href="logout.php" class="login-btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="login-btn">Login</a>
    <?php endif; ?>
    <div id="container">
        <div id="location"></div>
        <div id="nonSecureMessage" class="hidden">Geolocation services are not available on non-secure connections.</div>
        <button id="fetchLocation">Fetch Current Location</button>
        <button id="toggleUpdates">Start Continuous Updates</button>
        <div id="map">Map will appear here</div>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <?php include 'map.php'; ?>
</body>
</html>
