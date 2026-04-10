<?php
include('../config.php');

if (isset($_GET['id'])) {
    $nguoimuon_id = intval($_GET['id']);  // Đảm bảo an toàn

    // Lấy danh sách masothietbi_id từ bảng chitietmuon
    $query = mysqli_query($conn, "SELECT masothietbi_id FROM chitietmuon WHERE nguoimuon_id = $nguoimuon_id");

    $device_ids = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $device_ids[] = $row['masothietbi_id'];
    }

    if (!empty($device_ids)) {
        $ids_str = implode(',', $device_ids);

        // Cập nhật trạng thái từng thiết bị về "Sẵn sàng"
        $update = mysqli_query($conn, "UPDATE masothietbi SET trangthai = 'Sẵn sàng' WHERE id IN ($ids_str)");

        if (!$update) {
            echo "Lỗi khi cập nhật trạng thái thiết bị: " . mysqli_error($conn);
            exit();
        }
    }

    // Cập nhật trạng thái mượn trong bảng nguoimuon (nếu bạn có cột 'trangthai')
    mysqli_query($conn, "UPDATE nguoimuon SET trangthai = 'Đã trả' WHERE id = $nguoimuon_id");
}

header('Location: quanlynguoimuon.php');
exit();
?>
