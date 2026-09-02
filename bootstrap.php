<?php

declare(strict_types=1);

const APP_SESSION_IDLE_TIMEOUT = 1800;

function app_is_local(): bool
{
    if (PHP_SAPI === 'cli') {
        return true;
    }

    return in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
}

function app_is_https(): bool
{
    if (!in_array(strtolower((string) ($_SERVER['HTTPS'] ?? '')), ['', 'off', '0'], true)) {
        return true;
    }

    return getenv('APP_TRUST_PROXY') === '1'
        && strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

function app_canonical_host(): string
{
    $host = getenv('APP_CANONICAL_HOST') ?: 'whisperconnection.com';

    if (!preg_match('/\A[A-Za-z0-9.-]+(?::\d+)?\z/', $host)) {
        throw new RuntimeException('APP_CANONICAL_HOST is invalid.');
    }

    return $host;
}

function app_enforce_https(): void
{
    if (!app_is_local() && !app_is_https()) {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        if ($requestUri === '' || $requestUri[0] !== '/') {
            $requestUri = '/';
        }
        header('Location: https://' . app_canonical_host() . $requestUri, true, 308);
        exit;
    }
}

function app_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(self)');
    header("Content-Security-Policy: default-src 'self'; object-src 'none'; script-src 'self' https://unpkg.com 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data: https://tile.openstreetmap.org; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'");

    if (!app_is_local()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function app_start_session(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !app_is_local(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function app_destroy_session(): void
{
    app_start_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'] ?: '/',
            'domain' => $params['domain'] ?? '',
            'secure' => (bool) ($params['secure'] ?? false),
            'httponly' => (bool) ($params['httponly'] ?? true),
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}

function app_current_username(): ?string
{
    app_start_session();

    if (empty($_SESSION['username'])) {
        return null;
    }

    $lastActivity = (int) ($_SESSION['last_activity'] ?? 0);
    if ($lastActivity === 0 || time() - $lastActivity > APP_SESSION_IDLE_TIMEOUT) {
        app_destroy_session();
        return null;
    }

    $_SESSION['last_activity'] = time();
    return (string) $_SESSION['username'];
}

function app_login(string $username): void
{
    app_start_session();
    session_regenerate_id(true);
    $_SESSION['username'] = $username;
    $_SESSION['last_activity'] = time();
}

function app_csrf_token(): string
{
    app_start_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function app_verify_csrf_token(?string $token): bool
{
    app_start_session();

    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function app_bootstrap(): void
{
    static $bootstrapped = false;
    if ($bootstrapped) {
        return;
    }

    app_enforce_https();
    app_security_headers();
    app_start_session();
    $bootstrapped = true;
}
