<?php
include('../config.php');
include('../navbar.php');

// Lấy danh sách thiết bị không bị "Đang bảo trì"
$query_thietbi = "SELECT * FROM masothietbi 
                  WHERE tinhtrang != 'Đang bảo trì'"; // Điều kiện lọc thiết bị không phải "Đang bảo trì"
$result_thietbi = mysqli_query($conn, $query_thietbi);

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masothietbi_id = $_POST['masothietbi_id'];
    $ngaysua = $_POST['ngaysua'];
    $diachi = mysqli_real_escape_string($conn, $_POST['diachi']);
    $kiensua = $_POST['kiensua'];
    $nguoisua = mysqli_real_escape_string($conn, $_POST['nguoisua']);
    $chiphi = $_POST['chiphi'];
    $ghichu = mysqli_real_escape_string($conn, $_POST['ghichu']);

    // Lấy id thiết bị từ bảng masothietbi
    $getThietbi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT thietbi_id FROM masothietbi WHERE id = '$masothietbi_id'"));
    $thietbi_id = $getThietbi['thietbi_id'];

    // ✅ Thêm bản ghi bảo trì với trạng thái riêng
    $sql = "INSERT INTO baotri (thietbi_id, masothietbi_id, ngaysua, diachi, kiensua, nguoisua, chiphi, ghichu, tinhtrang)
            VALUES ('$thietbi_id', '$masothietbi_id', '$ngaysua', '$diachi', '$kiensua', '$nguoisua', '$chiphi', '$ghichu', 'Đang bảo trì')";

    if (mysqli_query($conn, $sql)) {
        // ✅ Cập nhật tình trạng tổng thể của thiết bị
        $updateStatus = "UPDATE masothietbi SET trangthai = 'Đang bảo trì' WHERE id = '$masothietbi_id'";
        mysqli_query($conn, $updateStatus);

        echo "<script>alert('Thêm bảo trì thành công!'); window.location.href='quanlybaotri.php';</script>";
        exit;
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}

// Lấy danh sách khối
$khoi_query = mysqli_query($conn, "SELECT * FROM khoi");
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Bảo Trì</title>
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
    <h2>Thêm Bảo Trì Thiết Bị</h2>
    <form method="POST">
        <label>Khối:</label>
        <select id="khoi" name="khoi" required>
            <option value="">-- Chọn khối --</option>
            <?php while ($row = mysqli_fetch_assoc($khoi_query)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['ten_khoi'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Môn học</label>
        <select name="monhoc" id="monhoc">
            <option value="">-- Chọn môn học --</option>
        </select>

        <label>Loại thiết bị</label>
        <select name="loaithietbi" id="loaithietbi">
            <option value="">-- Chọn loại thiết bị --</option>
        </select>

        <label>Thiết bị</label>
        <select name="thietbi" id="thietbi">
            <option value="">-- Chọn thiết bị --</option>
        </select>

        <label>Mã số thiết bị:</label>
        <select name="masothietbi_id" id="masothietbi">
            <option value="">-- Chọn thiết bị --</option>
        </select>

        <label>Ngày Sửa:</label>
        <input type="date" name="ngaysua" required>

        <label>Địa Chỉ:</label>
        <input type="text" name="diachi" required>

        <label>Kiểu Sửa:</label>
        <select name="kiensua" required>
            <option value="sửa phần cứng">Sửa phần cứng</option>
            <option value="sửa phần mềm">Sửa phần mềm</option>
        </select>

        <label>Người Sửa:</label>
        <input type="text" name="nguoisua" required>

        <label>Chi Phí (VNĐ):</label>
        <input type="number" name="chiphi" required>

        <label>Ghi Chú:</label>
        <input type="text" name="ghichu" required>

        <button type="submit">Lưu Thông Tin</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const khoiSelect = document.getElementById('khoi');
    const monhocSelect = document.getElementById('monhoc');
    const loaiThietBiSelect = document.getElementById('loaithietbi');
    const thietbiSelect = document.getElementById('thietbi');
    const masoThietBiSelect = document.getElementById('masothietbi');

    khoiSelect.addEventListener('change', function () {
        const khoi_id = this.value;
        monhocSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch('../quanlythietbi/tb_get_monhoc.php?khoi_id=' + khoi_id)
            .then(response => response.json())
            .then(data => {
                monhocSelect.innerHTML = '<option value="">Chọn Môn Học</option>';
                data.forEach(monhoc => {
                    let opt = document.createElement('option');
                    opt.value = monhoc.id;
                    opt.textContent = monhoc.tenmonhoc;
                    monhocSelect.appendChild(opt);
                });
                loaiThietBiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
                thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
                masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
            })
            .catch(() => {
                monhocSelect.innerHTML = '<option value="">Không tải được môn học</option>';
            });
    });

    monhocSelect.addEventListener('change', function () {
        const monhocId = this.value;
        loaiThietBiSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch('../quanlythietbi/tb_get_loaithietbi.php?monhoc_id=' + monhocId)
            .then(response => response.json())
            .then(data => {
                loaiThietBiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
                data.forEach(loai => {
                    let opt = document.createElement('option');
                    opt.value = loai.id;
                    opt.textContent = loai.tenloaithietbi;
                    loaiThietBiSelect.appendChild(opt);
                });
                thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
                masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
            })
            .catch(() => {
                loaiThietBiSelect.innerHTML = '<option value="">Không tải được loại thiết bị</option>';
            });
    });

    loaiThietBiSelect.addEventListener('change', function () {
        const loaiId = this.value;
        thietbiSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch('../quanlythietbi/tb_get_thietbi.php?loai_id=' + loaiId)
            .then(response => response.json())
            .then(data => {
                thietbiSelect.innerHTML = '<option value="">Chọn Thiết Bị</option>';
                data.forEach(thietbi => {
                    let opt = document.createElement('option');
                    opt.value = thietbi.id;
                    opt.textContent = thietbi.tenthietbi;
                    thietbiSelect.appendChild(opt);
                });
                masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
            })
            .catch(() => {
                thietbiSelect.innerHTML = '<option value="">Không tải được thiết bị</option>';
            });
    });

    thietbiSelect.addEventListener('change', function () {
        const thietbi_id = this.value;
        masoThietBiSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch('../quanlythietbi/tb_get_masothietbi.php?thietbi_id=' + thietbi_id)
            .then(response => response.json())
            .then(data => {
                masoThietBiSelect.innerHTML = '<option value="">Chọn Mã Số Thiết Bị</option>';
                data.forEach(item => {
                    let opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.masothietbi;
                    masoThietBiSelect.appendChild(opt);
                });
            })
            .catch(() => {
                masoThietBiSelect.innerHTML = '<option value="">Không tải được mã thiết bị</option>';
            });
    });
});
</script>
</body>
</html>
