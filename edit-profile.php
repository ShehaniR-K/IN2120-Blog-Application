<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();


$userId = $_SESSION['user_id'];


// Get current user

$statement = $pdo->prepare(
    'SELECT username, email
     FROM user
     WHERE id = :id
     LIMIT 1'
);

$statement->execute([
    'id' => $userId
]);

$user = $statement->fetch();



if (!$user) {

    exit('User not found.');

}



$error = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);



    if ($username === '' || $email === '') {

        $error = 'Username and email are required.';

    } else {


        if ($password !== '') {


            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            $update = $pdo->prepare(
                'UPDATE user
                 SET username = :username,
                     email = :email,
                     password = :password
                 WHERE id = :id'
            );


            $update->execute([

                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'id' => $userId

            ]);

        } else {


            $update = $pdo->prepare(
                'UPDATE user
                 SET username = :username,
                     email = :email
                 WHERE id = :id'
            );


            $update->execute([

                'username' => $username,
                'email' => $email,
                'id' => $userId

            ]);

        }



        $_SESSION['username'] = $username;


        header('Location: profile.php');

        exit;

    }

}



$pageTitle = 'Edit Profile - DevTalks';


require 'includes/header.php';

?>


<section class="form-page">


<h1>Edit Profile</h1>



<?php if ($error): ?>

<p class="error-message">

<?= htmlspecialchars($error) ?>

</p>

<?php endif; ?>



<form method="POST" class="auth-form">


<label>
Username
</label>


<input
type="text"
name="username"
value="<?= htmlspecialchars($user['username']) ?>"
>


<label>
Email
</label>


<input
type="email"
name="email"
value="<?= htmlspecialchars($user['email']) ?>"
>


<label>
New Password (optional)
</label>


<input
type="password"
name="password"
placeholder="Leave empty to keep current password"
>



<button type="submit">

Save Changes

</button>


</form>


</section>



<?php require 'includes/footer.php'; ?>