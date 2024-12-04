<?php
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập hoặc không phải admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Kết nối cơ sở dữ liệu MySQL
$servername = "localhost";
$username = "root";
$password = "";
$database = "doanv2";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ID người dùng cần chỉnh sửa
if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit();
}

$user_id = $_GET['id'];
$user_to_edit = null;

// Truy vấn thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_to_edit = $result->fetch_assoc();

// Nếu không tìm thấy người dùng
if (!$user_to_edit) {
    header('Location: manage_users.php');
    exit();
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname'], $_POST['username'], $_POST['password'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cập nhật thông tin người dùng trong cơ sở dữ liệu
    $sql_update = "UPDATE users SET fullname = ?, username = ?, password = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $fullname, $username, $password, $user_id);
    $stmt_update->execute();

    // Sau khi cập nhật, chuyển hướng về trang quản lý người dùng
    header('Location: manage_users.php');
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
    <title>Sửa Người Dùng</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h3>Chào mừng, <?php echo $_SESSION['username']; ?> (Admin)</h3>  
    <section id="header">   
        <div>
            <ul id="navbar">
                <a href="./admin_dashboard.php"><img src="./img/logo.png"class="logo" style="width:30%; height: 10%;"></a>
                <li><a href="./admin_dashboard.php">Trang Chủ</a></li>
                <li><a href="./manage_products.php">Quản Lý Sản Phẩm</a></li>
                <li><a href="./manage_users.php">Quản Lý Người Dùng</a></li>
                <li><a href="./manage_orders.php">Quản Lý Đơn Hàng</a></li>
                <li><a href="./logout.php">Đăng Xuất</a></li>
            </ul>
        </div>
    </section> 
    <h2>Sửa Thông Tin Người Dùng</h2>
    <form method="POST">
        <label for="fullname">Tên Người Dùng</label><br>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user_to_edit['fullname']); ?>" required><br>
        
        <label for="username">Tên Tài Khoản</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_to_edit['username']); ?>" required><br>
        
        <label for="password">Mật Khẩu</label><br>
        <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($user_to_edit['password']); ?>" required><br>
        
        <button type="submit">Cập Nhật</button>
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
</body>
</html>
