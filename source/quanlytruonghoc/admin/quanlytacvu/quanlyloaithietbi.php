<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối cơ sở dữ liệu
include '../config.php';
include '../navbar.php';

// Truy vấn để lấy danh sách loại thiết bị kèm theo tên môn học
$danhsach_query = "
    SELECT lt.id, lt.tenloaithietbi, mh.tenmonhoc 
    FROM loaithietbi lt
    JOIN monhoc mh ON lt.monhoc_id = mh.id
";
$danhsach_result = mysqli_query($conn, $danhsach_query);

// Xử lý thêm loại thiết bị
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tenloaithietbi']) && isset($_POST['monhoc'])) {
    $tenloaithietbi = trim($_POST['tenloaithietbi']);
    $monhoc_id = intval($_POST['monhoc']);
    $khoi_id = intval($_POST['khoi']); // ✅ Đổi tên biến để khớp với HTML

    // Kiểm tra xem đã tồn tại loại thiết bị này trong môn học đó chưa
    $check_sql = "SELECT * FROM loaithietbi WHERE tenloaithietbi = ? AND monhoc_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $tenloaithietbi, $monhoc_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Loại thiết bị này đã tồn tại trong môn học đã chọn.');</script>";
    } else {
        $insert_sql = "INSERT INTO loaithietbi (tenloaithietbi, monhoc_id, khoi_id) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $insert_sql);
mysqli_stmt_bind_param($stmt, "sii", $tenloaithietbi, $monhoc_id, $khoi_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Thêm loại thiết bị thành công!'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        } else {
            echo "<script>alert('Có lỗi xảy ra khi thêm loại thiết bị.');</script>";
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($check_stmt);
}

// Xử lý xóa loại thiết bị
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $delete_sql = "DELETE FROM loaithietbi WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Đã xóa loại thiết bị'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa.');</script>";
    }
    mysqli_stmt_close($stmt);
}

// Lấy danh sách khối
$khoi_query = "SELECT * FROM khoi";
$khoi_result = mysqli_query($conn, $khoi_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Loại Thiết Bị</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 100px;
            padding: 0;
            background-color: rgb(114, 170, 199);
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
        }

        .add-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            text-decoration: none;
            margin: 20px auto;
            display: block;
            width: 200px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-btn:hover {
            background-color: #0056b3;
             transform: translateY(-3px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .form-container {
            display: none;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #27ae60;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
    <script>
        function toggleForm() {
            var form = document.getElementById('addForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function loadMonhocByKhoi(khoi_id) {
            if (!khoi_id) {
                document.getElementById("monhoc").innerHTML = "<option value=''>Chọn môn học</option>";
                return;
            }

            var xhttp = new XMLHttpRequest();
            xhttp.open("GET", "qltv_getmonhoc.php?khoi_id=" + khoi_id, true);
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("monhoc").innerHTML = this.responseText;
                }
            };
            xhttp.send();
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>🔌Quản lý Loại Thiết Bị</h2>
        <button class="add-btn" onclick="toggleForm()">➕ Thêm loại thiết bị</button>

        <!-- Form thêm loại thiết bị -->
        <div id="addForm" class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="khoi">Khối:</label>
                    <select id="khoi" name="khoi" onchange="loadMonhocByKhoi(this.value)">
                        <option value="">Chọn khối</option>
                        <?php while ($k = mysqli_fetch_assoc($khoi_result)) : ?>
                            <option value="<?= $k['id'] ?>"><?= $k['ten_khoi'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="monhoc">Môn học:</label>
                    <select name="monhoc" id="monhoc" required>
                        <option value="">Chọn môn học</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tenloaithietbi">Tên loại thiết bị:</label>
                    <input type="text" name="tenloaithietbi" id="tenloaithietbi" required placeholder="Nhập tên loại thiết bị">
                </div>

                <button type="submit">Thêm</button>
            </form>
        </div>

        <!-- Bảng danh sách loại thiết bị -->
        <table>
            <thead>
                <tr>
                    <th>Tên Loại Thiết Bị</th>
                    <th>Môn Học</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($danhsach_result)) : ?>
                    <tr>
                        <td><?= $row['tenloaithietbi'] ?></td>
                        <td><?= $row['tenmonhoc'] ?></td>
                        <td><a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Bạn có chắc muốn xóa?')">🗑️ Xóa</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
