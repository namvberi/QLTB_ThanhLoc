<?php
include('../config.php');
include('../navbar.php');

// Lấy danh sách khối
$query_khoi = "SELECT * FROM khoi";
$result_khoi = mysqli_query($conn, $query_khoi);

// Xử lý form thêm thiết bị
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenthietbi = $_POST['ten_thietbi'];
    $loaithietbi_id = $_POST['loai_thietbi'];
    $monhoc_id = $_POST['monhoc'];
    $giatien = $_POST['giatien'];
    $soluong = $_POST['soluong'];

    // Lấy khoi_id từ bảng monhoc
    $query_khoi_id = "SELECT khoi_id FROM monhoc WHERE id = '$monhoc_id'";
    $result_khoi_id = mysqli_query($conn, $query_khoi_id);
    $khoi_id = mysqli_fetch_assoc($result_khoi_id)['khoi_id'];

    // Kiểm tra xem thiết bị đã tồn tại chưa (dựa vào tên thiết bị, loại thiết bị và môn học)
    $query_check_thietbi = "SELECT * FROM thietbi WHERE tenthietbi = '$tenthietbi' AND giatien = '$giatien' AND loaithietbi_id = '$loaithietbi_id' AND monhoc_id = '$monhoc_id'";
    $result_check_thietbi = mysqli_query($conn, $query_check_thietbi);

    if (mysqli_num_rows($result_check_thietbi) > 0) {
        // Thiết bị đã tồn tại
        $thietbi_row = mysqli_fetch_assoc($result_check_thietbi);
        $thietbi_id = $thietbi_row['id'];

        // Cập nhật số lượng trong kho
        $query_check_kho = "SELECT * FROM kho WHERE thietbi_id = '$thietbi_id' AND khoi_id = '$khoi_id'";
        $result_check_kho = mysqli_query($conn, $query_check_kho);

        if (mysqli_num_rows($result_check_kho) > 0) {
            $query_update_kho = "UPDATE kho SET soluong = soluong + $soluong WHERE thietbi_id = '$thietbi_id' AND khoi_id = '$khoi_id'";
            mysqli_query($conn, $query_update_kho);
        } else {
            $query_insert_kho = "INSERT INTO kho (thietbi_id, khoi_id, soluong) VALUES ('$thietbi_id', '$khoi_id', '$soluong')";
            mysqli_query($conn, $query_insert_kho);
        }

        // Lấy mã số thiết bị của thiết bị đã tồn tại
// Tạo prefix từ tên thiết bị
$words = explode(' ', $tenthietbi);
$prefix = '';
foreach ($words as $word) {
    $prefix .= strtoupper(mb_substr($word, 0, 1));
    if (strlen($prefix) == 2) break;
}
if (strlen($prefix) < 2) {
    $prefix = strtoupper(mb_substr($tenthietbi, 0, 2));
}

// Lấy mã số cuối cùng theo prefix
$query_ma_thietbi = "SELECT masothietbi FROM masothietbi 
                     WHERE masothietbi LIKE '$prefix%' 
                     ORDER BY id DESC LIMIT 1";
$result_ma_thietbi = mysqli_query($conn, $query_ma_thietbi);
$last_ma = $prefix . '000';
if ($row = mysqli_fetch_assoc($result_ma_thietbi)) {
    $last_ma = $row['masothietbi'];
}
$number = (int)substr($last_ma, strlen($prefix));
        $result_ma_thietbi = mysqli_query($conn, $query_ma_thietbi);
        $last_ma = 'MT000';
        if ($row = mysqli_fetch_assoc($result_ma_thietbi)) {
            $last_ma = $row['masothietbi'];
        }
        $number = (int)substr($last_ma, 2);

        // Tạo mã số thiết bị mới
        $words = explode(' ', $tenthietbi);
        $prefix = '';
        foreach ($words as $word) {
            $prefix .= strtoupper(mb_substr($word, 0, 1));
            if (strlen($prefix) == 2) break;
        }
        if (strlen($prefix) < 2) {
            $prefix = strtoupper(mb_substr($tenthietbi, 0, 2));
        }

        echo "Prefix: $prefix <br>";

        // Thêm mã số thiết bị mới nếu số lượng thay đổi
        for ($i = 1; $i <= $soluong; $i++) {
            $number++;
            $ma = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
            $query_insert_ma = "INSERT INTO masothietbi (masothietbi, thietbi_id, tinhtrang) 
                                VALUES ('$ma', '$thietbi_id', 'Tốt')";
            mysqli_query($conn, $query_insert_ma);
        }

        echo "<script>alert('Cập nhật thiết bị thành công!'); window.location='quanlythietbi.php';</script>";
    } else {
        // Nếu thiết bị chưa tồn tại, thực hiện thêm mới thiết bị
        $query_insert_thietbi = "INSERT INTO thietbi (tenthietbi, loaithietbi_id, monhoc_id, giatien)
                                 VALUES ('$tenthietbi', '$loaithietbi_id', '$monhoc_id', '$giatien')";
        if (mysqli_query($conn, $query_insert_thietbi)) {
            $thietbi_id = mysqli_insert_id($conn);

            // Thêm vào bảng kho
            $query_insert_kho = "INSERT INTO kho (thietbi_id, khoi_id, soluong) 
                                 VALUES ('$thietbi_id', '$khoi_id', '$soluong')";
            mysqli_query($conn, $query_insert_kho);

            // Lấy mã số thiết bị mới
// Tạo prefix từ tên thiết bị
$words = explode(' ', $tenthietbi);
$prefix = '';
foreach ($words as $word) {
    $prefix .= strtoupper(mb_substr($word, 0, 1));
    if (strlen($prefix) == 2) break;
}
if (strlen($prefix) < 2) {
    $prefix = strtoupper(mb_substr($tenthietbi, 0, 2));
}

// Lấy mã số cuối cùng theo prefix
$query_ma_thietbi = "SELECT masothietbi FROM masothietbi 
                     WHERE masothietbi LIKE '$prefix%' 
                     ORDER BY id DESC LIMIT 1";
$result_ma_thietbi = mysqli_query($conn, $query_ma_thietbi);
$last_ma = $prefix . '000';
if ($row = mysqli_fetch_assoc($result_ma_thietbi)) {
    $last_ma = $row['masothietbi'];
}
$number = (int)substr($last_ma, strlen($prefix));
            $result_ma_thietbi = mysqli_query($conn, $query_ma_thietbi);
            $last_ma = 'MT000';
            if ($row = mysqli_fetch_assoc($result_ma_thietbi)) {
                $last_ma = $row['masothietbi'];
            }
            $number = (int)substr($last_ma, 2);

            // Tạo mã số thiết bị theo tên
            $words = explode(' ', $tenthietbi);
            $prefix = '';
            foreach ($words as $word) {
                $prefix .= strtoupper(mb_substr($word, 0, 1));
                if (strlen($prefix) == 2) break;
            }
            if (strlen($prefix) < 2) {
                $prefix = strtoupper(mb_substr($tenthietbi, 0, 2));
            }

            // Thêm mã thiết bị
            for ($i = 1; $i <= $soluong; $i++) {
                $number++;
                $ma = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
                $query_insert_ma = "INSERT INTO masothietbi (masothietbi, thietbi_id, tinhtrang)
                                    VALUES ('$ma', '$thietbi_id', 'Tốt')";
                mysqli_query($conn, $query_insert_ma);
            }

            echo "<script>alert('Thêm thiết bị thành công!'); window.location='quanlythietbi.php';</script>";
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Thiết Bị</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin-top: 100px;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: auto;
            padding-top: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 16px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Thêm Thiết Bị Mới</h1>
    <div class="form-container">
        <form method="POST">
            <!-- Khối -->
            <div class="form-group">
                <label for="khoi">Khối</label>
                <select id="khoi" name="khoi" required>
                    <option value="">Chọn Khối</option>
                    <?php while ($row = mysqli_fetch_assoc($result_khoi)) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['ten_khoi']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Môn Học -->
            <div class="form-group">
                <label for="monhoc">Môn Học</label>
                <select id="monhoc" name="monhoc" required>
                    <option value="">Chọn Môn Học</option>
                </select>
            </div>

            <!-- Loại Thiết Bị -->
            <div class="form-group">
                <label for="loai_thietbi">Loại Thiết Bị</label>
                <select id="loai_thietbi" name="loai_thietbi" required>
                    <option value="">Chọn Loại Thiết Bị</option>
                </select>
            </div>

            <!-- Tên Thiết Bị -->
            <div class="form-group">
                <label for="ten_thietbi">Tên Thiết Bị</label>
                <input type="text" id="ten_thietbi" name="ten_thietbi" required>
            </div>

            <!-- Số Lượng -->
            <div class="form-group">
                <label for="soluong">Số Lượng</label>
                <input type="number" id="soluong" name="soluong" required min="1" value="1">
            </div>

            <!-- Giá Tiền -->
            <div class="form-group">
                <label for="giatien">Giá Tiền</label>
                <input type="number" id="giatien" name="giatien" required>
            </div>

            <!-- Nút Thêm -->
            <div class="form-group">
                <button type="submit" class="btn">Thêm Thiết Bị</button>
            </div>
        </form>
    </div>
</div>

<!-- AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const khoiSelect = document.getElementById('khoi');
    const monhocSelect = document.getElementById('monhoc');
    const loaiSelect = document.getElementById('loai_thietbi');

    khoiSelect.addEventListener('change', function () {
        const khoiId = this.value;
        monhocSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch('tb_get_monhoc.php?khoi_id=' + khoiId)
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
            })
            .catch(() => {
                monhocSelect.innerHTML = '<option value="">Không tải được môn học</option>';
            });
    });

    monhocSelect.addEventListener('change', function () {
        const monhocId = this.value;
        loaiSelect.innerHTML = '<option value="">Đang tải...</option>';
        fetch('tb_get_loaithietbi.php?monhoc_id=' + monhocId)
            .then(response => response.json())
            .then(data => {
                loaiSelect.innerHTML = '<option value="">Chọn Loại Thiết Bị</option>';
                data.forEach(loai => {
                    let opt = document.createElement('option');
                    opt.value = loai.id;
                    opt.textContent = loai.tenloaithietbi;
                    loaiSelect.appendChild(opt);
                });
            })
            .catch(() => {
                loaiSelect.innerHTML = '<option value="">Không tải được loại thiết bị</option>';
            });
    });
});
</script>
</body>
</html>
