<?php

require_once __DIR__ . '/bootstrap.php';
app_bootstrap();

$csrfToken = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || !app_verify_csrf_token($csrfToken)) {
    http_response_code(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? 403 : 405);
    header('Allow: POST');
    exit('Invalid logout request.');
}

app_destroy_session();
header('Location: index.php', true, 303);
exit;
