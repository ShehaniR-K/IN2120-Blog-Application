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
    blogPost.image,
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

$plainContent = trim(strip_tags($post['content']));
$wordCount = str_word_count($plainContent);
$readingTime = max(1, (int) ceil($wordCount / 200));

$isOwner = isset($_SESSION['user_id'])
    && (int) $_SESSION['user_id'] === (int) $post['user_id'];

$authorInitial = strtoupper(
    mb_substr($post['username'], 0, 1)
);

$isUpdated = strtotime($post['updated_at'])
    > strtotime($post['created_at']);

require 'includes/header.php';
?>

<article class="article-page">
    <div class="article-shell">

        <a href="index.php" class="article-back-link">
            <span>←</span>
            Back to stories
        </a>

        <header class="article-hero">
            <div class="article-glow article-glow-one"></div>
            <div class="article-glow article-glow-two"></div>

                 
<?php if (!empty($post['image'])): ?>

    <div class="post-cover-image">

        <img
            src="uploads/posts/<?= htmlspecialchars($post['image']) ?>"
            alt="<?= htmlspecialchars($post['title']) ?>"
        >

    </div>

<?php endif; ?>

            <div class="article-hero-content">

                <div class="article-top-row">
                    <span class="article-category">
                        Developer Story
                    </span>

                    <span class="article-reading-time">
                        <?= $readingTime ?> min read
                    </span>
                </div>

                <h1>
                    <?= htmlspecialchars($post['title']) ?>
                </h1>

                <p class="article-introduction">
                    A developer story shared with the DevTalks community.
                </p>

                <div class="article-author-section">
                    <div class="article-author">
                        <div class="article-avatar">
                            <?= htmlspecialchars($authorInitial) ?>
                        </div>

                        <div class="article-author-info">
                            <strong>
                                <?= htmlspecialchars($post['username']) ?>
                            </strong>

                            <div class="article-meta">
                                <time datetime="<?= htmlspecialchars($post['created_at']) ?>">
                                    <?= date(
                                        'F j, Y',
                                        strtotime($post['created_at'])
                                    ) ?>
                                </time>

                                <?php if ($isUpdated): ?>
                                    <span>•</span>
                                    <span>Updated</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($isOwner): ?>
                        <div class="article-owner-actions">
                            <a
                                href="edit-post.php?id=<?= (int) $post['id'] ?>"
                                class="article-edit-button"
                            >
                                Edit story
                            </a>

                            <form
                                method="POST"
                                action="delete-post.php"
                                onsubmit="return confirm(
                                    'Are you sure you want to delete this story?'
                                );"
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

                                <button
                                    type="submit"
                                    class="article-delete-button"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </header>

        <section class="article-reading-card">
            <div class="article-reading-header">
                <span>Article</span>
                <span><?= number_format($wordCount) ?> words</span>
            </div>

            <div class="article-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
        </section>

        <footer class="article-footer-card">
            <div class="article-footer-author">
                <div class="article-avatar article-avatar-small">
                    <?= htmlspecialchars($authorInitial) ?>
                </div>

                <div>
                    <span>Written by</span>
                    <strong>
                        <?= htmlspecialchars($post['username']) ?>
                    </strong>
                </div>
            </div>

            <a href="index.php">
                Explore more stories
                <span>→</span>
            </a>
        </footer>

    </div>
</article>

<?php require 'includes/footer.php'; ?>