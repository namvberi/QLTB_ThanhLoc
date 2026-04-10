<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['selected_ids'])) {
        $selectedIds = $_POST['selected_ids'];

        // Chuẩn bị câu SQL động
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $sql = "DELETE FROM donthietbi WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        // Gán tham số kiểu `i` (integer) hoặc `s` (string) tùy cột id của bạn
        $types = str_repeat('i', count($selectedIds));
        $stmt->bind_param($types, ...$selectedIds);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể xóa dữ liệu']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không có ID nào được chọn']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ']);
}
?>
