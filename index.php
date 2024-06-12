<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geolocation with Leaflet</title>
    <link rel="icon" href="favicon_io/favicon.ico" type="image/x-icon">
    <style>
        #map {
            width: 600px;
            height: 400px;
        }
        .hidden {
            display: none;
        }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
        let map, marker;
        let watchId = null;
        let updatesEnabled = false; // Track if continuous updates are enabled

        function isSecureConnection() {
            return window.location.protocol === "https:" || window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1";
        }

        function initMap() {
            map = L.map('map').setView([-34.397, 150.644], 16);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            marker = L.marker([0, 0]);
            fetchLocationFromIP();
        }

        function updateMap(lat, lng) {
            const location = [lat, lng];
            const currentZoom = map.getZoom();
            map.setView(location, currentZoom);

            if (!marker._map) {
                marker.setLatLng(location).addTo(map);
            } else {
                marker.setLatLng(location);
            }
        }

        function fetchLocationFromIP() {
            fetch('https://ipapi.co/json/')
                .then(response => response.json())
                .then(data => {
                    const { latitude, longitude } = data;
                    updateMap(latitude, longitude);
                })
                .catch(error => console.error('Error fetching location from IP:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {

            function showError(message) {
                document.getElementById('location').innerText = message;
            }

            function updateLocation(position) {
                if (!updatesEnabled) return; // Do nothing if updates are disabled

                const { latitude, longitude } = position.coords;
                document.getElementById('location').textContent = `Latitude: ${latitude}, Longitude: ${longitude}`;
                updateMap(latitude, longitude);

                // Restart the watch for continuous updates
                clearAndRestartWatch();
            }

            function promptForLocationPermission() {
                const permissionPrompt = confirm("This site needs your location to show the map. Would you like to allow location access?");
                if (permissionPrompt) {
                    getLocation();
                } else {
                    showError("Location access denied. Please enable location services to use this feature.");
                }
            }

            function checkPermissionAndFetchLocation() {
                updatesEnabled = true;
                navigator.permissions.query({ name: 'geolocation' }).then(permissionStatus => {
                    if (permissionStatus.state === 'denied') {
                        showError("Location permission denied. Please enable it in your browser settings.");
                    } else if (permissionStatus.state === 'prompt' || permissionStatus.state === 'granted') {
                        getLocation();
                    }
                });
            }

            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        updateLocation,
                        error => {
                            if (error.code === error.PERMISSION_DENIED) {
                                promptForLocationPermission();
                            } else {
                                showError(`Error: ${error.message}`);
                            }
                        },
                        { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
                    );
                } else {
                    showError('Browser does not support geolocation!');
                }
            }

            function toggleContinuousUpdates() {
                if (updatesEnabled) {
                    stopContinuousUpdates();
                } else {
                    startContinuousUpdates();
                }
            }

            function startContinuousUpdates() {
                if (navigator.geolocation) {
                    updatesEnabled = true;
                    watchId = navigator.geolocation.watchPosition(
                        updateLocation,
                        error => showError(`Error: ${error.message}`),
                        { enableHighAccuracy: true, maximumAge: 0, timeout: 2000 }
                    );
                    document.getElementById('toggleUpdates').innerText = 'Stop Continuous Updates';
                } else {
                    showError('Browser does not support geolocation!');
                }
            }

            function stopContinuousUpdates() {
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                updatesEnabled = false;
                document.getElementById('toggleUpdates').innerText = 'Start Continuous Updates';
            }

            function clearAndRestartWatch() {
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                }
                if (updatesEnabled) {
                    watchId = navigator.geolocation.watchPosition(
                        updateLocation,
                        error => showError(`Error: ${error.message}`),
                        { enableHighAccuracy: true, maximumAge: 0, timeout: 2000 }
                    );
                }
            }

            initMap();

            if (!isSecureConnection()) {
                document.getElementById('fetchLocation').classList.add('hidden');
                document.getElementById('toggleUpdates').classList.add('hidden');
                document.getElementById('nonSecureMessage').classList.remove('hidden');
            }

            document.getElementById('fetchLocation').onclick = checkPermissionAndFetchLocation;
            document.getElementById('toggleUpdates').onclick = toggleContinuousUpdates;
        });
    </script>
</head>
<body>
    <div id="container">
        <div id="location"></div>
        <div id="nonSecureMessage" class="hidden">Geolocation services are not available on non-secure connections.</div>
        <button id="fetchLocation">Fetch Current Location</button>
        <button id="toggleUpdates">Start Continuous Updates</button>
        <div id="map">Map will appear here</div>
    </div>
</body>
</html>
