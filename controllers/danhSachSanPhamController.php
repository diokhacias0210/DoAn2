<?php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/danhMuc.php';

$danhMucModel = new DanhMuc($conn);

// Lấy danh sách danh mục để đổ vào dropdown
$danhSachDanhMuc = $danhMucModel->getDanhMuc();

include_once __DIR__ . '/../views/danhSachSanPham.php';
