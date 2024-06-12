<?php
include 'utils.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$users = loadUsers();
$currentUser = null;

foreach ($users as $user) {
    if ($user['username'] === $username) {
        $currentUser = $user;
        break;
    }
}

if (!$currentUser) {
    echo "User not found.";
    exit;
}

function getUserReviews($username, $books) {
    $reviews = [];
    foreach ($books as $book) {
        if (isset($book['reviews'])) {
            foreach ($book['reviews'] as $review) {
                if ($review['username'] === $username) {
                    $reviews[] = [
                        'book_title' => $book['title'],
                        'review' => $review
                    ];
                }
            }
        }
    }
    return $reviews;
}

$books = loadBooks();
$userReviews = getUserReviews($username, $books);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | IK-Library</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > User Profile</h1>
    </header>
    <div id="content">
        <h2>Profile of <?= htmlspecialchars($currentUser['username']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($currentUser['email']) ?></p>
        <p><strong>Last Login:</strong> <?= htmlspecialchars($currentUser['last_login']) ?: 'Never' ?></p>

        <h3>Your Reviews</h3>
        <?php if ($userReviews): ?>
            <ul>
                <?php foreach ($userReviews as $review): ?>
                    <li>
                        <strong><?= htmlspecialchars($review['book_title']) ?>:</strong> <?= htmlspecialchars($review['review']['comment']) ?> (Rating: <?= $review['review']['rating'] ?>/5)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You haven't written any reviews yet.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
