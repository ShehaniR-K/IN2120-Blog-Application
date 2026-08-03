<?php

require_once 'config/database.php';

$pageTitle = 'DevTalks - Developer Stories';

$statement = $pdo->query(
    'SELECT
        blogPost.id,
        blogPost.title,
        blogPost.content,
        blogPost.created_at,
        user.username
     FROM blogPost
     INNER JOIN user ON blogPost.user_id = user.id
     ORDER BY blogPost.created_at DESC'
);

$posts = $statement->fetchAll();

require 'includes/header.php';
?>

<section class="hero">
    <p class="eyebrow">Developer community</p>
    <h1>Ideas, lessons and stories from people who build.</h1>
    <p>
        Explore practical articles about programming, software engineering,
        technology, design and developer careers.
    </p>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="create-post.php" class="button-link">Write a story</a>
    <?php else: ?>
        <a href="register.php" class="button-link">Join DevTalks</a>
    <?php endif; ?>
</section>

<section class="stories-section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Latest articles</p>
            <h2>Developer stories</h2>
        </div>
    </div>

    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <h3>No articles published yet</h3>
            <p>Be the first person to publish a story on DevTalks.</p>
        </div>
    <?php else: ?>
        <div class="post-grid">
            <?php foreach ($posts as $post): ?>
                <?php
                $plainContent = trim(strip_tags($post['content']));

                $excerpt = mb_strlen($plainContent) > 180
                    ? mb_substr($plainContent, 0, 180) . '...'
                    : $plainContent;
                ?>

                <article class="post-card">
                    <div class="post-meta">
                        <span><?= htmlspecialchars($post['username']) ?></span>
                        <span>•</span>
                        <time datetime="<?= htmlspecialchars($post['created_at']) ?>">
                            <?= date('M j, Y', strtotime($post['created_at'])) ?>
                        </time>
                    </div>

                    <h3>
                        <a href="post.php?id=<?= (int) $post['id'] ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h3>

                    <p><?= htmlspecialchars($excerpt) ?></p>

                    <a
                        href="post.php?id=<?= (int) $post['id'] ?>"
                        class="read-more"
                    >
                        Read article →
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>