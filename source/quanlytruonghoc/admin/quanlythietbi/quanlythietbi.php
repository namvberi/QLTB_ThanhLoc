<?php
// Kết nối cơ sở dữ liệu
include('../config.php');
include('../navbar.php');
function getAbbreviation($text) {
    $words = explode(' ', $text); // Tách theo khoảng trắng
    $abbreviation = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $abbreviation .= mb_substr($word, 0, 1, 'UTF-8'); // Lấy chữ cái đầu tiên
        }
    }
    return strtoupper($abbreviation); // Viết hoa toàn bộ
}
// Khởi tạo điều kiện lọc
$where_clause = '';

// Kiểm tra và xử lý tìm kiếm
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause .= " AND thietbi.tenthietbi LIKE '%$search_term%'";
}

// Kiểm tra và xử lý lọc theo khối, môn học, loại thiết bị
if (isset($_GET['khoi']) && $_GET['khoi'] != '') {
    $khoi = mysqli_real_escape_string($conn, $_GET['khoi']);
    $where_clause .= " AND kho.khoi_id = '$khoi'";
}

if (isset($_GET['monhoc']) && $_GET['monhoc'] != '') {
    $monhoc = mysqli_real_escape_string($conn, $_GET['monhoc']);
    $where_clause .= " AND thietbi.monhoc_id = '$monhoc'";
}

if (isset($_GET['loaithietbi']) && $_GET['loaithietbi'] != '') {
    $loaithietbi = mysqli_real_escape_string($conn, $_GET['loaithietbi']);
    $where_clause .= " AND thietbi.loaithietbi_id = '$loaithietbi'";
}

// Truy vấn danh sách thiết bị và các thông tin liên quan
$query_thietbi = "
    SELECT 
        thietbi.*, 
        thietbi.giatien, 
        monhoc.tenmonhoc, 
        loaithietbi.tenloaithietbi, 
        khoi.ten_khoi,
        kho.soluong,
        -- Tính số lượng còn lại
        (kho.soluong 
         - IFNULL(muon_tong.dang_muon, 0) 
         - IFNULL(bao_tri_tong.dang_baotri, 0)
        ) AS soluongconlai,
         -- Tính thành tiền (giá tiền * số lượng còn lại)
        (thietbi.giatien * (kho.soluong 
         - IFNULL(bao_tri_tong.dang_baotri, 0))) AS thanhtien
    FROM thietbi
    JOIN kho ON thietbi.id = kho.thietbi_id
    JOIN khoi ON kho.khoi_id = khoi.id
    JOIN monhoc ON thietbi.monhoc_id = monhoc.id
    JOIN loaithietbi ON thietbi.loaithietbi_id = loaithietbi.id

    -- Tính số lượng đang mượn từ bảng masothietbi
    LEFT JOIN (
        SELECT thietbi_id, COUNT(*) AS dang_muon
        FROM masothietbi
        WHERE trangthai = 'Đang mượn'
        GROUP BY thietbi_id
    ) AS muon_tong ON thietbi.id = muon_tong.thietbi_id

    -- Tính số lượng đang bảo trì từ bảng masothietbi
    LEFT JOIN (
        SELECT thietbi_id, COUNT(*) AS dang_baotri
        FROM masothietbi
        WHERE tinhtrang = 'Đang bảo trì'
        GROUP BY thietbi_id
    ) AS bao_tri_tong ON thietbi.id = bao_tri_tong.thietbi_id

    WHERE 1=1 $where_clause
";




$result_thietbi = mysqli_query($conn, $query_thietbi);

if (!$result_thietbi) {
    echo "Lỗi truy vấn: " . mysqli_error($conn);
}

$result_khoi = mysqli_query($conn, "SELECT * FROM khoi");
$result_monhoc = mysqli_query($conn, "SELECT * FROM monhoc");
$result_loaithietbi = mysqli_query($conn, "SELECT * FROM loaithietbi");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thiết Bị</title>
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
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s, transform 0.2s;
            border: 1px solid #333;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        .btn:active {
            background-color: #004080;
            transform: translateY(1px);
        }

        .add-btn-container {
            text-align: right;
        }

        .action-buttons a {
            margin-right: 10px;
        }

        .action-buttons .btn-edit {
            background-color: #28a745;
        }

        .action-buttons .btn-edit:hover {
            background-color: #218838;
        }

        .action-buttons .btn-delete {
            background-color: #dc3545;
        }

        .action-buttons .btn-delete:hover {
            background-color: #c82333;
        }

        .filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-container input,
        .filter-container select {
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .filter-container .btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quản Lý Thiết Bị</h1>

        <!-- Form lọc -->
        <div class="filter-container">
            <form method="GET" action="quanlythietbi.php" style="display: flex; flex-grow: 1;">
                <input type="text" name="search" placeholder="Tìm kiếm tên thiết bị" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" />
                
                <select name="khoi" id="khoiSelect">
                    <option value="">Chọn Khối</option>
                    <?php while ($row_khoi = mysqli_fetch_assoc($result_khoi)) { ?>
                        <option value="<?php echo $row_khoi['id']; ?>" <?php echo (isset($_GET['khoi']) && $_GET['khoi'] == $row_khoi['id']) ? 'selected' : ''; ?>>
                            <?php echo $row_khoi['ten_khoi']; ?>
                        </option>
                    <?php } ?>
                </select>

                <select name="monhoc" id="monhocSelect">
                    <option value="">Chọn Môn Học</option>
                    <?php while ($row_monhoc = mysqli_fetch_assoc($result_monhoc)) { ?>
                        <option value="<?php echo $row_monhoc['id']; ?>" <?php echo (isset($_GET['monhoc']) && $_GET['monhoc'] == $row_monhoc['id']) ? 'selected' : ''; ?>>
                            <?php echo $row_monhoc['tenmonhoc']; ?>
                        </option>
                    <?php } ?>
                </select>

                <select name="loaithietbi" id="loaiSelect">
                    <option value="">Chọn Loại Thiết Bị</option>
                    <?php while ($row_loaithietbi = mysqli_fetch_assoc($result_loaithietbi)) { ?>
                        <option value="<?php echo $row_loaithietbi['id']; ?>" <?php echo (isset($_GET['loaithietbi']) && $_GET['loaithietbi'] == $row_loaithietbi['id']) ? 'selected' : ''; ?>>
                            <?php echo $row_loaithietbi['tenloaithietbi']; ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="submit" class="btn">Tìm kiếm</button>
            </form>
            <!-- Nút thêm thiết bị -->
            <div class="add-btn-container">
                <a href="/quanlytruonghoc/admin/quanlythietbi/themthietbi.php" class="btn">Thêm Thiết Bị</a>
            </div>
        </div>
        <!-- Bảng thiết bị -->
        <table>
            <thead>
                <tr>
                    <th>Mã Thiết Bị</th>
                    <th>Tên Thiết Bị</th>
                    <th>Khối</th>
                    <th>Môn Học</th>
                    <th>Loại Thiết Bị</th>
                    <th>Số lượng trong kho</th>
                    <th>Số Lượng còn lại</th>
                    <th>Thành tiền</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_thietbi) > 0) {
                    while ($row_thietbi = mysqli_fetch_assoc($result_thietbi)) {
                        // Lấy chữ viết tắt của tên thiết bị
                        $prefix = getAbbreviation($row_thietbi['tenthietbi']);
                        ?>
                        <tr>
                            <td><?php echo $prefix; ?></td> <!-- Chỉ hiển thị chữ viết tắt -->
                            <td><?php echo $row_thietbi['tenthietbi']; ?></td>
                            <td><?php echo $row_thietbi['ten_khoi']; ?></td>
                            <td><?php echo $row_thietbi['tenmonhoc']; ?></td>
                            <td><?php echo $row_thietbi['tenloaithietbi']; ?></td>
                            <td><?php echo $row_thietbi['soluong']; ?></td>
                            <td><?php echo $row_thietbi['soluongconlai']; ?></td>
                            <td><?php echo $row_thietbi['thanhtien']; ?></td>


                            <td class="action-buttons">
                                <a href="chitietthietbi.php?id=<?php echo $row_thietbi['id']; ?>" class="btn btn-view" style="background-color:#17a2b8;">Chi tiết</a>
                                <a href="xoathietbi.php?id=<?php echo $row_thietbi['id']; ?>" class="btn btn-delete">Xóa</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="8">Không có thiết bị nào.</td></tr>';
                }
                ?>
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
document.getElementById('monhocSelect').addEventListener('change', function() {
    var monhocId = this.value;
    var loaithietbiSelect = document.getElementById('loaiSelect');

    // Xóa sạch option cũ
    loaithietbiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';

    if (monhocId) {
        fetch('../quanlythietbi/tb_get_loaithietbi.php?monhoc_id=' + monhocId)
            .then(response => response.json())
            .then(data => {
                // Kiểm tra xem có loại thiết bị trả về không
                if (data.length > 0) {
                    data.forEach(function(loaithietbi) {
                        var option = document.createElement('option');
                        option.value = loaithietbi.id;
                        option.textContent = loaithietbi.tenloaithietbi;
                        loaithietbiSelect.appendChild(option);
                    });
                } else {
                    // Nếu không có loại thiết bị nào, thông báo cho người dùng
                    var option = document.createElement('option');
                    option.textContent = 'Không có loại thiết bị';
                    option.disabled = true;
                    loaithietbiSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
            });
    } else {
        // Nếu không chọn môn học, hiển thị option mặc định
        var option = document.createElement('option');
        option.textContent = 'Chọn Loại Thiết Bị';
        option.disabled = true;
        loaithietbiSelect.appendChild(option);
    }
});
    </script>
</body>
</html>
