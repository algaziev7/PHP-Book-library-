<?php
function loadBooks() {
    $jsonFilePath = 'books.json';
    if (!file_exists($jsonFilePath) || !is_readable($jsonFilePath)) {
        return [];
    }
    $booksJson = file_get_contents($jsonFilePath);
    return json_decode($booksJson, true);
}

function loadUsers() {
    $jsonFilePath = 'users.json';
    if (!file_exists($jsonFilePath) || !is_readable($jsonFilePath)) {
        return [];
    }
    $usersJson = file_get_contents($jsonFilePath);
    return json_decode($usersJson, true);
}

function saveUsers($users) {
    $jsonFilePath = 'users.json';
    file_put_contents($jsonFilePath, json_encode($users, JSON_PRETTY_PRINT));
}
