<?php

session_start();
require_once 'config/database.php';

$pageTitle = 'Login - DevTalks';
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $statement = $pdo->prepare(
            'SELECT id, username, email, password, role
             FROM `user`
             WHERE email = :email
             LIMIT 1'
        );

        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: index.php');
            exit;
        }

        $error = 'Invalid email or password.';
    }
}

require 'includes/header.php';
?>

<section class="form-card">
    <h1>Welcome back</h1>
    <p>Log in to publish and manage your DevTalks articles.</p>

    <?php if ($error !== ''): ?>
        <div class="alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
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
                required
            >
        </div>

        <button type="submit">Log in</button>
    </form>

    <p>
        No account?
        <a href="register.php">Create one</a>
    </p>
</section>

<?php require 'includes/footer.php'; ?>