<?php
require_once __DIR__ . '/../includes/ketnoi.php';

// Xóa toàn bộ tài khoản AI và dữ liệu liên quan (cascade)
$sqlXoa = "DELETE FROM TaiKhoan WHERE Email LIKE '%seller_ai_%'";

if ($conn->query($sqlXoa)) {
    $soHangXoa = $conn->affected_rows;

    // ✅ Reset AUTO_INCREMENT để lần chạy tiếp theo ID không bị nhảy cóc
    // MySQL tự tính ID tiếp theo dựa trên record lớn nhất còn lại → hoàn toàn an toàn
    $conn->query("ALTER TABLE TaiKhoan AUTO_INCREMENT = 1");

    echo "<h2 style='color:green;'>✔️ Đã xóa sạch $soHangXoa Người bán AI và toàn bộ Sản phẩm của họ!</h2>";
    echo "<p>AUTO_INCREMENT đã được reset. ID lần chạy tiếp theo sẽ tiếp tục từ đúng vị trí.</p>";
    echo "<p><a href='taoDuLieuMau.php'>👉 Chạy lại taoDuLieuMau.php</a></p>";
} else {
    echo "<p style='color:red;'>Lỗi: " . $conn->error . "</p>";
}
?>
