<?php

require_once 'config/database.php';

$pageTitle = 'DevTalks - Developer Stories';


$statement = $pdo->query(
    'SELECT
        blogPost.id,
        blogPost.title,
        blogPost.content,
        blogPost.image,
        blogPost.created_at,
        user.username
     FROM blogPost
     INNER JOIN user 
        ON blogPost.user_id = user.id
     ORDER BY blogPost.created_at DESC'
);


$posts = $statement->fetchAll();


require 'includes/header.php';

?>



<section class="home-hero">
    <video class="hero-background-video"
       autoplay
       muted
       loop
       playsinline>

    <source src="assets/videos/dev-bg.mp4" type="video/mp4">

</video>

<div class="hero-video-overlay"></div>

    <div class="hero-content">

        <p class="hero-label">
            A COMMUNITY FOR DEVELOPERS
        </p>


        <h1>
            Learn, build and share with
            <span>DevTalks.</span>
        </h1>


        <p class="hero-description">
            Discover practical stories about programming,
            software, technology, design and developer careers.
        </p>



        <div class="hero-actions">


            <?php if (isset($_SESSION['user_id'])): ?>

                <a 
                    href="create-post.php"
                    class="primary-action"
                >
                    Write an article
                </a>


            <?php else: ?>


                <a 
                    href="register.php"
                    class="primary-action"
                >
                    Join DevTalks
                </a>


                <a 
                    href="login.php"
                    class="secondary-action"
                >
                    Sign in
                </a>


            <?php endif; ?>


        </div>


    </div>




    <div class="hero-code-card">

        <div class="code-card-header">
            <span></span>
            <span></span>
            <span></span>
        </div>


<pre><code>const developer = {
    learn: true,
    build: true,
    share: true
};

developer.join("DevTalks");</code></pre>


    </div>


</section>





<section class="topic-section">


    <div class="topic-card">

        <span>01</span>

        <h3>
            Web Development
        </h3>

        <p>
            Frontend, backend and full-stack development.
        </p>

    </div>



    <div class="topic-card">

        <span>02</span>

        <h3>
            Programming
        </h3>

        <p>
            Languages, concepts and practical coding lessons.
        </p>

    </div>




    <div class="topic-card">

        <span>03</span>

        <h3>
            Technology
        </h3>

        <p>
            Tools, trends and modern software engineering.
        </p>

    </div>





    <div class="topic-card">

        <span>04</span>

        <h3>
            Developer Career
        </h3>

        <p>
            Skills, interviews and career development.
        </p>

    </div>


</section>







<section class="latest-section">


<div class="section-title-row">


    <div>

        <p class="section-label">
            LATEST STORIES
        </p>


        <h2>
            Ideas from developers
        </h2>

    </div>




    <?php if (isset($_SESSION['user_id'])): ?>

        <a 
            href="create-post.php"
            class="write-link"
        >
            Write a story →
        </a>

    <?php endif; ?>


</div>






<?php if (empty($posts)): ?>


    <div class="empty-state">

        <h3>
            No stories published yet
        </h3>


        <p>
            Publish the first developer story on DevTalks.
        </p>

    </div>




<?php else: ?>



<div class="article-grid">


<?php foreach ($posts as $index => $post): ?>


<?php

$plainContent = trim(
    strip_tags($post['content'])
);


$excerpt = mb_strlen($plainContent) > 160

    ? mb_substr($plainContent, 0, 160) . '...'

    : $plainContent;


?>





<article class="article-card <?= $index === 0 ? 'featured-card' : '' ?>">





<?php if (!empty($post['image'])): ?>


<div class="article-image">


<img

src="uploads/posts/<?= htmlspecialchars($post['image']) ?>"

alt="<?= htmlspecialchars($post['title']) ?>"

>


</div>


<?php endif; ?>






<div class="article-category">

    Developer Story

</div>






<div class="article-meta">


<span>

<?= htmlspecialchars($post['username']) ?>

</span>



<span>
•
</span>




<time datetime="<?= htmlspecialchars($post['created_at']) ?>">


<?= date(
    'M j, Y',
    strtotime($post['created_at'])
) ?>


</time>



</div>







<h3>


<a href="post.php?id=<?= (int)$post['id'] ?>">


<?= htmlspecialchars($post['title']) ?>


</a>


</h3>







<p>


<?= htmlspecialchars($excerpt) ?>


</p>







<a

href="post.php?id=<?= (int)$post['id'] ?>"

class="article-link"

>

Read article →

</a>





</article>





<?php endforeach; ?>



</div>




<?php endif; ?>



</section>





<?php require 'includes/footer.php'; ?>