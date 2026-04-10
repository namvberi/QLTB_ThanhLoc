<?php
include '../config.php';
include '../navbar.php';

// Kiểm tra nếu có ID đơn trong URL
if (isset($_GET['id'])) {
    $don_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Truy vấn để lấy thông tin chi tiết của đơn từ cơ sở dữ liệu
    $query = "SELECT dt.*, tb.tenthietbi, u.tennguoidung, mh.tenmonhoc, mst.masothietbi
    FROM donthietbi dt 
    JOIN thietbi tb ON dt.thietbi_id = tb.id 
    JOIN users u ON dt.users_id = u.id 
    JOIN monhoc mh ON tb.monhoc_id = mh.id
    JOIN masothietbi mst ON dt.masothietbi_id = mst.id  -- Thay đổi liên kết ở đây
    WHERE dt.id = '$don_id'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        // Nếu không tìm thấy đơn, chuyển hướng về trang quản lý đơn
        header("Location: quanlydon.php");
        exit();
    }
} else {
    // Nếu không có ID đơn, chuyển hướng về trang quản lý đơn
    header("Location: quanlydon.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Chi Tiết Đơn Thiết Bị</h1>

        <div class="card shadow p-4">
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th scope="row" style="width: 200px;">Người gửi</th>
                        <td><?= htmlspecialchars($row['tennguoidung']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Loại đơn</th>
                        <td><?= htmlspecialchars($row['loai_don']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Thiết bị</th>
                        <td><?= htmlspecialchars($row['tenthietbi']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Tên môn học</th>
                        <td><?= htmlspecialchars($row['tenmonhoc']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Mã số thiết bị</th>
                        <td><?= htmlspecialchars($row['masothietbi']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Số lượng</th>
                        <td><?= htmlspecialchars($row['soluong']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Ghi chú</th>
                        <td><?= htmlspecialchars($row['ghichu']) ?: '<span class="text-muted">Không có ghi chú</span>' ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Trạng thái</th>
                        <td>
                            <?php
                                $status = htmlspecialchars($row['trangthai']);
                                if ($status == "Chờ duyệt") {
                                    echo "<span class='badge bg-warning text-dark'>$status</span>";
                                } elseif ($status == "Đã duyệt") {
                                    echo "<span class='badge bg-success'>$status</span>";
                                } elseif ($status == "Từ chối") {
                                    echo "<span class='badge bg-danger'>$status</span>";
                                } else {
                                    echo "<span class='badge bg-secondary'>$status</span>";
                                }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="text-end">
                <a href="quanlydon.php" class="btn btn-primary">← Trở lại</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS nếu cần -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
