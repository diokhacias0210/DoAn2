<?php
session_start();
require_once '../../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];

// =======================================================
// XỬ LÝ KHI NGƯỜI DÙNG BẤM "LƯU THAY ĐỔI"
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenCuaHang = $_POST['TenCuaHang'] ?? '';
    $soCCCD = $_POST['SoCCCD'] ?? '';
    $diaChiKhoHang = $_POST['DiaChiKhoHang'] ?? '';
    $tenNganHang = $_POST['TenNganHang'] ?? '';
    $soTaiKhoanNganHang = $_POST['SoTaiKhoanNganHang'] ?? '';
    $tenChuTaiKhoan = $_POST['TenChuTaiKhoan'] ?? '';

    $conn->begin_transaction();
    try {
        // 1. Cập nhật thông tin chữ vào bảng HoSoNguoiBan
        $sql_update_shop = "UPDATE HoSoNguoiBan 
                            SET TenCuaHang=?, SoCCCD=?, DiaChiKhoHang=?, TenNganHang=?, SoTaiKhoanNganHang=?, TenChuTaiKhoan=? 
                            WHERE IdTaiKhoan=?";
        $stmt = $conn->prepare($sql_update_shop);
        $stmt->bind_param("ssssssi", $tenCuaHang, $soCCCD, $diaChiKhoHang, $tenNganHang, $soTaiKhoanNganHang, $tenChuTaiKhoan, $idUser);
        $stmt->execute();

        // 2. Xử lý Upload Avatar vào bảng TaiKhoan
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileName = time() . '_' . basename($_FILES['avatar']['name']);
            // Thư mục lưu ảnh (lùi ra 2 cấp để vào root/assets/)
            $uploadDir = __DIR__ . '/../../assets/images/avatars/';

            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $destPath = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $duongDanDB = 'assets/images/avatars/' . $fileName;

                // Cập nhật Avatar
                $sql_avatar = "UPDATE TaiKhoan SET Avatar=? WHERE IdTaiKhoan=?";
                $stmt_avt = $conn->prepare($sql_avatar);
                $stmt_avt->bind_param("si", $duongDanDB, $idUser);
                $stmt_avt->execute();

                // Cập nhật lại session Avatar nếu đang dùng
                $_SESSION['Avatar'] = $duongDanDB;
            }
        }

        $conn->commit();
        $_SESSION['msg'] = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Cập nhật thông tin cửa hàng thành công!</div>";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['msg'] = "<div class='alert alert-danger'><i class='fa-solid fa-triangle-exclamation'></i> Lỗi hệ thống: " . $e->getMessage() . "</div>";
    }

    // Tải lại trang để hiện dữ liệu mới
    header("Location: sellerThongTinController.php");
    exit;
}

// =======================================================
// LẤY DỮ LIỆU HIỂN THỊ RA GIAO DIỆN
// =======================================================
// Kết hợp bảng HoSoNguoiBan và TaiKhoan để lấy Avatar
$sql = "SELECT h.*, t.Avatar 
        FROM HoSoNguoiBan h 
        JOIN TaiKhoan t ON h.IdTaiKhoan = t.IdTaiKhoan 
        WHERE h.IdTaiKhoan = $idUser";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $thongTin = $result->fetch_assoc();
} else {
    $thongTin = null; // Trường hợp lỗi không tìm thấy
}

// Gọi giao diện View ra
require_once '../views/sellerThongTinCuaHang.php';
