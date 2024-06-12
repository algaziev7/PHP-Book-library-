<?php
include 'utils.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $users = loadUsers();
        foreach ($users as $user) {
            if ($user['username'] == $username) {
                $error = 'Username already taken.';
                break;
            }
            if ($user['email'] == $email) {
                $error = 'Email already registered.';
                break;
            }
        }

        if (!$error) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $newUser = [
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'last_login' => date('Y-m-d H:i:s')
            ];
            $users[] = $newUser;
            saveUsers($users);
            $_SESSION['username'] = $username;
            $_SESSION['message'] = 'Registration successful!';
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | IK-Library</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Register</h1>
    </header>
    <div id="content">
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" action="register.php">
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" required><br>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <button type="submit">Register</button>
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
