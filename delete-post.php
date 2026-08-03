<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$postId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$csrfToken = $_POST['csrf_token'] ?? null;

if (!$postId || !verifyCsrfToken($csrfToken)) {
    http_response_code(400);
    exit('Invalid request.');
}

$statement = $pdo->prepare(
    'DELETE FROM blogPost
     WHERE id = :id AND user_id = :user_id'
);

$statement->execute([
    'id' => $postId,
    'user_id' => $_SESSION['user_id']
]);

if ($statement->rowCount() === 0) {
    http_response_code(403);
    exit('You are not allowed to delete this blog post.');
}

header('Location: index.php');
exit;