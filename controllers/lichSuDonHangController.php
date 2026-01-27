<?php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/donHang.php';

// KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['IdTaiKhoan'])) {
    header('Location: dangNhapController.php');
    exit();
}

$idUser = $_SESSION['IdTaiKhoan'];
$donHangModel = new DonHang($conn);
$message = '';

// Xử lý hủy đơn hàng 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel') {
    $maDH = intval($_POST['MaDH']);

    // Lấy lý do từ form
    $lyDoChinh = $_POST['lyDoSelect'] ?? '';
    $lyDoKhac = trim($_POST['lyDoKhac'] ?? '');

    $lyDoCuoiCung = ($lyDoChinh === 'Khác') ? $lyDoKhac : $lyDoChinh;

    if (empty($lyDoCuoiCung)) {
        $lyDoCuoiCung = "Không có lý do cụ thể";
    }

    try {
        // Truyền lý do vào hàm huyDonHang
        $result = $donHangModel->huyDonHang($maDH, $idUser, $lyDoCuoiCung);
        $_SESSION['message'] = "<div class='alert alert-" . ($result['success'] ? 'success' : 'danger') . "'>" . $result['message'] . "</div>";
    } catch (Exception $e) {
        $_SESSION['message'] = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    }
    header('Location: lichSuDonHangController.php');
    exit();
}

// Xử lý xóa đơn hàng
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['MaDH'])) {
    $maDH = intval($_GET['MaDH']);
    try {
        // Gọi Model để xử lý
        $donHangModel->xoaDonHang($maDH, $idUser);
        $_SESSION['message'] = "<div class='alert alert-success'>Xóa đơn hàng thành công.</div>";
    } catch (Exception $e) {
        $_SESSION['message'] = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    }
    header('Location: lichSuDonHangController.php');
    exit();
}

// Lấy thông báo từ session 
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Lấy danh sách đơn hàng 
$orders = $donHangModel->getDonHangTheoKhachHang($idUser);

// Lấy chi tiết và lịch sử cho mỗi đơn hàng 
foreach ($orders as &$order) {
    $order['details'] = $donHangModel->getChiTietDonHang($order['MaDH']);
    $order['lichsu'] = $donHangModel->getLichSuDonHang($order['MaDH']);
}
unset($order); // Hủy tham chiếu của biến $order sau vòng lặp

include_once __DIR__ . '/../views/lichSuDonHang.php';
