<?php
include '../config.php';
include '../navbar.php';

// Khởi tạo mảng để chứa các điều kiện WHERE
$where_conditions = [];

// Kiểm tra và thêm điều kiện lọc theo loại đơn (loai_don) nếu có
if (!empty($_GET['loai_don'])) {
    $loai_don = mysqli_real_escape_string($conn, $_GET['loai_don']);
    $where_conditions[] = "dt.loai_don = '$loai_don'";
}

// Kiểm tra và thêm điều kiện lọc theo trạng thái (trangthai) nếu có
if (!empty($_GET['trangthai'])) {
    $trangthai = mysqli_real_escape_string($conn, $_GET['trangthai']);
    $where_conditions[] = "dt.trangthai = '$trangthai'";
}

// Xây dựng câu truy vấn cơ bản
$query = "SELECT dt.*, tb.tenthietbi, u.tennguoidung 
          FROM donthietbi dt 
          JOIN thietbi tb ON dt.thietbi_id = tb.id 
          JOIN users u ON dt.users_id = u.id";

// Nếu có các điều kiện lọc, thêm chúng vào câu truy vấn
if (count($where_conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

// Sắp xếp theo ID đơn giảm dần
$query .= " ORDER BY dt.id DESC";

// Thực hiện truy vấn
$result = mysqli_query($conn, $query);

if (!$result) {
    // Nếu truy vấn không thành công, hiển thị lỗi
    die('Lỗi truy vấn: ' . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đơn</title>
    <style>
        body {
            margin-top: 100px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding-top: 30px;
        }

        h1 {
            color: #333;
            font-weight: bold;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #333;
            color: white;
        }

        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s, transform 0.2s;
            border: 1px solid #333;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn:active {
            background-color: #004080;
            transform: translateY(1px);
        }

        .filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .filter-container form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }

        .filter-container input,
        .filter-container select {
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .add-btn-container {
            text-align: right;
        }

        .btn-edit {
            background-color: #28a745;
        }

        .btn-edit:hover {
            background-color: #218838;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Quản Lý Đơn</h1>

        <div class="filter-container">
            <form method="GET" action="quanlydon.php">
                <select name="loai_don">
                    <option value="">Loại đơn</option>
                    <option value="Mượn thiết bị" <?= (($_GET['loai_don'] ?? '') == 'Mượn Thiết bị') ? 'selected' : '' ?>>Mượn Thiết bị</option>
                    <option value="Bảo trì thiết bị" <?= (($_GET['loai_don'] ?? '') == 'Bảo trì thiết bị') ? 'selected' : '' ?>>Bảo trì thiết bị</option>
                </select>

                <select name="trangthai">
                    <option value="">Trạng thái</option>
                    <option value="Đã duyệt" <?= (($_GET['trangthai'] ?? '') == 'Đã duyệt') ? 'selected' : '' ?>>Đã duyệt</option>
                    <option value="Chờ duyệt" <?= (($_GET['trangthai'] ?? '') == 'Chờ duyệt') ? 'selected' : '' ?>>Chờ duyệt</option>
                </select>
                <button type="submit" class="btn">Tìm kiếm</button>
            </form>
            <form method="POST" action="xoanguoimuon.php" id="delete-form">
    <div class="add-btn-container">
        <a href="#" class="btn btn-delete" id="delete-selected">Xóa đã chọn</a>
        <a href="/quanlytruonghoc/admin/quanlydon/guidon.php" class="btn">Gửi đơn </a>
        </div>
</form>
        </div>

        <table>
            <thead>
                <tr>
                <th><input type="checkbox" id="select-all"></th>
                    <th>Người gửi</th>
                    <th>Loại đơn</th>
                    <th>Thiết bị</th>
                    <th>Số lượng</th>
                    <th>Ghi chú</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr id="row_<?= $row['id'] ?>">
                    <td><input type="checkbox" class="select-item" value="<?= $row['id'] ?>"></td>
                    <td><?= htmlspecialchars($row['tennguoidung']) ?></td>
                        <td><?= $row['loai_don'] ?></td>
                        <td><?= $row['tenthietbi'] ?></td>
                        <td><?= $row['soluong'] ?></td>
                        <td><?= $row['ghichu'] ?></td>
                        <td><?= $row['trangthai'] ?></td>
                        <td class="action-buttons">
    <!-- Kiểm tra loại đơn để điều hướng tới file chi tiết tương ứng -->
                            <?php if ($row['loai_don'] == 'Mượn thiết bị'): ?>
                                <a href="chitietdonmuon.php?id=<?= $row['id'] ?>" class="btn btn-view" style="background-color:#17a2b8;">Chi tiết</a>
                            <?php elseif ($row['loai_don'] == 'Bảo trì thiết bị'): ?>
                                <a href="chitietdonbaotri.php?id=<?= $row['id'] ?>" class="btn btn-view" style="background-color:#17a2b8;">Chi tiết</a>
                            <?php endif; ?>
                            <?php if ($row['trangthai'] == 'Chờ duyệt'): ?>
                                <button class="btn-duyet btn btn-success btn-sm" data-id="<?= $row['id'] ?>" data-loai="<?= $row['loai_don'] ?>">Duyệt</button>
                                <button class="btn-tu-choi btn btn-danger btn-sm" data-id="<?php echo $row['id']; ?>">Từ chối</button>
                                <?php else: ?>
                                <i></i>
                            <?php endif; ?>
                        </td>
                            </tr>
                            <?php endwhile; ?>
            </tbody>
        </table>
    </div> 
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll(".btn-duyet");

    buttons.forEach(function(button) {
        button.addEventListener("click", function() {
            const donId = this.dataset.id;
            const loaiDon = this.dataset.loai;

            if (!loaiDon) {
                alert('Không xác định được loại đơn!');
                return;
            }

            if (confirm("Bạn có chắc chắn muốn duyệt đơn này?")) {
                let url = "";

                if (loaiDon.includes("Mượn")) {
                    url = "duyetdonmuon_ajax.php";
                } else if (loaiDon.includes("Bảo trì")) {
                    url = "duyetdonbaotri_ajax.php";
                } else {
                    alert('Loại đơn không hợp lệ!');
                    return;
                }

                const xhr = new XMLHttpRequest();
                xhr.open("POST", url, true);
                xhr.setRequestHeader("Content-Type", "application/json");

                const data = JSON.stringify({ don_id: donId });

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            alert('Đơn đã được duyệt thành công!');
                            // Cập nhật trạng thái trong bảng mà không reload lại trang
                            const row = document.getElementById(`row_${donId}`);
                            row.querySelector('.btn-duyet').style.display = 'none';
                            row.querySelector('.btn-tu-choi').style.display = 'none';  // Ẩn nút Duyệt
                            row.querySelector('i').textContent = 'Đã xong';  // Cập nhật chữ thành "Đã xong"
                        } else {
                            alert('Có lỗi xảy ra: ' + response.message);
                        }
                    } else {
                        alert('Lỗi trong quá trình duyệt đơn.');
                    }
                };

                xhr.send(data);
            }
        });
    });
});
// Sử dụng jQuery để xử lý sự kiện nút từ chối
$(document).on('click', '.btn-tu-choi', function() {
    var don_id = $(this).data('id'); // Lấy ID của đơn từ thuộc tính data-id
    var $row = $('#row_' + don_id); // Chọn dòng bảng

    if (confirm('Bạn có chắc chắn muốn từ chối đơn này?')) {
        // Gửi yêu cầu AJAX đến file 'tuchoidon.php'
        $.ajax({
            url: 'tuchoidon.php', // Đường dẫn tới file xử lý từ chối
            type: 'POST',
            data: { id: don_id },
            success: function(response) {
                // Cập nhật trạng thái trong ô trạng thái (giả sử cột trạng thái có class 'trangthai-cell')
                $row.find('.trangthai-cell').text('Đã từ chối');

                // Ẩn nút "Duyệt" và nút "Từ chối"
                $row.find('.btn-duyet').hide();
                $row.find('.btn-tu-choi').hide();

                // Hiển thị thông báo từ server
                alert(response);
            },
            error: function() {
                alert('Có lỗi xảy ra khi từ chối đơn.');
            }
        });
    }
});
// xóa
document.getElementById('select-all').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('.select-item').forEach(item => {
            item.checked = checked;
        });
    });

    document.getElementById('delete-selected').addEventListener('click', function(event) {
        event.preventDefault();
        
        const selectedIds = [];
        document.querySelectorAll('.select-item:checked').forEach(item => {
            selectedIds.push(item.value);
        });

        if (selectedIds.length > 0) {
            if (confirm('Bạn có chắc chắn muốn xóa các mục đã chọn?')) {
                // Gửi AJAX request để xóa
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'xoadon.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        alert('Xóa thành công');
                        location.reload(); // Tải lại trang sau khi xóa thành công
                    } else {
                        alert(response.message || 'Có lỗi xảy ra');
                    }
                };
                xhr.send('selected_ids[]=' + selectedIds.join('&selected_ids[]='));
            }
        } else {
            alert('Vui lòng chọn ít nhất một mục để xóa');
        }
    });
</script>

</html>
