<?php

session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/sellerDonHangModel.php';

// 1. Check quyền Seller
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idNguoiBan = $_SESSION['IdTaiKhoan'];
$model = new SellerDonHangModel($conn);
$message = '';

// 2. Xử lý cập nhật trạng thái (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $maDH = intval($_POST['maDH']);
    $status = $_POST['trangThai'];

    // Các trạng thái hợp lệ
    $validStatus = ['Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Hoàn tất', 'Đã hủy'];

    if (in_array($status, $validStatus)) {
        if ($model->capNhatTrangThai($maDH, $idNguoiBan, $status)) {
            $message = "Cập nhật đơn hàng #$maDH thành công!";
        } else {
            $message = "Lỗi cập nhật.";
        }
    }
}

// 3. Lấy dữ liệu hiển thị
$keyword = $_GET['search'] ?? '';
$dsDonHang = $model->getDonHangCuaToi($idNguoiBan, $keyword);

// Nếu user bấm xem chi tiết đơn hàng (Modal hoặc trang riêng)
$chiTietDon = [];
if (isset($_GET['view_id'])) {
    $viewId = intval($_GET['view_id']);
    $chiTietDon = $model->getChiTietDonHang($viewId, $idNguoiBan);
}

// 4. Gọi View
include_once __DIR__ . '/../views/sellerQuanLyDonHang.php';
