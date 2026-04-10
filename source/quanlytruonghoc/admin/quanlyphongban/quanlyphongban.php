<?php
ob_start();
session_start();
// Kết nối cơ sở dữ liệu
include('../config.php');
include('../navbar.php');
// Xử lý xóa phòng
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // 1. Cập nhật tất cả thiết bị của phòng đó về NULL trước
    $conn->query("UPDATE thietbi SET phongban_id = NULL WHERE phongban_id = $id");
    
    // 2. Sau đó mới xóa phòng
    $conn->query("DELETE FROM phongban WHERE id = $id");
    
    // 3. Sau khi xóa, chuyển hướng lại tránh lỗi reload
    header("Location: /quanlytruonghoc/admin/quanlyphongban/quanlyphongban.php");
    exit();
}

// Khởi tạo điều kiện lọc
$where_clause = '';

// Xử lý tìm kiếm
if (!empty($_GET['search_tenphong'])) {
    $search_tenphong = mysqli_real_escape_string($conn, $_GET['search_tenphong']);
    $where_clause .= " AND phongban.tenphong LIKE '%$search_tenphong%'";
}

if (!empty($_GET['search_nguoiphutrach'])) {
    $search_nguoiphutrach = mysqli_real_escape_string($conn, $_GET['search_nguoiphutrach']);
    $where_clause .= " AND phongban.nguoiphutrach LIKE '%$search_nguoiphutrach%'";
}

// Truy vấn danh sách phòng ban
$query_phongban = "
    SELECT 
        phongban.*, 
        COUNT(DISTINCT masothietbi.id) AS so_luong_thietbi,
        SUM(CASE WHEN masothietbi.trangthai = 'Sẵn sàng' THEN 1 ELSE 0 END) AS soluong_con_lai
    FROM phongban
    LEFT JOIN masothietbi ON phongban.id = masothietbi.phongban_id
    LEFT JOIN thietbi ON masothietbi.thietbi_id = thietbi.id
    WHERE 1=1
    $where_clause
    GROUP BY phongban.id
";


$result_phongban = mysqli_query($conn, $query_phongban);

if (!$result_phongban) {
    echo "Lỗi truy vấn: " . mysqli_error($conn);
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Phòng Ban</title>
    <style>
        body {
            margin-top: 100px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding-top: 30px;
        }

        h1 {
            color: #333;
            font-weight: bold;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
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
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s, transform 0.2s;
            border: 1px solid #333;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        .btn:active {
            background-color: #004080;
            transform: translateY(1px);
        }

        .add-btn-container {
            text-align: right;
        }

        .action-buttons a {
            margin-right: 10px;
        }

        .action-buttons .btn-edit {
            background-color: #28a745;
        }

        .action-buttons .btn-edit:hover {
            background-color: #218838;
        }

        .action-buttons .btn-delete {
            background-color: #dc3545;
        }

        .action-buttons .btn-delete:hover {
            background-color: #c82333;
        }

        .filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-container input,
        .filter-container select {
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .filter-container .btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Quản Lý Phòng Ban</h1>

    <!-- Form tìm kiếm -->
    <div class="filter-container">
        <form method="GET" action="quanlyphongban.php" style="display: flex; flex-grow: 1;">
            <input type="text" name="search_tenphong" placeholder="Tìm kiếm tên phòng" value="<?php echo isset($_GET['search_tenphong']) ? htmlspecialchars($_GET['search_tenphong']) : ''; ?>" />
            <input type="text" name="search_nguoiphutrach" placeholder="Tìm kiếm người phụ trách" value="<?php echo isset($_GET['search_nguoiphutrach']) ? htmlspecialchars($_GET['search_nguoiphutrach']) : ''; ?>" />
            <button type="submit" class="btn">Tìm kiếm</button>
        </form>
        <div class="add-btn-container">
            <a href="themphong.php" class="btn">Thêm phòng</a>
        </div>
    </div>
    <!-- Bảng danh sách phòng -->
    <table>
        <thead>
            <tr>
                <th>Tên phòng</th>
                <th>Người phụ trách</th>
                <th>Số lượng thiết bị</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result_phongban)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['tenphong']); ?></td>
                    <td><?php echo htmlspecialchars($row['nguoiphutrach']); ?></td>
                    <td><?php echo (int)$row['so_luong_thietbi']; ?></td>
                    <td class="action-buttons">
                        <a href="themtbphong.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Thêm thiết bị</a>
                        <a href="chitiet_phongban.php?id=<?php echo $row['id']; ?>"  class="btn btn-view" style="background-color:#17a2b8;">Chi tiết</a>
                        <a href="quanlyphongban.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
?>

</body>
</html>
