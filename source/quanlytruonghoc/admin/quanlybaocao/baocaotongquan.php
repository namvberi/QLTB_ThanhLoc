<?php
session_start();
include '../config.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về trang login
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy danh sách môn học và khối để đổ vào dropdown
$monhoc_result = mysqli_query($conn, "SELECT id, tenmonhoc FROM monhoc");
$khoi_result = mysqli_query($conn, "SELECT id, ten_khoi FROM khoi");

// Xử lý filter nếu có
$filter_monhoc = isset($_GET['monhoc']) ? intval($_GET['monhoc']) : '';
$filter_khoi = isset($_GET['khoi']) ? intval($_GET['khoi']) : '';

$conditions = [];
if (!empty($filter_monhoc)) {
    $conditions[] = "mh.id = $filter_monhoc";
}
if (!empty($filter_khoi)) {
    $conditions[] = "k.id = $filter_khoi";
}
$where_sql = (count($conditions) > 0) ? ' WHERE ' . implode(' AND ', $conditions) : '';

// Tổng hợp số lượng thiết bị (áp dụng lọc)
$sql_summary = "SELECT COUNT(*) as total_devices, 
        SUM(IF(masothietbi.tinhtrang = 'Tốt', 1, 0)) as active_devices, 
        SUM(IF(masothietbi.tinhtrang = 'Hỏng', 1, 0)) as broken_devices 
        FROM thietbi
        JOIN masothietbi ON thietbi.id = masothietbi.thietbi_id
        JOIN monhoc mh ON thietbi.monhoc_id = mh.id
        JOIN khoi k ON mh.khoi_id = k.id
        $where_sql";
$result_summary = mysqli_query($conn, $sql_summary);
$data_summary = mysqli_fetch_assoc($result_summary);

// Chi tiết thiết bị với điều kiện lọc
$sql_detail = "SELECT 
    tb.tenthietbi, 
    mh.tenmonhoc, 
    k.ten_khoi AS ten_khoi,  
    lt.tenloaithietbi AS loai,  
    COUNT(*) as soluong
FROM 
    thietbi tb
JOIN 
    monhoc mh ON tb.monhoc_id = mh.id
JOIN 
    khoi k ON mh.khoi_id = k.id
JOIN 
    loaithietbi lt ON tb.loaithietbi_id = lt.id  
JOIN 
    masothietbi mstb ON tb.id = mstb.thietbi_id
$where_sql
GROUP BY tb.id, mh.id, k.id, lt.id";
$result_detail = mysqli_query($conn, $sql_detail);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Báo Cáo Tổng Quan Thiết Bị</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin-top: 40px;
        padding: 0;
    }
    .container {
        width: 90%;
        margin: 0 auto;
        padding: 30px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }
    h1, h3 {
        text-align: center;
        color: #333;
    }
    .report p {
        font-size: 16px;
        margin: 10px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th, td {
        padding: 8px;
        text-align: center;
    }
    th {
        background-color: #f0f0f0;
    }
    .btn-back, .btn-submit {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 15px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 6px;
    }
    .btn-back:hover, .btn-submit:hover {
        background-color: #5a6268;
    }
    .btn-submit {
        background-color: #28a745;
    }
    .btn-submit:hover {
        background-color: #218838;
    }
    .btn-reset {
        background-color: #dc3545;
    }
    .btn-reset:hover {
        background-color: #c82333;
    }
    select {
        padding: 5px;
        margin-right: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Báo Cáo Tổng Quan Thiết Bị</h1>
  
  <div class="report">
    <p>Tổng số thiết bị: <?php echo $data_summary['total_devices']; ?></p>
    <p>Số thiết bị hoạt động (tốt): <?php echo $data_summary['active_devices']; ?></p>
    <p>Số thiết bị hỏng: <?php echo $data_summary['broken_devices']; ?></p>
  </div>

  <div class="filter-container">
    <form method="GET" action="baocaotongquan.php" id="filterForm">
      <select name="khoi" id="khoiSelect">
        <option value="">Chọn Khối</option>
        <?php while ($row_khoi = mysqli_fetch_assoc($khoi_result)) { ?>
          <option value="<?= $row_khoi['id'] ?>" <?= ($filter_khoi == $row_khoi['id']) ? 'selected' : '' ?>>
            <?= $row_khoi['ten_khoi'] ?>
          </option>
        <?php } ?>
      </select>

      <select name="monhoc" id="monhocSelect">
        <option value="">Chọn Môn Học</option>
        <?php while ($row_monhoc = mysqli_fetch_assoc($monhoc_result)) { ?>
          <option value="<?= $row_monhoc['id'] ?>" <?= ($filter_monhoc == $row_monhoc['id']) ? 'selected' : '' ?>>
            <?= $row_monhoc['tenmonhoc'] ?>
          </option>
        <?php } ?>
      </select>

      <button type="submit" class="btn-submit">Lọc</button>
      <a href="baocaotongquan.php" class="btn-back btn-reset">Xóa Lọc</a>
    </form>
  </div>

  <h3>Chi Tiết Thiết Bị</h3>
  <table>
    <thead>
      <tr>
        <th>Tên Thiết Bị</th>
        <th>Môn Học</th>
        <th>Khối</th>
        <th>Loại</th>
        <th>Số Lượng</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (mysqli_num_rows($result_detail) > 0) {
          while ($row = mysqli_fetch_assoc($result_detail)) {
              echo "<tr>";
              echo "<td>{$row['tenthietbi']}</td>";
              echo "<td>{$row['tenmonhoc']}</td>";
              echo "<td>{$row['ten_khoi']}</td>";
              echo "<td>{$row['loai']}</td>";
              echo "<td>{$row['soluong']}</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='5'>Không có dữ liệu chi tiết thiết bị.</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <a href="pdf_tongquan.php?khoi=<?= $filter_khoi ?>&monhoc=<?= $filter_monhoc ?>" class="btn-back" style="background-color: #007bff; margin-left: 10px;">📄 Xuất file PDF</a>
  <a href="quanlybaocao.php" class="btn-back">← Quay lại danh sách báo cáo</a>
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
