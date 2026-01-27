<?php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/TaiKhoanModel.php';
require_once __DIR__ . '/../models/DiaChiModel.php';
if (!isset($_SESSION['IdTaiKhoan'])) {
    header('Location: dangNhapController.php');
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];

// KHỞI TẠO MODEL
$tkModel = new TaiKhoanModel($conn);
$dcModel = new DiaChiModel($conn);

//  LẤY DỮ LIỆU TỪ MODEL
$user = $tkModel->getUserById($idUser);
$default_address = $dcModel->getDefaultAddress($idUser);
$all_addresses = $dcModel->getAllAddresses($idUser);

// LẤY DỮ LIỆU GIỎ HÀNG TỪ SESSION
$cart_items = $_SESSION['checkout_cart']['items'] ?? [];
$tongTien = $_SESSION['checkout_cart']['total_amount'] ?? 0;

// Chuyển hướng nếu giỏ hàng thanh toán trống
if (empty($cart_items)) {
    $_SESSION['message'] = '<div class="alert alert-warning">Giỏ hàng thanh toán của bạn đang trống. Vui lòng chọn lại sản phẩm.</div>';
    header('Location: gioHangController.php');
    exit;
}

$phiVanChuyen = 0;
$tongThanhToan = $tongTien + $phiVanChuyen;

// GỌI VIEW (Luôn ở cuối)include_once __DIR__ . '/../views/thanhToan.php';
include_once __DIR__ . '/../views/thanhToan.php';
