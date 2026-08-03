<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($pageTitle ?? 'DevTalks') ?></title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="site-header">
    <nav class="site-nav">

        <a href="index.php" class="site-logo">
            DevTalks
        </a>

        <button
            type="button"
            class="nav-toggle"
            id="navToggle"
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="navLinks"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-links" id="navLinks">

            <a href="index.php">Home</a>

            <?php if (isset($_SESSION['user_id'])): ?>

                <a href="create-post.php">Write</a>

                <span class="nav-username">
                    Hello, <?= htmlspecialchars($_SESSION['username']) ?>
                </span>

                <a href="logout.php" class="nav-button">
                    Logout
                </a>

            <?php else: ?>

                <a href="login.php">Login</a>

                <a href="register.php" class="nav-button">
                    Register
                </a>

            <?php endif; ?>

        </div>
    </nav>
</header>

<main class="container">