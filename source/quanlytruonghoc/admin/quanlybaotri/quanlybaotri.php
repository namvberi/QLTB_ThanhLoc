<?php
    // Kết nối cơ sở dữ liệu
    include('../config.php');
    include('../navbar.php');

    // Tạo điều kiện tìm kiếm
    $where_conditions = [];

    if (isset($_GET['tenthietbi']) && $_GET['tenthietbi'] != '') {
        $tenthietbi = mysqli_real_escape_string($conn, $_GET['tenthietbi']);
        $where_conditions[] = "thietbi.tenthietbi LIKE '%$tenthietbi%'";
    }

    if (isset($_GET['tinhtrang']) && $_GET['tinhtrang'] != '') {
        $tinhtrang = mysqli_real_escape_string($conn, $_GET['tinhtrang']);
        $where_conditions[] = "baotri.tinhtrang = '$tinhtrang'";
    }

    // Tạo câu truy vấn SQL với các điều kiện tìm kiếm
    $where_sql = '';
    if (count($where_conditions) > 0) {
        $where_sql = ' WHERE ' . implode(' AND ', $where_conditions);
    }

    // Truy vấn lấy thông tin bảo trì với điều kiện tìm kiếm
    $query_baotri = "SELECT baotri.*, thietbi.tenthietbi, thietbi.giatien, masothietbi.masothietbi, masothietbi.tinhtrang, khoi.ten_khoi, baotri.tinhtrang 
                 FROM baotri
                 JOIN masothietbi ON baotri.masothietbi_id = masothietbi.id
                 JOIN thietbi ON masothietbi.thietbi_id = thietbi.id
                 JOIN kho ON thietbi.id = kho.thietbi_id
                 JOIN khoi ON kho.khoi_id = khoi.id" . $where_sql;
                 
$result_baotri = mysqli_query($conn, $query_baotri);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Bảo Trì</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin-top: 100px;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding-top: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-weight: bold;
            font-size: 2rem;
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
            font-weight: bold;
            border-radius: 6px;
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

        img.avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quản Lý Bảo Trì</h1>

        <div class="filter-container">
            <form class="search-form" method="GET" action="quanlybaotri.php">
                <input type="text" name="tenthietbi" placeholder="Tìm kiếm tên thiết bị" value="<?php echo isset($_GET['tenthietbi']) ? $_GET['tenthietbi'] : ''; ?>">
                <select name="tinhtrang">
                <option value="">Chọn Tình trạng</option>
                <option value="Đang bảo trì" <?php echo (isset($_GET['tinhtrang']) && $_GET['tinhtrang'] == 'Đang bảo trì') ? 'selected' : ''; ?>>Đang bảo trì</option>
                <option value="Tốt" <?php echo (isset($_GET['tinhtrang']) && $_GET['tinhtrang'] == 'Tốt') ? 'selected' : ''; ?>>Tốt</option>
            </select>

            <button type="submit" class="btn">Tìm kiếm</button>
        </form>
        <div class="add-btn-container">
        <a href="#" class="btn btn-delete" id="delete-selected">Xóa đã chọn</a>
        <a href="thembaotri.php" class="btn add-btn">Thêm Bảo Trì</a>
        </div>
    </div>
        <table>
            <thead>
                <tr>
                <th><input type="checkbox" id="select-all"></th>
                    <th>Mã Số Thiết Bị</th>
                    <th>Tên Thiết Bị</th>
                    <th>Ngày Sửa</th>
                    <th>Chú Thích</th>
                    <th>Giá tiền</th>
                    <th>Tình trạng</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_baotri = mysqli_fetch_assoc($result_baotri)) { ?>
                    <tr>
                    <td><input type="checkbox" class="select-item" value="<?= $row_baotri['id'] ?>"></td>
                    <td><?php echo $row_baotri['masothietbi']; ?></td>
                        <td><?php echo $row_baotri['tenthietbi']; ?></td>
                        <td><?php echo $row_baotri['ngaysua']; ?></td>
                        <td><?php echo $row_baotri['ghichu']; ?></td>
                        <td><?php echo $row_baotri['giatien']; ?></td>
                        <td><?php echo $row_baotri['tinhtrang']; ?></td>
                        <td>
                        <?php if ($row_baotri['tinhtrang'] != 'Hoàn thành') { ?>
        <a href="trabaotri.php?id=<?php echo $row_baotri['id']; ?>" class="btn btn-edit">Đã xong</a>
    <?php } ?>
    <?php if ($row_baotri['tinhtrang'] == 'Đang bảo trì') { ?>
    <a href="hong.php?id=<?php echo $row_baotri['id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn đổi tình trạng thiết bị thành Hỏng?')">Hỏng</a>
<?php } ?>

                            <a href="xoabaotri.php?id=<?php echo $row_baotri['id']; ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa thông tin bảo trì này?')">Xóa</a>
                            
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
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
                xhr.open('POST', 'xoabaotri.php', true);
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
                const params = new URLSearchParams();
selectedIds.forEach(id => params.append('selected_ids[]', id));
xhr.send(params.toString());            }
        } else {
            alert('Vui lòng chọn ít nhất một mục để xóa');
        }
    });
    </script>
</body>
</html>
