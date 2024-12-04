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

// Fetch sản phẩm từ cơ sở dữ liệu
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    $products = [];
}

// Xóa sản phẩm
if (isset($_GET['delete_product_id'])) {
    $delete_product_id = $_GET['delete_product_id'];
    
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $delete_product_id);

    if ($stmt->execute()) {
        header('Location: manage_products.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'], $_POST['product_desc'], $_POST['product_price'], $_POST['product_qty'])) {
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $product_price = $_POST['product_price'];
    $product_qty = $_POST['product_qty'];

    if (isset($_FILES['product_image'])) {
        $image_name = $_FILES['product_image']['name'];
        $image_tmp = $_FILES['product_image']['tmp_name'];
        move_uploaded_file($image_tmp, "uploads/" . $image_name);
    } else {
        $image_name = null;
    }

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $product_name, $product_desc, $product_price, $product_qty, $image_name);

    if ($stmt->execute()) {
        header('Location: manage_products.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Quản Lý Sản Phẩm</title>
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

    <h2>Thêm Sản Phẩm</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="product_name">Tên Sản Phẩm</label><br>
        <input type="text" id="product_name" name="product_name" required><br>

        <label for="product_desc">Mô Tả</label><br>
        <textarea id="product_desc" name="product_desc" required></textarea><br>

        <label for="product_price">Giá</label><br>
        <input type="number" id="product_price" name="product_price" required><br>

        <label for="product_qty">Số Lượng</label><br>
        <input type="number" id="product_qty" name="product_qty" required><br>

        <label for="product_image">Hình Ảnh</label><br>
        <input type="file" id="product_image" name="product_image" accept="image/*"><br>

        <button type="submit">Thêm Sản Phẩm</button>
    </form>

    <h2>Danh Sách Sản Phẩm</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Sản Phẩm</th>
                <th>Mô Tả</th>
                <th>Giá</th>
                <th>Số Lượng</th>
                <th>Hình Ảnh</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['description']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><img src="uploads/<?php echo $product['image']; ?>" style="width: 100px; height: auto;"></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>"><button>Sửa</button></a> |
                        <a href="manage_products.php?delete_product_id=<?php echo $product['product_id']; ?>" onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')"><button>Xóa</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
