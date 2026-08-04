<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();

$pageTitle = 'Write a Story - DevTalks';

$title = '';
$content = '';
$image = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? null;


    /*
    |--------------------------------------------------------------------------
    | CSRF Validation
    |--------------------------------------------------------------------------
    */

    if (!verifyCsrfToken($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }


    /*
    |--------------------------------------------------------------------------
    | Text Validation
    |--------------------------------------------------------------------------
    */

    if ($title === '') {

        $errors[] = 'Blog title is required.';

    } elseif (mb_strlen($title) > 255) {

        $errors[] = 'Blog title cannot exceed 255 characters.';
    }


    if ($content === '') {

        $errors[] = 'Blog content is required.';
    }


    /*
    |--------------------------------------------------------------------------
    | Image Upload Validation
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        $maxFileSize = 5 * 1024 * 1024; // 5MB


        $temporaryFile = $_FILES['image']['tmp_name'];


        $imageType = mime_content_type($temporaryFile);


        if (!in_array($imageType, $allowedTypes)) {

            $errors[] = 'Only JPG, PNG and WEBP images are allowed.';
        }


        if ($_FILES['image']['size'] > $maxFileSize) {

            $errors[] = 'Image size must be less than 5MB.';
        }


        if (empty($errors)) {


            $extension = strtolower(
                pathinfo(
                    $_FILES['image']['name'],
                    PATHINFO_EXTENSION
                )
            );


            $fileName = uniqid('post_', true)
                . '.'
                . $extension;


            $uploadDirectory = 'uploads/posts/';


            $uploadPath = $uploadDirectory . $fileName;


            if (
                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    $uploadPath
                )
            ) {

                $image = $fileName;

            } else {

                $errors[] = 'Image upload failed.';
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Insert Blog Post
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {


        $statement = $pdo->prepare(
            'INSERT INTO blogPost
            (
                user_id,
                title,
                content,
                image
            )
            VALUES
            (
                :user_id,
                :title,
                :content,
                :image
            )'
        );


        $statement->execute([

            'user_id' => $_SESSION['user_id'],

            'title' => $title,

            'content' => $content,

            'image' => $image

        ]);


        $postId = $pdo->lastInsertId();


        header(
            'Location: post.php?id=' . $postId
        );

        exit;
    }
}


require 'includes/header.php';

?>


<section class="editor-card">

    <div class="page-heading">

        <p class="eyebrow">
            New story
        </p>


        <h1>
            Share something with developers
        </h1>


        <p>
            Write about programming, technology,
            design, or your development journey.
        </p>

    </div>



    <?php if (!empty($errors)): ?>

        <div class="alert-error">

            <?php foreach ($errors as $error): ?>

                <p>
                    <?= htmlspecialchars($error) ?>
                </p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>




    <form
        method="POST"
        action="create-post.php"
        enctype="multipart/form-data"
    >


        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(csrfToken()) ?>"
        >



        <div class="form-group">

            <label for="title">
                Blog title
            </label>


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

            <label for="image">
                Featured image
            </label>


            <input
                type="file"
                id="image"
                name="image"
                accept="image/jpeg,image/png,image/webp"
            >


            <small>
                JPG, PNG or WEBP. Maximum size 5MB.
            </small>

        </div>





        <div class="form-group">

            <label for="content">
                Blog content
            </label>


            <textarea
                id="content"
                name="content"
                rows="14"
                placeholder="Start writing your story..."
                required
            ><?= htmlspecialchars($content) ?></textarea>

        </div>




        <button type="submit">
            Publish story
        </button>


    </form>


</section>



<?php require 'includes/footer.php'; ?>