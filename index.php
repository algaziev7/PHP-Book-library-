<?php
session_start();

function loadBooks() {
    $jsonFilePath = 'books.json';
    if (!file_exists($jsonFilePath) || !is_readable($jsonFilePath)) {
        return [];
    }
    $booksJson = file_get_contents($jsonFilePath);
    return json_decode($booksJson, true);
}

$books = loadBooks();
$filteredBooks = $books;
$selectedGenre = '';

if (isset($_GET['genre']) && $_GET['genre'] !== '') {
    $selectedGenre = $_GET['genre'];
    $filteredBooks = array_filter($books, function($book) use ($selectedGenre) {
        return isset($book['genre']) && $book['genre'] === $selectedGenre;
    });
}

$message = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$genres = array_unique(array_map(function($book) {
    return $book['genre'] ?? '';
}, $books));
$genres = array_filter($genres); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library | Home</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Home</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Welcome, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> | <a href="logout.php">Logout</a> | <a href="user-profile.php">Profile</a></p>
            <?php if ($_SESSION['is_admin']): ?>
                <p><a href="admin.php">Add New Book</a></p>
            <?php endif; ?>
        <?php else: ?>
            <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
        <?php endif; ?>
    </header>
    <div id="content">
        <?php if ($message): ?>
            <p style="color: green;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        
        <form method="GET" action="index.php">
            <label for="genre">Filter by Genre:</label>
            <select name="genre" id="genre">
                <option value="">All Genres</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?= htmlspecialchars($genre, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedGenre === $genre ? 'selected' : '' ?>><?= htmlspecialchars($genre, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
        </form>
        
        <div id="card-list">
            <?php foreach ($filteredBooks as $book): ?>
                <div class='book-card'>
                    <div class='image'><img src='assets/<?= htmlspecialchars($book['image'], ENT_QUOTES, 'UTF-8') ?>' alt='Cover image of <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>'></div>
                    <div class='details'>
                        <h2><a href='details.php?id=<?= htmlspecialchars($book['id'], ENT_QUOTES, 'UTF-8') ?>'><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></a></h2>
                        <p>By <?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <div class='edit'><a href='edit-book.php?id=<?= htmlspecialchars($book['id'], ENT_QUOTES, 'UTF-8') ?>'>Edit</a></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
