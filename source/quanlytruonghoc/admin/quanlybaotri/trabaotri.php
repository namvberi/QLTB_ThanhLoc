<?php
ob_start();
// Đảm bảo không có bất kỳ nội dung nào được in ra trước đây
include('../config.php');
include('../navbar.php');

// Kiểm tra nếu có ID bảo trì được truyền vào
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cập nhật trạng thái "Đã xong" trong bảng baotri
    $query_update_baotri = "UPDATE baotri SET tinhtrang = 'Hoàn thành' WHERE id = '$id'";

    // Cập nhật trạng thái thiết bị trong bảng masothietbi
    $query_update_masothietbi = "UPDATE masothietbi SET trangthai = 'Sẵn sàng' WHERE id = (SELECT masothietbi_id FROM baotri WHERE id = '$id' LIMIT 1)";

    // Thực thi các câu lệnh cập nhật
    $result_baotri = mysqli_query($conn, $query_update_baotri);
    $result_masothietbi = mysqli_query($conn, $query_update_masothietbi);

    if ($result_baotri && $result_masothietbi) {
        // Nếu cập nhật thành công, chuyển hướng về trang quản lý bảo trì
        header('Location: quanlybaotri.php?success=1');
        exit;
    } else {
        // Nếu có lỗi, hiển thị thông báo
        echo "<script>alert('Có lỗi xảy ra khi cập nhật trạng thái.'); window.location.href = 'quanlybaotri.php';</script>";
    }
} else {
    // Nếu không có ID, chuyển hướng về trang quản lý bảo trì
    header('Location: quanlybaotri.php');
    exit;
}

ob_end_flush();
?>
