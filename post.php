<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$postId) {
    http_response_code(404);
    exit('Blog post not found.');
}

$statement = $pdo->prepare(
    'SELECT
        blogPost.id,
        blogPost.user_id,
        blogPost.title,
        blogPost.content,
        blogPost.created_at,
        blogPost.updated_at,
        user.username
     FROM blogPost
     INNER JOIN user ON blogPost.user_id = user.id
     WHERE blogPost.id = :id
     LIMIT 1'
);

$statement->execute(['id' => $postId]);
$post = $statement->fetch();

if (!$post) {
    http_response_code(404);
    exit('Blog post not found.');
}

$pageTitle = $post['title'] . ' - DevTalks';

require 'includes/header.php';
?>

<article class="single-post">
    <header class="single-post-header">
        <p class="eyebrow">DevTalks story</p>

        <h1><?= htmlspecialchars($post['title']) ?></h1>

        <div class="post-meta">
            <span>By <?= htmlspecialchars($post['username']) ?></span>
            <span>•</span>
            <time datetime="<?= htmlspecialchars($post['created_at']) ?>">
                <?= date('F j, Y', strtotime($post['created_at'])) ?>
            </time>
        </div>

        <?php if (
            isset($_SESSION['user_id'])
            && (int) $_SESSION['user_id'] === (int) $post['user_id']
        ): ?>
            <div class="owner-actions">
                <a
                    href="edit-post.php?id=<?= (int) $post['id'] ?>"
                    class="secondary-button"
                >
                    Edit
                </a>

                <form
                    method="POST"
                    action="delete-post.php"
                    onsubmit="return confirm('Are you sure you want to delete this article?');"
                >
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $post['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(csrfToken()) ?>"
                    >

                    <button type="submit" class="danger-button">
                        Delete
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </header>

    <div class="article-content">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>
</article>

<?php require 'includes/footer.php'; ?>