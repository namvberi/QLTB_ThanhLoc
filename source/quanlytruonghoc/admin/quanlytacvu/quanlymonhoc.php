<?php
include '../config.php';
include '../navbar.php';

// Lấy danh sách khối
$khoi_result = mysqli_query($conn, "SELECT * FROM khoi");

// Xử lý thêm môn học
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tenmonhoc']) && isset($_POST['khoi'])) {
    $tenmonhoc = $_POST['tenmonhoc'];
    $khoi_id = $_POST['khoi'];

    $check_sql = "SELECT * FROM monhoc WHERE tenmonhoc = '$tenmonhoc' AND khoi_id = '$khoi_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Môn học này đã tồn tại trong khối này!";
    } else {
        $sql = "INSERT INTO monhoc (tenmonhoc, khoi_id) VALUES ('$tenmonhoc', '$khoi_id')";
        mysqli_query($conn, $sql);
        $success_message = "Môn học đã được thêm thành công!";
    }
}

// Xử lý xóa môn học
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM monhoc WHERE id = $id";

    $check = mysqli_query($conn, "SELECT * FROM loaithietbi WHERE monhoc_id = $id");
    if (mysqli_num_rows($check) > 0) {
        // Nếu có thiết bị tham chiếu, thông báo lỗi và quay về trang danh sách
        echo "<script>
                alert('Không thể xóa môn học này vì còn thiết bị đang tham chiếu!');
                window.location='quanlymonhoc.php';
              </script>";
    } else {
        // Nếu không có thì cho xóa
        $sql = "DELETE FROM monhoc WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo "<script>
                    alert('Xóa môn học thành công!');
                    window.location='quanlymonhoc.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Xóa thất bại!');
                    window.location='quanlymonhoc.php';
                  </script>";
        }
    }
}

// Lấy danh sách môn học kết hợp với bảng khoi
$result = mysqli_query($conn, "SELECT monhoc.*, khoi.ten_khoi FROM monhoc JOIN khoi ON monhoc.khoi_id = khoi.id");
?>

<style>
body {
    padding: 0;
    font-family: 'Arial', sans-serif;
    margin-top: 100px;
    background-color: rgb(114, 170, 199);
}

.container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 20px;
    text-align: center;
}

.add-btn {
    background-color: #2ecc71;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 30px;
    text-decoration: none;
    margin-bottom: 20px;
    display: block;
    width: 200px;
    margin-left: auto;
    margin-right: auto;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.add-btn:hover {
    background-color: #27ae60;
}

form#addForm {
    display: none;
    margin-top: 20px;
    text-align: center;
}

form#addForm select,
form#addForm input[type="text"] {
    padding: 8px;
    width: 200px;
    margin: 5px;
}

form#addForm button {
    padding: 8px 16px;
    background-color: #3498db;
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

form#addForm button:hover {
    background-color: #2980b9;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border-radius: 10px;
    overflow: hidden;
}

th, td {
    padding: 15px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #34495e;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #ecf0f1;
}

.delete-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 15px;
    background-color: #e74c3c;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s;
}

.delete-btn:hover {
    background-color: #c0392b;
    transform: scale(1.05);
}

.error-message, .success-message {
    text-align: center;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-weight: bold;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
}
</style>

<div class="container">
    <h2>📘 Quản lý môn học</h2>

    <?php if (isset($error_message)): ?>
        <div class="error-message"><?= $error_message ?></div>
    <?php endif; ?>
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?= $success_message ?></div>
    <?php endif; ?>

    <button class="add-btn" onclick="toggleForm()">➕ Thêm môn</button>

    <form method="POST" id="addForm">
        <select name="khoi" required>
            <option value="">Chọn khối</option>
            <?php
            mysqli_data_seek($khoi_result, 0); // Reset result pointer
            while ($khoi = mysqli_fetch_assoc($khoi_result)): ?>
                <option value="<?= $khoi['id'] ?>"><?= $khoi['ten_khoi'] ?></option>
            <?php endwhile; ?>
        </select>

        <input type="text" name="tenmonhoc" placeholder="Nhập tên môn học" required>
        <button type="submit">Lưu</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>Tên môn học</th>
            <th>Khối</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['tenmonhoc'] ?></td>
                <td><?= $row['ten_khoi'] ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Bạn có chắc muốn xóa?')">🗑️ Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function toggleForm() {
    const form = document.getElementById("addForm");
    form.style.display = form.style.display === "none" || form.style.display === "" ? "block" : "none";
}
</script>
