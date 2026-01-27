<?php
// File: admin/controllers/dashboardController.php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminThongKeModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header('Location: ../../controllers/dangNhapController.php');
    exit();
}

$thongKeModel = new ThongKe($conn);

// ========== THỐNG KÊ TỔNG QUAN (Luôn hiển thị) ==========
$tongSanPham = $thongKeModel->getTongSanPham();
$tongDonHang = $thongKeModel->getTongDonHang();
$tongNguoiDung = $thongKeModel->getTongNguoiDung();
$tongDoanhThu = $thongKeModel->getTongDoanhThu();

// Thống kê theo thời gian cố định
$doanhThuHomNay = $thongKeModel->getDoanhThuTheoNgay();
$doanhThuThangNay = $thongKeModel->getDoanhThuTheoThang();
$doanhThuNamNay = $thongKeModel->getDoanhThuTheoNam();

// ========== XỬ LÝ BỘ LỌC (Tùy chọn) ==========
$loaiLoc = isset($_GET['loai_loc']) ? $_GET['loai_loc'] : 'tat-ca';
$tuNgay = isset($_GET['tu_ngay']) ? $_GET['tu_ngay'] : date('Y-m-01');
$denNgay = isset($_GET['den_ngay']) ? $_GET['den_ngay'] : date('Y-m-d');
$thang = isset($_GET['thang']) ? (int)$_GET['thang'] : (int)date('m');
$nam = isset($_GET['nam']) ? (int)$_GET['nam'] : (int)date('Y');

// Khởi tạo biến
$doanhThuLoc = null;
$soDonHangLoc = null;
$topSanPham = [];

// Xử lý theo loại lọc
if ($loaiLoc == 'ngay') {
    // Lọc theo khoảng ngày
    $result = $thongKeModel->getDoanhThuTheoKhoangThoiGian($tuNgay, $denNgay);
    foreach ($result as $item) {
        $doanhThuLoc += $item['DoanhThu'];
        $soDonHangLoc += $item['SoDonHang'];
    }
    $topSanPham = $thongKeModel->getTopSanPhamBanChayTheoKhoangThoiGian($tuNgay, $denNgay);
    
} elseif ($loaiLoc == 'thang') {
    // Lọc theo tháng
    $result = $thongKeModel->getDoanhThuTheoThang($thang, $nam);
    $doanhThuLoc = $result['DoanhThu'];
    $soDonHangLoc = $result['SoDonHang'];
    $topSanPham = $thongKeModel->getTopSanPhamBanChayTheoThang($thang, $nam);
    
} elseif ($loaiLoc == 'nam') {
    // Lọc theo năm
    $result = $thongKeModel->getDoanhThuTheoNam($nam);
    $doanhThuLoc = $result['DoanhThu'];
    $soDonHangLoc = $result['SoDonHang'];
    $topSanPham = $thongKeModel->getTopSanPhamBanChayTheoNam($nam);
    
} else {
    // Mặc định: Hiển thị top sản phẩm tất cả thời gian
    $topSanPham = $thongKeModel->getTopSanPhamBanChay();
}

// Load view
include_once __DIR__ . '/../views/dashboard.php';
?>