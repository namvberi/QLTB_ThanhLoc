<?php
include '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users_id = $_SESSION['users_id'];
    $khoi = $_POST['khoi'];
    $monhoc = $_POST['monhoc'];
    $loaithietbi = $_POST['loaithietbi'];
    $thietbi = $_POST['thietbi'];
    $masothietbi = $_POST['masothietbi'];
    $ghichu = $_POST['ghichu'];
    $loai_don = "Bảo trì thiết bị";
    $trangthai = "Chờ duyệt";
    $ngaygui = date('Y-m-d');
    $soluong = 1; // BẢO TRÌ 1 mã số => số lượng luôn là 1

    // Lấy tên người dùng
    $res_user = mysqli_query($conn, "SELECT tennguoidung FROM users WHERE id = $users_id");
    $row_user = mysqli_fetch_assoc($res_user);
    $tennguoidung = $row_user ? $row_user['tennguoidung'] : "Không rõ";

    // Lấy tên thiết bị
    $res_tb = mysqli_query($conn, "SELECT tenthietbi FROM thietbi WHERE id = $thietbi");
    $row_tb = mysqli_fetch_assoc($res_tb);
    $tenthietbi = $row_tb ? $row_tb['tenthietbi'] : "Không rõ";

    // Chuẩn bị câu lệnh SQL để chèn đơn bảo trì
    $sql = "INSERT INTO donthietbi (users_id, loai_don, thietbi_id, masothietbi_id, ghichu, trangthai, ngaygui, soluong) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    // Gán tham số vào câu lệnh SQL
    mysqli_stmt_bind_param($stmt, "isiisssi", $users_id, $loai_don, $thietbi, $masothietbi, $ghichu, $trangthai, $ngaygui, $soluong);

    // Thực thi câu lệnh
    if (mysqli_stmt_execute($stmt)) {
        $donthietbi_id = mysqli_insert_id($conn);  // Lấy ID đơn vừa tạo

        // Gửi thông báo đến admin kèm donthietbi_id
        $noidung = "Giáo viên $tennguoidung đã gửi đơn bảo trì thiết bị $tenthietbi.";
        
        // Sử dụng câu lệnh SQL an toàn hơn để tránh SQL Injection
        $insert_tb = $conn->prepare("INSERT INTO thongbao (users_id, noidung, trangthai, donthietbi_id) 
                                     VALUES (?, ?, 'chưa đọc', ?)");
        $insert_tb->bind_param("isi", $users_id, $noidung, $donthietbi_id);
        $insert_tb->execute();
        
        // Thông báo thành công và chuyển hướng
        echo "<script>alert('Gửi đơn thành công!'); window.location.href='guidon.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>
