<?php
set_time_limit(0);
require_once __DIR__ . '/../includes/ketnoi.php';

$csvFile = __DIR__ . '/events.csv';
$limit = 50000;

if (!file_exists($csvFile)) {
    die("Lỗi: Không tìm thấy file tại $csvFile.");
}

// 1. LẤY TẤT CẢ MÃ HÀNG HÓA (MaHH) THẬT ĐANG CÓ TRÊN WEB
$sqlHangHoa = "SELECT MaHH FROM HangHoa WHERE TrangThaiDuyet = 'DaDuyet'";
$resultHH = $conn->query($sqlHangHoa);
$danhSachSanPhamThat = [];
while ($row = $resultHH->fetch_assoc()) {
    $danhSachSanPhamThat[] = $row['MaHH'];
}

if (count($danhSachSanPhamThat) < 5) {
    die("<b style='color:red;'>Lỗi: Web của bạn cần có ít nhất 5 sản phẩm thật để thực hiện ánh xạ!</b>");
}

$file = fopen($csvFile, 'r');
fgetcsv($file); // Bỏ qua dòng tiêu đề

$count = 0;
$insertedRatings = 0;
$tuDienAnhXa = []; // Từ điển dùng để dịch: ID Kaggle -> ID Thật

echo "<h3>Đang dùng thuật toán Ánh xạ 50.000 dòng Kaggle vào Sản Phẩm Thật...</h3>";

$conn->begin_transaction();

try {
    while (($row = fgetcsv($file)) !== FALSE && $count < $limit) {
        $visitorId = (int)$row[1];
        $event = $row[2];
        $kaggleItemId = (int)$row[3];

        // 2. THUẬT TOÁN ÁNH XẠ (MAPPING)
        // Nếu ID Kaggle này chưa từng xuất hiện, ta chọn ngẫu nhiên 1 món đồ Thật gắn cho nó
        if (!isset($tuDienAnhXa[$kaggleItemId])) {
            $tuDienAnhXa[$kaggleItemId] = $danhSachSanPhamThat[array_rand($danhSachSanPhamThat)];
        }

        // Lấy Mã Hàng Hóa Thật đã được dịch
        $maHHThap = $tuDienAnhXa[$kaggleItemId];

        // 3. Quy đổi sự kiện thành sao
        $soSao = 1; // view
        if ($event == 'addtocart') $soSao = 3;
        if ($event == 'transaction') $soSao = 5;

        // 4. Tạo User Ảo (Vẫn giữ User ảo để AI học)
        $emailFake = "user_" . $visitorId . "@kaggle.com";
        $sqlUser = "INSERT IGNORE INTO TaiKhoan (IdTaiKhoan, TenTK, Email, Sdt, MatKhau, VaiTro, TrangThaiBanHang) 
                    VALUES ($visitorId, 'Khách $visitorId', '$emailFake', '0000000000', '123456', 0, 'ChuaKichHoat')";
        $conn->query($sqlUser);

        // 5. Lưu Hành vi ngầm (Lưu ID Khách ảo + Mã Hàng Thật vào bảng AI)
        $sqlRating = "INSERT INTO HanhVi_AI (IdTaiKhoan, MaHH, Diem) 
                      VALUES ($visitorId, $maHHThap, $soSao) 
                      ON DUPLICATE KEY UPDATE Diem = GREATEST(Diem, VALUES(Diem))";
        $conn->query($sqlRating);

        $count++;
        $insertedRatings++;
    }

    $conn->commit();
    echo "<p style='color:green; font-weight:bold;'>Thành công! Đã xử lý $count dòng CSV.</p>";
    echo "<p>AI bây giờ sẽ học các quy luật cực kỳ phức tạp của Kaggle trực tiếp trên kho hàng thật của bạn mới!!!@@#.</p>";
} catch (Exception $e) {
    $conn->rollback();
    echo "<p style='color:red;'>Lỗi: " . $e->getMessage() . "</p>";
}

fclose($file);
