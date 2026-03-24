<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminDonHangModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$model = new AdminDonHangModel($conn);

// Lấy tham số Lọc & Tìm kiếm
$keyword = $_GET['search'] ?? '';
$filter_status = $_GET['status'] ?? '';
$tuNgay = $_GET['tungay'] ?? '';
$denNgay = $_GET['denngay'] ?? '';
$sort = $_GET['sort'] ?? 'new';

// Thiết lập phân trang
$limit = 15; // Admin xem nhiều đơn hơn trên 1 trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_records = $model->countDonHang($keyword, $filter_status, $tuNgay, $denNgay);
$total_pages = ceil($total_records / $limit);

$dsDonHang = $model->getDanhSachDonHang($keyword, $filter_status, $tuNgay, $denNgay, $sort, $limit, $offset);

include_once __DIR__ . '/../views/quanLyDonHang.php';
