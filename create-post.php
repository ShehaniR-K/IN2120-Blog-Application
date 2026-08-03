<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();

$pageTitle = 'Write a Story - DevTalks';

$title = '';
$content = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? null;

    if (!verifyCsrfToken($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($title === '') {
        $errors[] = 'Blog title is required.';
    } elseif (mb_strlen($title) > 255) {
        $errors[] = 'Blog title cannot exceed 255 characters.';
    }

    if ($content === '') {
        $errors[] = 'Blog content is required.';
    }

    if (empty($errors)) {
        $statement = $pdo->prepare(
            'INSERT INTO blogPost (user_id, title, content)
             VALUES (:user_id, :title, :content)'
        );

        $statement->execute([
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'content' => $content
        ]);

        $postId = $pdo->lastInsertId();

        header('Location: post.php?id=' . $postId);
        exit;
    }
}

require 'includes/header.php';
?>

<section class="editor-card">
    <div class="page-heading">
        <p class="eyebrow">New story</p>
        <h1>Share something with developers</h1>
        <p>Write about programming, technology, design, or your development journey.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="create-post.php">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(csrfToken()) ?>"
        >

        <div class="form-group">
            <label for="title">Blog title</label>
            <input
                type="text"
                id="title"
                name="title"
                maxlength="255"
                value="<?= htmlspecialchars($title) ?>"
                placeholder="Enter an interesting title"
                required
            >
        </div>

        <div class="form-group">
            <label for="content">Blog content</label>
            <textarea
                id="content"
                name="content"
                rows="14"
                placeholder="Start writing your story..."
                required
            ><?= htmlspecialchars($content) ?></textarea>
        </div>

        <button type="submit">Publish story</button>
    </form>
</section>

<?php require 'includes/footer.php'; ?>