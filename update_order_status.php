<?php
session_start();

// Kiểm tra xem người dùng có phải là admin không
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Kiểm tra và lấy thông tin đơn hàng và trạng thái mới từ POST
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo "Thiếu thông tin đơn hàng hoặc trạng thái.";
    exit();
}

$order_id = $_POST['order_id'];
$new_status = $_POST['status'];

// Kết nối cơ sở dữ liệu MySQL
$servername = "localhost";
$username = "root";
$password = "";
$database = "doanv2"; // Đảm bảo thay thế với tên cơ sở dữ liệu thực của bạn

$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Cập nhật trạng thái đơn hàng trong cơ sở dữ liệu
$sql = "UPDATE orders SET status = ? WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $order_id); // "si" có nghĩa là 1 chuỗi và 1 số nguyên
if ($stmt->execute()) {
    // Cập nhật thành công
    header('Location: manager_orders.php');
    exit();
} else {
    echo "Cập nhật trạng thái thất bại.";
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>
