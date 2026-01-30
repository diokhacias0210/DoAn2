<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/sanPham.php';

$sanPhamModel = new SanPham($conn);

$action = $_GET['action'] ?? '';

if ($action === 'getProducts') {
    $idSeller = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $limit = 8; // Số lượng lấy mỗi lần
    $sort = $_GET['sort'] ?? 'new';
    $category = isset($_GET['category']) && $_GET['category'] != '' ? intval($_GET['category']) : null;
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : null;

    if ($idSeller <= 0) {
        echo json_encode(['success' => false, 'message' => 'Shop không hợp lệ']);
        exit;
    }

    $products = $sanPhamModel->getSanPhamCuaNguoiBan($idSeller, $offset, $limit, $sort, $category, $keyword);
    $total = $sanPhamModel->demSanPhamCuaNguoiBan($idSeller, $category, $keyword);

    echo json_encode([
        'success' => true,
        'products' => $products,
        'hasMore' => ($offset + $limit) < $total,
        'total' => $total
    ]);
}
