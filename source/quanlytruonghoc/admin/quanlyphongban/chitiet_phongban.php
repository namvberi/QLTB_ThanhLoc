<?php
include('../config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy id phòng ban từ URL
if (!isset($_GET['id'])) {
    echo "Thiếu ID phòng ban.";
    exit();
}
$phongban_id = intval($_GET['id']);

// Lấy thông tin phòng ban
$query_phongban = "SELECT * FROM phongban WHERE id = ?";
$stmt_phongban = $conn->prepare($query_phongban);
$stmt_phongban->bind_param("i", $phongban_id);
$stmt_phongban->execute();
$result_phongban = $stmt_phongban->get_result();

if ($result_phongban->num_rows == 0) {
    echo "Phòng ban không tồn tại.";
    exit();
}

$phongban = $result_phongban->fetch_assoc();

// Lấy danh sách thiết bị trong phòng ban
$query_thietbi = "
    SELECT 
        thietbi.*, 
        loaithietbi.tenloaithietbi, 
        monhoc.tenmonhoc,
        kho.soluong,
        masothietbi.id AS masothietbi_id
    FROM masothietbi
    LEFT JOIN thietbi ON masothietbi.thietbi_id = thietbi.id
    LEFT JOIN loaithietbi ON thietbi.loaithietbi_id = loaithietbi.id
    LEFT JOIN monhoc ON thietbi.monhoc_id = monhoc.id
    LEFT JOIN kho ON thietbi.id = kho.thietbi_id
    WHERE masothietbi.phongban_id = ?
    GROUP BY thietbi.id
";

$stmt_thietbi = $conn->prepare($query_thietbi);
$stmt_thietbi->bind_param("i", $phongban_id);
$stmt_thietbi->execute();
$result_thietbi = $stmt_thietbi->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết phòng ban: <?php echo htmlspecialchars($phongban['tenphong']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin-top: 80px;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
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
    <h1>Thiết bị trong phòng ban: <?php echo htmlspecialchars($phongban['tenphong']); ?></h1>

    <?php if ($result_thietbi->num_rows > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Thiết Bị</th>
                    <th>Loại Thiết Bị</th>
                    <th>Môn Học</th>
                    <th>Giá Tiền</th>
                    <th>Số Lượng</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tb = $result_thietbi->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $tb['id']; ?></td>
                        <td><?php echo htmlspecialchars($tb['tenthietbi']); ?></td>
                        <td><?php echo htmlspecialchars($tb['tenloaithietbi']); ?></td>
                        <td><?php echo htmlspecialchars($tb['tenmonhoc']); ?></td>
                        <td><?php echo number_format($tb['giatien'], 0, ',', '.') . " VNĐ"; ?></td>
                        <td><?php echo $tb['soluong']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="no-data">Chưa có thiết bị nào trong phòng này.</p>
    <?php } ?>

    <a href="quanlyphongban.php" class="btn-back">← Quay lại danh sách</a>
</div>
</body>
</html>
