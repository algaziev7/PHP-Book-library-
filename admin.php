<?php
include 'utils.php';
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $year = (int)$_POST['year'];
    $planet = trim($_POST['planet']);
    $genre = trim($_POST['genre']); // Genre input
    $image = $_POST['image'];

    if (empty($title) || empty($author) || empty($year) || empty($genre)) {
        $error = 'Title, author, year, and genre are required.';
    } else {
        $books = loadBooks();
        $newBook = [
            'id' => uniqid('book'),
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'year' => $year,
            'genre' => $genre, // Save genre
            'image' => $image,
            'planet' => $planet,
            'ratings' => [],
            'reviews' => []
        ];
        $books[] = $newBook;
        file_put_contents('books.json', json_encode($books, JSON_PRETTY_PRINT));
        $message = 'Book added successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book | Admin</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
<header>
    <h1>Add New Book</h1>
    <a href="index.php" style="text-decoration: none; color: #FFF; background-color: #007BFF; padding: 8px 15px; border-radius: 5px; font-size: 16px;">Return to Main Page</a>
</header>

    <div id="content">
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($message): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" required><br>
            <input type="text" name="author" placeholder="Author" required><br>
            <textarea name="description" placeholder="Description"></textarea><br>
            <input type="number" name="year" placeholder="Year" required><br>
            <input type="text" name="genre" placeholder="Genre" required><br> <!-- Genre field -->
            <select name="image" required>
                <option value="">Select a book cover</option>
                <option value="book_cover_1.png">Book Cover 1</option>
                <option value="book_cover_2.png">Book Cover 2</option>
                <option value="book_cover_3.png">Book Cover 3</option>
                <option value="book_cover_4.png">Book Cover 4</option>
                <option value="book_cover_5.png">Book Cover 5</option>
                <option value="book_cover_6.png">Book Cover 6</option>
            </select><br>
            <input type="text" name="planet" placeholder="Planet"><br>
            <button type="submit" name="submit">Add Book</button>
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
