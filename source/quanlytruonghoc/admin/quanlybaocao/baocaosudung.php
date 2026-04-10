<?php
session_start();
include '../config.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về trang login
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

$users_id = $_SESSION['users_id'];

$sql = "SELECT thietbi.tenthietbi, COUNT(*) as usage_count
        FROM nguoimuon 
        JOIN thietbi ON nguoimuon.thietbi_id = thietbi.id
        GROUP BY thietbi.tenthietbi";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Báo Cáo Sử Dụng Thiết Bị</title>
  <style>
    /* Style tương tự như trong trang quản lý báo cáo */
  </style>
</head>
<body>
<div class="container">
  <h2>Báo Cáo Sử Dụng Thiết Bị</h2>
  <table border="1">
    <tr>
      <th>Tên Thiết Bị</th>
      <th>Số Lần Mượn</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo $row['tenthietbi']; ?></td>
        <td><?php echo $row['usage_count']; ?></td>
      </tr>
    <?php } ?>
  </table>
  <a href="quanlybaocao.php" class="btn-back">← Quay lại danh sách báo cáo</a>
</div>
</body>
</html>
