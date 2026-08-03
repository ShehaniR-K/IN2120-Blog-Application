<?php

require_once 'config/database.php';

$pageTitle = 'Register - DevTalks';

$errors = [];
$success = '';

$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must contain at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $checkStatement = $pdo->prepare(
            'SELECT id FROM `user`
             WHERE email = :email OR username = :username
             LIMIT 1'
        );

        $checkStatement->execute([
            'email' => $email,
            'username' => $username
        ]);

        if ($checkStatement->fetch()) {
            $errors[] = 'The username or email is already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStatement = $pdo->prepare(
                'INSERT INTO `user` (username, email, password, role)
                 VALUES (:username, :email, :password, :role)'
            );

            $insertStatement->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'user'
            ]);

            $success = 'Registration completed successfully.';

            $username = '';
            $email = '';
        }
    }
}

require 'includes/header.php';
?>

<section class="form-card">
    <h1>Create an account</h1>
    <p>Join DevTalks and start publishing development articles.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars($username) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($email) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                required
            >
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                minlength="8"
                required
            >
        </div>

        <button type="submit">Create account</button>
    </form>
</section>

<?php require 'includes/footer.php'; ?>