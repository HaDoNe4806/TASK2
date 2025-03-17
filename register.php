<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" type="text/css" href="style.css"> 
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            if (password.length < 8) {
                alert("Password phải dài hơn 8 ký tự!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="register-container">
        <h2>ĐĂNG KÝ</h2>
        <form action="register.php" method="post" onsubmit="return validateForm()">
            <div class="input-group">     
                <input type="text" name="username" placeholder="Nhập tên đăng ký" required>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>    
            </div>
            <button type="submit" class="btn-register">Đăng ký</button>
        </form>
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
    </div>
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "webfu";

// Kết nối database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// nhận request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // kiểm tra nếu không điền 1 trong 2 ô
    if (empty($username) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location='register.php';</script>";
        exit();
    }

    // Kiểm tra username đã tồn tại hay chưa
    $stmt2 = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt2->bind_param("s", $username);
    $stmt2->execute();
    $stmt2->store_result();

    if ($stmt2->num_rows > 0) {
        echo "<script>alert('Tên đăng nhập đã tồn tại!'); window.location='register.php';</script>";
        $stmt2->close();
        exit();
    }
    $stmt2->close();

    // Băm mật khẩu trước khi lưu vào database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Sử dụng Prepared Statement để INSERT
    $stmt = $conn->prepare("INSERT INTO users(username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Đăng ký thành công!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Đăng ký thất bại!');</script>";
    }

    $stmt->close(); // Đóng statement
}

$conn->close(); // Đóng kết nối database
?>
</body>
</html>