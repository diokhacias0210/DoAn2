<?php
session_start();

// 1. Kiểm tra đăng nhập (Nếu chưa thì đuổi ra trang Login)
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

// 2. Gọi các file kết nối và Model cần thiết
require_once '../includes/ketnoi.php';
require_once '../models/kichHoatBanHangModel.php';

$idUser = $_SESSION['IdTaiKhoan'];
$model = new kichHoatBanHangModel($conn);

// 3. Kiểm tra xem đã kích hoạt bán hàng chưa
// Nếu kích hoạt rồi thì đuổi thẳng sang trang quản lý của Seller
$sql = "SELECT TrangThaiBanHang FROM TaiKhoan WHERE IdTaiKhoan = $idUser";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if ($row['TrangThaiBanHang'] === 'DangHoatDong') {
        header("Location: ../seller/controllers/sellerThongTinController.php");
        exit;
    }
}

// ==========================================================
// 4. XỬ LÝ LƯU DỮ LIỆU (Khi người dùng bấm nút XÁC NHẬN trong Form)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'kich_hoat') {
        $tenCuaHang = $_POST['TenCuaHang'] ?? '';
        $soCCCD = $_POST['SoCCCD'] ?? '';
        $diaChiKhoHang = $_POST['DiaChiKhoHang'] ?? '';
        $tenNganHang = $_POST['TenNganHang'] ?? '';
        $soTaiKhoan = $_POST['SoTaiKhoanNganHang'] ?? '';
        $tenChuTaiKhoan = $_POST['TenChuTaiKhoan'] ?? '';

        // XỬ LÝ NHẬN TỌA ĐỘ TỪ BẢN ĐỒ
        $viDo = !empty($_POST['ViDo']) ? (float)$_POST['ViDo'] : 'NULL';
        $kinhDo = !empty($_POST['KinhDo']) ? (float)$_POST['KinhDo'] : 'NULL';

        // Gọi model thực hiện lưu thông tin cơ bản
        $model->kichHoat($idUser, $tenCuaHang, $soCCCD, $diaChiKhoHang, $tenNganHang, $soTaiKhoan, $tenChuTaiKhoan);
        
        // Cập nhật tọa độ vào bảng TaiKhoan
        $sqlUpdateToaDo = "UPDATE TaiKhoan SET ViDo = $viDo, KinhDo = $kinhDo WHERE IdTaiKhoan = $idUser";
        $conn->query($sqlUpdateToaDo); 
                
        // Chuyển hướng sang trang Seller sau khi lưu thành công
        header("Location: ../seller/controllers/sellerThongTinController.php");
        exit;
    }
}

// ==========================================================
// 5. HIỂN THỊ GIAO DIỆN (Khi người dùng mới truy cập vào trang)
// ==========================================================
// Code sẽ chạy xuống đây nếu phương thức không phải là POST
require_once '../views/kichHoatBanHang.php';
?>