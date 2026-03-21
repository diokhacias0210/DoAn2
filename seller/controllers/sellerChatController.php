<?php
session_start();
require_once '../../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idSeller = $_SESSION['IdTaiKhoan'];

// LẤY DANH SÁCH PHÒNG CHAT KÈM SỐ TIN NHẮN CHƯA ĐỌC CỦA TỪNG PHÒNG
$sql = "SELECT p.MaPhong, p.MaHH, h.TenHH, t.TenTK AS TenNguoiChat,
               (SELECT COUNT(tn.MaTN) 
                FROM TinNhan tn 
                WHERE tn.MaPhong = p.MaPhong 
                AND tn.IdNguoiGui != $idSeller 
                AND tn.DaXem = 0) AS SoTinNhanMoi
        FROM PhongChat p
        JOIN TaiKhoan t ON p.IdNguoiMua = t.IdTaiKhoan
        JOIN HangHoa h ON p.MaHH = h.MaHH
        WHERE p.IdNguoiBan = $idSeller
        ORDER BY p.MaPhong DESC";

$result = $conn->query($sql);
$danhSachPhong = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $danhSachPhong[] = $row;
    }
}

// Lấy mã phòng đang chọn (nếu có)
$maPhong = isset($_GET['MaPhong']) ? (int)$_GET['MaPhong'] : 0;

// Cập nhật trạng thái "Đã đọc" cho các tin nhắn của khách trong phòng này
if ($maPhong > 0) {
    $sqlUpdateDaDoc = "UPDATE TinNhan
                       SET DaXem = 1 
                       WHERE MaPhong = $maPhong 
                       AND IdNguoiGui != $idSeller";
    $conn->query($sqlUpdateDaDoc);
}

// Gọi View hiển thị
require_once '../views/sellerChat.php';
?>