<?php
include 'utils.php';
session_start();

$bookId = $_GET['id'] ?? '';
$books = loadBooks();
$selectedBook = null;
$error = '';
$message = '';

foreach ($books as $book) {
    if ($book['id'] === $bookId) {
        $selectedBook = $book;
        break;
    }
}

function calculateAverageRating($ratings) {
    if (is_array($ratings) && count($ratings) > 0) {
        return array_sum($ratings) / count($ratings);
    }
    return 0; 
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['username'])) {
        $error = 'You must be logged in to submit a review.';
    } else {
        $username = $_SESSION['username'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);

        if ($rating < 1 || $rating > 5) {
            $error = 'Rating must be between 1 and 5.';
        } elseif (empty($comment)) {
            $error = 'Comment cannot be empty.';
        } else {
            $review = [
                'username' => $username,
                'comment' => htmlspecialchars($comment),
                'rating' => $rating
            ];

            // Ensure 'ratings' and 'reviews' keys exist
            if (!isset($selectedBook['ratings'])) {
                $selectedBook['ratings'] = [];
            }
            if (!isset($selectedBook['reviews'])) {
                $selectedBook['reviews'] = [];
            }

            // Add the review and rating
            $selectedBook['reviews'][] = $review;
            $selectedBook['ratings'][] = $rating;

            // Update the books data and save it
            foreach ($books as &$book) {
                if ($book['id'] === $bookId) {
                    $book = $selectedBook;
                    break;
                }
            }
            file_put_contents('books.json', json_encode($books, JSON_PRETTY_PRINT));
            $message = 'Review submitted successfully!';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details | IK-Library</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/details.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Book Details</h1>
    </header>
    <div id="content">
        <?php if ($selectedBook): ?>
            <h1><?= htmlspecialchars($selectedBook['title']) ?></h1>
            <img src='assets/<?= $selectedBook['image'] ?>' alt='<?= htmlspecialchars($selectedBook['title']) ?>' style='width:200px;'>
            <p><strong>Author:</strong> <?= htmlspecialchars($selectedBook['author']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($selectedBook['description']) ?></p>
            <p><strong>Year:</strong> <?= $selectedBook['year'] ?></p>
            <p><strong>Planet:</strong> <?= htmlspecialchars($selectedBook['planet']) ?></p>
            <p><strong>Average Rating:</strong> <?= calculateAverageRating($selectedBook['ratings'] ?? []) ?></p>
            <div>
                <h3>Reviews:</h3>
                <?php if (!empty($selectedBook['reviews'])): ?>
                    <?php foreach ($selectedBook['reviews'] as $review): ?>
                        <p><strong><?= htmlspecialchars($review['username']) ?>:</strong> <?= htmlspecialchars($review['comment']) ?> (Rating: <?= $review['rating'] ?>/5)</p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>
            </div>

            <?php if ($error): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($message): ?>
                <p style="color: green;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['username'])): ?>
                <h3>Submit Your Review</h3>
                <form method="post" action="">
                    <label for="rating">Rating (1-5):</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" required><br>
                    <label for="comment">Comment:</label>
                    <textarea id="comment" name="comment" required></textarea><br>
                    <button type="submit">Submit Review</button>
                </form>
            <?php else: ?>
                <p>You must be <a href="login.php">logged in</a> to submit a review.</p>
            <?php endif; ?>

        <?php else: ?>
            <p>Book not found.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
