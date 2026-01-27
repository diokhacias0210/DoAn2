<?php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../../models/donHang.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header('Location: ../../controllers/dangNhapController.php');
    exit();
}

if (!isset($_GET['MaDH'])) {
    header('Location: adminDonHangController.php'); // Về trang danh sách
    exit();
}

$maDH = intval($_GET['MaDH']);
$donHangModel = new DonHang($conn);

$thongTinDH = $donHangModel->getThongTinDonHang($maDH);
$chiTietSP = $donHangModel->getChiTietDonHang($maDH);

if (!$thongTinDH) {
    header('Location: adminDonHangController.php');
    exit();
}

include_once __DIR__ . '/../views/quanLyChiTietDonHang.php';
