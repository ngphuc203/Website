<?php
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập hoặc không phải là admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');  // Chuyển hướng đến trang đăng nhập nếu không phải admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị Admin</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>  
    <h3>Chào mừng, <?php echo $_SESSION['username']; ?> (Admin)</h3>   
    <section id="'header">   
        <div>
            <ul id="navbar">
                <a href="./admin_dashboard.php"><img src="./img/logo.png"class="logo" style="width:30%; height: 10%;"></a>
                <li><a href="./admin_dashboard.php">Trang Chủ</a></li>
                <li><a href="./manage_products.php">Quản Lý Sản Phẩm</a></li>
                <li><a href="./manage_customers.php">Quản Lý Khách Hàng</a></li>
                <li><a href="./manage_orders.php">Quản Lý Đơn Hàng</a></li>
                <li><a href="./logout.php">Đăng Xuất</a></li>
            </ul>
        </div>
    </section> 


    <div class="admin-content">
        <h2>Quản Lý Sản Phẩm và Khách Hàng</h2>
        <p>Chọn chức năng dưới đây để quản lý sản phẩm hoặc khách hàng.</p>
        
        <div class="admin-options">
            <a href="manage_products.php" class="admin-option">Quản Lý Sản Phẩm</a><br>
            <a href="manage_customers.php" class="admin-option">Quản Lý Khách Hàng</a><br>
            <a href="./manage_orders.php">Quản Lý Đơn Hàng</a></li>
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
