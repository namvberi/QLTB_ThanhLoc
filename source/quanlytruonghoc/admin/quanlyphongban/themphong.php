<?php
include('../config.php'); // kết nối database
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenphong = mysqli_real_escape_string($conn, $_POST['tenphong']);
    $nguoiphutrach = mysqli_real_escape_string($conn, $_POST['nguoiphutrach']);

    // Thêm phòng ban mới
    $query = "INSERT INTO phongban (tenphong, nguoiphutrach) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $tenphong, $nguoiphutrach);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm phòng ban thành công!'); window.location.href='quanlyphongban.php';</script>";
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Phòng Ban</title>
</head>
<body>
<div class="container">
    <h1>Thêm Phòng Ban</h1>
    <form method="POST" action="themphong.php">
        <div class="form-group">
            <label for="tenphong">Tên Phòng</label>
            <input type="text" name="tenphong" id="tenphong" required>
        </div>

        <div class="form-group">
            <label for="nguoiphutrach">Người Phụ Trách</label>
            <input type="text" name="nguoiphutrach" id="nguoiphutrach" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn">Thêm Phòng Ban</button>
            <a href="quanlyphongban.php" class="btn">Quay lại</a>
        </div>
    </form>
</div>

<style>
.container {
    width: 400px;
    margin: 50px auto;
}
.form-group {
    margin-bottom: 15px;
}
label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}
input {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
}
.btn {
    padding: 8px 16px;
    background-color:#007bff;
    color: white;
    text-decoration: none;
    border: none;
    cursor: pointer;
    margin-right: 10px;
}
.btn:hover {
    background-color: #007bff;
}
</style>
</body>
</html>
