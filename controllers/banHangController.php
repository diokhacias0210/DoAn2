<?php
session_start();
require_once '../models/banHangModel.php';
require_once '../includes/ketnoi.php';

// Kiểm tra xem đã đăng nhập chưa
if (!isset($_SESSION['IdTaiKhoan'])) {
    die("Bạn cần đăng nhập để thực hiện chức năng này!");
}

$idUser = $_SESSION['IdTaiKhoan'];
$model = new banHangModel($conn);

// Xử lý POST từ Form gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Nút xác nhận đăng ký
    if (isset($_POST['action']) && $_POST['action'] === 'kich_hoat') {
        $tenCuaHang = $_POST['TenCuaHang'] ?? '';
        $soCCCD = $_POST['SoCCCD'] ?? '';
        $diaChiKhoHang = $_POST['DiaChiKhoHang'] ?? '';
        $tenNganHang = $_POST['TenNganHang'] ?? '';
        $soTaiKhoan = $_POST['SoTaiKhoanNganHang'] ?? '';
        $tenChuTaiKhoan = $_POST['TenChuTaiKhoan'] ?? '';

        // Gọi model thực hiện lưu CSDL và đổi trạng thái
        $model->kichHoat($idUser, $tenCuaHang, $soCCCD, $diaChiKhoHang, $tenNganHang, $soTaiKhoan, $tenChuTaiKhoan);
        
        // Chuyển hướng lại trang thông tin tài khoản
        header("Location: thongTinTaiKhoanController.php");
        exit;
    }

    // Nút hủy bán hàng
    if (isset($_POST['action']) && $_POST['action'] === 'huy') {
        $model->huy($idUser);
        header("Location: thongTinTaiKhoanController.php");
        exit;
    }
}

// Lấy trạng thái GET
$trangThaiBanHang = $model->getTrangThai($idUser);
?>