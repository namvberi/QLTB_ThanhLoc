<?php
include '../config.php';

// Nhận dữ liệu JSON gửi từ fetch/ajax
$data = json_decode(file_get_contents("php://input"), true);
$don_id = $data['don_id'] ?? null;

if ($don_id) {
    // Lấy dữ liệu đơn từ bảng donthietbi
    $sql = "SELECT * FROM donthietbi WHERE id = $don_id";
    $result = mysqli_query($conn, $sql);
    $don = mysqli_fetch_assoc($result);

    if (!$don) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn.']);
        exit;
    }

    // Lấy các thông tin từ bảng donthietbi
    $masothietbi_id = $don['masothietbi_id'];  // Lấy masothietbi_id từ bảng donthietbi
    $thietbi_id = $don['thietbi_id'];
    $soluong = $don['soluong'];
    $ghichu = $don['ghichu'];
    $users_id = $don['users_id'];

    // Kiểm tra nếu masothietbi_id hợp lệ
    if (!$masothietbi_id) {
        echo json_encode(['status' => 'error', 'message' => 'Không có mã số thiết bị trong đơn.']);
        exit;
    }

    // Lấy danh sách thiết bị đang "Sẵn sàng" từ bảng masothietbi
    $sql_maso = "SELECT id, masothietbi FROM masothietbi WHERE id = $masothietbi_id AND trangthai = 'Sẵn sàng' LIMIT $soluong";
    $res_maso = mysqli_query($conn, $sql_maso);

    if (mysqli_num_rows($res_maso) == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Không đủ thiết bị để bảo trì.']);
        exit;
    }

    // Thêm từng thiết bị vào bảng baotri
    while ($row_maso = mysqli_fetch_assoc($res_maso)) {
        $maso_id = $row_maso['id'];

        // Thêm thông tin vào bảng baotri
        $sql_insert = "INSERT INTO baotri (thietbi_id, masothietbi_id, ghichu, ngaysua, tinhtrang) 
                       VALUES ($thietbi_id, $maso_id, '$ghichu', NOW(), 'Đang bảo trì')";

        if (!mysqli_query($conn, $sql_insert)) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi thêm vào bảng baotri: ' . mysqli_error($conn)]);
            exit;
        }

        // Cập nhật trạng thái masothietbi thành 'Đang bảo trì'
        mysqli_query($conn, "UPDATE masothietbi SET trangthai = 'Đang bảo trì' WHERE id = $maso_id");
    }

    // Cập nhật trạng thái đơn thành 'Đã duyệt'
    mysqli_query($conn, "UPDATE donthietbi SET trangthai = 'Đã duyệt' WHERE id = $don_id");

    // Trả về kết quả cho AJAX
    echo json_encode([
        'status' => 'success',
        'message' => 'Duyệt đơn thành công!',
        'don_id' => $don_id, // trả về ID đơn
        'new_status' => 'Đã duyệt' // trả về trạng thái mới
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy ID đơn.']);
}
?>
