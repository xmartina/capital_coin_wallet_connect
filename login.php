<?php

$current_url = $_SERVER['REQUEST_URI'];
if (strpos($current_url, 'index') === false) {
    if (!isset($_SESSION['user_id'])) {
        session_start();
    }
}

require 'db.php';

if (isset($_GET['user_id'])) {
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php?user_id=' . $user['id']);
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        input {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.25rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #000;
            color: white;
            border: none;
            padding: 0.75rem;
            width: 100%;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        .error {
            color: #dc3545;
            margin: 1rem 0;
            text-align: center;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Wallet Login</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>