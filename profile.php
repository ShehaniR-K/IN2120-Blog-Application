<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();

$userId = $_SESSION['user_id'];


// Get user details

$userStatement = $pdo->prepare(
    'SELECT id, username
FROM user
WHERE id = :id
LIMIT 1'
);
$userStatement->execute([
    'id' => $userId
]);

$user = $userStatement->fetch();


if (!$user) {
    exit('User not found.');
}


// Get user's posts

$postStatement = $pdo->prepare(
    'SELECT
        id,
        title,
        content,
        created_at
     FROM blogPost
     WHERE user_id = :user_id
     ORDER BY created_at DESC'
);

$postStatement->execute([
    'user_id' => $userId
]);


$posts = $postStatement->fetchAll();


$pageTitle = 'My Profile - DevTalks';


$initial = strtoupper(
    mb_substr($user['username'], 0, 1)
);


require 'includes/header.php';

?>


<section class="profile-page">


<div class="profile-header">


    <div class="profile-avatar">

        <?= htmlspecialchars($initial) ?>

    </div>


    <div class="profile-info">


        <h1>
            <?= htmlspecialchars($user['username']) ?>
        </h1>


        <p>
            Developer at DevTalks
        </p>
<a href="edit-profile.php" class="profile-edit-button">
    Edit Profile
</a>


    </div>


</div>



<div class="profile-stats">


    <div class="profile-stat">

        <strong>
            <?= count($posts) ?>
        </strong>

        <span>
            Stories
        </span>

    </div>



    <div class="profile-stat">

    <strong>
        2026
    </strong>

    <span>
        Joined
    </span>

</div>


    <div class="profile-stat">

        <strong>
            Dev
        </strong>

        <span>
            Role
        </span>

    </div>


</div>
        <h2>
            My Stories
        </h2>



        <?php if (empty($posts)): ?>


            <div class="empty-state">

                <h3>
                    No stories yet
                </h3>

                <p>
                    Start sharing your developer journey.
                </p>

            </div>


        <?php else: ?>


            <div class="article-grid">


            <?php foreach ($posts as $post): ?>


                <article class="article-card">


                    <h3>

                        <a href="post.php?id=<?= (int)$post['id'] ?>">

                            <?= htmlspecialchars($post['title']) ?>

                        </a>

                    </h3>


                    <p>

                        <?= htmlspecialchars(
                            mb_substr(
                                strip_tags($post['content']),
                                0,
                                150
                            )
                        ) ?>...

                    </p>


                    <small>

                        <?= date(
                            'M j, Y',
                            strtotime($post['created_at'])
                        ) ?>

                    </small>


                </article>


            <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </section>


</section>


<?php require 'includes/footer.php'; ?>