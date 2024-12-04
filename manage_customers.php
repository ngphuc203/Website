<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); 
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "doanv2";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

$customers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
} else {
    $customers = [];
}

// Xóa khách hàng
if (isset($_GET['delete_customer_id'])) {
    $delete_customer_id = $_GET['delete_customer_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("s", $delete_customer_id);

    if ($stmt->execute()) {
        header('Location: manage_customers.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Thêm khách hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname'], $_POST['username'], $_POST['password'], $_POST['gender'], $_POST['dob'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Không mã hóa
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    $id = uniqid();

    $stmt = $conn->prepare("INSERT INTO users (id, fullname, username, password, gender, dob) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $id, $fullname, $username, $password, $gender, $dob);

    if ($stmt->execute()) {
        header('Location: manage_customers.php');
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
    <title>Quản Lý Khách Hàng</title>
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

    <h2>Thêm Khách Hàng</h2>
    <form method="POST">
        <label for="fullname">Tên Khách Hàng</label><br>
        <input type="text" id="fullname" name="fullname" required><br>

        <label for="username">Tên Tài Khoản</label><br>
        <input type="text" id="username" name="username" required><br>
        
        <label for="password">Mật Khẩu</label><br>
        <input type="password" id="password" name="password" required><br>
        
        <label for="gender">Giới Tính</label><br>
        <input type="radio" id="male" name="gender" value="Nam" required style="width: 50px;"> Nam
        <input type="radio" id="female" name="gender" value="Nữ" required style="width: 50px;"> Nữ<br>

        <label for="dob">Ngày Sinh</label><br>
        <input type="date" id="dob" name="dob" required><br>

        <button type="submit">Thêm Khách Hàng</button>
    </form>

    <div class="admin-content">
        <h2>Danh Sách Khách Hàng</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Khách Hàng</th>
                    <th>Tên Tài Khoản</th>
                    <th>Mật Khẩu</th>
                    <th>Giới Tính</th>
                    <th>Ngày Sinh</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo $customer['id']; ?></td>
                        <td><?php echo $customer['fullname']; ?></td>
                        <td><?php echo $customer['username']; ?></td>
                        <td><?php echo $customer['password']; ?></td>
                        <td><?php echo $customer['gender']; ?></td>
                        <td><?php echo $customer['dob']; ?></td>
                        <td>
                            <a href="edit_customer.php?id=<?php echo $customer['id']; ?>"><button>Sửa</button></a> |
                            <a href="manage_customers.php?delete_customer_id=<?php echo $customer['id']; ?>" onclick="return confirm('Bạn chắc chắn muốn xóa khách hàng này?')"><button>Xóa</button></a>
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
