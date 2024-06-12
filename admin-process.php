<?php
if (isset($_POST['submit'])) {
    $jsonFilePath = 'books.json'; 
    $books = json_decode(file_get_contents($jsonFilePath), true) ?? [];
    
    $uploadDir = 'uploads/';
    $imageName = basename($_FILES['image']['name']);
    $imagePath = $uploadDir . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    
    $newBook = [
        'id' => uniqid('book'),
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'description' => $_POST['description'],
        'year' => $_POST['year'],
        'image' => $imagePath,
        'planet' => $_POST['planet']
    ];
    
    $books[] = $newBook;
    file_put_contents($jsonFilePath, json_encode($books, JSON_PRETTY_PRINT));
    echo "Book added successfully!";
} else {
    echo "No data submitted.";
}
?>
