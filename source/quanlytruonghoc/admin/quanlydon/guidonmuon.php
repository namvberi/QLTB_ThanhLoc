<?php
session_start();
include '../config.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về trang login
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

$users_id = $_SESSION['users_id'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm Người Mượn</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
        body {
            margin-top: 100px;
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
        }
        .container {
            width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
  </head>
<body>
<div class="container">
  <h2>Gửi đơn mượn thiết bị</h2>
  <form action="xuly_guidonmuon.php" method="POST">
    <label>Chọn thiết bị:</label><br>
      <select name="thietbi_id" required>
    <option value="">-- Chọn thiết bị --</option>
    <?php
    $res = mysqli_query($conn, "SELECT * FROM thietbi");
    while ($row = mysqli_fetch_assoc($res)) {
      echo "<option value='{$row['id']}'>{$row['tenthietbi']}</option>";
    }
    ?>
    </select><br><br>

  <label>Số lượng cần mượn:</label><br>
  <input type="number" name="soluong" min="1" required><br><br>

  <label>Ngày mượn:</label><br>
  <input type="date" name="ngaymuon" required><br><br>

  <label>Ngày trả:</label><br>
  <input type="date" name="ngaytra" required><br><br>

  <label>Ghi Chú:</label>
  <input type="text" name="ghichu" required>
  <button type="submit" value="Gửi đơn">Gửi đơn</button>

  <a href="guidon.php" class="btn-back">← Quay lại danh sách</a>
</form>
</div>
</body>
</html>
