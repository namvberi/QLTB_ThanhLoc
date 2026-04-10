<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
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
      // Nếu chưa đăng nhập, gán avatar mặc định hoặc chuyển hướng
      echo'chưa đăng nhập';
  }

  $sql_tb_count = "SELECT COUNT(*) AS total FROM thongbao WHERE trangthai = 'chưa đọc'";
  $result_tb_count = mysqli_query($conn, $sql_tb_count);
  $row_tb_count = mysqli_fetch_assoc($result_tb_count);
  $total_tb = $row_tb_count['total'];
  
  // Lấy danh sách thông báo mới nhất (giới hạn 5 cái)
  $sql_tb_list = "SELECT * FROM thongbao ORDER BY thoigiangui DESC LIMIT 5";
  $result_tb_list = mysqli_query($conn, $sql_tb_list);
?>

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
    margin-right: 5px;
    border: none; 
  }

  .navbar a {
    color: white;
    text-decoration: none;
    margin-right: 10px;
    font-weight: bold;
    font-size: 14px;
    padding: 5px 8px;
    border-radius: 5px;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  .navbar a:hover {
    background-color: #3498db;
    color: #fff;
    text-decoration: none;
  }

  .navbar a.active {
    font-weight: bold;
    color: #7f8c8d;
    background-color: #ecf0f1;
  }

  .navbar-title {
    font-size: 14px;
    margin-right: auto;
    font-weight: bold;
  }

  .avatar-container {
    position: relative;
    margin-left: auto;
    cursor: pointer;
  }

  .avatar {
    height: 40px;
    width: 40px;
    border-radius: 50%;
  }

  .dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 45px;
    background-color: #34495e;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    min-width: 150px;
    z-index: 1000;
  }

  .dropdown a {
    color: white;
    padding: 10px;
    display: block;
    text-decoration: none;
    font-size: 14px;
  }

  .dropdown a:hover {
    background-color: #3498db;
  }

  .avatar-container:hover .dropdown {
    display: block;
  }

  /* Dropdown Bảo trì */
  .maintenance-dropdown {
    position: relative;
    cursor: pointer;
  }

  .maintenance-menu {
    display: none;
    position: absolute;
    background-color: #34495e;
    border-radius: 5px;
    width: 200px;
    z-index: 1000;
    top: 30px;
    right: 0;
  }

  .maintenance-menu a {
    padding: 10px;
    display: block;
    font-size: 14px;
    color: white;
    text-decoration: none;
  }

  .maintenance-menu a:hover {
    background-color: #3498db;
  }

  /* Dropdown Quản lý tác vụ */
  .task-dropdown {
    position: relative;
    cursor: pointer;
  }

  .task-menu {
    display: none;
    position: absolute;
    background-color: #34495e;
    border-radius: 5px;
    width: 200px;
    z-index: 1000;
    top: 30px;
    right: 0;
  }

  .task-menu a {
    padding: 10px;
    display: block;
    font-size: 14px;
    color: white;
    text-decoration: none;
  }

  .task-menu a:hover {
    background-color: #3498db;
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

<div class="navbar">
    <img src="http://localhost:8080/quanlytruonghoc/admin/images/logo.jpg" alt="Logo" />
  </a>
  <div class="navbar-title">THCS Thạnh Lộc</div>
  <a href="/quanlytruonghoc/admin/dashboardadmin.php" class="<?= $current_page == 'dashboardadmin.php' ? 'active' : '' ?>">🏠 Trang chủ</a>
  <a href="/quanlytruonghoc/admin/quanlythietbi/quanlythietbi.php" class="<?= $current_page == 'quanlythietbi.php' ? 'active' : '' ?>">📦 Thiết bị</a>
  <a href="/quanlytruonghoc/admin/quanlyphongban/quanlyphongban.php" class="<?= $current_page == 'quanlyphong.php' ? 'active' : '' ?>">📊 Phòng ban</a>
  <a href="/quanlytruonghoc/admin/quanlynguoimuon/quanlynguoimuon.php" class="<?= $current_page == 'quanlynguoimuon.php' ? 'active' : '' ?>">👨‍🎓 Người mượn</a>
  <a href="/quanlytruonghoc/admin/quanlynguoidung/quanlynguoidung.php" class="<?= $current_page == 'quanlynguoidung.php' ? 'active' : '' ?>">👤 Người dùng</a>
  
  <!-- Bảo trì Dropdown -->
  <div class="maintenance-dropdown" id="maintenance-dropdown">
    <a href="javascript:void(0)" class="<?= $current_page == 'quanlybaotri.php' ? 'active' : '' ?>">🛠️ Bảo trì</a>
    <div class="maintenance-menu" id="maintenance-menu">
      <a href="/quanlytruonghoc/admin/quanlybaotri/quanlybaotri.php">📋 Danh sách thiết bị bảo trì</a>
      <a href="/quanlytruonghoc/admin/quanlybaotri/kieusua.php">🔧 Kiểu sửa chữa</a>
    </div>
  </div>

  <!-- Quản lý Tác vụ Dropdown -->
  <div class="task-dropdown" id="task-dropdown">
  <a href="javascript:void(0)" class="<?= ($current_page == 'quanlymonhoc.php' || $current_page == 'quanlylop.php' || $current_page == 'quanlyloaithietbi.php') ? 'active' : '' ?>">📚 Quản lý tác vụ</a>
  <div class="task-menu" id="task-menu">
    <a href="/quanlytruonghoc/admin/quanlytacvu/quanlymonhoc.php">📖 Quản lý môn học</a>
    <a href="/quanlytruonghoc/admin/quanlytacvu/quanlylop.php">🏫 Quản lý lớp học</a>
    <a href="/quanlytruonghoc/admin/quanlytacvu/quanlyloaithietbi.php">🔌 Quản lý loại thiết bị</a>
  </div>
</div>
  <a href="/quanlytruonghoc/admin/quanlydon/quanlydon.php">📖 Quản lý Đơn</a>
  <a href="/quanlytruonghoc/admin/quanlybaocao/quanlybaocao.php" class="<?= $current_page == 'quanlybaocao.php' ? 'active' : '' ?>">📈 Báo cáo</a>

  <div class="avatar-container">
  <img src="/quanlytruonghoc/admin/images/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar" style="margin-right: 50px;" />
  <div class="dropdown">
      <a href="/quanlytruonghoc/admin/profile.php">👤 Profile</a>
      <!-- thongbao -->
      <div class="notification">
    <span id="notifBell" style="font-size: 14px; cursor: pointer; padding: 10px;">🔔 Thông báo</span>
    <?php if ($total_tb > 0): ?>
        <span class="badge"><?php echo $total_tb; ?></span>
    <?php endif; ?>
    <div class="notification-dropdown" id="notifDropdown">
        <?php while ($tb = mysqli_fetch_assoc($result_tb_list)): ?>
            <a href="/quanlytruonghoc/admin/thongbao/xem_thongbao.php?id=<?php echo $tb['id']; ?>">
                <?php echo htmlspecialchars($tb['noidung']); ?> <br>
                <small><?php echo $tb['thoigiangui']; ?></small>
            </a>
        <?php endwhile; ?>
        <a href="/quanlytruonghoc/admin/thongbao/tatca_thongbao.php" style="text-align:center; font-weight:bold;">Xem tất cả</a>
    </div>
</div>
      <a href="/quanlytruonghoc/admin/logout.php">🚪 Đăng xuất</a>
    </div>
  </div>
</div>

<script>
  // Toggle dropdown for maintenance
  document.getElementById('maintenance-dropdown').addEventListener('click', function (event) {
    var menu = document.getElementById('maintenance-menu');
    var taskMenu = document.getElementById('task-menu');
    
    // Đảm bảo rằng dropdown task-menu bị đóng nếu đang mở
    if (taskMenu.style.display === 'block') {
      taskMenu.style.display = 'none';
    }

    // Toggle cho maintenance-menu
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    event.stopPropagation();
  });

  // Toggle dropdown for task menu
  document.getElementById('task-dropdown').addEventListener('click', function (event) {
    var taskMenu = document.getElementById('task-menu');
    var menu = document.getElementById('maintenance-menu');
    
    // Đảm bảo rằng dropdown maintenance-menu bị đóng nếu đang mở
    if (menu.style.display === 'block') {
      menu.style.display = 'none';
    }

    // Toggle cho task-menu
    taskMenu.style.display = taskMenu.style.display === 'block' ? 'none' : 'block';
    event.stopPropagation();
  });

  // Close dropdowns when clicking outside
  document.addEventListener('click', function (event) {
    var maintenanceDropdown = document.getElementById('maintenance-dropdown');
    var maintenanceMenu = document.getElementById('maintenance-menu');
    if (!maintenanceDropdown.contains(event.target)) {
      maintenanceMenu.style.display = 'none';
    }

    var taskDropdown = document.getElementById('task-dropdown');
    var taskMenu = document.getElementById('task-menu');
    if (!taskDropdown.contains(event.target)) {
      taskMenu.style.display = 'none';
    }
  });
  document.getElementById("notifBell").addEventListener("click", function() {
    var dropdown = document.getElementById("notifDropdown");
    dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
});

// Đóng dropdown khi click ngoài
window.onclick = function(event) {
    if (!event.target.matches('#notifBell')) {
        var dropdown = document.getElementById("notifDropdown");
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }
}
</script>
