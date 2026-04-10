<?php
// Kết nối CSDL
include('../config.php');

// Lấy ID người dùng từ URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Truy vấn để lấy ảnh người dùng (nếu có) để xóa khỏi thư mục
    $getImageQuery = "SELECT image FROM users WHERE id = $id";
    $result = mysqli_query($conn, $getImageQuery);
    $user = mysqli_fetch_assoc($result);

    // Nếu có ảnh và file tồn tại, thì xóa
    if ($user && !empty($user['image']) && file_exists($user['image'])) {
        unlink($user['image']);
    }

    // Thực hiện xóa người dùng
    $sql = "DELETE FROM users WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Quay lại trang quản lý người dùng sau khi xóa
        header("Location: quanlynguoidung.php");
        exit();
    } else {
        echo "Lỗi khi xóa người dùng: " . mysqli_error($conn);
    }
} else {
    echo "Không tìm thấy ID người dùng cần xóa.";
}
?>
