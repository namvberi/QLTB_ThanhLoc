<?php
// Kết nối cơ sở dữ liệu
include('config.php');

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['users_id'])) {
    header("Location: login.php");
    exit();
}

$users_id = $_SESSION['users_id'];
// Lấy thông tin người dùng
$query = "SELECT * FROM users WHERE id = '$users_id'";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_assoc($result);

$role = $users['role'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin-top: 80px;
        }

        .profile-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        .profile-container h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .profile-container label {
            font-weight: 500;
            display: block;
            margin-top: 10px;
            text-align: left;
            color: #555;
        }

        .profile-container input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            color: #333;
            font-size: 14px;
            box-sizing: border-box;
        }

        .profile-container input[readonly] {
            background-color: #f1f1f1;
            cursor: not-allowed;
        }

        .profile-container img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            margin-top: 20px;
        }

        .profile-container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            margin-top: 20px;
            border-radius: 5px;
            cursor: not-allowed;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        .profile-container button:disabled {
            background-color: #007bff;
            opacity: 0.7;
        }
        .btn-edit {
    display: inline-block;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 5px;
    margin-top: 20px;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-edit:hover {
    background-color: #218838;
    transform: translateY(-2px); /* Hiệu ứng nâng khi di chuột */
}

/* Nút "Quay lại" */
.btn-back {
    display: inline-block;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 5px;
    margin-top: 20px;
    font-size: 16px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-back:hover {
    background-color: #0056b3;
    transform: translateY(-2px); /* Hiệu ứng nâng khi di chuột */
}

        /* Định dạng cho màn hình nhỏ */
        @media (max-width: 400px) {
            .profile-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h1>Profile của <?php echo $users['tennguoidung']; ?></h1>

    <form method="POST">
        <label for="tennguoidung">Họ và tên:</label>
        <input type="text" name="tennguoidung" value="<?php echo $users['tennguoidung']; ?>" readonly>

        <label for="tendangnhap">Tài khoản:</label>
        <input type="text" name="tendangnhap" value="<?php echo $users['tendangnhap']; ?>" readonly>

        <label for="matkhau">Mật khẩu:</label>
        <input type="text" name="matkhau" value="<?php echo $users['matkhau']; ?>" readonly>

        <label for="SDT">Số điện thoại:</label>
        <input type="text" name="SDT" value="<?php echo $users['SDT']; ?>" readonly>

        <label for="gmail">Gmail:</label>
        <input type="text" name="gmail" value="<?php echo $users['gmail']; ?>" readonly>

        <label for="diachi">Địa chỉ:</label>
        <input type="text" name="diachi" value="<?php echo $users['diachi']; ?>" readonly>

        <label for="image">Hình ảnh:</label>
        <div>
            <img src="/quanlytruonghoc/admin/images/<?php echo $users['image']; ?>" alt="Avatar">
        </div>

        <a href="/quanlytruonghoc/admin/quanlynguoidung/suanguoidung.php?id=<?php echo $users['id']; ?>" class="btn btn-edit">Sửa</a>
</form>

<?php if ($role == 'admin'): ?>
    <a href="/quanlytruonghoc/admin/dashboardadmin.php" class="btn-back">Quay lại</a>
<?php elseif ($role == 'Giáo viên'): ?>
    <a href="/quanlytruonghoc/admin/dashboardgiaovien.php" class="btn-back">Quay lại</a>
<?php endif; ?>
</div>

</body>
</html>
