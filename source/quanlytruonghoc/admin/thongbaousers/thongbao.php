<?php
include '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['users_id'])) {
    header("Location: /quanlytruonghoc/login.php");
    exit;
}

$giaovien_id = $_SESSION['users_id'];

// Lấy danh sách đơn của giáo viên, thêm loai_don và masothietbi_id
$query = "SELECT dt.id, dt.loai_don, dt.masothietbi_id, tb.tenthietbi, dt.ngaymuon, dt.ngaytra, dt.trangthai
          FROM donthietbi dt
          JOIN thietbi tb ON dt.thietbi_id = tb.id
          WHERE dt.users_id = $giaovien_id
          ORDER BY dt.ngaymuon DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trạng Thái Đơn</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status-waiting { color: orange; font-weight: bold; }
        .status-approved { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Trạng Thái Các Đơn Thiết Bị</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Loại Đơn</th>
                    <th>Thiết Bị</th>
                    <th>Mã Số (nếu bảo trì)</th>
                    <th>Ngày Mượn</th>
                    <th>Ngày Trả</th>
                    <th>Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['loai_don']); ?></td>
                        <td><?php echo htmlspecialchars($row['tenthietbi']); ?></td>
                        <td>
                            <?php
                            if ($row['loai_don'] == 'Bảo trì thiết bị') {
                                echo htmlspecialchars($row['masothietbi_id']);
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php echo $row['ngaymuon'] ?: '-'; ?></td>
                        <td><?php echo $row['ngaytra'] ?: '-'; ?></td>
                        <td>
                            <?php
                            $loaiDon = $row['loai_don'];
                            $tenTB = $row['tenthietbi'];
                            $status = $row['trangthai'];

                            if ($status == 'Chờ duyệt') {
                                echo '<span class="status-waiting">Đơn ' . $loaiDon . ' cho thiết bị ' . $tenTB . ' của bạn đang chờ duyệt</span>';
                            } elseif ($status == 'Đã duyệt') {
                                echo '<span class="status-approved">Đơn ' . $loaiDon . ' cho thiết bị ' . $tenTB . ' của bạn đã được duyệt</span>';
                            } elseif ($status == 'Đã từ chối') {
                                echo '<span class="status-rejected">Đơn ' . $loaiDon . ' cho thiết bị ' . $tenTB . ' của bạn đã bị từ chối</span>';
                            } else {
                                echo htmlspecialchars($status);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">Hiện tại bạn chưa gửi đơn nào.</p>
    <?php endif; ?>

</body>
</html>
