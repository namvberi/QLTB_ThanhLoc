<?php
include './admin/config.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - THCS Thạnh Lộc</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            line-height: 1.6;
        }
        header {
            background-color: #004080;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
        }
        header .logo img {
            height: 60px;
            width: 60px; /* đảm bảo là hình vuông */
            object-fit: cover; /* cắt đầy hình */
            border-radius: 50%; /* bo tròn */
            border: 2px solid white;
        }
        header nav ul {
            list-style: none;
            display: flex;
        }
        header nav ul li {
            margin-left: 20px;
        }
        header nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .navbar {
            background-color: #0066cc;
        }
        .navbar ul {
            display: flex;
            list-style: none;
            justify-content: center;
            padding: 10px 0;
        }
        .navbar ul li {
            margin: 0 15px;
        }
        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .carousel-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            margin: 20px auto;
        }
        .carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .carousel img {
            width: 100%;
            height: auto;
        }
        .carousel-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            font-size: 30px;
            padding: 10px;
            cursor: pointer;
        }
        .carousel-button.prev {
            left: 10px;
        }
        .carousel-button.next {
            right: 10px;
        }
        section.about-section {
            padding: 40px 20px;
            max-width: 1000px;
            margin: auto;
        }
        section.about-section h2 {
            color: #004080;
            margin-bottom: 10px;
            border-bottom: 2px solid #004080;
            display: inline-block;
            padding-bottom: 5px;
        }
        section.about-section p {
            margin-top: 10px;
        }
        footer {
            background-color: #f4f4f4;
            padding: 30px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        footer div {
            margin-bottom: 20px;
            max-width: 250px;
        }
        footer h4 {
            color: #004080;
            margin-bottom: 10px;
        }
        footer p, footer a {
            color: #333;
            font-size: 14px;
        }
        footer a img {
            margin-top: 5px;
        }
        footer p:last-child {
            width: 100%;
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="/quanlytruonghoc/admin/images/logo.jpg" alt="Logo THCS Thạnh Lộc">
    </div>
    <nav>
        <ul>
            <li><a href="/quanlytruonghoc/admin/login.php">Đăng nhập</a></li>
        </ul>
    </nav>
</header>

<div class="navbar">
    <ul>
        <li><a href="#cocautochuc">Cơ cấu tổ chức</a></li>
        <li><a href="#kehoachgiaoduc">Kế hoạch giáo dục</a></li>
        <li><a href="#vanbancongvan">Văn bản công văn</a></li>
        <li><a href="#tintucsukien">Tin tức sự kiện</a></li>
        <li><a href="#thongtinhs">Thông tin học sinh</a></li>
        <li><a href="#lienhe">Liên hệ</a></li>
    </ul>
</div>

<div class="carousel-container" id="carouselContainer">
    <button class="carousel-button prev" id="prevBtn">&#10094;</button>
    <div class="carousel" id="carousel">
        <img src="/quanlytruonghoc/admin/images/congvan1.jpg" alt="Slide 1">
        <img src="/quanlytruonghoc/admin/images/congvan2.jpg" alt="Slide 2">
        <img src="/quanlytruonghoc/admin/images/congvannhaphoc.jpg" alt="Slide 3">
    </div>
    <button class="carousel-button next" id="nextBtn">&#10095;</button>
</div>

<section class="about-section">
    <h2>Về Trường THCS Thạnh Lộc</h2>
    <p>Trường THCS Thạnh Lộc tự hào là nơi đào tạo thế hệ học sinh giỏi, năng động và sáng tạo. Với đội ngũ giáo viên tận tâm và cơ sở vật chất hiện đại, chúng tôi cam kết mang đến môi trường học tập chất lượng cao, giúp các em phát triển toàn diện.</p>
</section>

<section id="cocautochuc" class="about-section">
    <h2>Cơ cấu tổ chức</h2>
    <p>Ban giám hiệu, tổ chuyên môn, tổ văn phòng, các đoàn thể, và các phòng ban của trường.</p>
</section>

<section id="kehoachgiaoduc" class="about-section">
    <h2>Kế hoạch giáo dục</h2>
    <p>Các chương trình giảng dạy, kế hoạch năm học, hoạt động ngoại khóa và bồi dưỡng học sinh giỏi.</p>
</section>

<section id="vanbancongvan" class="about-section">
    <h2>Văn bản công văn</h2>
    <p>Danh sách các văn bản chính thức từ Sở Giáo dục, thông báo của nhà trường và các công văn liên quan.</p>
</section>

<section id="tintucsukien" class="about-section">
    <h2>Tin tức sự kiện</h2>
    <p>Cập nhật các tin tức nổi bật, sự kiện nội bộ và hoạt động của học sinh và giáo viên.</p>
</section>

<section id="thongtinhs" class="about-section">
    <h2>Thông tin học sinh</h2>
    <p>Cổng tra cứu điểm số, kết quả học tập, rèn luyện và các thông tin liên quan đến học sinh.</p>
</section>

<section id="lienhe" class="about-section">
    <h2>Liên hệ</h2>
    <p>Thông tin liên hệ trực tiếp với nhà trường, bao gồm địa chỉ, số điện thoại và email.</p>
</section>

<footer>
    <div class="contact-info">
        <h4>Liên hệ</h4>
        <p>Địa chỉ: 123 Đường Thạnh Lộc, Quận 12, TP.HCM</p>
        <p>Điện thoại: (028) 1234 5678</p>
        <p>Email: info@thcs-thanhloc.edu.vn</p>
    </div>
    <div class="map">
        <h4>Bản đồ</h4>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.5746762186156!2d106.6641890153344!3d10.76262226241745!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4b5a8b3d4f%3A0xa3f52a5e1b3d6d1e!2zVHLGsOG7nW5nIFRIQ1MgVGjDoG5oIEzhu5tj!5e0!3m2!1svi!2s!4v1627654304893!5m2!1svi!2s"
        width="250" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
    <div class="social-media">
        <h4>Mạng xã hội</h4>
        <a href="https://www.facebook.com/Thoitranggioitretre">
            <img src="/quanlytruonghoc/admin/images/logofb.jpeg" alt="Facebook" width="30">
        </a>
    </div>
    <p>&copy; 2025 Trường THCS Thạnh Lộc. All rights reserved.</p>
</footer>

<script>
    const carousel = document.getElementById('carousel');
    const totalSlides = carousel.children.length;
    let currentSlide = 0;

    function updateSlide() {
        const offset = -currentSlide * 100;
        carousel.style.transform = `translateX(${offset}%)`;
    }

    function moveSlide(step) {
        currentSlide = (currentSlide + step + totalSlides) % totalSlides;
        updateSlide();
    }

    let autoSlide = setInterval(() => moveSlide(1), 5000);

    document.getElementById('prevBtn').addEventListener('click', (e) => {
        e.stopPropagation();
        moveSlide(-1);
        resetInterval();
    });

    document.getElementById('nextBtn').addEventListener('click', (e) => {
        e.stopPropagation();
        moveSlide(1);
        resetInterval();
    });

    function resetInterval() {
        clearInterval(autoSlide);
        autoSlide = setInterval(() => moveSlide(1), 5000);
    }

    updateSlide();
</script>

</body>
</html>
