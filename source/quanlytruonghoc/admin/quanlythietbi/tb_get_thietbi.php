<?php
include('../config.php');
header('Content-Type: application/json');

if (isset($_GET['loai_id'])) {
    $loai_id = $_GET['loai_id'];

    $stmt = $conn->prepare("SELECT id, tenthietbi FROM thietbi WHERE loaithietbi_id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Lỗi truy vấn']);
        exit;
    }

    $stmt->bind_param("i", $loai_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Thiếu tham số loai_id']);
}
?>
