<?php
include '../config.php';

// Nhận dữ liệu JSON gửi từ fetch/ajax
$data = json_decode(file_get_contents("php://input"), true);
$don_id = $data['don_id'] ?? null;

if ($don_id) {
    // Lấy dữ liệu đơn
    $sql = "SELECT * FROM donthietbi WHERE id = $don_id";
    $result = mysqli_query($conn, $sql);
    $don = mysqli_fetch_assoc($result);

    if (!$don) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn.']);
        exit;
    }

    $users_id = $don['users_id'];
    $thietbi_id = $don['thietbi_id'];
    $ngaymuon = $don['ngaymuon'];
    $ngaytra = $don['ngaytra'];
    $soluong = $don['soluong'];
    $ghichu = $don['ghichu'];

    // Lấy tên người dùng từ bảng users
    $sql_user = "SELECT tennguoidung FROM users WHERE id = $users_id";
    $res_user = mysqli_query($conn, $sql_user);
    $row_user = mysqli_fetch_assoc($res_user);
    $tennguoimuon = $row_user['tennguoidung'];

    // Chèn vào bảng nguoimuon
    $sql_insert = "INSERT INTO nguoimuon (tennguoimuon, thietbi_id, soluongmuon, ngaymuon, ngaytra, trangthai, users_id)
                   VALUES ('$tennguoimuon', $thietbi_id, $soluong, '$ngaymuon', '$ngaytra', 'Đang mượn', $users_id)";
    mysqli_query($conn, $sql_insert);
    $nguoimuon_id = mysqli_insert_id($conn);

    // Lấy danh sách mã số thiết bị sẵn sàng
    $sql_maso = "SELECT id FROM masothietbi WHERE thietbi_id = $thietbi_id AND trangthai = 'Sẵn sàng' LIMIT $soluong";
    $res_maso = mysqli_query($conn, $sql_maso);

    while ($row_maso = mysqli_fetch_assoc($res_maso)) {
        $maso_id = $row_maso['id'];

        // Cập nhật trạng thái sang "Đang mượn"
        mysqli_query($conn, "UPDATE masothietbi SET trangthai = 'Đang mượn' WHERE id = $maso_id");

        // Chèn vào bảng chitietmuon
        mysqli_query($conn, "INSERT INTO chitietmuon (nguoimuon_id, masothietbi_id) VALUES ($nguoimuon_id, $maso_id)");
    }

    // Cập nhật trạng thái đơn
    mysqli_query($conn, "UPDATE donthietbi SET trangthai = 'Đã duyệt' WHERE id = $don_id");
    mysqli_query($conn, "UPDATE thongbao SET tinhtrang = 'Đã duyệt' WHERE donthietbi_id = $don_id");
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy ID đơn.']);
}
?>
