<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminSanPhamModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: adminSanPhamController.php");
    exit;
}

$mahh = (int)$_GET['id'];
$sanPhamModel = new AdminSanPhamModel($conn);

$chiTiet = $sanPhamModel->getChiTietSanPhamAdmin($mahh);
if (!$chiTiet) {
    header("Location: adminSanPhamController.php");
    exit;
}
$danhSachAnh = $sanPhamModel->getAnhSanPham($mahh);

include_once __DIR__ . '/../views/adminChiTietSanPham.php';
