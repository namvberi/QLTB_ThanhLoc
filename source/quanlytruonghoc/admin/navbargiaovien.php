<?php
include('config.php');
$current_page = basename($_SERVER['PHP_SELF']);

if (isset($_SESSION['users_id'])) {
    $usersId = $_SESSION['users_id'];

    $sql = "SELECT image FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usersId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $avatar = $row['image'];
} else {
    echo 'chưa đăng nhập';
    exit;
}

// Đếm số đơn đã duyệt của giáo viên
$sql_approved = "SELECT COUNT(*) AS total FROM donthietbi WHERE users_id = ? AND trangthai = 'Đã duyệt'";
$stmt = $conn->prepare($sql_approved);
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result_approved = $stmt->get_result();
$row_approved = $result_approved->fetch_assoc();
$total_don_approved = $row_approved['total'];

// Đếm số đơn chờ duyệt của giáo viên
$sql_pending = "SELECT COUNT(*) AS total FROM donthietbi WHERE users_id = ? AND trangthai = 'Chờ duyệt'";
$stmt = $conn->prepare($sql_pending);
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result_pending = $stmt->get_result();
$row_pending = $result_pending->fetch_assoc();
$total_don_pending = $row_pending['total'];

// Đếm số đơn bị từ chối của giáo viên
$sql_rejected = "SELECT COUNT(*) AS total FROM donthietbi WHERE users_id = ? AND trangthai = 'Đã từ chối'";
$stmt = $conn->prepare($sql_rejected);
$stmt->bind_param("i", $usersId);
$stmt->execute();
$result_rejected = $stmt->get_result();
$row_rejected = $result_rejected->fetch_assoc();
$total_don_rejected = $row_rejected['total'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Giáo Viên</title>
    <style>
        .navbar {
            background-color: #2c3e50;
            padding: 5px 15px;
            display: flex;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        .navbar img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            margin-right: 10px;
            border: none;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 10px;
            font-weight: bold;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .navbar a:hover {
            background-color: #3498db;
            color: #fff;
        }
        .navbar-title {
            font-size: 14px;
            margin-right: auto;
            font-weight: bold;
        }
        .avatar-container {
            position: relative;
            margin-left: 10px;
            cursor: pointer;
        }
        .avatar {
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }
        .dropdown, .notification-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 45px;
            background-color: #34495e;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            min-width: 180px;
            z-index: 1000;
        }
        .dropdown a, .notification-dropdown a {
            color: white;
            padding: 10px;
            display: block;
            text-decoration: none;
            font-size: 14px;
        }
        .dropdown a:hover, .notification-dropdown a:hover {
            background-color: #3498db;
        }
        .avatar-container:hover .dropdown {
            display: block;
        }
        .notification {
            position: relative;
            display: inline-block;
            margin-right: 20px;
        }
        .notification .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            padding: 4px 7px;
            border-radius: 50%;
            background: red;
            color: white;
            font-size: 12px;
        }
        .notification-dropdown {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 300px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0;
            top: 30px;
            border-radius: 8px;
            overflow: hidden;
        }
        .notification-dropdown a {
            color: black;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            border-bottom: 1px solid #ddd;
        }
        .notification-dropdown a:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="http://localhost:8080/quanlytruonghoc/admin/images/logo.jpg" alt="Logo" class="logo" />
        <div class="navbar-title">THCS Thạnh Lộc</div>
        <a href="/quanlytruonghoc/admin/quanlydon/guidon.php">Gửi đơn</a>

        <div class="avatar-container">
            <img src="/quanlytruonghoc/admin/images/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar" />
            <div class="dropdown">
                <a href="/quanlytruonghoc/admin/profile.php">👤 Profile</a>
                <div class="notification">
    <span id="notifBell" style="font-size: 14px; cursor: pointer; padding: 10px;">🔔 Thông báo</span>
    <?php
    $total_all = $total_don_approved + $total_don_pending + $total_don_rejected;
    if ($total_all > 0): ?>
        <span class="badge"><?php echo $total_all; ?></span>
    <?php endif; ?>
    <div class="notification-dropdown" id="notifDropdown">
        <?php if ($total_all > 0): ?>
            <a href="/quanlytruonghoc/admin/thongbaousers/tatcathongbao.php">
                Bạn có <?php echo $total_all; ?> thông báo về đơn thiết bị.
            </a>
        <?php else: ?>
            <a href="javascript:void(0)" style="text-align:center; font-weight:bold;">
                Hiện bạn chưa có thông báo nào.
            </a>
        <?php endif; ?>
    </div>
</div>

                <a href="/quanlytruonghoc/admin/logout.php">🚪 Đăng xuất</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("notifBell").addEventListener("click", function() {
            var dropdown = document.getElementById("notifDropdown");
            dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
        });
        window.onclick = function(event) {
            if (!event.target.matches('#notifBell')) {
                var dropdown = document.getElementById("notifDropdown");
                if (dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
