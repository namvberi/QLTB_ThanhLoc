<?php
include '../config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}
    
$khoi_query = mysqli_query($conn, "SELECT * FROM khoi");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gửi đơn bảo trì thiết bị</title>
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
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<div class="container">
<h2>Gửi đơn bảo trì thiết bị</h2>

<form action="xuly_donbaotri.php" method="POST">
    <input type="hidden" name="loai_don" value="Bảo trì thiết bị">

    <label>Khối:</label>
    <select name="khoi" id="khoi" required>
        <option value="">-- Chọn khối --</option>
        <?php while ($row = mysqli_fetch_assoc($khoi_query)) : ?>
            <option value="<?= $row['id'] ?>"><?= $row['ten_khoi'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Môn học:</label>
    <select name="monhoc" id="monhoc" required></select><br><br>

    <label>Loại thiết bị:</label>
    <select name="loaithietbi" id="loaithietbi" required></select><br><br>

    <label>Thiết bị:</label>
    <select name="thietbi" id="thietbi" required></select><br><br>

    <label>Mã số thiết bị:</label>
    <select name="masothietbi" id="masothietbi" required></select><br><br>

    <label>Ghi Chú:</label>
    <input type="text" name="ghichu" required>
    <button type="submit">Gửi đơn</button>
    <a href="guidon.php" class="btn-back">← Quay lại danh sách</a>
</form>
</div>
<script>
// JS đổ dữ liệu chọn theo khối -> môn -> loại thiết bị -> thiết bị -> mã số thiết bị
const khoiSelect = document.getElementById('khoi');
const monhocSelect = document.getElementById('monhoc');
const loaiThietBiSelect = document.getElementById('loaithietbi');
const thietbiSelect = document.getElementById('thietbi');
const masoThietBiSelect = document.getElementById('masothietbi');

khoiSelect.addEventListener('change', function () {
    const khoi_id = this.value;
    monhocSelect.innerHTML = '<option value="">Đang tải...</option>';
    fetch('../quanlythietbi/tb_get_monhoc.php?khoi_id=' + khoi_id)
        .then(res => res.json())
        .then(data => {
            monhocSelect.innerHTML = '<option value="">Chọn Môn Học</option>';
            data.forEach(mon => monhocSelect.innerHTML += `<option value="${mon.id}">${mon.tenmonhoc}</option>`);
            loaiThietBiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
            thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
            masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
        });
});

monhocSelect.addEventListener('change', function () {
    const monhoc_id = this.value;
    loaiThietBiSelect.innerHTML = '<option value="">Đang tải...</option>';
    fetch('../quanlythietbi/tb_get_loaithietbi.php?monhoc_id=' + monhoc_id)
        .then(res => res.json())
        .then(data => {
            loaiThietBiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
            data.forEach(loai => loaiThietBiSelect.innerHTML += `<option value="${loai.id}">${loai.tenloaithietbi}</option>`);
            thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
            masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
        });
});

loaiThietBiSelect.addEventListener('change', function () {
    const loai_id = this.value;
    thietbiSelect.innerHTML = '<option value="">Đang tải...</option>';
    fetch('../quanlythietbi/tb_get_thietbi.php?loai_id=' + loai_id)
        .then(res => res.json())
        .then(data => {
            thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
            data.forEach(tb => thietbiSelect.innerHTML += `<option value="${tb.id}">${tb.tenthietbi}</option>`);
            masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
        });
});

thietbiSelect.addEventListener('change', function () {
    const thietbi_id = this.value;
    masoThietBiSelect.innerHTML = '<option value="">Đang tải...</option>';
    fetch('../quanlythietbi/tb_get_masothietbi.php?thietbi_id=' + thietbi_id)
        .then(res => res.json())
        .then(data => {
            masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
            data.forEach(item => masoThietBiSelect.innerHTML += `<option value="${item.id}">${item.masothietbi}</option>`);
        });
});
</script>
</body>
</html>
