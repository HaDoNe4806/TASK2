<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Kết nối database
$host = "localhost";
$user = "root";
$pass = "";
$db = "webfu";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['user'];

// Xử lý upload file
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "uploads/";
    //  không có thư mục uploads thì sẽ tạo mới
    if (!file_exists($target_dir)) {  
        mkdir($target_dir, 0777, true);
    }
    // Dễ Dính lỗ hỏng File Upload vì không có kiểm tra gì hết
    $target_file = $target_dir . basename($_FILES["avatar"]["name"]);

    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO avatars(user_id, file_path) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $target_file);
        $stmt->execute();
        $stmt->close();

        $upload_message = "<p class='success'>The file ". htmlspecialchars(basename($_FILES["avatar"]["name"])). " has been uploaded.</p>";
    } else {
        $upload_message = "<p class='error'>Sorry, there was an error uploading your file.</p>";
    }
}

// Lấy avatar mới nhất
$stmt = $conn->prepare("SELECT file_path FROM avatars JOIN users ON avatars.user_id = users.id WHERE users.username = ? ORDER BY avatars.id DESC LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($avatar_path);
$stmt->fetch();
$stmt->close();

$conn->close();

$default_avatar = "uploads/default.jpg";
$avatar_url = $avatar_path ? $avatar_path : $default_avatar;  //nếu tài khoản chưa uplaod file lần nào thì lấy avatar default
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Avatar</title>
    <style>
        .upload-container {
            width: 300px;
            margin: auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        .avatar-preview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #ddd;
            margin-bottom: 10px;
        }
        .input-group {
            margin-bottom: 10px;
        }
        .btn-upload, .btn-reset {
            padding: 5px 10px;
            margin: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <h2>Upload New Avatar</h2>

        <div class="avatar-preview">
            <img src="<?= $avatar_url ?>" alt="Avatar" class="avatar-img">
        </div>

        <?= isset($upload_message) ? $upload_message : '' ?>

        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <input type="file" name="avatar" required>
            </div>
            <button type="submit" class="btn-upload">Upload</button>
            <button type="reset" class="btn-reset">Reset</button>
        </form>
    </div>
</body>
</html>
