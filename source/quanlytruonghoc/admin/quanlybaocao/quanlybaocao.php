<?php
include("../navbar.php");
include '../config.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về trang login
if (!isset($_SESSION['users_id'])) {
    header("Location: ../login.php");
    exit();
}

$users_id = $_SESSION['users_id'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản Lý Báo Cáo</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #eef2f7;
        margin-top: 80px;
    }
    .container {
        width: 800px;
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
    .report-list {
        list-style: none;
        padding: 0;
    }
    .report-list li {
        margin: 15px 0;
    }
    .report-list a {
        text-decoration: none;
        color: #007bff;
        font-size: 18px;
    }
    .report-list a:hover {
        color: #0056b3;
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
  <h2>Quản Lý Báo Cáo</h2>
  <ul class="report-list">
    <li><a href="baocaotongquan.php">1. Báo cáo Tổng Quan Thiết Bị</a></li>
    <li><a href="baocaobaotri.php">2. Báo Cáo Thiết bị bảo trì</a></li>
    <li><a href="baocaotinhtrang.php">3. Báo Cáo Tình Trạng Thiết Bị</a></li>
  </ul>
  
  <a href="/quanlytruonghoc/admin/dashboardadmin.php" class="btn-back">← Quay lại trang chính</a>
</div>

</body>
</html>
