<?php
session_start();
require_once '../models/banHangModel.php';
require_once '../includes/ketnoi.php';

// Kiểm tra xem đã đăng nhập chưa
if (!isset($_SESSION['IdTaiKhoan'])) {
    die("Bạn cần đăng nhập để thực hiện chức năng này!");
}

$idUser = $_SESSION['IdTaiKhoan'];
$model = new banHangModel($conn);

// Xử lý POST từ Form gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Nút xác nhận đăng ký
    if (isset($_POST['action']) && $_POST['action'] === 'kich_hoat') {
        $tenCuaHang = $_POST['TenCuaHang'] ?? '';
        $soCCCD = $_POST['SoCCCD'] ?? '';
        $diaChiKhoHang = $_POST['DiaChiKhoHang'] ?? '';
        $tenNganHang = $_POST['TenNganHang'] ?? '';
        $soTaiKhoan = $_POST['SoTaiKhoanNganHang'] ?? '';
        $tenChuTaiKhoan = $_POST['TenChuTaiKhoan'] ?? '';

        // XỬ LÝ NHẬN TỌA ĐỘ TỪ BẢN ĐỒ
        // Ép kiểu về float, nếu rỗng thì set thành chữ 'NULL' để đưa vào lệnh SQL an toàn
        $viDo = !empty($_POST['ViDo']) ? (float)$_POST['ViDo'] : 'NULL';
        $kinhDo = !empty($_POST['KinhDo']) ? (float)$_POST['KinhDo'] : 'NULL';

        // Gọi model thực hiện lưu CSDL (Các thông tin cơ bản)
        $model->kichHoat($idUser, $tenCuaHang, $soCCCD, $diaChiKhoHang, $tenNganHang, $soTaiKhoan, $tenChuTaiKhoan);
        
        // THỰC THI CÂU LỆNH UPDATE ĐỂ BỔ SUNG TỌA ĐỘ VÀO BẢNG TÀI KHOẢN
        $sql = "UPDATE TaiKhoan 
                SET ViDo = $viDo, 
                    KinhDo = $kinhDo 
                WHERE IdTaiKhoan = $idUser";
                
        // Bạn thiếu dòng này nè, phải có dòng này thì CSDL mới cập nhật
        $conn->query($sql); 
                
        // Chuyển hướng lại trang thông tin tài khoản
        header("Location: thongTinTaiKhoanController.php");
        exit;
    }

    // Nút hủy bán hàng
    if (isset($_POST['action']) && $_POST['action'] === 'huy') {
        $model->huy($idUser);
        header("Location: thongTinTaiKhoanController.php");
        exit;
    }
}

// Lấy trạng thái GET (Để hiển thị nếu code này có trả dữ liệu ra View)
$trangThaiBanHang = $model->getTrangThai($idUser);
?>