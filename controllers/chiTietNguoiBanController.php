<?php
// File: controllers/chiTietNguoiBanController.php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/sanPhamChiTiet.php';
require_once __DIR__ . '/../models/danhMuc.php'; // <--- Thêm dòng này

$idNguoiBan = isset($_GET['IdTaiKhoan']) ? intval($_GET['IdTaiKhoan']) : 0;

if ($idNguoiBan <= 0) {
    header("Location: trangChuController.php");
    exit;
}

// 1. Lấy thông tin Shop
$spChiTietModel = new SanPhamChiTiet($conn);
$shopInfo = $spChiTietModel->getThongTinNguoiBan($idNguoiBan);

if (!$shopInfo) {
    echo "Người bán không tồn tại.";
    exit;
}

// 2. Lấy danh sách danh mục để đổ vào Dropdown
$danhMucModel = new DanhMuc($conn);
$danhSachDanhMuc = $danhMucModel->getDanhMuc();

// KHÔNG CẦN query sản phẩm ở đây nữa vì JS sẽ lo việc đó qua API

// 3. Gọi View
include_once __DIR__ . '/../views/chiTietNguoiBan.php';
