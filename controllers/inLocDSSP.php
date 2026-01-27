<?php
header('Content-Type: application/json');
require_once '../includes/ketnoi.php';
require_once '../models/sanPham.php';
require_once '../models/danhMuc.php';

$sanPhamModel = new SanPham($conn);
$danhMucModel = new DanhMuc($conn);
$hanhdong = isset($_GET['action']) ? $_GET['action'] : '';

switch ($hanhdong) {
    case 'getAllSanPham':
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
        $sapxep = isset($_GET['sort']) ? $_GET['sort'] : 'new';
        $iddanhmuc = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $tukhoa = isset($_GET['keyword']) ? $_GET['keyword'] : null;

        if ($iddanhmuc === 0) {
            $iddanhmuc = null;
        }

        $sanPham = $sanPhamModel->getSanPhamPhanTrang($offset, $limit, $sapxep, $iddanhmuc, $tukhoa);
        $tongSoSanPham = $sanPhamModel->demSanPham($iddanhmuc, $tukhoa);

        echo json_encode([
            'success' => true,
            'products' => $sanPham,
            'totalCount' => $tongSoSanPham,
            'hasMore' => ($offset + $limit) < $tongSoSanPham
        ]);
        break;

    case 'getAllDanhMuc':
        $danhmuc = $danhMucModel->getDanhMuc();
        echo json_encode([
            'success' => true,
            'danhmuc' => $danhmuc
        ]);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Hành động không hợp lệ'
        ]);
        break;
}
