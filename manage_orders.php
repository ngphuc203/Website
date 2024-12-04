<?php
session_start();

// Kiểm tra xem người dùng có phải là admin không
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Chuyển hướng nếu không phải admin
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

// Đọc danh sách đơn hàng từ cơ sở dữ liệu
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Tính tổng tiền của các sản phẩm trong đơn hàng
        $order_id = $row['order_id'];
        $sql_items = "SELECT SUM(oi.price * oi.quantity) AS total_amount
                      FROM order_items oi
                      WHERE oi.order_id = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        $item_total = $result_items->fetch_assoc();
        
        $row['total_amount'] = $item_total['total_amount']; // Gán tổng tiền vào đơn hàng

        $orders[] = $row;
    }
}

// Thống kê tổng số đơn hàng và tổng doanh thu
$total_orders = count($orders);
$total_revenue = 0;
$pending_orders = 0;
$confirmed_orders = 0;
$cancelled_orders = 0;

foreach ($orders as $order) {
    $total_revenue += $order['total_amount'];
    if ($order['status'] === 'Pending') {
        $pending_orders++;
    } elseif ($order['status'] === 'Confirmed') {
        $confirmed_orders++;
    } elseif ($order['status'] === 'Cancelled') {
        $cancelled_orders++;
    }
}

// Hiển thị thông báo khi cập nhật trạng thái thành công
$update_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Cập nhật trạng thái đơn hàng
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    // Thông báo thành công
    $update_message = "Cập nhật trạng thái đơn hàng thành công!";
}

// Xử lý xóa đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order_id'])) {
    $delete_order_id = $_POST['delete_order_id'];

    // Xóa đơn hàng khỏi cơ sở dữ liệu
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $delete_order_id);
    $stmt->execute();
    $stmt->close();

    // Thông báo xóa thành công
    $update_message = "Đơn hàng đã được xóa thành công!";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
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
                <li><a href="./manage_customers.php">Quản Lý Khách Hàng</a></li>
                <li><a href="./manage_orders.php">Quản Lý Đơn Hàng</a></li>
                <li><a href="./logout.php">Đăng Xuất</a></li>
            </ul>
        </div>
    </section>

    <div class="order-management">
        <h2>Danh Sách Đơn Hàng</h2>

        <!-- Thống kê -->
        <div class="statistics">
            <h3>Thống Kê</h3>
            <p>Tổng số đơn hàng: <?php echo $total_orders; ?></p>
            <p>Tổng doanh thu: <?php echo number_format($total_revenue, 0, ',', '.') . " đ"; ?></p>
            <p>Số đơn hàng chưa xử lý: <?php echo $pending_orders; ?></p>
            <p>Số đơn hàng đã xác nhận: <?php echo $confirmed_orders; ?></p>
            <p>Số đơn hàng đã hủy: <?php echo $cancelled_orders; ?></p>
        </div>

        <!-- Thông báo cập nhật trạng thái thành công -->
        <?php if ($update_message): ?>
            <div class="success-message">
                <p style="color: green;"><?php echo $update_message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Danh sách đơn hàng -->
        <table>
            <thead>
                <tr>
                    <th>Mã Đơn Hàng</th>
                    <th>Họ và Tên</th>
                    <th>Địa Chỉ</th>
                    <th>Phương Thức Thanh Toán</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Đặt Hàng</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['fullname']; ?></td>
                        <td><?php echo $order['address']; ?></td>
                        <td><?php echo $order['payment_method']; ?></td>
                        <td><?php echo number_format($order['total_amount'], 0, ',', '.') . " đ"; ?></td>
                        <td><?php echo $order['status']; ?></td>
                        <td><?php echo isset($order['order_date']) ? $order['order_date'] : 'Không có thông tin'; ?></td>
                        <td>                       
                            <form method="POST" action="manage_orders.php">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <select name="status">
                                    <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Chưa xử lý</option>
                                    <option value="Confirmed" <?php echo $order['status'] === 'Confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                    <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                </select>
                                <button type="submit">Cập nhật</button><br>
                            </form>

                            <form method="POST" action="view_order_details_admin.php">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <button type="submit">Xem Chi Tiết</button><br>
                            </form>

                            <form method="POST" action="manage_orders.php" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">
                                <input type="hidden" name="delete_order_id" value="<?php echo $order['order_id']; ?>">
                                <button type="submit">Xóa</button><br>
                            </form>
                        </td>
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
