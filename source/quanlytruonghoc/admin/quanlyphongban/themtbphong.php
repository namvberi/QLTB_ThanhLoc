<?php
include('../config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy id phòng ban từ URL
if (!isset($_GET['id'])) {
    echo "Thiếu thông tin phòng ban.";
    exit();
}
$phongban_id = intval($_GET['id']); // ID phòng ban muốn gán

// Lấy danh sách khối
$query_khoi = "SELECT * FROM khoi";
$result_khoi = mysqli_query($conn, $query_khoi);

// Xử lý khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thietbi']) && isset($_POST['soluong'])) {
    $thietbi_id = intval($_POST['thietbi']);
    $soluong = intval($_POST['soluong']);

    // Lấy danh sách các mã số thiết bị chưa gán phòng ban
    $query_masothietbi = "SELECT id FROM masothietbi WHERE thietbi_id = ? AND (phongban_id IS NULL OR phongban_id = 0) LIMIT ?";
    $stmt_masothietbi = $conn->prepare($query_masothietbi);
    $stmt_masothietbi->bind_param("ii", $thietbi_id, $soluong);
    $stmt_masothietbi->execute();
    $result_masothietbi = $stmt_masothietbi->get_result();

    if ($result_masothietbi->num_rows >= $soluong) {
        // Cập nhật phongban_id cho các mã số thiết bị đó
        while ($row = $result_masothietbi->fetch_assoc()) {
            $masothietbi_id = $row['id'];

            $update_stmt = $conn->prepare("UPDATE masothietbi SET phongban_id = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $phongban_id, $masothietbi_id);
            $update_stmt->execute();
        }
        // Cập nhật lại số lượng thiết bị trong bảng phongban
        $update_soluong = $conn->prepare("
        UPDATE phongban 
        SET soluongthietbi = (
            SELECT COUNT(*) 
            FROM masothietbi 
            WHERE phongban_id = ?
        ) 
        WHERE id = ?
    ");
    $update_soluong->bind_param("ii", $phongban_id, $phongban_id);
    $update_soluong->execute();

        echo "<script>alert('Gán thiết bị vào phòng thành công!'); window.location.href='quanlyphongban.php';</script>";
        exit();
    } else {
        echo "<script>alert('Không đủ thiết bị để gán vào phòng.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Thiết Bị vào Phòng</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin-top: 100px;
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
        }
        .container {
            width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Gán thiết bị vào phòng</h1>
    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label for="khoi">Khối</label>
                <select id="khoi" name="khoi" required>
                    <option value="">Chọn Khối</option>
                    <?php while ($row = mysqli_fetch_assoc($result_khoi)) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['ten_khoi']; ?></option>
                    <?php } ?>
                </select>
            </div>    

            <div class="form-group">
                <label for="monhoc">Môn Học</label>
                <select id="monhoc" name="monhoc" required>
                    <option value="">Chọn Môn Học</option>
                </select>
            </div>

            <div class="form-group">
                <label for="loai_thietbi">Loại Thiết Bị</label>
                <select id="loai_thietbi" name="loai_thietbi" required>
                    <option value="">Chọn Loại Thiết Bị</option>
                </select>
            </div>

            <div class="form-group">
                <label for="thietbi">Tên Thiết Bị</label>
                <select name="thietbi" id="thietbi" required>
                    <option value="">Chọn Thiết Bị</option>
                </select>
            </div>
            <div class="form-group">
                <label for="soluong">Số lượng</label>
                <input type="number" name="soluong" id="soluong" min="1" required />
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Gán thiết bị vào phòng</button>
            </div>
        </form>
    </div>      
</div>

<!-- AJAX Load dữ liệu -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const khoiSelect = document.getElementById('khoi');
    const monhocSelect = document.getElementById('monhoc');
    const loaiSelect = document.getElementById('loai_thietbi');
    const thietbiSelect = document.getElementById('thietbi');

    khoiSelect.addEventListener('change', function () {
        const khoiId = this.value;
        monhocSelect.innerHTML = '<option>Đang tải...</option>';

        fetch('../quanlythietbi/tb_get_monhoc.php?khoi_id=' + khoiId)
            .then(response => response.json())
            .then(data => {
                monhocSelect.innerHTML = '<option value="">Chọn Môn Học</option>';
                data.forEach(monhoc => {
                    let opt = document.createElement('option');
                    opt.value = monhoc.id;
                    opt.textContent = monhoc.tenmonhoc;
                    monhocSelect.appendChild(opt);
                });
                loaiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
                thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
            });
    });

    monhocSelect.addEventListener('change', function () {
        const monhocId = this.value;
        loaiSelect.innerHTML = '<option>Đang tải...</option>';

        fetch('../quanlythietbi/tb_get_loaithietbi.php?monhoc_id=' + monhocId)
            .then(response => response.json())
            .then(data => {
                loaiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
                data.forEach(loai => {
                    let opt = document.createElement('option');
                    opt.value = loai.id;
                    opt.textContent = loai.tenloaithietbi;
                    loaiSelect.appendChild(opt);
                });
                thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
            });
    });

    loaiSelect.addEventListener('change', function () {
        const loaiId = this.value;
        thietbiSelect.innerHTML = '<option>Đang tải...</option>';

        fetch('../quanlythietbi/tb_get_thietbi.php?loai_id=' + loaiId)
            .then(response => response.json())
            .then(data => {
                thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
                data.forEach(tb => {
                    let opt = document.createElement('option');
                    opt.value = tb.id;
                    opt.textContent = tb.tenthietbi;
                    thietbiSelect.appendChild(opt);
                });
            });
    });
});
</script>
</body>
</html>
