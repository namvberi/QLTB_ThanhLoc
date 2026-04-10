<?php
include('../config.php');
header('Content-Type: application/json');

if (isset($_GET['khoi_id'])) {
    $khoi_id = $_GET['khoi_id'];

    // Sử dụng chuẩn bị truy vấn để tránh lỗi cú pháp hoặc injection
    $stmt = $conn->prepare("SELECT id, tenmonhoc FROM monhoc WHERE khoi_id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Lỗi chuẩn bị câu truy vấn']);
        exit;
    }
    
    $stmt->bind_param("i", $khoi_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu có kết quả trả về
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data); // Trả về danh sách môn học
    } else {
        echo json_encode(['error' => 'Không có môn học nào cho khối này']);
    }
    
    $stmt->close(); // Đóng truy vấn sau khi sử dụng
} else {
    echo json_encode(['error' => 'Thiếu tham số khoi_id']);
}
?>
