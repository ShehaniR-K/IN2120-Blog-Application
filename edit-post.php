<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();

$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$postId) {
    http_response_code(404);
    exit('Blog post not found.');
}

$statement = $pdo->prepare(
    'SELECT id, user_id, title, content
     FROM blogPost
     WHERE id = :id
     LIMIT 1'
);

$statement->execute(['id' => $postId]);
$post = $statement->fetch();

if (!$post) {
    http_response_code(404);
    exit('Blog post not found.');
}

if ((int) $post['user_id'] !== (int) $_SESSION['user_id']) {
    http_response_code(403);
    exit('You are not allowed to edit this blog post.');
}

$pageTitle = 'Edit Story - DevTalks';

$title = $post['title'];
$content = $post['content'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? null;

    if (!verifyCsrfToken($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh and try again.';
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
        $updateStatement = $pdo->prepare(
            'UPDATE blogPost
             SET title = :title, content = :content
             WHERE id = :id AND user_id = :user_id'
        );

        $updateStatement->execute([
            'title' => $title,
            'content' => $content,
            'id' => $postId,
            'user_id' => $_SESSION['user_id']
        ]);

        header('Location: post.php?id=' . $postId);
        exit;
    }
}

require 'includes/header.php';
?>

<section class="editor-card">
    <div class="page-heading">
        <p class="eyebrow">Update story</p>
        <h1>Edit your article</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit-post.php?id=<?= (int) $postId ?>">
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
                required
            >
        </div>

        <div class="form-group">
            <label for="content">Blog content</label>
            <textarea
                id="content"
                name="content"
                rows="14"
                required
            ><?= htmlspecialchars($content) ?></textarea>
        </div>

        <button type="submit">Save changes</button>
    </form>
</section>

<?php require 'includes/footer.php'; ?>