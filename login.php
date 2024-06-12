<?php
include 'utils.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $users = loadUsers();
    foreach ($users as &$user) {  
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $user['is_admin'] ?? false;
            $user['last_login'] = date('Y-m-d H:i:s');  
            saveUsers($users);  
            $_SESSION['message'] = 'Login successful!';
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | IK-Library</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Login</a></h1>
    </header>
    <div class="container">
        <form method="post" action="login.php" class="card">
            <div class="card-body">
                <h2 class="card-title">Sign In</h2>
                <input type="text" name="username" placeholder="Username" class="form-control" required>
                <input type="password" name="password" placeholder="Password" class="form-control" required>
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
    </div>
    <footer>
        <p>IK-Library | All rights reserved</p>
    </footer>
</body>
</html>
