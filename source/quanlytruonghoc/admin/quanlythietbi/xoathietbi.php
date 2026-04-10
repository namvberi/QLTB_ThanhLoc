<?php
include('../config.php');

if (isset($_GET['id'])) {
    $thietbi_id = $_GET['id'];

    // Xóa các mã số thiết bị liên quan
    $query_delete_masothietbi = "DELETE FROM masothietbi WHERE thietbi_id = '$thietbi_id'";
    mysqli_query($conn, $query_delete_masothietbi);

    // Xóa thiết bị khỏi bảng kho
    $query_delete_kho = "DELETE FROM kho WHERE thietbi_id = '$thietbi_id'";
    mysqli_query($conn, $query_delete_kho);

    // Xóa thiết bị chính
    $query_delete_thietbi = "DELETE FROM thietbi WHERE id = '$thietbi_id'";
    if (mysqli_query($conn, $query_delete_thietbi)) {
        echo "<script>alert('Xóa thiết bị thành công!'); window.location='quanlythietbi.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa thiết bị: " . mysqli_error($conn) . "'); window.location='quanlythietbi.php';</script>";
    }
} else {
    echo "<script>alert('ID thiết bị không hợp lệ!'); window.location='quanlythietbi.php';</script>";
}
?>
