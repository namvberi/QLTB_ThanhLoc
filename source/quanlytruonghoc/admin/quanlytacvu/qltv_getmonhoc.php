<?php 
// Kết nối cơ sở dữ liệu
include '../config.php';

// Kiểm tra nếu tham số 'khoi_id' tồn tại và là số
if (isset($_GET['khoi_id']) && is_numeric($_GET['khoi_id'])) {
    $khoi_id = intval($_GET['khoi_id']);

    // Truy vấn để lấy môn học theo khối (dựa vào cột khoi_id trong bảng monhoc)
    $query = "
        SELECT id, tenmonhoc
        FROM monhoc
        WHERE khoi_id = $khoi_id
    ";
    $result = mysqli_query($conn, $query);

    // Kiểm tra kết quả truy vấn
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<option value=''>Chọn môn học</option>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['id'] . "'>" . $row['tenmonhoc'] . "</option>";
        }
    } else {
        echo "<option value=''>Không có môn học</option>";
    }
} else {
    echo "<option value=''>Vui lòng chọn khối</option>";
}

// Đóng kết nối
mysqli_close($conn);
?>
