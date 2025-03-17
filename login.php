<!DOCTYPE html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" type="text/css" href="style.css"> 
</head>
<body>
    <div class="register-container">
        <h2>ĐĂNG NHẬP</h2>
        <form action="login.php" method="post">
            <div class="input-group">
                <input type="text" name="username" placeholder="Nhập tên" required>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="btn-register">LOGIN</button>
        </form>
        <p>Chưa có tài khoản? <a href="register.php">Đăng kí ngay</a></p>
    </div>
    <?php
    session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "webfu";

// Kết nối database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra nếu form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Sử dụng Prepared Statement để tránh SQL Injection
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Kiểm tra mật khẩu
        if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
            $_SESSION['user'] = $username; // Lưu session
            header("Location: upload.php"); // Chuyển hướng đến trang index
            exit();
        } else {
            echo "<p class='error'>Sai username hoặc password!</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='error'>Vui lòng nhập đầy đủ thông tin!</p>";
    }
}
$conn->close();
?>

</body>
</html>