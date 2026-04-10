<?php
include '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Bạn không có quyền.");
}

// Lấy tất cả thông báo kèm theo thông tin loai_don từ bảng donthietbi
$sql = "SELECT thongbao.*, donthietbi.loai_don FROM thongbao
        INNER JOIN donthietbi ON thongbao.donthietbi_id = donthietbi.id
        ORDER BY thongbao.thoigiangui DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tất cả thông báo</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
            padding: 30px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 800px;
            margin: auto;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
        }
        .notif-item {
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        .notif-item:last-child {
            border-bottom: none;
        }
        .notif-item small {
            color: gray;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Danh sách thông báo</h2>
    <?php while ($tb = mysqli_fetch_assoc($result)): ?>
        <div class="notif-item">
            <p>
                <?php
                // Kiểm tra loại đơn và hiển thị thông báo phù hợp
                if ($tb['loai_don'] == 'Bảo trì thiết bị') {
                    echo "Đơn bảo trì thiết bị của bạn đã được gửi.";
                } elseif ($tb['loai_don'] == 'Mượn thiết bị') {
                    echo "Đơn mượn thiết bị của bạn đã được gửi.";
                } else {
                    echo htmlspecialchars($tb['noidung']);
                }
                ?>
            </p>
            <small><?php echo $tb['thoigiangui']; ?> | Trạng thái: <?php echo $tb['trangthai']; ?></small>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
