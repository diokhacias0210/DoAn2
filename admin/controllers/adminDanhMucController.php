<?php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminDanhMucModel.php';

if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$danhMucModel = new AdminDanhMucModel($conn);
$message = '';
$error = '';
$keyword = ''; // Biến lưu từ khóa tìm kiếm
// 4. XỬ LÝ LOGIC (POST, GET)
try {

    // ---- Xử lý TÌM KIẾM (GET) ----
    if (isset($_GET['search'])) {
        $keyword = trim($_GET['search']);
    }
    // ---- Xử lý THÊM (POST) ----
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
        $ten = $_POST['ten_danhmuc'];
        if ($danhMucModel->themDanhMuc($ten)) {
            $message = 'Thêm danh mục mới thành công!';
        } else {
            $error = 'Thêm danh mục thất bại. Tên không được để trống.';
        }
    }

    // ---- Xử lý SỬA (POST) ----
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['capnhat_id'];
        $ten = $_POST['capnhat_ten'];
        if ($danhMucModel->suaDanhMuc($id, $ten)) {
            $message = 'Cập nhật danh mục thành công!';
        } else {
            $error = 'Cập nhật thất bại. Tên không được để trống.';
        }
    }

    // ---- Xử lý XÓA (GET) ----
    if (isset($_GET['xoa'])) {
        $idXoa = intval($_GET['xoa']);

        // Bắt đầu transaction để đảm bảo an toàn
        $conn->begin_transaction();

        try {
            //  Lấy ID danh mục "Chưa phân loại"
            $idMacDinh = $danhMucModel->layIdDanhMucMacDinh();

            if (!$idMacDinh) {
                throw new Exception("Không thể tạo danh mục mặc định.");
            }

            //  Kiểm tra xem có đang xóa chính danh mục mặc định không
            if ($idXoa == $idMacDinh) {
                throw new Exception("Không thể xóa danh mục mặc định 'Chưa phân loại'.");
            }

            //  Chuyển sản phẩm  sang danh mục mặc định
            $soLuongChuyen = $danhMucModel->chuyenSanPhamSangDanhMucMoi($idXoa, $idMacDinh);

            //  Xóa danh mục cũ
            if ($danhMucModel->xoaDanhMuc($idXoa)) {
                $conn->commit();
                $message = "Đã xóa danh mục! " . ($soLuongChuyen > 0 ? "Đã chuyển $soLuongChuyen sản phẩm sang 'Chưa phân loại'." : "");
            } else {
                throw new Exception("Lỗi khi xóa danh mục.");
            }
        } catch (Exception $e) {
            $conn->rollback(); // Hoàn tác nếu có lỗi
            $error = $e->getMessage();
        }
    }
} catch (Exception $e) {
    // Bắt lỗi (ví dụ: lỗi SQL trùng tên...)
    $error = 'Đã xảy ra lỗi: ' . $e->getMessage();
}

$danhSachDanhMuc = $danhMucModel->getTatCaDanhMuc($keyword);

include_once __DIR__ . '/../views/quanLyDanhMuc.php';
