<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../../models/thongBaoModel.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idAdmin = $_SESSION['IdTaiKhoan'];
$thongBaoModel = new ThongBaoModel($conn);
$message = '';
$error = '';

// Xử lý Gửi thông báo (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'send') {
    $tieuDe = trim($_POST['tieuDe']);
    $noiDung = trim($_POST['noiDung']);
    $loaiTB = $_POST['loaiTB'];
    $cheDo = $_POST['cheDoGui']; // 'all', 'group', 'custom'
    $danhSachNhan = isset($_POST['nguoiNhanCustom']) ? $_POST['nguoiNhanCustom'] : [];

    if (empty($tieuDe) || empty($noiDung)) {
        $_SESSION['err'] = "Vui lòng nhập đầy đủ Tiêu đề và Nội dung!";
    } elseif ($cheDo == 'custom' && empty($danhSachNhan)) {
        $_SESSION['err'] = "Vui lòng chọn ít nhất 1 người nhận!";
    } else {
        $result = $thongBaoModel->guiThongBaoNangCao($tieuDe, $noiDung, $loaiTB, $idAdmin, $cheDo, $danhSachNhan);
        if ($result) {
            $_SESSION['msg'] = "Đã gửi thông báo thành công!";
        } else {
            $_SESSION['err'] = "Lỗi hệ thống khi gửi thông báo.";
        }
    }

    header("Location: adminThongBaoController.php");
    exit;
}

// Xử lý Xóa thông báo (GET)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $idXoa = intval($_GET['id']);
    if ($thongBaoModel->xoaThongBao($idXoa)) {
        $message = "Đã xóa thông báo thành công!";
    } else {
        $error = "Lỗi khi xóa thông báo.";
    }
}

// Lấy dữ liệu để hiển thị và Xử lý Lọc
$loaiFilter = $_GET['loai_filter'] ?? '';
$doiTuongFilter = $_GET['doituong_filter'] ?? '';

// Gọi hàm model truyền thêm tham số lọc
$danhSachTB = $thongBaoModel->getTatCaThongBao($loaiFilter, $doiTuongFilter);

// Lấy danh sách user để đổ vào thẻ <select> khi chọn người gửi
$sql_users = "SELECT IdTaiKhoan, TenTK, Email, TrangThaiBanHang FROM TaiKhoan WHERE VaiTro = 0";
$danhSachUser = $conn->query($sql_users)->fetch_all(MYSQLI_ASSOC);

// Gọi View
include_once __DIR__ . '/../views/quanLyThongBao.php';
