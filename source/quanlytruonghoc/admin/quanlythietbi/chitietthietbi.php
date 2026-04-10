<?php
include('../config.php');
include('../navbar.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Thiếu ID thiết bị.";
    exit;
}

$thietbi_id = mysqli_real_escape_string($conn, $_GET['id']);

// Lấy thông tin thiết bị
$query_info = "SELECT thietbi.tenthietbi, monhoc.tenmonhoc, loaithietbi.tenloaithietbi 
               FROM thietbi 
               JOIN monhoc ON thietbi.monhoc_id = monhoc.id 
               JOIN loaithietbi ON thietbi.loaithietbi_id = loaithietbi.id 
               WHERE thietbi.id = '$thietbi_id'";
$info_result = mysqli_query($conn, $query_info);
$info = mysqli_fetch_assoc($info_result);

// Lấy danh sách mã số thiết bị
$query_maso = "SELECT masothietbi.masothietbi, masothietbi.tinhtrang, masothietbi.trangthai 
               FROM masothietbi 
               WHERE thietbi_id = '$thietbi_id'";
$result_maso = mysqli_query($conn, $query_maso);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Thiết Bị</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin-top: 100px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: white;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #007bff;
            color: white;
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

        /* Màu nền cho trạng thái */
        .status-ready { background-color: #d4edda; color: #155724; }
        .status-borrowed { background-color: #fff3cd; color: #856404; }
        .status-maintenance { background-color: #cce5ff; color: #004085; }
        .status-broken { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Chi Tiết Thiết Bị</h2>
        <h3><?php echo $info['tenmonhoc']; ?> -  <?php echo $info['tenloaithietbi']; ?> - <?php echo $info['tenthietbi']; ?> </h3>

        <table>
            <thead>
                <tr>
                    <th>Mã Số Thiết Bị</th>
                    <th>Tình Trạng</th>
                    <th>Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_maso) > 0) {
                    while ($row = mysqli_fetch_assoc($result_maso)) {
                        // Định dạng trạng thái
                        $status_class = "";
                        $status_icon = "";

                        switch (strtolower($row['trangthai'])) {
                            case 'sẵn sàng':
                                $status_class = "status-ready";
                                $status_icon = "<i class='fas fa-check-circle'></i>";
                                break;
                            case 'đang mượn':
                                $status_class = "status-borrowed";
                                $status_icon = "<i class='fas fa-rotate'></i>";
                                break;
                            case 'đang bảo trì':
                                $status_class = "status-maintenance";
                                $status_icon = "<i class='fas fa-screwdriver-wrench'></i>";
                                break;
                            case 'hỏng':
                                $status_class = "status-broken";
                                $status_icon = "<i class='fas fa-times-circle'></i>";
                                break;
                            default:
                                $status_class = "";
                                $status_icon = "";
                        }

                        echo "<tr>";
                        echo "<td>{$row['masothietbi']}</td>";
                        echo "<td>{$row['tinhtrang']}</td>";
                        echo "<td class='$status_class'>{$status_icon} {$row['trangthai']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Không có mã số thiết bị nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="quanlythietbi.php" class="btn-back">← Quay lại danh sách</a>
    </div>
</body>
</html>
