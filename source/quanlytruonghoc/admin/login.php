<?php
session_start();
include 'config.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (isset($_SESSION['users_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        // Nếu đã đăng nhập và là admin, chuyển hướng đến trang quản lý admin
        header("Location: /quanlytruonghoc/admin/dashboardadmin.php");
        exit();
    } elseif ($_SESSION['role'] === 'Giáo viên') {
        // Nếu đã đăng nhập và là giáo viên, chuyển hướng đến trang quản lý giáo viên
        header("Location: /quanlytruonghoc/admin/dashboardgiaovien.php");
        exit();
    }
}

// Kiểm tra nếu người dùng gửi form đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tendangnhap']) && isset($_POST['matkhau'])) {
        // Lấy dữ liệu người dùng nhập vào
        $tendangnhap = mysqli_real_escape_string($conn, $_POST['tendangnhap']);
        $matkhau = mysqli_real_escape_string($conn, $_POST['matkhau']);

        // Kiểm tra thông tin đăng nhập
        $query = "SELECT * FROM users WHERE tendangnhap = '$tendangnhap' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $users = mysqli_fetch_assoc($result);

            // So sánh mật khẩu trực tiếp
            if ($matkhau == $users['matkhau']) {
                // Đăng nhập thành công, lưu thông tin người dùng và quyền vào session
                $_SESSION['users_id'] = $users['id'];
                $_SESSION['tendangnhap'] = $users['tendangnhap'];
                $_SESSION['role'] = $users['role']; // Giả sử bảng 'users' có cột 'role'

                // Chuyển hướng đến trang quản lý theo quyền
                if ($_SESSION['role'] == 'admin') {
                    header("Location: /quanlytruonghoc/admin/dashboardadmin.php");
                    exit();
                } elseif ($_SESSION['role'] == 'Giáo viên') {
                    header("Location: /quanlytruonghoc/admin/dashboardgiaovien.php");
                    exit();
                }
            } else {
                $error_message = "Mật khẩu không chính xác.";
            }
        } else {
            $error_message = "Tên đăng nhập không tồn tại.";
        }
    } else {
        $error_message = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.";
    }
}
?>

<!-- Hiển thị lỗi nếu có -->
<?php if (isset($error_message)): ?>
    <div class="error-message"><?= $error_message; ?></div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('/quanlytruonghoc/admin/images/thcs2.jpg') no-repeat center center fixed; /* Hình nền */
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 350px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .login-container:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .login-container h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .login-container input:focus {
            border-color: #007bff;
            outline: none;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-sizing: border-box;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        /* Định dạng mobile */
        @media (max-width: 400px) {
            .login-container {
                width: 90%;
            }
        }
        .show-password {
    display: inline-flex;
    align-items: center;
    font-size: 14px;
    color: #555;
    margin-top: -10px; /* Để điều chỉnh vị trí của checkbox và label */
}

.show-password input[type="checkbox"] {
    margin-right: 5px; /* Giảm khoảng cách giữa checkbox và text */
    accent-color: #007bff;
    width: 16px;
    height: 16px;
    cursor: pointer;
}

    </style>
</head>
<body>

<div class="login-container">
    <h2>Đăng Nhập</h2>
    <form method="POST" action="login.php">
        <input type="text" name="tendangnhap" placeholder="Tên đăng nhập" required>
        <input type="password" name="matkhau" placeholder="Mật khẩu" id="password" required>
        <div class="show-password">
    <input type="checkbox" id="showPass" onclick="togglePassword()">
    <label for="showPass">Hiện mật khẩu</label>
</div>
        <button type="submit">Đăng Nhập</button>
    </form>

    <?php if (isset($error_message)): ?>
        <div class="error-message"><?= $error_message; ?></div>
    <?php endif; ?>
</div>
<script>
function togglePassword() {
    var x = document.getElementById("password");
    x.type = (x.type === "password") ? "text" : "password";
}
</script>
</body>
</html>
