<?php
session_start();
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/thongBaoModel.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

$idTaiKhoan = $_SESSION['IdTaiKhoan'];
$thongBaoModel = new ThongBaoModel($conn);

// Xử lý AJAX: Đánh dấu đã đọc
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_read') {
    $maTB = intval($_POST['maTB']);
    if ($maTB > 0) {
        $thongBaoModel->danhDauDaDoc($idTaiKhoan, $maTB);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Lấy danh sách thông báo của người dùng này
$dsThongBao = $thongBaoModel->getThongBaoCuaToi($idTaiKhoan);

// Gọi View
include_once __DIR__ . '/../views/thongBao.php';
