<?php
session_start();
include '../config.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về trang login
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

$users_id = $_SESSION['users_id'];

// Khởi tạo các giá trị mặc định cho bộ lọc
$tenthietbi_filter = '';
$tinhtrang_filter = '';
$monhoc_filter = '';
$khoi_filter = '';

// Kiểm tra xem có giá trị lọc từ form gửi lên hay không
if (isset($_GET['filter'])) {
    if (!empty($_GET['tenthietbi'])) {
        $tenthietbi_filter = $_GET['tenthietbi'];
    }
    if (!empty($_GET['tinhtrang'])) {
        $tinhtrang_filter = $_GET['tinhtrang'];
    }
    if (!empty($_GET['monhoc'])) {
        $monhoc_filter = $_GET['monhoc'];
    }
    if (!empty($_GET['khoi'])) {
        $khoi_filter = $_GET['khoi'];
    }
}

// Xây dựng câu truy vấn SQL với điều kiện lọc
$sql = "SELECT 
            masothietbi.masothietbi, 
            thietbi.tenthietbi, 
            monhoc.tenmonhoc, 
            khoi.ten_khoi, 
            masothietbi.tinhtrang 
        FROM thietbi
        JOIN masothietbi ON thietbi.id = masothietbi.thietbi_id
        JOIN monhoc ON thietbi.monhoc_id = monhoc.id
        JOIN khoi ON monhoc.khoi_id = khoi.id
        WHERE 1=1";

// Thêm điều kiện lọc nếu có
if ($tenthietbi_filter != '') {
    $sql .= " AND thietbi.tenthietbi LIKE '%$tenthietbi_filter%'";
}
if ($tinhtrang_filter != '') {
    $sql .= " AND masothietbi.tinhtrang = '$tinhtrang_filter'";
}
if ($monhoc_filter != '') {
    $sql .= " AND monhoc.id = '$monhoc_filter'";
}
if ($khoi_filter != '') {
    $sql .= " AND khoi.id = '$khoi_filter'";
}
if ($tenthietbi_filter == '' && $tinhtrang_filter == '' && $monhoc_filter == '' && $khoi_filter == '') {
  $sql .= " LIMIT 10";
}
$result = mysqli_query($conn, $sql);

// Lấy danh sách môn học và khối cho dropdown
$monhoc_query = mysqli_query($conn, "SELECT id, tenmonhoc FROM monhoc");
$khoi_query = mysqli_query($conn, "SELECT id, ten_khoi FROM khoi");
?>


<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Báo Cáo Tình Trạng Thiết Bị</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin-top: 80px;
        padding: 0;
    }
    .container {
        width: 80%;
        margin: 0 auto;
        padding: 30px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }
    h1 {
        text-align: center;
        color: #333;
    }
    .report {
        margin-top: 20px;
    }
    .report table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .report table, .report th, .report td {
        border: 1px solid #ddd;
    }
    .report th, .report td {
        padding: 10px;
        text-align: center;
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
    .filter-container {
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.filter-container input[type="text"],
.filter-container select {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
}

.filter-container button {
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
}

.filter-container button:hover {
    background-color: #0056b3;
}
  </style>
</head>
<body>

<div class="container">
  <h1>Báo Cáo Tình Trạng Thiết Bị</h1>

  <!-- Form Lọc -->
  <div class="filter-container">
  <form method="GET" action="baocaotinhtrang.php" id="filterForm">
  <input type="text" name="tenthietbi" placeholder="Nhập tên thiết bị" value="<?php echo $tenthietbi_filter; ?>">
  
  <select name="khoi" id="khoiSelect">
    <option value="">Chọn Khối</option>
    <?php while ($row_khoi = mysqli_fetch_assoc($khoi_query)) { ?>
        <option value="<?= $row_khoi['id'] ?>" <?= ($khoi_filter == $row_khoi['id']) ? 'selected' : '' ?>>
            <?= $row_khoi['ten_khoi'] ?>
        </option>
    <?php } ?>
</select>

<select name="monhoc" id="monhocSelect">
    <option value="">Chọn Môn Học</option>
    <?php while ($row_monhoc = mysqli_fetch_assoc($monhoc_query)) { ?>
        <option value="<?= $row_monhoc['id'] ?>" <?= ($monhoc_filter == $row_monhoc['id']) ? 'selected' : '' ?>>
            <?= $row_monhoc['tenmonhoc'] ?>
        </option>
    <?php } ?>
</select>
  
  <select name="tinhtrang">
        <option value="">Chọn tình trạng</option>
        <option value="Tốt" <?php echo ($tinhtrang_filter == 'Tốt' ? 'selected' : ''); ?>>Tốt</option>
        <option value="Hỏng" <?php echo ($tinhtrang_filter == 'Hỏng' ? 'selected' : ''); ?>>Hỏng</option>
      </select>
      <button type="submit" name="filter">Lọc</button>
    </form>
  </div>

  <div class="report">
    <!-- Bảng hiển thị thông tin tình trạng của thiết bị -->
    <table>
      <thead>
        <tr>
          <th>Mã Thiết Bị</th>
          <th>Tên Thiết Bị</th>
          <th>Môn Học</th>
          <th>Khối</th>
          <th>Tình Trạng</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['masothietbi'] . "</td>";
            echo "<td>" . $row['tenthietbi'] . "</td>";
            echo "<td>" . $row['tenmonhoc'] . "</td>";
            echo "<td>" . $row['ten_khoi'] . "</td>";
            echo "<td>" . $row['tinhtrang'] . "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='3'>Không có dữ liệu thiết bị.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <a href="pdf_tinhtrang.php?tenthietbi=<?php echo urlencode($tenthietbi_filter); ?>&tinhtrang=<?php echo urlencode($tinhtrang_filter); ?>&monhoc=<?php echo urlencode($monhoc_filter); ?>&khoi=<?php echo urlencode($khoi_filter); ?>" 
  class="btn-back" style="background-color: #007bff; margin-left: 10px;">
   📄 Xuất file PDF
</a>  <a href="quanlybaocao.php" class="btn-back">← Quay lại danh sách báo cáo</a>
</div>
<script>
  document.getElementById('khoiSelect').addEventListener('change', function() {
    var khoiId = this.value;
    var monhocSelect = document.getElementById('monhocSelect');
    monhocSelect.innerHTML = '<option value="">Chọn Môn Học</option>';

    if (khoiId) {
        fetch('../quanlythietbi/tb_get_monhoc.php?khoi_id=' + khoiId)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(function(monhoc) {
                        var option = document.createElement('option');
                        option.value = monhoc.id;
                        option.textContent = monhoc.tenmonhoc;
                        monhocSelect.appendChild(option);
                    });
                } else {
                    var option = document.createElement('option');
                    option.textContent = 'Không có môn học';
                    option.disabled = true;
                    monhocSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
            });
    }
});
</script>
</body>
</html>
