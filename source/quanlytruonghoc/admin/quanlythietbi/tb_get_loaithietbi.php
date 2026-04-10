<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../config.php');
header('Content-Type: application/json');

if (isset($_GET['monhoc_id'])) {
    $monhoc_id = $_GET['monhoc_id'];

    // Kiểm tra xem có kết nối được DB không
    if (!$conn) {
        echo json_encode(['error' => 'Lỗi kết nối CSDL']);
        exit;
    }

    // Thử in debug ID
    // file_put_contents("debug.txt", "monhoc_id = $monhoc_id\n", FILE_APPEND);

    $stmt = $conn->prepare("SELECT id, tenloaithietbi FROM loaithietbi WHERE monhoc_id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Lỗi chuẩn bị câu truy vấn']);
        exit;
    }

    $stmt->bind_param("i", $monhoc_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Thiếu tham số monhoc_id']);
}
