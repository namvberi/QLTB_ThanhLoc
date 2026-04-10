<?php
// Kết nối cơ sở dữ liệu
include('../config.php');
include('../navbar.php');

// Tạo điều kiện tìm kiếm
$where_conditions = [];

if (isset($_GET['tennguoidung']) && $_GET['tennguoidung'] != '') {
    $tennguoidung = mysqli_real_escape_string($conn, $_GET['tennguoidung']);
    $where_conditions[] = "users.tennguoidung LIKE '%$tennguoidung%'";
}

if (isset($_GET['role']) && $_GET['role'] != '') {
    $role = mysqli_real_escape_string($conn, $_GET['role']);
    $where_conditions[] = "users.role = '$role'";
}

// Tạo câu truy vấn SQL với các điều kiện tìm kiếm
$where_sql = '';
if (count($where_conditions) > 0) {
    $where_sql = ' WHERE ' . implode(' AND ', $where_conditions);
}

// Truy vấn lấy danh sách người dùng
$query_users = "SELECT * FROM users" . $where_sql;
$result_users = mysqli_query($conn, $query_users);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin-top: 100px;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding-top: 30px;
        }

        h1 {
            font-weight: bold;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #333;
            color: white;
        }

        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s, transform 0.2s;
            border: 1px solid #333;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn:active {
            background-color: #004080;
            transform: translateY(1px);
        }

        .filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .filter-container form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }

        .filter-container input,
        .filter-container select {
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .add-btn-container {
            text-align: right;
        }

        .btn-edit {
            background-color: #28a745;
        }

        .btn-edit:hover {
            background-color: #218838;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        img.avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quản Lý Người Dùng</h1>

        <div class="filter-container">
            <form class="search-form" method="GET" action="quanlynguoidung.php">
                <input type="text" name="tennguoidung" placeholder="Tìm kiếm tên người dùng" value="<?php echo isset($_GET['tennguoidung']) ? $_GET['tennguoidung'] : ''; ?>">

                <select name="role">
                    <option value="">Chọn vai trò</option>
                    <option value="admin" <?php echo (isset($_GET['role']) && $_GET['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?php echo (isset($_GET['role']) && $_GET['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                </select>

                <button type="submit" class="btn">Tìm kiếm</button>
            </form>

            <a href="themnguoidung.php" class="btn add-btn">Thêm Người Dùng</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tên Người Dùng</th>
                    <th>Tên Đăng Nhập</th>
                    <th>Mật Khẩu</th>
                    <th>Số Điện Thoại</th>
                    <th>Gmail</th>
                    <th>Ảnh Đại Diện</th>
                    <th>Quyền</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_user = mysqli_fetch_assoc($result_users)) { ?>
                    <tr>
                        <td><?php echo $row_user['tennguoidung']; ?></td>
                        <td><?php echo $row_user['tendangnhap']; ?></td>
                        <td><?php echo $row_user['matkhau']; ?></td>
                        <td><?php echo $row_user['SDT']; ?></td>
                        <td><?php echo $row_user['gmail']; ?></td>
                        <td>
                            <?php if (!empty($row_user['image']) && file_exists($row_user['image'])): ?>
                                <img src="<?php echo $row_user['image']; ?>" class="avatar">
                            <?php else: ?>
                                <img src="../uploads/default.png" class="avatar">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row_user['role']; ?></td>
                        <td>
                            <a href="suanguoidung.php?id=<?php echo $row_user['id']; ?>" class="btn btn-edit">Sửa</a>
                            <a href="xoanguoidung.php?id=<?php echo $row_user['id']; ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">Xóa</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
