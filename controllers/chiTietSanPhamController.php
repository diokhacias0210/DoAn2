<?php

include_once __DIR__ . '/../includes/ketnoi.php';
include_once __DIR__ . '/../models/sanPhamChiTiet.php';
require_once __DIR__ . '/../models/yeuThichModel.php';
$maHH = isset($_GET['id']) ? intval($_GET['id']) : 0;
$chiTiet = null;
$hinhAnhs = [];
$binhLuans = [];
$giaGoc = 0;
$giamGia = 0;
$giaSauGiam = 0;
$daYeuThich = false;
$errorMessage = '';

// 3. VALIDATE VÀ LẤY DỮ LIỆU TỪ MODEL
if ($maHH <= 0) {
    $errorMessage = 'ID sản phẩm không hợp lệ';
} else {
    try {
        $sanPham = new SanPhamChiTiet($conn);

        // Lấy thông tin chi tiết (GỌI MODEL)
        $chiTiet = $sanPham->getChiTietSanPham($maHH);

        if (!$chiTiet) {
            $errorMessage = 'Không tìm thấy sản phẩm';
        } else {
            // Lấy hình ảnh (GỌI MODEL)
            $hinhAnhs = $sanPham->getHinhAnhSanPham($maHH);

            // Lấy bình luận (GỌI MODEL)
            $binhLuans = $sanPham->getBinhLuanSanPham($maHH);

            // Tính giá sau giảm
            $giaGoc = $chiTiet['Gia'];
            $giamGia = $chiTiet['GiamGia'] ?? 0;
            $giaSauGiam = $giaGoc - ($giaGoc * ($giamGia / 100));

            // Logic cho nút bấm (dựa trên số lượng)
            $nutThemGioHangClass = $chiTiet['SoLuongHH'] == 0 ?  "id = 'add-to-cart-button' style ='text-decoration: line-through;'" : "id ='add-to-cart-button'";
            $nutMuaNgayClass = $chiTiet['SoLuongHH'] == 0 ?  "id = 'buy-now-button' style ='text-decoration: line-through;'" : "id ='buy-now-button'";
            //yêu thích
            if (isset($_SESSION['IdTaiKhoan'])) {
                $yeuThichModel = new YeuThichModel($conn);
                // Gọi hàm kiểm tra từ model (đã tạo ở bài trước)
                $daYeuThich = $yeuThichModel->kiemTraYeuThich($_SESSION['IdTaiKhoan'], $maHH);
            }
        }
    } catch (Exception $e) {
        $errorMessage = 'Lỗi: ' . $e->getMessage();
    }
}

if ($errorMessage) {
    // Chuyển hướng về trang chủ Controller
    header('Location: trangChuController.php?error=' . urlencode($errorMessage));
    exit;
}
include_once __DIR__ . '/../views/chiTietSanPham.php';
