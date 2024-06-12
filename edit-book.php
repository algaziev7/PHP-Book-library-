<?php
include 'utils.php';
session_start();

if (!isset($_SESSION['username']) || !($_SESSION['is_admin'] ?? false)) {
    header('Location: login.php');
    exit;
}

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

if (!$selectedBook) {
    echo "Book not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $year = (int)$_POST['year'];
    $planet = trim($_POST['planet']);
    $image = $_FILES['image'];

    if (empty($title) || empty($author) || empty($year)) {
        $error = 'Title, author, and year are required.';
    } else {
        if ($image['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/';
            $imageName = basename($image['name']);
            $imagePath = $uploadDir . $imageName;

            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                $selectedBook['image'] = $imageName;
            } else {
                $error = 'Failed to save the uploaded image.';
            }
        }

        if (!$error) {
            $selectedBook['title'] = $title;
            $selectedBook['author'] = $author;
            $selectedBook['description'] = $description;
            $selectedBook['year'] = $year;
            $selectedBook['planet'] = $planet;
            $selectedBook['genre'] = $genre;
            foreach ($books as &$book) {
                if ($book['id'] === $bookId) {
                    $book = $selectedBook;
                    break;
                }
            }

            if (file_put_contents('books.json', json_encode($books, JSON_PRETTY_PRINT))) {
                $message = 'Book updated successfully!';
            } else {
                $error = 'Failed to save book data.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book | Admin</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1>Edit Book</h1>
    </header>
    <div id="content">
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($message): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form action="edit-book.php?id=<?= htmlspecialchars($bookId) ?>" method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" value="<?= htmlspecialchars($selectedBook['title']) ?>" required><br>
            <input type="text" name="author" placeholder="Author" value="<?= htmlspecialchars($selectedBook['author']) ?>" required><br>
            <textarea name="description" placeholder="Description"><?= htmlspecialchars($selectedBook['description']) ?></textarea><br>
            <input type="number" name="year" placeholder="Year" value="<?= $selectedBook['year'] ?>" required><br>
            <input type="file" name="image" accept="image/*"><br>
            <input type="text" name="planet" placeholder="Planet" value="<?= htmlspecialchars($selectedBook['planet']) ?>"><br>
            <button type="submit" name="submit">Update Book</button>
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
