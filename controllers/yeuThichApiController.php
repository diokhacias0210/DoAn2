<?php
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/yeuThichModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['IdTaiKhoan'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để sử dụng tính năng này.']);
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];
$mahh = intval($_POST['MaHH'] ?? 0);

if ($mahh <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ.']);
    exit;
}

$model = new YeuThichModel($conn);

// Kiểm tra trạng thái hiện tại
if ($model->kiemTraYeuThich($idUser, $mahh)) {
    // Đã có -> Xóa
    if ($model->xoaYeuThich($idUser, $mahh)) {
        echo json_encode(['success' => true, 'favorited' => false]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa yêu thích.']);
    }
} else {
    // Chưa có -> Thêm
    if ($model->themYeuThich($idUser, $mahh)) {
        echo json_encode(['success' => true, 'favorited' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm yêu thích (Sản phẩm có thể không tồn tại).']);
    }
}
