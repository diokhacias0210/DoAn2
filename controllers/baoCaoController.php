<?php
session_start();
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/baoCaoModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['IdTaiKhoan'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để báo cáo!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNguoiBaoCao = $_SESSION['IdTaiKhoan'];
    $idBiBaoCao = intval($_POST['idBiBaoCao']);
    $maHH = !empty($_POST['maHH']) ? intval($_POST['maHH']) : null;
    $loaiBaoCao = $_POST['loaiBaoCao']; // 'SanPham' hoặc 'NguoiBan'
    $lyDoChinh = trim($_POST['lyDoChinh']);
    $chiTiet = trim($_POST['chiTiet']);

    if (empty($lyDoChinh)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng chọn lý do!']);
        exit;
    }

    $baoCaoModel = new BaoCaoModel($conn);
    $result = $baoCaoModel->guiBaoCao($idNguoiBaoCao, $idBiBaoCao, $maHH, $loaiBaoCao, $lyDoChinh, $chiTiet);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Báo cáo thành công. Quản trị viên sẽ xem xét sớm nhất!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại!']);
    }
}
