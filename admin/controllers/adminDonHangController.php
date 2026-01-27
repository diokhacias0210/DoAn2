<?php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../../models/donHang.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header('Location: ../../controllers/dangNhapController.php');
    exit();
}

$donHangModel = new DonHang($conn);
$message = '';

if (isset($_GET['action']) && $_GET['action'] == 'duyet' && isset($_GET['MaDH'])) {
    $maDH = intval($_GET['MaDH']);
    $trangThaiMoi = isset($_GET['status']) ? $_GET['status'] : 'Đã xác nhận';

    try {
        // Gọi Model để xử lý
        $donHangModel->duyetDonHang($maDH, $trangThaiMoi);
        $_SESSION['message'] = "<div class='alert alert-success'>Cập nhật trạng thái đơn hàng #{$maDH} thành công.</div>";
    } catch (Exception $e) {
        $_SESSION['message'] = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    }
    header('Location: adminDonHangController.php');
    exit();
} elseif (isset($_GET['action']) && $_GET['action'] == 'duyet_tat_ca') {
    try {
        $soLuong = $donHangModel->duyetTatCa();
        if ($soLuong > 0) {
            $_SESSION['message'] = "<div class='alert alert-success'>Đã duyệt thành công <strong>$soLuong</strong> đơn hàng.</div>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-warning'>Không có đơn hàng nào cần xử lý.</div>";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    }
    header('Location: adminDonHangController.php');
    exit();
}
// Xử lý tìm kiếm
$keyword = '';
$danhSachDonHang = [];

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $keyword = trim($_GET['search']);
    try {
        $danhSachDonHang = $donHangModel->timKiemDonHang($keyword);
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Lỗi tìm kiếm: " . $e->getMessage() . "</div>";
    }
} else {
    // Hiển thị tất cả đơn hàng
    $danhSachDonHang = $donHangModel->getDonHang();
}
include_once __DIR__ . '/../views/quanLyDonHang.php';
