<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$uploadFeedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $uploadDir = dirname(__FILE__) . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $maxFileSize = 100 * 1024 * 1024;
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'exe', 'zip'];
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['fileToUpload'];
        $fileName = basename($file['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uniqueFileName = $_SESSION['user_id'] . '_' . uniqid() . '_' . $fileName;
        $uploadPath = $uploadDir . $uniqueFileName;
        $errors = [];
        if ($file['size'] > $maxFileSize) {
            $errors[] = "File size exceeds 100MB limit.";
        }
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = "File type not allowed. Allowed types: " . implode(', ', $allowedTypes);
        }
        if (!is_writable($uploadDir)) {
            $errors[] = "Upload directory is not writable.";
        }
        if (empty($errors)) {
            $baseUrl = 'https://funfiles.xyz/uploads/';
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $downloadLink = $baseUrl . $uniqueFileName;
            } else {
                $downloadLink = $baseUrl . $uniqueFileName;
                error_log("Failed to move uploaded file to $uploadPath. Check permissions or disk space. File: $fileName");
            }
            $uploadFeedback = '<div class="success">';
            $uploadFeedback .= '<input type="text" value="' . htmlspecialchars($downloadLink) . '" readonly style="width: 80%; padding: 5px; border: 1px solid #fff; border-radius: 0; font-family: monospace; background: rgba(255, 255, 255, 0.2);">';
            $uploadFeedback .= '<button class="copy-link" onclick="navigator.clipboard.writeText(\'' . htmlspecialchars($downloadLink) . '\'); alert(\'Link copied!\');">Copy</button>';
            $uploadFeedback .= '</div>';
        } else {
            $uploadFeedback = '<div class="error"><p>' . implode('<br>', $errors) . '</p></div>';
        }
    } else {
        $uploadFeedback = '<div class="error"><p>No file selected for upload.</p></div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funfiles - Upload</title>
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
        .upload-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.5s ease;
            max-width: 400px;
            margin: 0 auto;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        input[type="file"] {
            margin: 10px 0;
            color: #fff;
        }
        button {
            padding: 10px 20px;
            background: #2a5298;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        button:hover {
            transform: scale(1.05);
        }
        .success {
            padding: 15px;
            margin-top: 20px;
            border-radius: 10px;
            background: rgba(42, 82, 152, 0.8);
            color: #fff;
            animation: popIn 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .copy-link {
            background: #1e3c72;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .copy-link:hover {
            transform: scale(1.05);
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
            <a href="upload.php">Upload</a>
            <a href="filemanager.php">File Manager</a>
            <a href="logout.php">Logout</a>
        </div>
        <div class="content">
            <h1>Upload Files</h1>
            <div class="upload-form">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="file" name="fileToUpload" required>
                    <br>
                    <button type="submit" name="submit">Upload File</button>
                </form>
            </div>
            <?php echo $uploadFeedback; ?>
        </div>
    </div>
</body>
</html>