<?php
include("../config.php");

// Nếu chưa khai báo $where_sql thì gán rỗng để tránh lỗi
$where_sql = isset($where_sql) ? $where_sql : "";

// Truy vấn JOIN các bảng liên quan
$query_baotri = "
SELECT 
    baotri.*, 
    thietbi.tenthietbi,
    thietbi.giatien,
    masothietbi.masothietbi,
    loaithietbi.tenloaithietbi,
    monhoc.tenmonhoc,
    khoi.ten_khoi
FROM baotri
JOIN masothietbi ON baotri.masothietbi_id = masothietbi.id
JOIN thietbi ON masothietbi.thietbi_id = thietbi.id
JOIN loaithietbi ON thietbi.loaithietbi_id = loaithietbi.id
JOIN monhoc ON thietbi.monhoc_id = monhoc.id
JOIN khoi ON loaithietbi.khoi_id = khoi.id
$where_sql
";

$result_baotri = $conn->query($query_baotri);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách bảo trì</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 85%;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color:rgb(18, 19, 20);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #e0e0e0;
            font-size: 16px;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
        .no-data {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Danh sách bảo trì thiết bị</h2>
        <table>
            <thead>
                <tr>
                    <th>Khối</th>
                    <th>Môn học</th>
                    <th>Loại thiết bị</th>
                    <th>Tên thiết bị</th>
                    <th>Mã số</th>
                    <th>Ngày sửa</th>
                    <th>Địa chỉ</th>
                    <th>Kiến sửa</th>
                    <th>Người sửa</th>
                    <th>Chi phí</th>
                    <th>Ghi chú</th>
                    <th>Tình trạng</th>
                </tr>    
            </thead>
            <tbody>
                <?php if ($result_baotri && $result_baotri->num_rows > 0) { 
                    while ($row_baotri = mysqli_fetch_assoc($result_baotri)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row_baotri['ten_khoi']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['tenmonhoc']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['tenloaithietbi']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['tenthietbi']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['masothietbi']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['ngaysua']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['diachi']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['kiensua']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['nguoisua']); ?></td>
                    <td><?php echo number_format($row_baotri['chiphi'], 0, ',', '.') . ' đ'; ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['ghichu']); ?></td>
                    <td><?php echo htmlspecialchars($row_baotri['tinhtrang']); ?></td>
                </tr>
                <?php }} else { ?>
                    <tr><td colspan="12" class="no-data">Không có dữ liệu bảo trì.</td></tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="quanlybaotri.php" class="btn-back">← Quay lại danh sách</a>
    </div>
</body>
</html>
