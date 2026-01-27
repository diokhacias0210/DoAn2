<?php
require_once '../includes/ketnoi.php';
require_once '../models/sanPham.php';

if (isset($_GET['q'])) {
    $keyword = trim($_GET['q']);

    // Khởi tạo model sản phẩm
    $model = new SanPham($conn);

    // Gọi hàm tìm kiếm trong model
    $results = $model->timKiemSanPham($keyword);

    // Trả kết quả JSON về cho AJAX
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode([]);
exit;
