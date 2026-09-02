<script>
    let map;
    let marker;
    let watchId = null;
    let updatesEnabled = false;
    let centered = true;

    function isSecureConnection() {
        return window.location.protocol === 'https:'
            || window.location.hostname === 'localhost'
            || window.location.hostname === '127.0.0.1';
    }

    function showError(message) {
        document.getElementById('location').textContent = message;
    }

    function validCoordinates(latitude, longitude) {
        return Number.isFinite(latitude) && Number.isFinite(longitude)
            && latitude >= -90 && latitude <= 90
            && longitude >= -180 && longitude <= 180;
    }

    function updateMap(latitude, longitude) {
        if (!validCoordinates(latitude, longitude)) {
            showError('The location service returned invalid coordinates.');
            return;
        }

        const location = [latitude, longitude];
        if (centered) {
            map.setView(location, Math.max(map.getZoom(), 12));
        }

        marker.setLatLng(location).addTo(map);
    }

    async function permissionAllowsLocation() {
        if (!navigator.geolocation) {
            showError('This browser does not support geolocation.');
            return false;
        }

        if (!navigator.permissions || !navigator.permissions.query) {
            return true;
        }

        try {
            const permission = await navigator.permissions.query({ name: 'geolocation' });
            if (permission.state === 'denied') {
                showError('Location permission is denied. Enable it in your browser settings.');
                return false;
            }
        } catch (error) {
            // The geolocation call below remains the source of truth when the
            // Permissions API is unavailable or rejects the query.
        }

        return true;
    }

    function handleLocation(position) {
        const { latitude, longitude } = position.coords;
        document.getElementById('location').textContent = `Latitude: ${latitude}, Longitude: ${longitude}`;
        updateMap(latitude, longitude);
    }

    function handleLocationError(error) {
        if (error.code === error.PERMISSION_DENIED) {
            showError('Location access was denied. Enable it in your browser settings.');
        } else {
            showError(`Unable to fetch location: ${error.message}`);
        }
    }

    function getLocation() {
        navigator.geolocation.getCurrentPosition(handleLocation, handleLocationError, {
            enableHighAccuracy: true,
            maximumAge: 30000,
            timeout: 10000
        });
    }

    async function checkPermissionAndFetchLocation() {
        if (await permissionAllowsLocation()) {
            getLocation();
        }
    }

    async function startContinuousUpdates() {
        if (!(await permissionAllowsLocation())) {
            return;
        }

        if (watchId !== null) {
            return;
        }

        centered = true;
        updatesEnabled = true;
        watchId = navigator.geolocation.watchPosition(handleLocation, handleLocationError, {
            enableHighAccuracy: true,
            maximumAge: 30000,
            timeout: 10000
        });
        document.getElementById('toggleUpdates').textContent = 'Stop Continuous Updates';
    }

    function stopContinuousUpdates() {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        updatesEnabled = false;
        document.getElementById('toggleUpdates').textContent = 'Start Continuous Updates';
    }

    function toggleContinuousUpdates() {
        if (updatesEnabled) {
            stopContinuousUpdates();
        } else {
            startContinuousUpdates();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        marker = L.marker([0, 0]);
        map.on('dragstart', () => { centered = false; });

        if (!isSecureConnection()) {
            document.getElementById('fetchLocation').classList.add('hidden');
            document.getElementById('toggleUpdates').classList.add('hidden');
            document.getElementById('nonSecureMessage').classList.remove('hidden');
        }

        document.getElementById('fetchLocation').addEventListener('click', checkPermissionAndFetchLocation);
        document.getElementById('toggleUpdates').addEventListener('click', toggleContinuousUpdates);
    });
</script>
