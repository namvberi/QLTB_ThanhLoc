<?php
include('../config.php');
include('../navbar.php');


// Lấy danh sách khối và môn học
$result_khoi = mysqli_query($conn, "SELECT * FROM khoi");
$result_monhoc = mysqli_query($conn, "SELECT * FROM monhoc");

// Xử lý lọc
$where_conditions = [];

if (!empty($_GET['tennguoimuon'])) {
    $tennguoimuon = mysqli_real_escape_string($conn, $_GET['tennguoimuon']);
    $where_conditions[] = "nguoimuon.tennguoimuon LIKE '%$tennguoimuon%'";
}

if (!empty($_GET['khoi'])) {
    $khoi = intval($_GET['khoi']);
    $where_conditions[] = "khoi.id = $khoi";
}

if (!empty($_GET['monhoc'])) {
    $monhoc = intval($_GET['monhoc']);
    $where_conditions[] = "monhoc.id = $monhoc";
}

if (!empty($_GET['trangthai'])) {
    $trangthai = mysqli_real_escape_string($conn, $_GET['trangthai']);
    $where_conditions[] = "nguoimuon.trangthai = '$trangthai'";
}

$where_sql = '';
if (!empty($where_conditions)) {
    $where_sql = ' WHERE ' . implode(' AND ', $where_conditions);
}

// Truy vấn danh sách người mượn
$query_nguoimuon = "SELECT 
    nguoimuon.*, 
    thietbi.tenthietbi, 
    masothietbi.masothietbi,
    nguoimuon.trangthai,
    khoi.ten_khoi, 
    monhoc.tenmonhoc
FROM nguoimuon
JOIN thietbi ON nguoimuon.thietbi_id = thietbi.id
JOIN masothietbi ON masothietbi.thietbi_id = thietbi.id
JOIN kho ON thietbi.id = kho.thietbi_id
JOIN khoi ON kho.khoi_id = khoi.id
JOIN monhoc ON thietbi.monhoc_id = monhoc.id
$where_sql
GROUP BY nguoimuon.id";


$result_nguoimuon = mysqli_query($conn, $query_nguoimuon);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Người Mượn</title>
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
    <h1>Quản Lý Người Mượn</h1>

    <div class="filter-container">
    <form method="GET" action="quanlynguoimuon.php" id="filterForm">
    <input type="text" name="tennguoimuon" placeholder="Tên người mượn" value="<?= $_GET['tennguoimuon'] ?? '' ?>">
            <select name="khoi" id="khoiSelect">
            <option value="">Chọn Khối</option>
                <?php mysqli_data_seek($result_khoi, 0); while ($row_khoi = mysqli_fetch_assoc($result_khoi)) { ?>
                    <option value="<?= $row_khoi['id'] ?>" <?= (($_GET['khoi'] ?? '') == $row_khoi['id']) ? 'selected' : '' ?>>
                        <?= $row_khoi['ten_khoi'] ?>
                    </option>
                <?php } ?>
            </select>

            <select name="monhoc" id="monhocSelect">
            <option value="">Chọn Môn Học</option>
                <?php mysqli_data_seek($result_monhoc, 0); while ($row_monhoc = mysqli_fetch_assoc($result_monhoc)) { ?>
                    <option value="<?= $row_monhoc['id'] ?>" <?= (($_GET['monhoc'] ?? '') == $row_monhoc['id']) ? 'selected' : '' ?>>
                        <?= $row_monhoc['tenmonhoc'] ?>
                    </option>
                <?php } ?>
            </select>

            <select name="trangthai">
            <option value="">Trạng thái</option>
                <option value="Đang mượn" <?= (($_GET['trangthai'] ?? '') == 'Đang mượn') ? 'selected' : '' ?>>Đang mượn</option>
                <option value="Đã trả" <?= (($_GET['trangthai'] ?? '') == 'Đã trả') ? 'selected' : '' ?>>Đã trả</option>
            </select>

            <button type="submit" class="btn">Tìm kiếm</button>
        </form>
        <form method="POST" action="xoanguoimuon.php" id="delete-form">
    <div class="add-btn-container">
        <a href="#" class="btn btn-delete" id="delete-selected">Xóa đã chọn</a>
        <a href="/quanlytruonghoc/admin/quanlynguoimuon/themnguoimuon.php" class="btn">Thêm mượn</a>
    </div>
</form>
    </div>
    </div>

    <table>
        <thead>
        <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>Khối</th>
            <th>Môn Học</th>
            <th>Tên Thiết Bị</th>
            <th>Tên Người Mượn</th>
            <th>Số Lượng mượn</th>
            <th>Ngày Mượn</th>
            <th>Ngày Trả</th>
            <th>Trạng thái</th>
            <th>Hành Động</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result_nguoimuon)) { ?>
            <tr>
            <td><input type="checkbox" class="select-item" value="<?= $row['id'] ?>"></td>
            <td><?= $row['ten_khoi'] ?></td>
                <td><?= $row['tenmonhoc'] ?></td>
                <td><?= $row['tenthietbi'] ?></td>
                <td><?= $row['tennguoimuon'] ?></td>
                <td><?= $row['soluongmuon'] ?></td>
                <td><?= date('d/m/Y', strtotime($row['ngaymuon'])) ?></td>
                <td><?= date('d/m/Y', strtotime($row['ngaytra'])) ?></td>
                <td>
                    <?php if ($row['trangthai'] == 'Đang mượn') { ?>
                        <span style="color: orange; font-weight: bold;"><?= $row['trangthai'] ?></span>
                    <?php } else { ?>
                        <span style="color: green; font-weight: bold;"><?= $row['trangthai'] ?></span>
                    <?php } ?>
                </td>
                <td>
                <?php if ($row['trangthai'] == 'Đang mượn') { ?>
                        <a href="tra_nguoimuon.php?id=<?= $row['id'] ?>" class="btn btn-edit" onclick="return confirm('Xác nhận thiết bị đã được trả?')">Đã trả</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script>
document.getElementById('khoiSelect').addEventListener('change', function() {
    var khoiId = this.value;
    var monhocSelect = document.getElementById('monhocSelect');

    // Xóa sạch option cũ
    monhocSelect.innerHTML = '<option value="">Chọn Môn Học</option>';

    if (khoiId) {
        fetch('../quanlythietbi/tb_get_monhoc.php?khoi_id=' + khoiId)
            .then(response => response.json())
            .then(data => {
                // Kiểm tra xem có môn học trả về không
                if (data.length > 0) {
                    data.forEach(function(monhoc) {
                        var option = document.createElement('option');
                        option.value = monhoc.id;
                        option.textContent = monhoc.tenmonhoc;
                        monhocSelect.appendChild(option);
                    });
                } else {
                    // Nếu không có môn học nào, thông báo cho người dùng
                    var option = document.createElement('option');
                    option.textContent = 'Không có môn học';
                    option.disabled = true;
                    monhocSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
            });
    } else {
        // Nếu không chọn khối, hiển thị option mặc định
        var option = document.createElement('option');
        option.textContent = 'Chọn Môn Học';
        option.disabled = true;
        monhocSelect.appendChild(option);
    }
});

monhocSelect.addEventListener('change', function () {
        const monhocId = this.value;
        loaiSelect.innerHTML = '<option value="">Đang tải...</option>';
        fetch('../quanlythietbi/tb_get_loaithietbi.php?monhoc_id=' + monhocId)
            .then(response => response.json())
            .then(data => {
                loaiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
                data.forEach(loai => {
                    const opt = document.createElement('option');
                    opt.value = loai.id;
                    opt.textContent = loai.tenloaithietbi;
                    loaiSelect.appendChild(opt);
                });
            })
            .catch(() => {
                loaiSelect.innerHTML = '<option value="">Không tải được loại thiết bị</option>';
            });
    });

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
                xhr.open('POST', 'xoanguoimuon.php', true);
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
</body>
</html>
