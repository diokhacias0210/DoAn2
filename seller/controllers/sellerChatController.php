<?php
session_start();
require_once '../../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idSeller = $_SESSION['IdTaiKhoan'];

// LẤY DANH SÁCH PHÒNG CHAT MÀ USER NÀY LÀ NGƯỜI BÁN
// Đã thay đổi t.HoTen thành t.TenTK theo đúng bảng TaiKhoan trong DoAn2.sql
$sql = "SELECT p.MaPhong, p.MaHH, h.TenHH, t.TenTK AS TenNguoiChat 
        FROM PhongChat p
        JOIN TaiKhoan t ON p.IdNguoiMua = t.IdTaiKhoan
        JOIN HangHoa h ON p.MaHH = h.MaHH
        WHERE p.IdNguoiBan = $idSeller
        ORDER BY p.MaPhong DESC";

$result = $conn->query($sql);
$danhSachPhong = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $danhSachPhong[] = $row;
    }
}

// Lấy mã phòng đang chọn (nếu có)
$maPhong = isset($_GET['MaPhong']) ? (int)$_GET['MaPhong'] : 0;

// Gọi View hiển thị
require_once '../views/sellerChat.php';
?>