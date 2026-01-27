<?php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminNguoiDungModel.php';

if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}
$nguoiDungModel = new AdminNguoiDungModel($conn);

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

$danhSachNguoiDung = $nguoiDungModel->getTatCaNguoiDung($keyword);

include_once __DIR__ . '/../views/quanLyNguoiDung.php';
