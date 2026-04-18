<?php

include_once __DIR__ . '/../includes/ketnoi.php';
include_once __DIR__ . '/../models/sanPhamChiTiet.php';
require_once __DIR__ . '/../models/yeuThichModel.php';
$maHH = isset($_GET['id']) ? intval($_GET['id']) : 0;
$chiTiet = null;
$hinhAnhs = [];
$binhLuans = [];
$hoSoNguoiBan = [];
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
            // Lấy hồ sơ người bán
            if (isset($chiTiet['IdNguoiBan'])) {
                // Hàm này bạn vừa viết thêm trong Model ở bước trước
                $hoSoNguoiBan = $sanPham->getThongTinNguoiBan($chiTiet['IdNguoiBan']);
            }
            // Tính giá sau giảm
            $giaGoc = $chiTiet['Gia'];
            $giamGia = $chiTiet['GiamGia'] ?? 0;
            $giaSauGiam = $giaGoc - ($giaGoc * ($giamGia / 100));

            // Logic cho nút bấm (dựa trên số lượng)
            $nutThemGioHangClass = $chiTiet['SoLuongHH'] == 0 ?  "id = 'add-to-cart-button' style ='text-decoration: line-through;'" : "id ='add-to-cart-button'";
            $nutMuaNgayClass = $chiTiet['SoLuongHH'] == 0 ?  "id = 'buy-now-button' style ='text-decoration: line-through;'" : "id ='buy-now-button'";
            //yêu thích
            if (isset($_SESSION['IdTaiKhoan']) && isset($_GET['id'])) {
                $idKhachHang = (int)$_SESSION['IdTaiKhoan'];
                $maHHDangXem = (int)$_GET['id'];

                // 1. Lưu hành vi "Xem" (1 điểm) vào DB vẫn giữ nguyên để AI có dữ liệu sau này
                $sql_ai = "INSERT INTO HanhVi_AI (IdTaiKhoan, MaHH, Diem) 
               VALUES ($idKhachHang, $maHHDangXem, 1) 
               ON DUPLICATE KEY UPDATE Diem = GREATEST(Diem, 1)";
                $conn->query($sql_ai);

                // 2. CHIẾN THUẬT C: KIỂM SOÁT TẦN SUẤT RETRAIN
                // Khởi tạo biến đếm nếu chưa có
                if (!isset($_SESSION['view_count_ai'])) {
                    $_SESSION['view_count_ai'] = 0;
                    // Tạo một mốc ngẫu nhiên từ 5 đến 10 để retrain
                    $_SESSION['retrain_limit'] = rand(5, 10);
                }

                $_SESSION['view_count_ai']++;

                // Chỉ khi đạt đến hạn mức (5-10 lần) mới gọi Python học lại
                if ($_SESSION['view_count_ai'] >= $_SESSION['retrain_limit']) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/retrain");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    @curl_exec($ch);
                    curl_close($ch);

                    // Reset lại bộ đếm và tạo mốc ngẫu nhiên mới cho lần sau
                    $_SESSION['view_count_ai'] = 0;
                    $_SESSION['retrain_limit'] = rand(5, 10);

                    // Ghi log nhẹ nhàng để bạn theo dõi trong file log của PHP (nếu cần)
                    // error_log("AI đã thực hiện học lại sau " . $_SESSION['retrain_limit'] . " lần xem.");
                }
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
