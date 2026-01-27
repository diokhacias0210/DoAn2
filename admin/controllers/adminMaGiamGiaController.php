<?php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminMaGiamGiaModel.php';
require_once __DIR__ . '/../models/adminDanhMucModel.php';

if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

// KHỞI TẠO MODEL
$maGiamGiaModel = new AdminMaGiamGiaModel($conn);
$danhMucModel = new AdminDanhMucModel($conn);
$message = null;
$error = null;

$maGiamGiaModel->autoUpdateTrangThai();

// XỬ LÝ LOGIC (POST, GET)
try {
    // ---- Xử lý XÓA (GET) ----
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $conn->begin_transaction();
        if ($maGiamGiaModel->xoaMaGiamGia($id)) {
            $conn->commit();
            header("Location: adminMaGiamGiaController.php?status=deleted");
            exit;
        } else {
            throw new Exception("Xóa thất bại.");
        }
    }

    // ---- Xử lý THÊM / SỬA (POST) ----
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['MaGG'] ?? 0);
        $ngaykt = $_POST['NgayKetThuc'] ?: null; // Cho phép NULL

        $data = [
            'Code'       => trim($_POST['Code'] ?? ''),
            'MoTa'       => trim($_POST['MoTa'] ?? ''),
            'GiaTri'     => max(0, min(100, floatval($_POST['GiaTri'] ?? 0))),
            'SoLuong'    => intval($_POST['SoLuong'] ?? 0),
            'LoaiApDung' => $_POST['LoaiApDung'] ?? 'DongLoat',
            'TrangThai'  => $_POST['TrangThai'] ?? 'Hoạt động',
            'NgayBatDau' => $_POST['NgayBatDau'] ?: date('Y-m-d H:i:s'),
            'NgayKetThuc' => $ngaykt
        ];
        $danhMucIds = $_POST['MaDM'] ?? []; // Lấy mảng danh mục

        $conn->begin_transaction();

        if ($id === 0) {
            // Thêm mới
            $maGiamGiaModel->themMaGiamGia($data, $danhMucIds);
        } else {
            // Cập nhật
            $maGiamGiaModel->suaMaGiamGia($id, $data, $danhMucIds);
        }

        $conn->commit();
        header("Location: adminMaGiamGiaController.php?status=success");
        exit;
    }
} catch (Exception $e) {
    if (isset($conn) && $conn->begin_transaction()) $conn->rollback(); //$conn->in_transaction
    $error = 'Đã xảy ra lỗi: ' . $e->getMessage();
}

// XỬ LÝ THÔNG BÁO (TỪ REDIRECT)
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') $message = ' Thao tác thành công!';
    if ($_GET['status'] == 'deleted') $message = ' Xóa thành công!';
    if ($_GET['status'] == 'error') $error = '⚠️ Có lỗi xảy ra!';
}

// XỬ LÝ TÌM KIẾM
$keyword = '';
$danhSachMaGiamGia = [];

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $keyword = trim($_GET['search']);
    $danhSachMaGiamGia = $maGiamGiaModel->timKiemMaGiamGia($keyword);
} else {
    // Lấy tất cả nếu không có tìm kiếm
    $danhSachMaGiamGia = $maGiamGiaModel->getTatCaMaGiamGia();
}

// LẤY DỮ LIỆU CHO VIEW
$danhSachDanhMuc = $danhMucModel->getTatCaDanhMuc();

// ---- Lấy dữ liệu Sửa (GET) ----
$edit_item = null;
$edit_danhmuc_ids = [];
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $edit_item = $maGiamGiaModel->getMaGiamGiaTheoId($id_edit);
    if ($edit_item) {
        $edit_danhmuc_ids = $maGiamGiaModel->getDanhMucCuaMa($id_edit);
    }
}

include_once __DIR__ . '/../views/quanLyMaGiamGia.php';
