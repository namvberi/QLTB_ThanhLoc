<?php
include('config.php');
include('navbar.php');
// Đếm tổng thiết bị
$result_devices = $conn->query("SELECT COUNT(*) as total_devices FROM thietbi");
$row_devices = $result_devices->fetch_assoc();
$total_devices = $row_devices['total_devices'];

// Đếm tổng phòng ban
$result_departments = $conn->query("SELECT COUNT(*) as total_departments FROM phongban");
$row_departments = $result_departments->fetch_assoc();
$total_departments = $row_departments['total_departments'];

// Lấy tổng số nhân viên
$result_employees = $conn->query("SELECT COUNT(*) AS total_employees FROM users");
if ($result_employees) {
    $row_employees = $result_employees->fetch_assoc();
    $total_employees = $row_employees['total_employees'];
} else {
    $total_employees = 0; // Nếu không có kết quả hoặc truy vấn lỗi
}

// Đếm tổng loại thiết bị (giả sử cột `loai` lưu loại thiết bị)
$result_device_types = $conn->query("SELECT COUNT(*) AS total_device_types FROM loaithietbi");
$row_types = $result_device_types->fetch_assoc();
$total_types = $row_types['total_device_types'];

// Đếm tổng số thiết bị đang mượn
$result_borrowed = $conn->query("SELECT COUNT(*) AS total_borrowed_devices FROM masothietbi WHERE trangthai = 'Đang mượn'");
$row_borrowed = $result_borrowed->fetch_assoc();
$total_borrowed = $row_borrowed['total_borrowed_devices'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: rgb(114, 170, 199); margin: 0; padding: 20px; margin-top: 80px; }
        h1 { text-align: center; margin-bottom: 30px; }
        .dashboard-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 300px;
            display: flex;
            align-items: center;
            height: 150px; /* Set the height of the card */
        }
        .card-left {
            width: 30%; /* Chiếm 3 phần 10 chiều rộng của khung */
            height: 100%; /* Chiều cao bằng chiều cao của card */
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
        }
        .card-left i { font-size: 30px; color: white; }
        .card-content { 
            flex: 1; 
        }
        .card h2 { margin-top: 0; font-size: 20px; }
        .card p { font-size: 24px; font-weight: bold; color: #333; }
        .device { background-color: #3498db; }
        .department { background-color: #e67e22; }
        .employee { background-color: #2ecc71; }
        .device-type { background-color: #f39c12; }
        .borrowed { background-color: #9b59b6; } 

    </style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <h1>Admin</h1>
    <div class="dashboard-container">
        <!-- Tổng Thiết Bị -->
        <div class="card">
            <div class="card-left device">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="card-content">
                <h2>Tổng Thiết Bị</h2>
                <p><?php echo $total_devices; ?> thiết bị</p>
            </div>
        </div>
        <!-- Tổng Phòng Ban -->
        <div class="card">
            <div class="card-left department">
                <i class="fas fa-building"></i>
            </div>
            <div class="card-content">
                <h2>Tổng Phòng Ban</h2>
                <p><?php echo $total_departments; ?> phòng ban</p>
            </div>
        </div>
        <!-- Tổng Loại Thiết Bị -->
        <div class="card">
            <div class="card-left device-type">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="card-content">
                <h2>Tổng Loại Thiết Bị</h2>
                <p><?php echo $total_types; ?> loại</p>
            </div>
        </div>
        <!-- Tổng Nhân Viên -->
        <div class="card">
            <div class="card-left employee">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-content">
                <h2>Tổng Nhân Viên</h2>
                <p><?php echo $total_employees; ?> nhân viên</p>
            </div>
        </div>

         <!-- Tổng Thiết Bị Đang Mượn -->
         <div class="card">
            <div class="card-left borrowed">
            <i class="fa-solid fa-tablet"></i>
            </div>
            <div class="card-content">
                <h2>Tổng Thiết Bị Đang Mượn</h2>
                <p><?php echo $total_borrowed; ?> thiết bị</p>
            </div>
        </div>
    </div>
</body>
</html>
