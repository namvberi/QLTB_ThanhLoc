<?php
include 'config.php';
include 'navbargiaovien.php';
// Truy vấn lấy tổng số lớp
$query_lop = mysqli_query($conn, "SELECT COUNT(*) AS tong_lop FROM lop");
$row_lop = mysqli_fetch_assoc($query_lop);
$tong_lop = $row_lop['tong_lop'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Giáo Viên</title>
    <!-- Thêm Chart.js từ CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin-top: 100px;
            padding: 0;
        }
        .container {
            padding: 30px;
            text-align: center;
        }
        .stat-box {
            margin-bottom: 30px;
        }
        canvas {
            width: 100% !important;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center;">Giáo viên</h2>

    <div class="container">
        <!-- Biểu đồ tổng số lớp -->
        <div class="stat-box">
            <h4>Tổng số lớp</h4>
            <canvas id="lopChart"></canvas>
        </div>
    </div>

    <script>
        // Lấy tổng số lớp từ PHP
        var tongLop = <?php echo $tong_lop; ?>;

        // Khởi tạo biểu đồ
        var ctx = document.getElementById('lopChart').getContext('2d');
        var lopChart = new Chart(ctx, {
            type: 'bar', // Chọn loại biểu đồ (có thể thay 'bar' bằng các loại khác như 'line', 'pie', ...)
            data: {
                labels: ['Tổng số lớp'], // Nhãn cho biểu đồ
                datasets: [{
                    label: 'Số lớp',
                    data: [tongLop], // Dữ liệu lấy từ PHP
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Màu nền của các cột
                    borderColor: 'rgba(75, 192, 192, 1)', // Màu viền của các cột
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
