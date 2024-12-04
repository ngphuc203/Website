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

// Lấy giỏ hàng từ session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

// Lấy thông tin sản phẩm từ cơ sở dữ liệu
$product_ids = array_column($cart, 'id');
$products = [];

if (count($product_ids) > 0) {
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");

    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $products[$row['product_id']] = $row;
    }
    $stmt->close();
}

// Xử lý xóa sản phẩm từ giỏ hàng
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = $_GET['remove'];
    
    // Xóa sản phẩm khỏi giỏ hàng (session)
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    header('Location: cart.php'); // Tải lại trang giỏ hàng sau khi xóa
    exit();
}

// Cập nhật số lượng sản phẩm trong giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id'] == $product_id) {
                    $_SESSION['cart'][$key]['quantity'] = $quantity;
                    break;
                }
            }
        }
    }
    header('Location: cart.php'); // Tải lại trang giỏ hàng sau khi cập nhật
    exit();
}

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Kiểm tra nếu giỏ hàng không rỗng
    if (empty($cart)) {
        echo "<script>alert('Giỏ hàng của bạn trống, vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán.');</script>";
        header('Location: cart.php');
        exit();
    }

    // Lưu thông tin đơn hàng vào cơ sở dữ liệu
    $username = $_SESSION['username'];
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];
    $status = 'Pending';  // Trạng thái đơn hàng là "Chờ xử lý"

    // Tính tổng giá trị đơn hàng
    foreach ($cart as $item) {
        $total += $products[$item['id']]['price'] * $item['quantity'];
    }

    // Thêm đơn hàng vào bảng orders
    $stmt = $conn->prepare("INSERT INTO orders (username, fullname, address, phone, payment_method, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $fullname, $address, $phone, $payment_method, $total, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id;  // Lấy ID đơn hàng vừa tạo
    $stmt->close();

    // Thêm các sản phẩm vào bảng order_items
    foreach ($cart as $item) {
        $product_id = $item['id'];
        $product_name = $item['name'];
        $quantity = $item['quantity'];
        $price = $products[$item['id']]['price'];
        $total_item = $price * $quantity;

        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissdd", $order_id, $product_id, $product_name, $quantity, $price, $total_item);
        $stmt->execute();
        $stmt->close();
    }

    // Xóa giỏ hàng sau khi thanh toán
    unset($_SESSION['cart']);

    echo "<script>alert('Đơn hàng của bạn đã được thanh toán thành công!'); window.location.href='products.php';</script>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
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
    <h1>Giỏ Hàng</h1>
    <div class="cart-list">
        <?php if (count($cart) > 0): ?>
            <form method="POST" action="cart.php">  
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                            <th>Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item):
                            // Kiểm tra nếu item có 'id' và thông tin sản phẩm
                            if (isset($item['id']) && isset($products[$item['id']])) {
                                $product = $products[$item['id']];
                                $total += $product['price'] * $item['quantity'];
                        ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($product['name']); ?><br>
                                    <small><?php echo htmlspecialchars($product['description']); ?></small> <!-- Hiển thị mô tả sản phẩm -->
                                </td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="100">
                                </td>
                                <td><?php echo number_format($product['price'], 0, ',', '.') . " VND"; ?></td>
                                <td><?php echo number_format($product['price'] * $item['quantity'], 0, ',', '.') . " VND"; ?></td>
                                <td>
                                    <a href="cart.php?remove=<?php echo $item['id']; ?>">Xóa</a>
                                </td>
                            </tr>
                        <?php 
                            }
                        endforeach; ?>
                    </tbody>
                </table>
                
                <div class="update-cart">
                    <button type="submit" name="update_cart">Cập nhật giỏ hàng</button>
                </div>

                <h3>Tổng cộng: <?php echo number_format($total, 0, ',', '.') . " VND"; ?></h3>

                <!-- Form thanh toán -->
                <h2>Thông tin thanh toán</h2>
                <label for="fullname">Họ và tên:</label><br>
                <input type="text" id="fullname" name="fullname" required><br><br>

                <label for="address">Địa chỉ:</label><br>
                <input type="text" id="address" name="address" required><br><br>

                <label for="phone">Số điện thoại:</label><br>
                <input type="text" id="phone" name="phone" required><br><br>

                <label for="payment_method">Phương thức thanh toán:</label><br>
                <select id="payment_method" name="payment_method" required>
                    <option value="cash_on_delivery">Thanh toán khi nhận hàng</option>
                    <option value="online_payment">Thanh toán trực tuyến</option>
                </select><br><br>

                <button type="submit" name="checkout">Thanh toán</button>
            </form>
        <?php else: ?>
            <p>Giỏ hàng của bạn đang trống.</p>
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
