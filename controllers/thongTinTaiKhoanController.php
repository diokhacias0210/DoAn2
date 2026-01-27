<?php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/TaiKhoanModel.php';

$model = new TaiKhoanModel($conn);

//  KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];
$message = '';

// XỬ LÝ LOGIC POST (Thêm / Xóa địa chỉ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'them_diachi') {
        $result = $model->themDiaChi($idUser, trim($_POST['diachi_moi']));
        // Kiểm tra xem có yêu cầu chuyển hướng về thanh toán không
        $redirect = $_POST['redirect'] ?? '';
        if ($result === "max") {
            $_SESSION['message'] = '<div class="alert alert-danger">Bạn chỉ có thể thêm tối đa 5 địa chỉ.</div>';
        } elseif ($result === "ok") {
            $_SESSION['message'] = '<div class="alert alert-success">Đã thêm địa chỉ mới thành công!</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-warning">Thêm địa chỉ thất bại.</div>';
        }

        // ĐIỀU HƯỚNG
        if ($redirect === 'thanhtoan') {
            header("Location: thanhToanController.php"); // Quay lại trang thanh toán
        } else {
            header("Location: thongTinTaiKhoanController.php"); // Quay lại trang tài khoản
        }
        exit;
    } elseif ($action === 'xoa_diachi') {
        $result = $model->xoaDiaChi($idUser, (int)$_POST['MaDC_xoa']);
        if ($result === "default") {
            $message = '<div class="alert alert-warning">Không thể xóa địa chỉ mặc định.</div>';
        } elseif ($result === "ok") {
            header("Location: thongTinTaiKhoanController.php?status=delete_success");
            exit;
        } else {
            $message = '<div class="alert alert-danger">Lỗi khi xóa hoặc địa chỉ không tồn tại.</div>';
        }
    }
}

// XỬ LÝ THÔNG BÁO TỪ URL 
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'add_success') {
        $message = '<div class="alert alert-success">Đã thêm địa chỉ mới thành công!</div>';
    } elseif ($_GET['status'] === 'delete_success') {
        $message = '<div class="alert alert-success">Đã xóa địa chỉ thành công!</div>';
    }
}

// LẤY DỮ LIỆU ĐỂ HIỂN THỊ
$user = $model->getThongTin($idUser);
$addresses = $model->getDanhSachDiaChi($idUser);
$address_count = count($addresses);

include_once __DIR__ . '/../views/thongTinTaiKhoan.php';
