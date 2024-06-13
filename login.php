<?php
require_once 'functions.php';

$message = '';

// Start or resume the session
session_start();

// Check if user is already logged in
if (isset($_SESSION['token'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $action = $_POST['action'];

    if ($action === 'Register') {
        $tokenOrMessage = registerUser($username, $password);
        if (is_string($tokenOrMessage)) {
            // Automatically log in after registration
            $_SESSION['token'] = $tokenOrMessage;
            header('Location: index.php');
            exit();
        } else {
            $message = $tokenOrMessage;
        }
    } elseif ($action === 'Login') {
        $tokenOrMessage = loginUser($username, $password);
        if (is_string($tokenOrMessage)) {
            $_SESSION['token'] = $tokenOrMessage;
            header('Location: index.php');
            exit();
        } else {
            $message = $tokenOrMessage;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login / Register</h2>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" name="action" value="Login">
            <input type="submit" name="action" value="Register">
        </form>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
