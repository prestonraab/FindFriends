<?php
// Function to check if we are using HTTPS
function is_https() {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    }
    if (!empty($_SERVER['HTTP_CF_VISITOR'])) {
        $cfVisitor = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
        if ($cfVisitor && isset($cfVisitor['scheme']) && $cfVisitor['scheme'] === 'https') {
            return true;
        }
    }
    return false;
}

// Redirect to HTTPS if not already using it
if (!is_https()) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple HTTPS Test</title>
</head>
<body>
    <h1>Hello, World!</h1>
    <p>This is a simple HTTPS test page.</p>

    <?php
    if (is_https()) {
        echo "<p>You are using a secure connection (HTTPS).</p>";
    } else {
        echo "<p>You are not using a secure connection (HTTP).</p>";
    }
    ?>
</body>
</html>
