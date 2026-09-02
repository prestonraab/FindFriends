<?php

require_once __DIR__ . '/bootstrap.php';
app_bootstrap();
$currentUser = app_current_username();
?>

<header class="site-header">
    <h1>Find Your Friends</h1>
    <?php if ($currentUser !== null): ?>
        <div class="user-info">
            Logged in as: <?= htmlspecialchars($currentUser, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
        </div>
    <?php endif; ?>
</header>
<nav class="navbar" aria-label="Primary navigation">
    <a href="index.php">Home</a>
    <a href="index.php#map">Map</a>
    <?php if ($currentUser !== null): ?>
        <form action="logout.php" method="post" class="logout-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Logout</button>
        </form>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</nav>
