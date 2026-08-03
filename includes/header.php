<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle ?? 'DevTalks') ?></title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header>
    <nav>
        <a href="index.php" class="site-logo">DevTalks</a>

        <div class="nav-links">
            <a href="index.php">Home</a>

            <?php if (isset($_SESSION['user_id'])): ?>

                <span class="nav-username">
                    Hello, <?= htmlspecialchars($_SESSION['username']) ?>
                </span>

                <a href="logout.php">Logout</a>

            <?php else: ?>

                <a href="login.php">Login</a>
                <a href="register.php">Register</a>

            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="container">