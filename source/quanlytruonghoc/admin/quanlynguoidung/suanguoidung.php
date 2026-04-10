<?php
include('../config.php');
// Kiểm tra quyền truy cập và chọn navbar
if ($_SESSION['role'] == 'admin') {
    include('../navbar.php'); // Navbar dành cho admin
    $redirect_url = '/quanlytruonghoc/admin/quanlynguoidung/quanlynguoidung.php'; // Admin quay về quản lý người dùng
} elseif ($_SESSION['role'] == 'Giáo viên') {
    include('../navbargiaovien.php'); // Navbar dành cho giáo viên
    $redirect_url = '/quanlytruonghoc/admin/dashboardgiaovien.php'; // Admin quay về quản lý người dùng
} else {
    die("Bạn không có quyền truy cập trang này.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID người dùng không hợp lệ.");
}

$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("Không tìm thấy người dùng.");
}

$image_path = $user['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tennguoidung = mysqli_real_escape_string($conn, $_POST['tennguoidung']);
    $tendangnhap = mysqli_real_escape_string($conn, $_POST['tendangnhap']);
    $sdt = mysqli_real_escape_string($conn, $_POST['SDT']);
    $gmail = mysqli_real_escape_string($conn, $_POST['gmail']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $matkhau = isset($_POST['matkhau']) ? trim($_POST['matkhau']) : '';

    // Xử lý upload ảnh
    if ($_FILES['avatar']['name']) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $avatar_name = time() . '_' . basename($_FILES['avatar']['name']);
        $target_file = $upload_dir . $avatar_name;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    // Cập nhật mật khẩu không mã hóa nếu có nhập
    if (!empty($matkhau)) {
        $update_sql = "UPDATE users SET 
            tennguoidung = '$tennguoidung', 
            tendangnhap = '$tendangnhap', 
            SDT = '$sdt', 
            gmail = '$gmail', 
            diachi = '$diachi',
            role = '$role', 
            matkhau = '$matkhau', 
            image = '$image_path'
            WHERE id = $id";
    } else {
        $update_sql = "UPDATE users SET 
            tennguoidung = '$tennguoidung', 
            tendangnhap = '$tendangnhap', 
            SDT = '$sdt', 
            gmail = '$gmail',
            diachi = '$diachi', 
            role = '$role', 
            image = '$image_path'
            WHERE id = $id";
    }

    if (mysqli_query($conn, $update_sql)) {
        // Thông báo thành công và chuyển hướng
        echo "<script>
                alert('Lưu thành công!');
                window.location.href = '$redirect_url';
              </script>";
        exit;
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Người Dùng</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f9f9f9;
            padding-top: 100px;
        }

        .container {
            width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        img {
            max-width: 150px;
            display: block;
            margin-top: 8px;
            margin-bottom: 12px;
            border-radius: 6px;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Sửa Thông Tin Người Dùng</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Tên Người Dùng:</label>
        <input type="text" name="tennguoidung" value="<?php echo $user['tennguoidung']; ?>" required>

        <label>Tên Đăng Nhập:</label>
        <input type="text" name="tendangnhap" value="<?php echo $user['tendangnhap']; ?>" required>

        <label>Số Điện Thoại:</label>
        <input type="text" name="SDT" value="<?php echo $user['SDT']; ?>">

        <label>Gmail:</label>
        <input type="email" name="gmail" value="<?php echo $user['gmail']; ?>">

        <label>Địa Chỉ:</label>
        <input type="text" name="diachi" value="<?php echo $user['diachi']; ?>">

        <?php if ($_SESSION['role'] == 'admin'): ?>
    <!-- Nếu là admin, cho phép thay đổi quyền -->
    <label>Quyền:</label>
    <select name="role" required>
        <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
        <option value="giaovien" <?php echo ($user['role'] === 'giaovien') ? 'selected' : ''; ?>>Giáo viên</option>
    </select>
<?php else: ?>
    <!-- Nếu là giaovien, không cho phép thay đổi quyền -->
    <input type="hidden" name="role" value="<?php echo $user['role']; ?>"> <!-- Đảm bảo không thay đổi quyền -->
<?php endif; ?>

        <label>Mật Khẩu (để trống nếu không đổi):</label>
        <input type="password" name="matkhau">

        <label>Ảnh Đại Diện (Avatar):</label>
        <input type="file" name="avatar" accept="image/*">
        <?php if (!empty($user['image'])): ?>
            <img src="<?php echo $user['image']; ?>" alt="Avatar hiện tại">
        <?php else: ?>
            <p>Chưa có ảnh.</p>
        <?php endif; ?>

        <button type="submit" class="btn">Lưu Thay Đổi</button>
    </form>
</div>
</body>
</html>
