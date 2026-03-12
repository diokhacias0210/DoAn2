<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminNguoiDungModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: adminNguoiDungController.php");
    exit;
}

$idUser = (int)$_GET['id'];
$nguoiDungModel = new AdminNguoiDungModel($conn);

// Lấy thông tin cá nhân, cửa hàng
$chiTietUser = $nguoiDungModel->getChiTietNguoiDung($idUser);
if (!$chiTietUser) {
    header("Location: adminNguoiDungController.php");
    exit;
}

// Lấy danh sách sản phẩm họ đăng bán
$danhSachSanPham = $nguoiDungModel->getSanPhamCuaNguoiDung($idUser);

include_once __DIR__ . '/../views/adminChiTietNguoiDung.php';
