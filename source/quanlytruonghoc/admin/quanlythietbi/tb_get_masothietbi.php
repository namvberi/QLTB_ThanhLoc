<?php
include('../config.php');
header('Content-Type: application/json');

if (isset($_GET['thietbi_id'])) {
    $thietbi_id = $_GET['thietbi_id'];
    // Truy vấn để lấy mã số thiết bị
    $stmt = $conn->prepare("SELECT id, masothietbi FROM masothietbi WHERE thietbi_id = ? AND tinhtrang != 'Đang bảo trì'");
    if (!$stmt) {
        echo json_encode(['error' => 'Lỗi truy vấn']);
        exit;
    }

    $stmt->bind_param("i", $thietbi_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Thiếu tham số thietbi_id']);
}
?>
