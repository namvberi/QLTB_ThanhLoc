<?php
include '../config.php';

if (isset($_POST['masothietbi_id'])) {
    $masothietbi_id = $_POST['masothietbi_id'];

    // Lấy ID thiết bị từ bảng masothietbi để cập nhật kho
    $query = "SELECT thietbi_id FROM masothietbi WHERE id = '$masothietbi_id'";
    $result = mysqli_query($conn, $query);
    
    // Kiểm tra nếu thiết bị tồn tại trong bảng masothietbi
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $thietbi_id = $row['thietbi_id'];

        // Cập nhật tình trạng thiết bị trong bảng masothietbi sang "Sẵn sàng"
        $updateMasothietbi = mysqli_query($conn, "UPDATE masothietbi SET tinhtrang = 'Tốt' WHERE id = '$masothietbi_id'");

        // Cập nhật tình trạng thiết bị trong bảng baotri sang "Tốt"
        $updateBaotri = mysqli_query($conn, "UPDATE baotri SET tinhtrang = 'Đã hoàn thành' WHERE masothietbi_id = '$masothietbi_id' AND tinhtrang = 'Đang bảo trì'");

        // Cập nhật lại số lượng còn lại trong kho
        $updateKho = mysqli_query($conn, "UPDATE kho SET soluongconlai = soluongconlai + 1 WHERE thietbi_id = '$thietbi_id'");

        // Kiểm tra nếu tất cả các cập nhật thành công
        if ($updateMasothietbi && $updateBaotri && $updateKho) {
            echo 'success';
        } else {
            // Nếu có lỗi, trả về lỗi cụ thể
            echo 'error';
        }
    } else {
        // Nếu không tìm thấy thiết bị
        echo 'device_not_found';
    }
}
?>
