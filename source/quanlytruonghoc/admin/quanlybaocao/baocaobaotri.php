<?php
session_start();
include '../config.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về trang login
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy danh sách môn học để đổ vào dropdown
$monhoc_result = mysqli_query($conn, "SELECT id, tenmonhoc FROM monhoc");
$tenthietbi_filter = '';

// Xử lý filter nếu có
$filter_monhoc = isset($_GET['monhoc']) ? intval($_GET['monhoc']) : '';
$filter_tenthietbi = isset($_GET['tenthietbi']) ? $_GET['tenthietbi'] : '';

$conditions = [];
if (!empty($filter_tenthietbi)) {
    $tenthietbi_filter = $filter_tenthietbi;
    $conditions[] = "tenthietbi LIKE '%$filter_tenthietbi%'";
}
if (!empty($filter_monhoc)) {
    $conditions[] = "mh.id = $filter_monhoc";
}
$where_sql = (count($conditions) > 0) ? ' WHERE ' . implode(' AND ', $conditions) : '';

// Tổng hợp số lượng thiết bị (áp dụng lọc)
$sql_summary = "SELECT COUNT(*) as total_devices, 
        SUM(IF(masothietbi.tinhtrang = 'Tốt', 1, 0)) as active_devices, 
        SUM(IF(masothietbi.tinhtrang = 'Hỏng', 1, 0)) as broken_devices,
        SUM(IF(masothietbi.trangthai = 'Đang bảo trì', 1, 0)) as under_maintenance
        FROM thietbi
        JOIN masothietbi ON thietbi.id = masothietbi.thietbi_id
        JOIN monhoc mh ON thietbi.monhoc_id = mh.id
        JOIN khoi k ON mh.khoi_id = k.id
        $where_sql";
$result_summary = mysqli_query($conn, $sql_summary);
$data_summary = mysqli_fetch_assoc($result_summary);

// Chi tiết bảo trì thiết bị (lấy lần bảo trì mới nhất)
$sql_detail = "SELECT 
    b.id, 
    mstb.masothietbi, 
    tenthietbi, 
    mh.tenmonhoc, 
    k.ten_khoi AS ten_khoi,  
    lt.tenloaithietbi AS loai,  
    b.ngaysua, 
    b.nguoisua,
    b.ghichu
FROM 
    baotri b
JOIN 
    masothietbi mstb ON b.masothietbi_id = mstb.id
JOIN 
    thietbi tb ON mstb.thietbi_id = tb.id
JOIN 
    monhoc mh ON tb.monhoc_id = mh.id
JOIN 
    khoi k ON mh.khoi_id = k.id
JOIN 
    loaithietbi lt ON tb.loaithietbi_id = lt.id
JOIN 
    (SELECT masothietbi_id, MAX(ngaysua) AS ngaysua 
     FROM baotri 
     GROUP BY masothietbi_id) b_max
    ON b.masothietbi_id = b_max.masothietbi_id AND b.ngaysua = b_max.ngaysua
$where_sql
AND b.tinhtrang = 'Đang bảo trì'  -- Chỉ lấy thiết bị có tình trạng là 'Đang bảo trì'
ORDER BY b.ngaysua DESC";
$result_detail = mysqli_query($conn, $sql_detail);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Báo Cáo Bảo Trì Thiết Bị</title>
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
    select, input[type="text"] {
        padding: 5px;
        margin-right: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Báo Cáo Bảo Trì Thiết Bị</h1>
  
  <div class="report">
    <p>Tổng số thiết bị: <?php echo $data_summary['total_devices']; ?></p>
    <p>Số thiết bị hoạt động (tốt): <?php echo $data_summary['active_devices']; ?></p>
    <p>Số thiết bị hỏng: <?php echo $data_summary['broken_devices']; ?></p>
    <p>Số thiết bị đang bảo trì: <?php echo $data_summary['under_maintenance']; ?></p>
  </div>

  <div class="filter-container">
    <form method="GET" action="baocaobaotri.php" id="filterForm">
      <input type="text" name="tenthietbi" placeholder="Nhập tên thiết bị" value="<?php echo $tenthietbi_filter; ?>">

      <select name="monhoc" id="monhocSelect">
        <option value="">Chọn Môn Học</option>
        <?php while ($row_monhoc = mysqli_fetch_assoc($monhoc_result)) { ?>
          <option value="<?= $row_monhoc['id'] ?>" <?= ($filter_monhoc == $row_monhoc['id']) ? 'selected' : '' ?>>
            <?= $row_monhoc['tenmonhoc'] ?>
          </option>
        <?php } ?>
      </select>

      <button type="submit" class="btn-submit">Lọc</button>
      <a href="baocaobaotri.php" class="btn-back btn-reset">Xóa Lọc</a>
    </form>
  </div>

  <h3>Chi Tiết Bảo Trì</h3>
  <table>
    <thead>
      <tr>
        <th>Mã Số Thiết Bị</th>
        <th>Tên Thiết Bị</th>
        <th>Môn Học</th>
        <th>Khối</th>
        <th>Loại</th>
        <th>Ngày Sửa</th>
        <th>Người sửa</th>
        <th>Ghi Chú</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (mysqli_num_rows($result_detail) > 0) {
          while ($row = mysqli_fetch_assoc($result_detail)) {
              echo "<tr>";
              echo "<td>{$row['masothietbi']}</td>";
              echo "<td>{$row['tenthietbi']}</td>";
              echo "<td>{$row['tenmonhoc']}</td>";
              echo "<td>{$row['ten_khoi']}</td>";
              echo "<td>{$row['loai']}</td>";
              echo "<td>" . date('d/m/Y', strtotime($row['ngaysua'])) . "</td>";
              echo "<td>{$row['nguoisua']}</td>";
              echo "<td>{$row['ghichu']}</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='8'>Không có dữ liệu bảo trì.</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <a href="pdf_baotri.php?monhoc=<?= $filter_monhoc ?>&tenthietbi=<?= $filter_tenthietbi ?>" class="btn-back" style="background-color: #007bff; margin-left: 10px;">📄 Xuất file PDF</a>
  <a href="quanlybaocao.php" class="btn-back">← Quay lại danh sách báo cáo</a>
</div>

</body>
</html>
