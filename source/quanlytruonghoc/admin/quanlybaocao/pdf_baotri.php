<?php
require_once '../../vendor/autoload.php';
session_start();
include '../config.php';

if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy giá trị bộ lọc từ URL nếu có
$filter_monhoc = isset($_GET['monhoc']) ? intval($_GET['monhoc']) : '';
$filter_khoi = isset($_GET['khoi']) ? intval($_GET['khoi']) : '';

// Lưu giá trị bộ lọc để hiển thị trong báo cáo PDF
$monhoc_filter = $filter_monhoc ? $filter_monhoc : 'Tất cả';
$khoi_filter = $filter_khoi ? $filter_khoi : 'Tất cả';

// Lấy tên môn học và khối từ bảng monhoc và khoi
$monhoc_name = '';
$khoi_name = '';

// Lấy tên môn học nếu có bộ lọc
if (!empty($filter_monhoc)) {
    $result_monhoc = mysqli_query($conn, "SELECT tenmonhoc FROM monhoc WHERE id = $filter_monhoc");
    if ($row_monhoc = mysqli_fetch_assoc($result_monhoc)) {
        $monhoc_name = $row_monhoc['tenmonhoc'];
    }
}

// Lấy tên khối nếu có bộ lọc
if (!empty($filter_khoi)) {
    $result_khoi = mysqli_query($conn, "SELECT ten_khoi FROM khoi WHERE id = $filter_khoi");
    if ($row_khoi = mysqli_fetch_assoc($result_khoi)) {
        $khoi_name = $row_khoi['ten_khoi'];
    }
}

// Điều kiện lọc cho báo cáo
$conditions = [];
if (!empty($filter_monhoc)) {
    $conditions[] = "mh.id = $filter_monhoc";
}
if (!empty($filter_khoi)) {
    $conditions[] = "k.id = $filter_khoi";
}
$where_sql = (count($conditions) > 0) ? ' WHERE ' . implode(' AND ', $conditions) : '';

// Lấy tổng hợp số lượng thiết bị và tình trạng bảo trì
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

// Truy vấn chi tiết bảo trì thiết bị
$sql_detail = "SELECT 
    b.id, 
    mstb.masothietbi, 
    tb.tenthietbi, 
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
ORDER BY b.ngaysua DESC"; // Sắp xếp theo ngày bảo trì
$result_detail = mysqli_query($conn, $sql_detail);

// Tạo nội dung PDF
$html = '<h2 style="text-align:center;">BÁO CÁO BẢO TRÌ THIẾT BỊ</h2>';
$html .= '<p>Tổng số thiết bị: ' . $data_summary['total_devices'] . '</p>';
$html .= '<p>Số thiết bị hoạt động (tốt): ' . $data_summary['active_devices'] . '</p>';
$html .= '<p>Số thiết bị hỏng: ' . $data_summary['broken_devices'] . '</p>';
$html .= '<p>Số thiết bị đang bảo trì: ' . $data_summary['under_maintenance'] . '</p>';

$html .= '<p><strong>Bộ lọc:</strong> ';
$html .= ($khoi_name != '' ? 'Khối: ' . htmlspecialchars($khoi_name) : 'Khối: Tất cả');
$html .= ', ';
$html .= ($monhoc_name != '' ? 'Môn học: ' . htmlspecialchars($monhoc_name) : 'Môn học: Tất cả');
$html .= '</p>';

$html .= '<table border="1" cellspacing="0" cellpadding="5">
<tr>
<th>Tên thiết bị</th>
<th>Môn học</th>
<th>Khối</th>
<th>Loại</th>
<th>Ngày bảo trì</th>
<th>Người bảo trì</th>
<th>Mô tả bảo trì</th>
</tr>';

while($row = mysqli_fetch_assoc($result_detail)) {
    $html .= '<tr>
    <td>' . $row['tenthietbi'] . '</td>
    <td>' . $row['tenmonhoc'] . '</td>
    <td>' . $row['ten_khoi'] . '</td>
    <td>' . $row['loai'] . '</td>
    <td>' . date('d/m/Y', strtotime($row['ngaysua'])) . '</td>
    <td>' . $row['nguoisua'] . '</td>
    <td>' . $row['ghichu'] . '</td>
    </tr>';
}
$html .= '</table>';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('baocaobaotri.pdf', 'I');
exit();
?>
