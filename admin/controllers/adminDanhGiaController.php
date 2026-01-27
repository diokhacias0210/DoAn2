<?php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../../models/danhGia.php';

if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

//  KHỞI TẠO MODEL
$danhGiaModel = new DanhGia($conn);

//  XỬ LÝ LOGIC (GET) VÀ LẤY DỮ LIỆU
$dsSanPham = [];
$dsDanhGia = [];
$maHH = null;

if (!isset($_GET['maHH'])) {
    //Lấy danh sách sản phẩm 
    $dsSanPham = $danhGiaModel->getSanPhamCoDanhGia();
} else {
    //Lấy chi tiết đánh giá
    $maHH = intval($_GET['maHH']);
    $dsDanhGia = $danhGiaModel->getChiTietDanhGia($maHH);
}

include_once __DIR__ . '/../views/quanLyDanhGia.php';
