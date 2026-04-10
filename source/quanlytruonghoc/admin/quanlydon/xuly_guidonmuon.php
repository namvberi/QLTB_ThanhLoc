<?php
session_start();
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Kiểm tra xem người dùng đã đăng nhập chưa
  if (!isset($_SESSION['users_id'])) {
    echo "<script>alert('Bạn chưa đăng nhập!'); window.location.href='../login.php';</script>";
    exit;
  }

  $users_id = intval($_SESSION['users_id']); // Lấy từ session
  $thietbi_id = intval($_POST['thietbi_id']);
  $soluong = intval($_POST['soluong']);
  $ngaymuon = $_POST['ngaymuon'];
  $ngaytra = $_POST['ngaytra'];
  $ghichu = mysqli_real_escape_string($conn, $_POST['ghichu']);
  $ngaygui = date('Y-m-d');

  $res_user = mysqli_query($conn, "SELECT tennguoidung FROM users WHERE id = $users_id");
  $row_user = mysqli_fetch_assoc($res_user);
  $tennguoidung = $row_user ? $row_user['tennguoidung'] : "Không rõ";

  $res_tb = mysqli_query($conn, "SELECT tenthietbi FROM thietbi WHERE id = $thietbi_id");
  $row_tb = mysqli_fetch_assoc($res_tb);
  $tenthietbi = $row_tb ? $row_tb['tenthietbi'] : "Không rõ";

  $sql = "INSERT INTO donthietbi (users_id, thietbi_id, soluong, ngaymuon, ngaytra, ghichu, loai_don, trangthai)
          VALUES ($users_id, $thietbi_id, $soluong, '$ngaymuon', '$ngaytra', '$ghichu', 'Mượn thiết bị', 'Chờ duyệt')";

if (mysqli_query($conn, $sql)) {
  $donthietbi_id = mysqli_insert_id($conn);  // Lấy ID đơn vừa tạo

  // Gửi thông báo đến admin kèm donthietbi_id
  $noidung = "Giáo viên $tennguoidung đã gửi đơn mượn thiết bị $tenthietbi.";
  $insert_tb = "INSERT INTO thongbao (users_id, noidung, trangthai, donthietbi_id) 
                VALUES ($users_id, '$noidung', 'chưa đọc', $donthietbi_id)";
  mysqli_query($conn, $insert_tb);

  echo "<script>alert('Gửi đơn thành công!'); window.location.href='guidon.php';</script>";
} else {
  echo "Lỗi: " . mysqli_error($conn);
}
}
?>
