<?php
include '../config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Bạn không có quyền.");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE thongbao SET trangthai = 'đã đọc' WHERE id = $id";
    mysqli_query($conn, $sql);
}

header('Location: tatca_thongbao.php');
exit();
?>
