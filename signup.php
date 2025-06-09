<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}
$usersFile = 'users.json';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    }
    if (strlen($username) < 3 || strlen($username) > 20) {
        $errors[] = "Username must be 3-20 characters.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
        if (isset($users[$username])) {
            $errors[] = "Username already exists.";
        }
    } else {
        $users = [];
    }
    if (empty($errors)) {
        $users[$username] = [
            'id' => uniqid(),
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        $_SESSION['user_id'] = $users[$username]['id'];
        $_SESSION['username'] = $username;
        header("Location: home.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funfiles</title>
    <link rel="icon" type="image/png" href="https://i.imgur.com/zZiqVZ4.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(45deg, #1e3c72, #2a5298, #2a5298, #1e3c72);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #fff;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            display: flex;
            height: 100%;
        }
        .sidebar {
            width: 250px;
            background: rgba(0, 0, 0, 0.8);
            padding: 20px 0;
            transition: transform 0.3s ease;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }
        .sidebar img {
            display: block;
            margin: 0 auto 20px;
            width: 120px;
            border-radius: 15px;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background: #2a5298;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.5s ease;
            max-width: 400px;
            width: 100%;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #fff;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        button {
            padding: 10px 20px;
            background: #2a5298;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s ease;
            width: 100%;
        }
        button:hover {
            transform: scale(1.05);
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            padding: 15px;
            margin-top: 10px;
            border-radius: 10px;
            background: rgba(255, 0, 0, 0.2);
            color: #fff;
            animation: shake 0.5s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <img src="https://i.imgur.com/zZiqVZ4.png" alt="Funfiles Logo">
            <a href="home.php">Home</a>
            <a href="index.php">Uploader</a>
            <a href="filemanager.php">File Manager</a>
            <a href="logout.php">Logout</a>
        </div>
        <div class="content">
            <div class="form-container">
                <h1>Sign Up</h1>
                <form action="" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="signup">Sign Up</button>
                    <p>Already have an account? <a href="login.php">Log In</a></p>
                </form>
                <?php
                if (!empty($errors)) {
                    echo '<div class="error"><p>' . implode('<br>', $errors) . '</p></div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>