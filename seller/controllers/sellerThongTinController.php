<?php
session_start();
require_once '../../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];

// Lấy thông tin cửa hàng của user hiện tại
$sql = "SELECT TenCuaHang, SoCCCD, DiaChiKhoHang, TenNganHang, SoTaiKhoanNganHang, TenChuTaiKhoan 
        FROM HoSoNguoiBan 
        WHERE IdTaiKhoan = $idUser";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $thongTin = $result->fetch_assoc();
} else {
    $thongTin = null; // Trường hợp lỗi không tìm thấy
}

// Gọi giao diện View ra
require_once '../views/sellerThongTinCuaHang.php';
?>