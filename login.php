<?php

require_once __DIR__ . '/bootstrap.php';
app_bootstrap();

$databaseAvailable = true;
try {
    require_once __DIR__ . '/functions.php';
} catch (Throwable $exception) {
    $databaseAvailable = false;
    error_log('Database configuration unavailable: ' . $exception->getMessage());
}

$message = '';

if (app_current_username() !== null) {
    header('Location: index.php', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $csrfToken = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null;

    if (!$databaseAvailable) {
        $message = 'The service is temporarily unavailable. Please try again later.';
    } elseif (!app_verify_csrf_token($csrfToken)) {
        $message = 'Your session expired. Please try again.';
    } elseif (!preg_match('/\A[A-Za-z0-9_.-]{3,64}\z/', $username)) {
        $message = 'Use a username containing 3–64 letters, numbers, dots, dashes, or underscores.';
    } elseif ($action === 'Register' && strlen($password) < 12) {
        $message = 'Registration passwords must be at least 12 characters.';
    } elseif (!in_array($action, ['Login', 'Register'], true) || $password === '') {
        $message = 'Please provide a username and password.';
    } else {
        try {
            $result = $action === 'Register'
                ? registerUser($username, $password)
                : loginUser($username, $password);

            if ($result['success'] === true) {
                app_login($result['username']);
                header('Location: index.php', true, 303);
                exit;
            }

            $message = $result['message'];
        } catch (Throwable $exception) {
            error_log('Authentication request failed: ' . $exception->getMessage());
            $message = 'The service is temporarily unavailable. Please try again later.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f0f0; }
        .login-container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; width: min(360px, calc(100% - 2rem)); }
        input[type="text"], input[type="password"] { box-sizing: border-box; width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { background-color: #007BFF; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 0 4px; }
        .error { color: #a00; }
        .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
    </style>
</head>
<body>
    <main class="login-container">
        <h1>Login / Register</h1>
        <form action="login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <label>
                <span class="visually-hidden">Username</span>
                <input type="text" name="username" placeholder="Username" autocomplete="username" maxlength="64" required>
            </label>
            <label>
                <span class="visually-hidden">Password</span>
                <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
            </label>
            <button type="submit" name="action" value="Login">Login</button>
            <button type="submit" name="action" value="Register">Register</button>
        </form>
        <?php if ($message !== ''): ?>
            <p class="error" role="alert"><?= htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
