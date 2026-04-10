<?php
session_start();
include('../config.php');

// Kiểm tra xem người dùng đã đăng nhập và lấy role của họ
$role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Lấy role từ session

// Xác định URL của nút quay lại tùy theo role
if ($role == 'Giáo viên') {
    $back_url = '/quanlytruonghoc/admin/dashboardgiaovien.php'; // Đảm bảo đường dẫn chính xác
} elseif ($role == 'admin') {
    $back_url = '/quanlytruonghoc/admin/quanlydon/quanlydon.php'; // Đảm bảo đường dẫn chính xác
} else {
    $back_url = '/quanlytruonghoc/login.php'; // Nếu không xác định role, quay lại trang login
}
?>
<style>
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
</style>
<body style="margin: 0; padding: 0;">
    <div style="height: 70vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <h2 style="margin-bottom: 30px;">Chọn Loại Đơn Thiết Bị</h2>

        <div style="display: flex; gap: 20px;">
            <a href="guidonmuon.php?loai_don=Mượn thiết bị">
                <button style="padding: 20px 40px; font-size: 18px; background-color: #4CAF50; color: white; border: none; border-radius: 10px; cursor: pointer;">
                    Thêm Mượn Thiết Bị
                </button>
            </a>

            <a href="guidonbaotri.php?loai_don=bảo trì thiết bị">
                <button style="padding: 20px 40px; font-size: 18px; background-color: #f44336; color: white; border: none; border-radius: 10px; cursor: pointer;">
                    Thêm Bảo Trì Thiết Bị
                </button>
            </a>
        </div>

        <!-- Nút quay lại -->
        <a class="btn-back" href="<?php echo htmlspecialchars($back_url); ?>">
        ← Quay lại danh sách
        </a>
    </div>
</body>
