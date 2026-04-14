<?php
session_start();
require_once '../../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idSeller = $_SESSION['IdTaiKhoan'];

// ==================== THÊM MỚI: SỬ DỤNG MODEL ====================
require_once '../../models/chatModel.php';
$chatModel = new chatModel($conn);

// LẤY DANH SÁCH PHÒNG CHAT KÈM SỐ TIN NHẮN CHƯA ĐỌC (giữ nguyên code cũ của bạn)
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

$maPhong = isset($_GET['MaPhong']) ? (int)$_GET['MaPhong'] : 0;

// Cập nhật trạng thái "Đã đọc"
if ($maPhong > 0) {
    $sqlUpdateDaDoc = "UPDATE TinNhan SET DaXem = 1 WHERE MaPhong = $maPhong AND IdNguoiGui != $idSeller";
    $conn->query($sqlUpdateDaDoc);
    
    // LẤY CHI TIẾT PHÒNG (để hiển thị giống bên người mua)
    $chiTietPhong = $chatModel->layChiTietPhongChat($maPhong, $idSeller);
} else {
    $chiTietPhong = null;
}

// Gọi View
require_once '../views/sellerChat.php';
?>