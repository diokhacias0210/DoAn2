<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/sellerDonHangModel.php';

if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: sellerDonHangController.php");
    exit;
}

$idNguoiBan = $_SESSION['IdTaiKhoan'];
$maDH = intval($_GET['id']);
$model = new SellerDonHangModel($conn);

$message = '';
$error = '';
if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
if (isset($_SESSION['err'])) {
    $error = $_SESSION['err'];
    unset($_SESSION['err']);
}

// Lấy thông tin chung của đơn & Chi tiết sản phẩm
$thongTin = $model->getThongTinDonHang($maDH, $idNguoiBan);
$chiTiet = $model->getChiTietDonHang($maDH, $idNguoiBan);

if (empty($thongTin)) {
    header("Location: sellerDonHangController.php");
    exit;
}

include_once __DIR__ . '/../views/sellerChiTietDonHang.php';
