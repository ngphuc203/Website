<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$database = "doanv2";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Giỏ hàng được lưu trong session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Lấy sản phẩm từ cơ sở dữ liệu dựa trên product_id
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");  
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $_SESSION['cart'][] = [
            'id' => $product['product_id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image'] // Lưu thông tin ảnh vào giỏ hàng
        ];
    }
    $stmt->close();
    header('Location: products.php');
    exit();
}

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm</title>
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
        <h1>Danh Sách Sản Phẩm</h1>
            <div class="product-list">
                <?php if (empty($products)): ?>
                    <p>Không có sản phẩm nào!</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <section id="product1">
                            <div class="pro">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="uploads/<?php echo $product['image']; ?>" alt="Hình ảnh sản phẩm" width="200"><br>
                                <?php else: ?>
                                    <img src="uploads/default.jpg" alt="Hình ảnh sản phẩm" width="200"><br>
                                <?php endif; ?>
                                <span style="color:blueviolet;"><?php echo htmlspecialchars($product['name']); ?></span><br>
                                <p><?php echo htmlspecialchars($product['description']); ?></p><br>
                                <p style="color:red;">Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VND</p><br>
                                <form method="POST" action="products.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <label for="quantity">Số lượng:</label>
                                    <input type="number" name="quantity" id="quantity" min="1" max="<?php echo $product['quantity']; ?>" value="1" required>
                                    <button type="submit">Thêm vào giỏ hàng</button>
                                </form>
                            </div>
                        </section>
                    <?php endforeach; ?>
                <?php endif; ?>
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
