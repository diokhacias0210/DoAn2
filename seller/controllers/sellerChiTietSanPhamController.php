<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/sellerSanPhamModel.php';

if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idNguoiBan = $_SESSION['IdTaiKhoan'];
$maHH = isset($_GET['MaHH']) ? (int)$_GET['MaHH'] : 0;

if ($maHH <= 0) {
    $_SESSION['err'] = "Mã sản phẩm không hợp lệ!";
    header("Location: sellerSanPhamController.php");
    exit;
}

$sanPhamModel = new SellerSanPhamModel($conn);

// LẤY CHI TIẾT SẢN PHẨM + TÊN DANH MỤC
$sql = "SELECT hh.*, dm.TenDM 
        FROM HangHoa hh 
        LEFT JOIN DanhMuc dm ON hh.MaDM = dm.MaDM 
        WHERE hh.MaHH = ? AND hh.IdNguoiBan = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $maHH, $idNguoiBan);
$stmt->execute();
$sanPham = $stmt->get_result()->fetch_assoc();

if (!$sanPham) {
    $_SESSION['err'] = "Bạn không có quyền xem sản phẩm này hoặc sản phẩm không tồn tại!";
    header("Location: sellerSanPhamController.php");
    exit;
}

// Lấy danh sách ảnh
$dsAnh = $sanPhamModel->getAnhSanPham($maHH);

// Lấy đánh giá & bình luận (giữ nguyên như trước)
$sqlDanhGia = "SELECT dg.SoSao, dg.NgayDG, tk.TenTK, tk.Avatar 
               FROM DanhGiaSao dg 
               JOIN TaiKhoan tk ON dg.IdTaiKhoan = tk.IdTaiKhoan 
               WHERE dg.MaHH = ? AND dg.TrangThai = 'Hiển thị' 
               ORDER BY dg.NgayDG DESC";
$stmt = $conn->prepare($sqlDanhGia);
$stmt->bind_param("i", $maHH);
$stmt->execute();
$danhGiaList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$trungBinhSao = 0;
if (count($danhGiaList) > 0) {
    $total = array_sum(array_column($danhGiaList, 'SoSao'));
    $trungBinhSao = round($total / count($danhGiaList), 1);
}

$sqlBinhLuan = "SELECT bl.NoiDung, bl.NgayBL, tk.TenTK, tk.Avatar 
                FROM BinhLuan bl 
                JOIN TaiKhoan tk ON bl.IdTaiKhoan = tk.IdTaiKhoan 
                WHERE bl.MaHH = ? AND bl.TrangThai = 'Hiển thị' 
                ORDER BY bl.NgayBL DESC";
$stmt = $conn->prepare($sqlBinhLuan);
$stmt->bind_param("i", $maHH);
$stmt->execute();
$binhLuanList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include_once __DIR__ . '/../views/sellerChiTietSanPham.php';
?>