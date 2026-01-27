<?php
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/yeuThichModel.php';

if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];
$model = new YeuThichModel($conn);

// Lấy danh sách sản phẩm yêu thích từ Model
$danhSachYeuThich = $model->getDanhSachYeuThich($idUser);

// Gọi View
include_once __DIR__ . '/../views/danhSachYeuThich.php';
