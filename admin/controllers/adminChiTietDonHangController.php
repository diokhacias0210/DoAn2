<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminDonHangModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: adminDonHangController.php");
    exit;
}

$maDH = intval($_GET['id']);
$model = new AdminDonHangModel($conn);

$thongTin = $model->getThongTinDonHang($maDH);
$chiTiet = $model->getChiTietDonHang($maDH);

if (empty($thongTin)) {
    header("Location: adminDonHangController.php");
    exit;
}

include_once __DIR__ . '/../views/adminChiTietDonHang.php';
