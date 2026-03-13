<?php
// 1. Gọi file kết nối
require_once __DIR__ . '/../includes/ketnoi.php';
// 2. Gọi file Model 
require_once __DIR__ . '/../admin/models/adminBannerModel.php';

// 3. Lấy dữ liệu từ Model trước
$bannerModel = new BannerModel($conn);
$danhSachBanner = $bannerModel->getVisibleBanners();

// 4. Sau khi đã có dữ liệu ($danhSachBanner), mới gọi View ra để hiển thị ở cuối cùng
include_once __DIR__ . '/../views/trangChu.php';
?>