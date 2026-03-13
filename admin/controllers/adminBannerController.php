<?php
require_once __DIR__ . '/../../includes/ketnoi.php'; 
require_once __DIR__ . '/../models/adminBannerModel.php'; 

$bannerModel = new BannerModel($conn);

// ---- XỬ LÝ XÓA (GET) ----
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Xóa file ảnh trong thư mục trước khi xóa data
    $banner = $bannerModel->getBannerById($id);
    if ($banner) {
        $filePath = "../../assets/images/banners/" . $banner['HinhAnh'];
        if (file_exists($filePath) && !empty($banner['HinhAnh'])) {
            unlink($filePath);
        }
    }
    
    $bannerModel->deleteBanner($id);
    header("Location: adminBannerController.php");
    exit;
}

// ---- XỬ LÝ THÊM / SỬA (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maBanner = $_POST['maBanner'] ?? ''; // Dùng trường ẩn để phân biệt Thêm hay Sửa
    $tieuDe = $_POST['tieuDe'];
    $trangThai = $_POST['trangThai'];
    
    // Xử lý Upload ảnh
    $hinhAnh = $_FILES['hinhAnh']['name'];
    $target_dir = "../../assets/images/banners/";
    
    if (!empty($hinhAnh)) {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($hinhAnh);
        move_uploaded_file($_FILES['hinhAnh']['tmp_name'], $target_file);
    } else {
        $hinhAnh = null; // Bật cờ để giữ nguyên ảnh cũ khi sửa
    }

    // Logic kiểm tra xem đây là thao tác Thêm hay Sửa
    if (empty($maBanner)) {
        // Nếu $maBanner trống => THÊM MỚI
        if ($hinhAnh) { 
            $bannerModel->addBanner($tieuDe, $hinhAnh, $trangThai);
        }
    } else {
        // Nếu có ID => CẬP NHẬT (SỬA)
        $bannerModel->updateBanner($maBanner, $tieuDe, $trangThai, $hinhAnh);
    }
    
    header("Location: adminBannerController.php");
    exit;
}

// ---- XỬ LÝ SỬA (GET) ĐỂ LOAD DỮ LIỆU LÊN MODAL ----
$edit_item = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_item = $bannerModel->getBannerById($id);
}

// Lấy danh sách đổ ra giao diện
$banners = $bannerModel->getAllBanners();
include __DIR__ . '/../views/quanLyBanner.php';