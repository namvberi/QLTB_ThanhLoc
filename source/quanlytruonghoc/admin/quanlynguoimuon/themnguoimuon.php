<?php
ob_start();
include('../config.php');
include('../navbar.php');

$result_khoi = mysqli_query($conn, "SELECT * FROM khoi");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tennguoimuon = $_POST['tennguoimuon'];
    $thietbi_id = $_POST['thietbi'];
    $soluongmuon = isset($_POST['soluongmuon']) ? (int)$_POST['soluongmuon'] : 0;
    $ngaymuon = date('Y-m-d', strtotime($_POST['ngaymuon']));
    $ngaytra = date('Y-m-d', strtotime($_POST['ngaytra']));
    $trangthai = 'Đang mượn';

    // Kiểm tra số lượng mã số thiết bị đang "Sẵn sàng"
    $check_query = "SELECT id FROM masothietbi WHERE thietbi_id = '$thietbi_id' AND trangthai = 'Sẵn sàng'";
    $result = mysqli_query($conn, $check_query);

    $available_ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $available_ids[] = $row['id'];
    }

    if (count($available_ids) < $soluongmuon) {
        echo "<script>alert('Không đủ thiết bị sẵn sàng để mượn!'); window.history.back();</script>";
        exit();
    }

    // Thêm người mượn
    $sql = "INSERT INTO nguoimuon (tennguoimuon, thietbi_id, soluongmuon, ngaymuon, ngaytra)
            VALUES ('$tennguoimuon', '$thietbi_id', '$soluongmuon', '$ngaymuon', '$ngaytra')";

    if (mysqli_query($conn, $sql)) {
        $nguoimuon_id = mysqli_insert_id($conn);
        $masothietbi_ids = array_slice($available_ids, 0, $soluongmuon);

        // Lưu chi tiết từng thiết bị mượn
        foreach ($masothietbi_ids as $ms_id) {
            mysqli_query($conn, "INSERT INTO chitietmuon (nguoimuon_id, masothietbi_id) VALUES ($nguoimuon_id, $ms_id)");
        }

        // Cập nhật trạng thái thiết bị sang "Đang mượn"
        $ids_str = implode(",", $masothietbi_ids);
        $update_trangthai = "UPDATE masothietbi SET trangthai = 'Đang mượn' WHERE id IN ($ids_str)";
        mysqli_query($conn, $update_trangthai);

        header("Location: quanlynguoimuon.php");
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Người Mượn</title>
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
    <h2>Thêm Người Mượn</h2>
    <form method="POST">
        <label>Khối</label>
        <select name="khoi" id="khoi">
            <option value="">-- Chọn khối --</option>
            <?php while ($row = mysqli_fetch_assoc($result_khoi)) { ?>
                <option value="<?= $row['id'] ?>"><?= $row['ten_khoi'] ?></option>
            <?php } ?>
        </select>

        <label>Môn học</label>
        <select name="monhoc" id="monhoc">
            <option value="">-- Chọn môn học --</option>
        </select>

        <label>Loại thiết bị</label>
        <select name="loai_thietbi" id="loai_thietbi">
            <option value="">-- Chọn loại thiết bị --</option>
        </select>

        <label>Thiết bị</label>
        <select name="thietbi" id="thietbi">
            <option value="">-- Chọn thiết bị --</option>
        </select>

        <label>Tên người mượn</label>
        <input type="text" name="tennguoimuon" required>

        <label for="soluongmuon">Số lượng mượn:</label>
        <input type="number" name="soluongmuon" id="soluongmuon" required>

        <label>Ngày mượn</label>
        <input type="date" name="ngaymuon" required>

        <label>Ngày trả</label>
        <input type="date" name="ngaytra" required>

        <button type="submit">Thêm</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const khoiSelect = document.getElementById('khoi');
    const monhocSelect = document.getElementById('monhoc');
    const loaiSelect = document.getElementById('loai_thietbi');
    const thietbiSelect = document.getElementById('thietbi');

    // Khi chọn khối
    khoiSelect.addEventListener('change', function () {
        const khoiId = this.value;
        monhocSelect.innerHTML = '<option value="">Đang tải...</option>';
        fetch('../quanlythietbi/tb_get_monhoc.php?khoi_id=' + khoiId)
            .then(response => response.json())
            .then(data => {
                monhocSelect.innerHTML = '<option value="">-- Chọn môn học --</option>';
                data.forEach(monhoc => {
                    const opt = document.createElement('option');
                    opt.value = monhoc.id;
                    opt.textContent = monhoc.tenmonhoc;
                    monhocSelect.appendChild(opt);
                });
                loaiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
            })
            .catch(() => {
                monhocSelect.innerHTML = '<option value="">Không tải được môn học</option>';
                loaiSelect.innerHTML = '<option value="">Không tải được loại thiết bị</option>';
            });
    });

    // Khi chọn môn học
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

    // Khi chọn loại thiết bị
    loaiSelect.addEventListener('change', function () {
        const loaiId = this.value;
        thietbiSelect.innerHTML = '<option value="">Đang tải...</option>';
        fetch('../quanlythietbi/tb_get_thietbi.php?loai_id=' + loaiId)
            .then(response => response.json())
            .then(data => {
                thietbiSelect.innerHTML = '<option value="">-- Chọn thiết bị --</option>';
                data.forEach(tb => {
                    const opt = document.createElement('option');
                    opt.value = tb.id;
                    opt.textContent = tb.tenthietbi;
                    thietbiSelect.appendChild(opt);
                });
            })
            .catch(() => {
                thietbiSelect.innerHTML = '<option value="">Không tải được thiết bị</option>';
            });
    });
});
</script>
</body>
</html>
