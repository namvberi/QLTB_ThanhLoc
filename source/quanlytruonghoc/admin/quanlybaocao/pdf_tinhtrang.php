<?php
require_once '../../vendor/autoload.php';
session_start();
include '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy tham số lọc từ URL
$tenthietbi_filter = isset($_GET['tenthietbi']) ? mysqli_real_escape_string($conn, $_GET['tenthietbi']) : '';
$tinhtrang_filter = isset($_GET['tinhtrang']) ? mysqli_real_escape_string($conn, $_GET['tinhtrang']) : '';

// Xây dựng câu truy vấn SQL
$sql = "SELECT masothietbi.masothietbi, thietbi.tenthietbi, masothietbi.tinhtrang 
        FROM thietbi
        JOIN masothietbi ON thietbi.id = masothietbi.thietbi_id
        WHERE 1=1";

if ($tenthietbi_filter != '') {
    $sql .= " AND thietbi.tenthietbi LIKE '%$tenthietbi_filter%'";
}
if ($tinhtrang_filter != '') {
    $sql .= " AND masothietbi.tinhtrang = '$tinhtrang_filter'";
}

$result = mysqli_query($conn, $sql);

// Bắt đầu tạo nội dung HTML
$html = '<h2 style="text-align:center;">BÁO CÁO TÌNH TRẠNG THIẾT BỊ</h2>';
$html .= '<p><strong>Bộ lọc:</strong> ';
$html .= ($tenthietbi_filter != '' ? 'Tên thiết bị chứa "' . htmlspecialchars($tenthietbi_filter) . '"' : 'Tất cả');
$html .= ', ';
$html .= ($tinhtrang_filter != '' ? 'Tình trạng: ' . htmlspecialchars($tinhtrang_filter) : 'Tất cả');
$html .= '</p>';

$html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%">
<thead>
<tr>
<th>Mã Thiết Bị</th>
<th>Tên Thiết Bị</th>
<th>Tình Trạng</th>
</tr>
</thead>
<tbody>';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
        <td>' . htmlspecialchars($row['masothietbi']) . '</td>
        <td>' . htmlspecialchars($row['tenthietbi']) . '</td>
        <td>' . htmlspecialchars($row['tinhtrang']) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="3" style="text-align:center;">Không có dữ liệu thiết bị.</td></tr>';
}

$html .= '</tbody></table>';

// Xuất PDF bằng mPDF
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('baocao_tinhtrang.pdf', 'I');
exit();
?>
