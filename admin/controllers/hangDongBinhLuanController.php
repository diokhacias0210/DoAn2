<?php
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminDanhGiaModel.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maBL = isset($_POST['maBL']) ? intval($_POST['maBL']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($maBL <= 0 || empty($action)) {
        echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    $model = new AdminDanhGiaModel($conn);
    $result = false;

    // Mặc định xử lý cho BinhLuan (vì JS bạn gửi đang xử lý bình luận)
    $loai = 'binhluan';

    if ($action === 'xoa') {
        $result = $model->xoaRecord($loai, $maBL);
        if ($result) {
            echo json_encode(['success' => true, 'deleted' => true]);
            exit;
        }
    } elseif ($action === 'an') {
        $result = $model->capNhatTrangThai($loai, $maBL, 'Ẩn');
        if ($result) {
            echo json_encode(['success' => true, 'newStatus' => 'Ẩn']);
            exit;
        }
    } elseif ($action === 'hien') {
        $result = $model->capNhatTrangThai($loai, $maBL, 'Hiển thị');
        if ($result) {
            echo json_encode(['success' => true, 'newStatus' => 'Hiển thị']);
            exit;
        }
    }

    echo json_encode(['success' => false, 'error' => 'Thao tác thất bại']);
}
