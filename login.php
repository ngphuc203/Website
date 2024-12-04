<?php
session_start();

// Kiểm tra nếu form đăng nhập được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra tài khoản admin mặc định
    if ($username === 'ngphuc203' && $password === '1') {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;
        header('Location: admin_dashboard.php'); // Chuyển hướng đến trang quản trị
        exit();
    }

    // Kết nối MySQL
    $servername = "localhost";
    $username_db = "root";  // Thay đổi nếu cần
    $password_db = "";      // Thay đổi nếu cần
    $dbname = "doanv2";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    // Đọc danh sách khách hàng từ cơ sở dữ liệu
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if ($password === $customer['password']) { // Không dùng password_verify
            // Đăng nhập thành công
            $_SESSION['username'] = $customer['username'];
            $_SESSION['fullname'] = $customer['fullname'];
            $_SESSION['role'] = 'user';
            header('Location: index.php'); // Chuyển hướng đến trang chủ sau khi đăng nhập thành công
            exit();
        } else {
            // Mật khẩu không đúng
            $error_message = "Tên tài khoản hoặc mật khẩu không chính xác!";
        }
    } else {
        // Tên đăng nhập không tồn tại
        $error_message = "Tên tài khoản không tồn tại!";
    }

    $conn->close();
}
?>

<!-- HTML form cho trang đăng nhập -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
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
    <h2>ĐĂNG NHẬP</h2>
    <!-- Hiển thị thông báo lỗi nếu có -->
        <div class="login-container"> 
            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <label for="username">Tên đăng nhập:</label><br>
                <input type="text" id="username" name="username" required><br>

                <label for="password">Mật khẩu:</label><br>
                <input type="password" id="password" name="password" required><br><br>

                <button type="submit">Đăng nhập</button>

                <p>Chưa có tài khoản? <a href="./signup.php">Đăng Ký</a></p>
            </form>


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
        </div>
    </body>
</html>
