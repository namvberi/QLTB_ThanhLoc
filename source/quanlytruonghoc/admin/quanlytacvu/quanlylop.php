<?php
ob_start();
include '../config.php';

// Xử lý xóa lớp học
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  mysqli_query($conn, "DELETE FROM lop WHERE id = $delete_id");
  header('Location: quanlylop.php');
  exit();
}

// Xử lý thêm lớp học
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tenlop']) && isset($_POST['khoi']) && isset($_POST['soluong'])) {
  $tenlop = $_POST['tenlop'];
  $khoi_id = $_POST['khoi'];
  $soluong = $_POST['soluong'];

  $check_sql = "SELECT COUNT(*) AS count FROM lop WHERE tenlop = '$tenlop'";
  $check_result = mysqli_query($conn, $check_sql);
  $check_row = mysqli_fetch_assoc($check_result);

  if ($check_row['count'] > 0) {
    echo "<script>alert('Tên lớp đã tồn tại!');</script>";
  } elseif ($soluong < 0) {
    echo "<script>alert('Sĩ số không được âm!');</script>";
  } else {
    $sql = "INSERT INTO lop (tenlop, khoi_id, soluong) VALUES ('$tenlop', '$khoi_id', '$soluong')";
    mysqli_query($conn, $sql);
    header('Location: quanlylop.php');
    exit();
  }
}

include '../navbar.php';

// Lấy danh sách khối và lớp
$sql = "SELECT lop.id, lop.tenlop, khoi.ten_khoi, lop.soluong 
        FROM lop 
        INNER JOIN khoi ON lop.khoi_id = khoi.id";
$result = mysqli_query($conn, $sql);
$khoi_result = mysqli_query($conn, "SELECT * FROM khoi");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Lớp học</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- Thêm Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
        font-family: 'Arial', sans-serif;
        margin-top: 100px;
        padding: 0;
        background-color: rgb(114, 170, 199);
    }

    .container {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-size: 24px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
    }

    h2 i {
        margin-right: 10px;
    }

    /* Nút thêm lớp học */
    .add-btn {
        background-color: #2ecc71;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        margin-bottom: 20px;
        display: block;
        width: 200px;
        margin-left: auto;
        margin-right: auto;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .add-btn:hover {
        background-color: #27ae60;
    }

    /* Bảng quản lý lớp học */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 15px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #34495e;
        color: white;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ecf0f1;
    }

    .delete-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 15px;
        background-color: #e74c3c;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s;
    }

    .delete-btn:hover {
        background-color: #c0392b;
        transform: scale(1.05);
    }

    .delete-btn:active {
        background-color: #e74c3c;
        transform: scale(0.98);
    }

    /* Thêm một số khoảng cách cho các mục */
    .container > * {
        margin-bottom: 15px;
    }

    /* Form thêm lớp học */
    form#addClassForm {
        display: none;
        margin-top: 20px;
        text-align: center;
    }

    .form-group {
        display: inline-flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        width: 100%;
    }

    .form-group label {
        width: 30%;
        text-align: right;
        margin-right: 10px;
    }

    .form-group input,
    .form-group select {
        width: 65%;
        padding: 8px;
    }

    form#addClassForm button {
        padding: 8px 16px;
        background-color: #3498db;
        border: none;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    form#addClassForm button:hover {
        background-color: #2980b9;
    }
  </style>
  <script>
    function toggleForm() {
      var form = document.getElementById('addClassForm');
      form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
  </script>
</head>
<body>

<div class="container">
  <h2><i class="fas fa-chalkboard-teacher"></i> Quản lý Lớp học</h2>

  <button class="add-btn" onclick="toggleForm()">➕ Thêm lớp học</button>

  <!-- Form thêm lớp học -->
  <form method="POST" id="addClassForm">
    <div class="form-group">
      <label for="khoi">Chọn khối</label>
      <select name="khoi" required>
        <option value="">Chọn khối</option>
        <?php while ($khoi = mysqli_fetch_assoc($khoi_result)): ?>
          <option value="<?= $khoi['id'] ?>"><?= $khoi['ten_khoi'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="tenlop">Tên lớp</label>
      <input type="text" name="tenlop" placeholder="Nhập tên lớp" required>
    </div>

    <div class="form-group">
      <label for="soluong">Sĩ số</label>
      <input type="number" name="soluong" placeholder="Nhập sĩ số" required>
    </div>

    <button type="submit">Thêm lớp</button>
  </form>

  <!-- Bảng danh sách lớp học -->
  <table>
    <thead>
      <tr>
        <th>Tên lớp</th>
        <th>Khối</th>
        <th>Sĩ số</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($row['tenlop']) ?></td>
          <td><?= htmlspecialchars($row['ten_khoi']) ?></td>
          <td><?= htmlspecialchars($row['soluong']) ?></td>
          <td>
            <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Bạn có chắc muốn xóa?')">🗑️ Xóa</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>

<?php ob_end_flush(); ?>
