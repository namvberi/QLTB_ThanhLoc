<?php
include('../config.php');

// Kiểm tra xem có nhận ID nào để xóa không
if (isset($_POST['selected_ids'])) {
    $selected_ids = $_POST['selected_ids'];

    if (!empty($selected_ids)) {
        $ids = implode(',', array_map('intval', $selected_ids));

        // Chỉ cần xóa nguoimuon, các bảng liên quan sẽ tự xóa nhờ ON DELETE CASCADE
        $query = "DELETE FROM nguoimuon WHERE id IN ($ids)";

        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi xóa người mượn: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không có mục nào được chọn']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không nhận được dữ liệu']);
}
?>
