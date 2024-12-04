<?php
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Lấy thông tin người dùng đã đăng nhập
$username = $_SESSION['username'];

// Kết nối cơ sở dữ liệu MySQL
$servername = "localhost";
$db_username = "root"; // Tên người dùng cơ sở dữ liệu
$db_password = ""; // Mật khẩu cơ sở dữ liệu
$database = "doanv2"; // Tên cơ sở dữ liệu

$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username); // Truyền username vào truy vấn
$stmt->execute();
$result = $stmt->get_result();
$current_customer = $result->fetch_assoc();

// Kiểm tra nếu không tìm thấy thông tin người dùng
if (!$current_customer) {
    echo "Không tìm thấy thông tin người dùng!";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Tài Khoản</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<section id="header">      
    <div>
        <ul id="navbar">
            <div class="header-container">
                <!-- Logo bên trái -->
                <a href="./index.php"><img src="./img/logo.png" class="logo" style="width:30%; height: 10%;"></a>
                <!-- Các icon ở bên phải -->
                    <div class="icon-section">
                        <div class="icon-container">
                            <!-- Icon trang chủ -->
                            <a href="./index.php" class="icon">
                                <img src="./img/home.png" alt="Trang chủ" class="icon-image">
                            </a>
                            <!-- Icon sản phẩm -->
                            <a href="./products.php" class="icon">
                                <img src="./img/product.png" alt="Sản phẩm" class="icon-image">
                            </a>
                        </div>
                        <!-- Tìm kiếm -->
                        <div class="search-container">
                            <div class="search-icon">
                                <img src="./img/rabbit-icon.png" alt="Rabbit Icon">
                            </div>
                            <div class="search-box">
                                <input type="text" placeholder="TÌM KIẾM">
                                <span class="magnifying-glass">&#128269;</span>
                            </div>
                        </div>
                        <!-- Các icon: Giỏ hàng, Tài khoản, Đăng xuất -->
                        <div class="icon-container">
                            <!-- Icon giỏ hàng -->
                            <a href="./cart.php" class="icon">
                            <span class="cart-count">
                                <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                            </span>
                                <img src="./img/bag.png" alt="Giỏ hàng" class="icon-image">
                            </a>
                            <!-- Icon tài khoản -->
                            <a href="./my_account.php" class="icon">
                                <img src="./img/user.png" alt="Tài khoản" class="icon-image">
                            </a>
                            <!-- Icon đăng xuất -->
                            <a href="./logout.php" class="icon">
                                <img src="./img/logout.png" alt="Đăng xuất" class="icon-image">
                            </a>
                        </div>
                    </div>
                </div>
            </ul>
        </div>
    </section>
    <div class="header-mid-bar">
        <div class="header-marquee position-relative font-weight-bold text-light bg-dark">
            <div class="marquee d-flex justify-content-around position-absolute">
                <span>GIẢM GIÁ TỚI 100% MỌI SẢN PHẨM TẠI WEBSITE VÀ NHIỀU TRÒ HAY ĐANG CHỜ PHÍA SAU</span>
                <span>GIẢM GIÁ TỚI 100% MỌI SẢN PHẨM TẠI WEBSITE VÀ NHIỀU TRÒ HAY ĐANG CHỜ PHÍA SAU</span>
                <span>GIẢM GIÁ TỚI 100% MỌI SẢN PHẨM TẠI WEBSITE VÀ NHIỀU TRÒ HAY ĐANG CHỜ PHÍA SAU</span>
            </div>
            <div class="marquee marquee2 d-flex justify-content-around position-absolute">
                <span>GIẢM GIÁ TỚI 100% MỌI SẢN PHẨM TẠI WEBSITE VÀ NHIỀU TRÒ HAY ĐANG CHỜ PHÍA SAUU</span>
                <span>GIẢM GIÁ TỚI 100% MỌI SẢN PHẨM TẠI WEBSITE VÀ NHIỀU TRÒ HAY ĐANG CHỜ PHÍA SAU</span>
                <span>GIẢM GIÁ TỚI 100% MỌI SẢN PHẨM TẠI WEBSITE VÀ NHIỀU TRÒ HAY ĐANG CHỜ PHÍA SAU</span>
            </div>
        </div>
    </div>
    <h1>Tài Khoản</h1>

    <div class="account-container">
    <h1>Thông Tin Tài Khoản</h1>
        <div class="info-group">
            <p><strong>Tên Khách Hàng:</strong> <?php echo htmlspecialchars($current_customer['fullname']); ?></p>
        </div>
        <div class="info-group">
            <p><strong>Tên Tài Khoản:</strong> <?php echo htmlspecialchars($current_customer['username']); ?></p>
        </div>
        <div class="info-group">
            <p><strong>Giới Tính:</strong> <?php echo htmlspecialchars($current_customer['gender']); ?></p>
        </div>
        <div class="info-group">
            <p><strong>Ngày Sinh:</strong> <?php echo htmlspecialchars($current_customer['dob']); ?></p>
        </div>
        <div class="info-group">
            <p><strong>Mật Khẩu:</strong> <?php echo htmlspecialchars($current_customer['password']); ?></p>
        </div>
    </div>

    <footer>       
        <div class="footer-section">
        <div class="footer-header">
            <p>“POP_POP DIGITAL RETAIL STORE”</p>
        </div>
    
        <div class="footer-top">
            <div class="social-media">
                <a href="https://www.facebook.com/ngphuc203" target="_blank">
                    <i class="fab fa-facebook-f" style="font-size: 30px; color: #333;"></i>
                </a>
                <a href="https://www.instagram.com/ngphuc.exe" target="_blank">
                    <i class="fab fa-instagram" style="font-size: 30px; color: #333;"></i>
                </a>
                <a href="https://www.tiktok.com/@b1ackcat.exe" target="_blank">
                    <i class="fab fa-tiktok" style="font-size: 30px; color: #333;"></i>
                </a>
                <a href="https://github.com/shynsweet" target="_blank">
                    <i class="fab fa-github" style="font-size: 30px; color: #333;"></i>
                </a>
            </div>
        </div>
            <div class="newsletter">
                <h4>ĐĂNG KÝ ĐỂ NHẬN NHIỀU ƯU ĐÃI</h4>
                <input type="email" placeholder="Email address">
                <button>Đăng ký</button>
            </div>
            <div class="hotline">
                <p>HOTLINE: 0389.101.040 - 0359.077.334</p> 
            </div>
            </div>
            <div class="footer-bottom">
                <div class="company-info">
                    <h4>HỘ KINH DOANH POP_POP - POP_POP</h4>
                    <p>Trụ sở kinh doanh: 79 LÂM VĂN BỀN, PHƯỜNG TÂN THUẬN TÂY, QUẬN 7</p>
                    <p>Người đại diện: Nguyễn Phúc</p>
                    <p>Email: ngphuc1753@gmail.com</p>
                </div>
                <div class="branch-info">
                    <h4>CHI NHÁNH CỦA HÀNG</h4>
                    <p> 79 LÂM VĂN BỀN, PHƯỜNG TÂN THUẬN TÂY, QUẬN 7. Hotline: 0389.101.040</p>
                    <p> 10/41 ÂU DƯƠNG LÂN, PHƯỜNG 3, QUẬN 8. Hotline: 0359.077.334</p>
                    <p> MIỀN ĐẤT HỨA </p>
                </div>
                <div class="policies">
                    <h4>CHÍNH SÁCH</h4>
                    <p>Đổi trả sản phẩm</p>
                    <p>Chính sách đổi trả</p>
                    <p>Chính sách bảo mật thông tin</p>
                    <p>Chính sách vận chuyển, giao hàng</p>
                    <p>Điều khoản giao dịch chung</p>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-info">
                    <p>© 2024 “POP_POP OFFICIAL STORE ALL RIGHT RESERVED.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
