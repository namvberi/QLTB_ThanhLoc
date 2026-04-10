<?php
include('../config.php'); // Kết nối tới cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy ID đơn và trạng thái mới từ yêu cầu AJAX
    $id = $_POST['id'];
    $trangthai = 'Đã từ chối'; // Trạng thái mới là "Đã từ chối"

    // Cập nhật trạng thái của đơn trong cơ sở dữ liệu
    $sql = "UPDATE donthietbi SET trangthai = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('si', $trangthai, $id); // Bind các tham số

        if ($stmt->execute()) {
            echo "Trạng thái đơn đã được cập nhật thành công.";
        } else {
            echo "Có lỗi khi cập nhật trạng thái.";
        }

        $stmt->close();
    } else {
        echo "Lỗi trong câu lệnh SQL.";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
