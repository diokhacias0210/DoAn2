<?php
session_start();

// Kiểm tra chưa đăng nhập thì đuổi ra trang Login
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

require_once '../includes/ketnoi.php';
$idUser = $_SESSION['IdTaiKhoan'];

// Kiểm tra xem đã kích hoạt chưa. Nếu kích hoạt rồi thì đuổi thẳng sang trang quản lý của Seller
$sql = "SELECT TrangThaiBanHang FROM TaiKhoan WHERE IdTaiKhoan = $idUser";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if ($row['TrangThaiBanHang'] === 'DangHoatDong') {
        header("Location: ../seller/controllers/sellerSanPhamController.php");
        exit;
    }
}

// Nếu hợp lệ, hiển thị trang form đăng ký
require_once '../views/kichHoatBanHang.php';
?>