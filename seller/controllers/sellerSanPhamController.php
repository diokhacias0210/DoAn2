<?php
// File: seller/controllers/sellerSanPhamController.php

session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/sellerSanPhamModel.php';
require_once __DIR__ . '/../../admin/models/adminDanhMucModel.php'; // Dùng chung model DanhMuc của Admin

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idNguoiBan = $_SESSION['IdTaiKhoan'];
$sanPhamModel = new SellerSanPhamModel($conn);
$danhMucModel = new AdminDanhMucModel($conn);
$message = '';
$error = '';

try {
    // 2. XỬ LÝ POST (THÊM / SỬA / XÓA ẢNH)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        $mahh = (int)($_POST['mahh'] ?? 0);

        // Lấy dữ liệu form
        $ten = trim($_POST['ten'] ?? '');
        $gia = (float)($_POST['gia'] ?? 0);
        $madm = (int)($_POST['madm'] ?? 0);
        $soluong = (int)($_POST['soluong'] ?? 0);
        $mota = $_POST['mota'] ?? '';
        $chatluong = $_POST['chatluong'] ?? 'Mới';
        $tinhtrang = $_POST['tinhtranghang'] ?? 'Còn hàng';
        $giathitruong = 0; // Người dùng bán có thể không cần giá thị trường, hoặc thêm input sau

        // -- ACTION: THÊM MỚI --
        if ($action === 'add') {
            if (empty($ten) || $gia <= 0) throw new Exception("Vui lòng nhập tên và giá hợp lệ.");

            $newId = $sanPhamModel->themSanPham($idNguoiBan, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtrang, $mota);

            if ($newId) {
                $mahh = $newId; // Gán ID mới để lát upload ảnh
                $message = "Đăng bán thành công! Sản phẩm đang chờ duyệt.";
            } else {
                throw new Exception("Lỗi khi thêm sản phẩm vào CSDL.");
            }
        }
        // -- ACTION: CẬP NHẬT --
        elseif ($action === 'update' && $mahh > 0) {
            $result = $sanPhamModel->suaSanPham($idNguoiBan, $mahh, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtrang, $mota);
            if ($result) {
                $message = "Cập nhật thành công! Sản phẩm chuyển sang trạng thái chờ duyệt.";
            } else {
                throw new Exception("Lỗi cập nhật hoặc bạn không có quyền sửa sản phẩm này.");
            }
        }

        // -- XỬ LÝ UPLOAD ẢNH (Logic giống Admin) --
        if ($mahh > 0 && isset($_FILES['image_file'])) {
            // Đường dẫn vật lý để lưu file
            $upload_dir_physical = realpath(__DIR__ . "/../../assets/images/products/uploaded/");
            // Đường dẫn tương đối để lưu vào DB
            $web_path_relative = 'assets/images/products/uploaded/';

            if (!file_exists($upload_dir_physical)) {
                mkdir($upload_dir_physical, 0777, true);
            }

            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ds_anh_db = [];

            foreach ($_FILES['image_file']['tmp_name'] as $key => $tmp_name) {
                $name = $_FILES['image_file']['name'][$key];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed) && $_FILES['image_file']['size'][$key] < 5000000) {
                    $filename = time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmp_name, $upload_dir_physical . DIRECTORY_SEPARATOR . $filename)) {
                        $ds_anh_db[] = $web_path_relative . $filename;
                    }
                }
            }

            if (!empty($ds_anh_db)) {
                $sanPhamModel->themNhieuAnh($mahh, $ds_anh_db);
            }
        }

        // Refresh trang để tránh gửi lại form
        // header("Location: sellerSanPhamController.php?success=1");
        // exit; 
        // (Tạm comment header để debug nếu cần, bạn có thể uncomment)
    }

    // -- ACTION: XÓA SẢN PHẨM (GET) --
    if (isset($_GET['xoa'])) {
        $idXoa = (int)$_GET['xoa'];
        if ($sanPhamModel->xoaSanPham($idNguoiBan, $idXoa)) {
            $message = "Đã xóa sản phẩm.";
        } else {
            $error = "Xóa thất bại (Có thể sản phẩm không phải của bạn).";
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// 3. LẤY DỮ LIỆU ĐỂ HIỂN THỊ RA VIEW
$keyword = $_GET['search'] ?? '';
$danhSachSanPham = $sanPhamModel->getSanPhamCuaToi($idNguoiBan, $keyword);
$danhSachDanhMuc = $danhMucModel->getTatCaDanhMuc(); // Lấy danh mục để hiển thị modal thêm/sửa

// Xử lý lấy dữ liệu khi bấm Sửa
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_item = $sanPhamModel->getSanPhamById($idNguoiBan, (int)$_GET['edit']);
    if ($edit_item) {
        $edit_item['DanhSachAnh'] = $sanPhamModel->getAnhSanPham($edit_item['MaHH']);
    }
}

// Lấy ảnh đại diện cho danh sách
foreach ($danhSachSanPham as &$sp) {
    $sp['DanhSachAnh'] = $sanPhamModel->getAnhSanPham($sp['MaHH']);
}

// 4. GỌI VIEW
include_once __DIR__ . '/../views/sellerQuanLySanPham.php';
