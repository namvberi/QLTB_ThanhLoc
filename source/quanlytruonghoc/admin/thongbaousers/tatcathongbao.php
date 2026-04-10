<?php
include '../config.php';

// Kiểm tra xem người dùng đã đăng nhập và là giáo viên hay chưa
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Giáo viên') {
    die("Bạn không có quyền.");
}

// Lấy ID người dùng từ session
$usersId = $_SESSION['users_id'];

// Lấy tất cả đơn của giáo viên và join với bảng thietbi để lấy tên thiết bị
$sql = "SELECT donthietbi.*, thietbi.tenthietbi, masothietbi.masothietbi
FROM donthietbi 
INNER JOIN thietbi ON donthietbi.thietbi_id = thietbi.id
LEFT JOIN masothietbi ON donthietbi.masothietbi_id = masothietbi.id
WHERE donthietbi.users_id = ? 
ORDER BY donthietbi.ngaygui DESC";
    
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tất cả đơn của bạn</title>
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
    <h2>Danh sách đơn của bạn</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($don = $result->fetch_assoc()): ?>
            <div class="notif-item">
                <p>
                    <strong>Loại đơn:</strong> <?php echo htmlspecialchars($don['loai_don']); ?><br>
                    <strong>Thiết bị:</strong> <?php echo htmlspecialchars($don['tenthietbi']); ?><br>
                    <?php if ($don['loai_don'] === 'Bảo trì thiết bị'): ?>
                        <strong>Mã số thiết bị:</strong> <?php echo htmlspecialchars($don['masothietbi']); ?><br>
                    <?php endif; ?>
                </p>
                <small>Ngày gửi: <?php echo $don['ngaygui']; ?> | Trạng thái: <?php echo $don['trangthai']; ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Không có đơn nào.</p>
    <?php endif; ?>
    <a href="/quanlytruonghoc/admin/dashboardgiaovien.php" class="back-button">Quay lại</a>
</div>
</body>
</html>
