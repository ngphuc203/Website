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

// Lấy thông tin sản phẩm cần sửa
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Sản phẩm không tồn tại!";
        exit();
    }
    $stmt->close();
}

// Cập nhật thông tin sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'], $_POST['product_desc'], $_POST['product_price'], $_POST['product_qty'])) {
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $product_price = $_POST['product_price'];
    $product_qty = $_POST['product_qty'];

    // Xử lý ảnh nếu có
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['product_image']['name'];
        $image_tmp = $_FILES['product_image']['tmp_name'];
        move_uploaded_file($image_tmp, "uploads/" . $image_name);
    } else {
        // Nếu không có ảnh mới, giữ nguyên ảnh cũ
        $image_name = $product['image'];
    }

    // Cập nhật thông tin sản phẩm trong cơ sở dữ liệu
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, image = ? WHERE product_id = ?");
    $stmt->bind_param("ssdssi", $product_name, $product_desc, $product_price, $product_qty, $image_name, $product_id);

    // Execute the statement
    if ($stmt->execute()) {
        header('Location: manage_products.php');
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sản Phẩm</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h3>Chào mừng, <?php echo $_SESSION['username']; ?> (Admin)</h3>
    <section id="header">   
        <div>
            <ul id="navbar">
                <a href="./admin_dashboard.php"><img src="./img/logo.png" class="logo" style="width:30%; height: 10%;"></a>
                <li><a href="./admin_dashboard.php">Trang Chủ</a></li>
                <li><a href="./manage_products.php">Quản Lý Sản Phẩm</a></li>
                <li><a href="./manage_customers.php">Quản Lý Khách Hàng</a></li>
                <li><a href="./manage_orders.php">Quản Lý Đơn Hàng</a></li>
                <li><a href="./logout.php">Đăng Xuất</a></li>
            </ul>
        </div>
    </section>

    <h2>Sửa Thông Tin Sản Phẩm</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="product_name">Tên Sản Phẩm</label><br>
        <input type="text" id="product_name" name="product_name" value="<?php echo $product['name']; ?>" required><br>

        <label for="product_desc">Mô Tả</label><br>
        <textarea id="product_desc" name="product_desc" required><?php echo $product['description']; ?></textarea><br>

        <label for="product_price">Giá</label><br>
        <input type="number" id="product_price" name="product_price" value="<?php echo $product['price']; ?>" required><br>

        <label for="product_qty">Số Lượng</label><br>
        <input type="number" id="product_qty" name="product_qty" value="<?php echo $product['quantity']; ?>" required><br>

        <label for="product_image">Hình Ảnh</label><br>
        <input type="file" id="product_image" name="product_image" accept="image/*"><br>
        <?php if ($product['image']): ?>
            <img src="uploads/<?php echo $product['image']; ?>" style="width: 100px; height: auto;" alt="Hình ảnh sản phẩm hiện tại"><br>
        <?php endif; ?>

        <button type="submit">Cập Nhật Sản Phẩm</button>
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
