<?php
include('../config.php');

if (isset($_GET['id'])) {
    $baotri_id = intval($_GET['id']);

    // Cập nhật trạng thái bảo trì sang 'Hỏng'
    $updateBaotri = "UPDATE baotri SET tinhtrang = 'Hỏng' WHERE id = $baotri_id";
    if (mysqli_query($conn, $updateBaotri)) {
        // Nếu cần, bạn có thể thêm cập nhật bảng masothietbi ở đây:
        $getMaso = mysqli_fetch_assoc(mysqli_query($conn, "SELECT masothietbi_id FROM baotri WHERE id = $baotri_id"));
        $masothietbi_id = $getMaso['masothietbi_id'];
        mysqli_query($conn, "UPDATE masothietbi SET tinhtrang = 'Hỏng' WHERE id = $masothietbi_id");
        mysqli_query($conn, "UPDATE masothietbi SET trangthai = 'Hỏng' WHERE id = $masothietbi_id");


        echo "<script>alert('Đã cập nhật tình trạng thành Hỏng!'); window.location.href='quanlybaotri.php';</script>";
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
} else {
    echo "Không có ID được cung cấp.";
}
?>
