<?php
require_once __DIR__ . '/../includes/ketnoi.php';

// 1. lấy seller AI
$sql = "SELECT IdTaiKhoan FROM TaiKhoan WHERE Email LIKE 'seller_ai_%'";
$result = $conn->query($sql);

$sellerIds = [];

while ($row = $result->fetch_assoc()) {
    $sellerIds[] = $row['IdTaiKhoan'];
}

if (empty($sellerIds)) {
    die("⚠️ Không có dữ liệu AI để xóa");
}

$idList = implode(",", $sellerIds);

// 2. XÓA ĐÚNG BẢNG THEO CSDL

// ❌ KHÔNG PHẢI SanPham
// ✅ PHẢI là HangHoa
$conn->query("DELETE FROM HangHoa WHERE IdNguoiBan IN ($idList)");

// xóa ảnh sản phẩm
$conn->query("
    DELETE FROM HinhAnh 
    WHERE MaHH NOT IN (SELECT MaHH FROM HangHoa)
");

// xóa hồ sơ người bán
$conn->query("DELETE FROM HoSoNguoiBan WHERE IdTaiKhoan IN ($idList)");

// xóa tài khoản
$conn->query("DELETE FROM TaiKhoan WHERE IdTaiKhoan IN ($idList)");

// reset auto increment
$conn->query("ALTER TABLE TaiKhoan AUTO_INCREMENT = 1");
$conn->query("ALTER TABLE HangHoa AUTO_INCREMENT = 1");

echo "<h2>🧹 Đã xóa sạch dữ liệu AI theo đúng CSDL của bạn</h2>";
?>