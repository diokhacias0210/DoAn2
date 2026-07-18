<?php
// Gọi file kết nối
require_once __DIR__ . '/../includes/ketnoi.php';
// Gọi file Model 
require_once __DIR__ . '/../admin/models/adminBannerModel.php';

//  Lấy dữ liệu từ Model trước
$bannerModel = new BannerModel($conn);
$danhSachBanner = $bannerModel->getVisibleBanners();

// Lấy tọa độ mặc định của người dùng từ bảng DiaChi
$userLat = 0;
$userLng = 0;
if (isset($_SESSION['IdTaiKhoan'])) {
    $id = $_SESSION['IdTaiKhoan'];
    // Sửa SQL: Lấy từ bảng DiaChi với điều kiện MacDinh = 1
    $sql = "SELECT ViDo, KinhDo FROM DiaChi WHERE IdTaiKhoan = $id AND MacDinh = 1 LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $userLat = $row['ViDo'] ?? 0;
        $userLng = $row['KinhDo'] ?? 0;
    }
}
// Bây giờ biến $userLat và $userLng đã sẵn sàng để dùng ở trangChu.php

include_once __DIR__ . '/../views/trangChu.php';
?>