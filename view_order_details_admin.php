<?php
session_start();

// Kiểm tra xem người dùng có phải là admin không
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Chuyển hướng nếu không phải admin
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

// Kiểm tra mã đơn hàng
if (!isset($_POST['order_id'])) {
    echo "Không tìm thấy thông tin mã đơn hàng.";
    exit();
}

$order_id = $_POST['order_id'];

// Truy vấn thông tin đơn hàng từ cơ sở dữ liệu
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order_details = $result->fetch_assoc();

if (!$order_details) {
    echo "Đơn hàng không tồn tại.";
    exit();
}

// Truy vấn danh sách sản phẩm trong đơn hàng và lấy mô tả và hình ảnh sản phẩm từ bảng products
$sql_items = "
    SELECT oi.*, p.description, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

// Kiểm tra nếu không có sản phẩm
if ($result_items->num_rows == 0) {
    echo "Không có sản phẩm trong đơn hàng.";
    exit();
}

$items = [];
$total_amount = 0; // Khởi tạo biến tổng tiền của đơn hàng

while ($item = $result_items->fetch_assoc()) {
    $item_total = $item['price'] * $item['quantity']; // Tính tổng tiền cho mỗi sản phẩm
    $item['total'] = $item_total; // Thêm tổng tiền vào thông tin sản phẩm
    $items[] = $item; // Thêm sản phẩm vào danh sách

    $total_amount += $item_total; // Cộng tổng tiền của sản phẩm vào tổng tiền của đơn hàng
}

$stmt->close();
$stmt_items->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h3>Chào mừng, <?php echo $_SESSION['username']; ?> (Admin)</h3>
    <section id="header">
        <div>
            <ul id="navbar">
                <a href="./admin_dashboard.php"><img src="./img/logov2.png" class="logo" style="width:12%; height: 12%;"></a>
                <li><a href="./admin_dashboard.php">Trang Chủ</a></li>
                <li><a href="./manage_products.php">Quản Lý Sản Phẩm</a></li>
                <li><a href="./manage_customers.php">Quản Lý Khách Hàng</a></li>
                <li><a href="./manage_orders.php">Quản Lý Đơn Hàng</a></li>
                <li><a href="./logout.php">Đăng Xuất</a></li>
            </ul>
        </div>
    </section>

    <h3>Chi Tiết Đơn Hàng</h3>
    <div class="order-details">
        <h2>Thông Tin Đơn Hàng</h2>
        <p><strong>Mã Đơn Hàng:</strong> <?php echo htmlspecialchars($order_details['order_id']); ?></p>
        <p><strong>Họ và Tên:</strong> <?php echo htmlspecialchars($order_details['fullname']); ?></p>
        <p><strong>Địa Chỉ:</strong> <?php echo htmlspecialchars($order_details['address']); ?></p>
        <p><strong>Số Điện Thoại:</strong> <?php echo htmlspecialchars($order_details['phone']); ?></p>
        <p><strong>Phương Thức Thanh Toán:</strong> <?php echo htmlspecialchars($order_details['payment_method']); ?></p>
        <p><strong>Ngày Đặt Hàng:</strong> <?php echo htmlspecialchars($order_details['order_date']); ?></p>
        <p><strong>Trạng Thái:</strong> <?php echo htmlspecialchars($order_details['status']); ?></p>
        <h1><strong>Tổng Tiền:</strong> <?php echo number_format($total_amount, 0, ',', '.') . " đ"; ?></h1>

        <h2>Danh Sách Sản Phẩm</h2>
        <table>
            <thead>
                <tr>
                    <th>Hình Ảnh</th>
                    <th>Tên Sản Phẩm</th> 
                    <th>Số Lượng</th>
                    <th>Giá</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['description']); ?>" width="100" height="100"></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td> <!-- Hiển thị mô tả sản phẩm -->
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['price'], 0, ',', '.') . " đ"; ?></td>
                        <td><?php echo number_format($item['total'], 0, ',', '.') . " đ"; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
