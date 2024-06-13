<script>
        let map, marker;
        let watchId = null;
        let updatesEnabled = false; // Track if continuous updates are enabled
        let centered = true;
        let permissionGranted = false;

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
            if(centered){
                map.setView(location, currentZoom);
            }

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

                const { latitude, longitude } = position.coords;
                document.getElementById('location').textContent = `Latitude: ${latitude}, Longitude: ${longitude}`;
                updateMap(latitude, longitude);

                if (!updatesEnabled) return; // Do nothing if updates are disabled

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

            function locationPermitted() {
                if (permissionGranted){
                    return true;
                }
                navigator.permissions.query({ name: 'geolocation' }).then(permissionStatus => {
                    if (permissionStatus.state === 'denied') {
                        return false;
                    } else if (permissionStatus.state === 'prompt' || permissionStatus.state === 'granted') {
                        permissionGranted = true;
                        return true;
                    }
                });
            }

            function checkPermissionAndFetchLocation() {
                if (locationPermitted()) {
                    getLocation();
                }
                else {
                    showError("Location permission denied. Please enable it in your browser settings.");
                }
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
                if(!locationPermitted()) {
                    showError("Location permission denied. Please enable it in your browser settings.");
                }
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
                    centered = true;
                }
                updatesEnabled = false;
                document.getElementById('toggleUpdates').innerText = 'Start Continuous Updates';
            }

            function clearAndRestartWatch() {
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                }
                if (updatesEnabled) {
                    centered = false;
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