<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/sellerDonHangModel.php';

if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idNguoiBan = $_SESSION['IdTaiKhoan'];
$model = new SellerDonHangModel($conn);

$message = '';
$error = '';

// --- XỬ LÝ CẬP NHẬT TRẠNG THÁI ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $maDH = intval($_POST['maDH']);
    $newStatus = $_POST['trangThai'];

    $currentStatus = $model->getTrangThaiDonHang($maDH, $idNguoiBan);

    // RÀNG BUỘC LOGIC: Chỉ cho phép các bước tiến lên, không được lùi
    $allowed = false;
    if ($currentStatus == 'Chờ xử lý' && in_array($newStatus, ['Đã xác nhận', 'Đã hủy'])) $allowed = true;
    if ($currentStatus == 'Đã xác nhận' && $newStatus == 'Đang giao') $allowed = true;

    if ($allowed) {
        if ($model->capNhatTrangThai($maDH, $idNguoiBan, $newStatus)) {
            $_SESSION['msg'] = "Cập nhật đơn hàng #$maDH thành: $newStatus!";
        } else {
            $_SESSION['err'] = "Lỗi hệ thống khi cập nhật.";
        }
    } else {
        $_SESSION['err'] = "Lỗi logic: Không thể chuyển từ '$currentStatus' sang '$newStatus'.";
    }

    // Nếu đang ở trang chi tiết mà đổi trạng thái, thì quay lại trang chi tiết
    if (isset($_POST['from_detail']) && $_POST['from_detail'] == 1) {
        header("Location: sellerChiTietDonHangController.php?id=$maDH");
    } else {
        header("Location: sellerDonHangController.php");
    }
    exit;
}

if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
if (isset($_SESSION['err'])) {
    $error = $_SESSION['err'];
    unset($_SESSION['err']);
}

// --- LẤY DỮ LIỆU HIỂN THỊ (CÓ LỌC) ---
$keyword = $_GET['search'] ?? '';
$filter_status = $_GET['status'] ?? '';
$tuNgay = $_GET['tungay'] ?? '';
$denNgay = $_GET['denngay'] ?? '';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_records = $model->countDonHang($idNguoiBan, $keyword, $filter_status, $tuNgay, $denNgay);
$total_pages = ceil($total_records / $limit);

$dsDonHang = $model->getDanhSachDonHang($idNguoiBan, $keyword, $filter_status, $tuNgay, $denNgay, $limit, $offset);

include_once __DIR__ . '/../views/sellerQuanLyDonHang.php';
