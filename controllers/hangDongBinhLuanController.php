<?php
require_once '../includes/ketnoi.php';
require_once '../models/danhGia.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ']);
    exit;
}

$maBL = intval($_POST['maBL'] ?? 0);
$action = $_POST['action'] ?? '';

$danhGia = new DanhGia($conn);

switch ($action) {
    case 'an':
        $danhGia->capNhatTrangThaiBinhLuan($maBL, 'Ẩn');
        echo json_encode(['success' => true, 'newStatus' => 'Ẩn']);
        break;
    case 'hien':
        $danhGia->capNhatTrangThaiBinhLuan($maBL, 'Hiển thị');
        echo json_encode(['success' => true, 'newStatus' => 'Hiển thị']);
        break;
    case 'xoa':
        $danhGia->xoaBinhLuan($maBL);
        echo json_encode(['success' => true, 'deleted' => true]);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Hành động không hợp lệ']);
}
