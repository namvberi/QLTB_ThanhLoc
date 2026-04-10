<?php
// Kết nối với cơ sở dữ liệu
include('../config.php');
include('../navbar.php');

// Kiểm tra xem form đã được gửi chưa
if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $tendangnhap = $_POST['tendangnhap'];
    $tennguoidung = $_POST['tennguoidung'];
    $matkhau = $_POST['matkhau'];
    $sdt = $_POST['sdt'];
    $gmail = $_POST['gmail'];
    $role = $_POST['role'];

    // Kiểm tra tên đăng nhập đã tồn tại chưa
    $sql_check = "SELECT * FROM users WHERE tendangnhap = '$tendangnhap'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "<script>alert('Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!');</script>";
    } else {
        $image = "";

        // Kiểm tra và xử lý upload ảnh nếu có
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $file_type = $_FILES['image']['type'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

            if (in_array($file_type, $allowed_types)) {
                $image_new_name = uniqid() . "_" . $image_name;
                $upload_dir = '../uploads/';

                // Tạo thư mục nếu chưa có
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $image_path = $upload_dir . $image_new_name;

                // Di chuyển ảnh
                if (move_uploaded_file($image_tmp_name, $image_path)) {
                    $image = $image_path;
                } else {
                    echo "<script>alert('Lỗi khi tải lên ảnh!');</script>";
                }
            } else {
                echo "<script>alert('Chỉ hỗ trợ định dạng ảnh JPG, PNG, GIF');</script>";
            }
        }

        // Thêm người dùng vào database
        $sql = "INSERT INTO users (tennguoidung, tendangnhap, matkhau, SDT, gmail, image, role) 
                VALUES ('$tennguoidung', '$tendangnhap', '$matkhau', '$sdt', '$gmail', '$image', '$role')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Người dùng đã được thêm thành công!'); window.location.href='quanlynguoidung.php';</script>";
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Người Dùng</title>
    <style>
        body {
            margin-top: 100px;
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
        }
        .container {
            width: 600px;
            margin: auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button, input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover, input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thêm Người Dùng</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="tennguoidung">Tên Người Dùng:</label>
            <input type="text" name="tennguoidung" required>

            <label for="tendangnhap">Tên Đăng Nhập:</label>
            <input type="text" name="tendangnhap" required>

            <label for="matkhau">Mật Khẩu:</label>
            <input type="password" name="matkhau" required>

            <label for="sdt">Số Điện Thoại:</label>
            <input type="text" name="sdt" required>

            <label for="gmail">Email:</label>
            <input type="email" name="gmail" required>

            <label for="image">Ảnh:</label>
            <input type="file" name="image" accept="image/*">

            <label for="role">Vai Trò:</label>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="Giáo viên">Giáo viên</option>
            </select>

            <input type="submit" name="submit" value="Thêm Người Dùng">
        </form>
    </div>
</body>
</html>
