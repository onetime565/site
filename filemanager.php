<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $fileToDelete = $_POST['fileToDelete'];
    $uploadDir = dirname(__FILE__) . '/uploads/';
    $fullPath = $uploadDir . basename($fileToDelete);
    if (file_exists($fullPath) && strpos($fileToDelete, $_SESSION['user_id'] . '_') === 0) {
        unlink($fullPath);
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
        .file-list {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .file-item a {
            color: #fff;
            text-decoration: none;
            flex-grow: 1;
        }
        .file-item a:hover {
            text-decoration: underline;
        }
        .file-item .actions {
            display: flex;
            gap: 10px;
        }
        .copy-link, .delete-btn {
            background: #1e3c72;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .copy-link:hover, .delete-btn:hover {
            transform: scale(1.05);
        }
        .delete-btn {
            background: #a00;
        }
        .error {
            padding: 15px;
            margin-top: 20px;
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
            <h1>Your Files</h1>
            <div class="file-list">
                <?php
                $uploadDir = dirname(__FILE__) . '/uploads/';
                $userId = $_SESSION['user_id'];
                $files = glob($uploadDir . $userId . '_*');
                if (empty($files)) {
                    echo '<p>No files uploaded yet.</p>';
                } else {
                    foreach ($files as $file) {
                        $fileName = basename($file);
                        $baseUrl = 'https://funfiles.xyz/uploads/';
                        $downloadLink = $baseUrl . $fileName;
                        $originalName = preg_replace('/^' . $userId . '_[a-f0-9]+_/', '', $fileName);
                        echo '<div class="file-item">';
                        echo '<a href="' . htmlspecialchars($downloadLink) . '" target="_blank">' . htmlspecialchars($originalName) . '</a>';
                        echo '<div class="actions">';
                        echo '<button class="copy-link" onclick="navigator.clipboard.writeText(\'' . htmlspecialchars($downloadLink) . '\'); alert(\'Link copied!\');">Copy</button>';
                        echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete ' . htmlspecialchars($originalName) . '?\');">';
                        echo '<input type="hidden" name="fileToDelete" value="' . htmlspecialchars($fileName) . '">';
                        echo '<button type="submit" name="delete" class="delete-btn">Delete</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>