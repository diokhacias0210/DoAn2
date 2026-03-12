<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminNguoiDungModel.php';
require_once __DIR__ . '/../../models/thongBaoModel.php'; // Model thông báo

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$nguoiDungModel = new AdminNguoiDungModel($conn);
$thongBaoModel = new ThongBaoModel($conn);
$idAdmin = $_SESSION['IdTaiKhoan'];

//XỬ LÝ KHÓA / MỞ KHÓA TÀI KHOẢN
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $idUser = (int)$_GET['id'];

    // Không cho phép tự khóa chính mình
    if ($idUser == $idAdmin) {
        $_SESSION['err'] = "Bạn không thể tự khóa chính mình!";
    } else {
        if ($action === 'lock') {
            $nguoiDungModel->capNhatTrangThaiKhoa($idUser, 'BiKhoa');
            // Gửi thông báo
            $thongBaoModel->guiThongBaoNangCao("🔒 Tài khoản bị khóa", "Tài khoản bán hàng của bạn đã bị khóa bởi Quản trị viên do vi phạm tiêu chuẩn cộng đồng. Bạn không thể đăng bán sản phẩm mới.", 'ViPham', $idAdmin, 'custom', [$idUser]);
            $_SESSION['msg'] = "Đã KHÓA tài khoản thành công!";
        } elseif ($action === 'unlock') {
            $nguoiDungModel->capNhatTrangThaiKhoa($idUser, 'DangHoatDong');
            $thongBaoModel->guiThongBaoNangCao("✅ Khôi phục tài khoản", "Tài khoản của bạn đã được Admin mở khóa. Bây giờ bạn có thể tiếp tục hoạt động bán hàng.", 'HeThong', $idAdmin, 'custom', [$idUser]);
            $_SESSION['msg'] = "Đã MỞ KHÓA tài khoản thành công!";
        }
    }

    // Trở về trang trước đó
    $referer = $_SERVER['HTTP_REFERER'] ?? 'adminNguoiDungController.php';
    header("Location: $referer");
    exit;
}

// Xử lý hiển thị thông báo Alert
$message = '';
if (isset($_SESSION['msg'])) {
    $message = '<div class="alert alert-success alert-dismissible fade show shadow-sm" style="position:fixed; top:20px; right:20px; z-index:9999;">' . $_SESSION['msg'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['msg']);
}
if (isset($_SESSION['err'])) {
    $message = '<div class="alert alert-danger alert-dismissible fade show shadow-sm" style="position:fixed; top:20px; right:20px; z-index:9999;">' . $_SESSION['err'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['err']);
}

//LẤY DỮ LIỆU LỌC & PHÂN TRANG
$keyword = $_GET['search'] ?? '';
$filter_vaitro = $_GET['vaitro'] ?? '';
$filter_trangthai = $_GET['trangthai'] ?? '';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_records = $nguoiDungModel->countTatCaNguoiDung($keyword, $filter_vaitro, $filter_trangthai);
$total_pages = ceil($total_records / $limit);

$danhSachNguoiDung = $nguoiDungModel->getDanhSachNguoiDung($keyword, $filter_vaitro, $filter_trangthai, $limit, $offset);

include_once __DIR__ . '/../views/quanLyNguoiDung.php';
