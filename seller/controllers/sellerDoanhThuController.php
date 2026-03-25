<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/sellerDoanhThuModel.php';

if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idNguoiBan = $_SESSION['IdTaiKhoan'];
$model = new SellerDoanhThuModel($conn);

$message = '';
$error = '';

// --- XỬ LÝ YÊU CẦU RÚT TIỀN (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'rut_tien') {
    $soTien = floatval($_POST['soTien']);
    $nganHang = trim($_POST['nganHang']);
    $stk = trim($_POST['stk']);
    $chuTk = trim($_POST['chuTk']);

    if ($soTien < 50000) {
        $_SESSION['err'] = "Số tiền rút tối thiểu là 50.000đ.";
    } else {
        $kq = $model->taoYeuCauRutTien($idNguoiBan, $soTien, $nganHang, $stk, $chuTk);
        if ($kq) {
            $_SESSION['msg'] = "Đã gửi yêu cầu rút " . number_format($soTien, 0, ',', '.') . "đ. Vui lòng chờ Admin duyệt.";
        } else {
            $_SESSION['err'] = "Lỗi! Số dư của bạn không đủ hoặc hệ thống bận.";
        }
    }
    header("Location: sellerDoanhThuController.php");
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

// --- XỬ LÝ LỌC NGÀY THÁNG ---
// Mặc định là từ ngày 1 đầu tháng đến ngày hiện tại
$tuNgay = $_GET['tungay'] ?? date('Y-m-01');
$denNgay = $_GET['denngay'] ?? date('Y-m-d');

if (strtotime($tuNgay) > strtotime($denNgay)) {
    $error = "Ngày bắt đầu không được lớn hơn ngày kết thúc!";
}

// --- LẤY DỮ LIỆU HIỂN THỊ ---
$soDuHienTai = $model->getSoDu($idNguoiBan);
$thongKe = $model->getThongKeTrongKy($idNguoiBan, $tuNgay, $denNgay);
$topSanPham = $model->getTopSanPhamBanChay($idNguoiBan, $tuNgay, $denNgay);
$lichSuRut = $model->getLichSuRutTien($idNguoiBan);

include_once __DIR__ . '/../views/sellerDoanhThu.php';
