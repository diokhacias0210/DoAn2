<?php
// Bỏ giới hạn thời gian chạy vì 50.000 dòng sẽ mất khoảng 1-2 phút
set_time_limit(0);

require_once __DIR__ . '/../includes/ketnoi.php';

// File CSV đang nằm cùng thư mục database với file PHP này
$csvFile = __DIR__ . '/events.csv';
$limit = 50000;

if (!file_exists($csvFile)) {
    die("Lỗi: Không tìm thấy file tại $csvFile.");
}

$file = fopen($csvFile, 'r');
fgetcsv($file); // Bỏ qua dòng tiêu đề

$count = 0;
$insertedRatings = 0;
$idNguoiBanMacDinh = 2; // Gán cho Shop Cũ Người Mới Ta

echo "<h3>Đang bơm 50.000 dòng dữ liệu vào CSDL... Vui lòng không đóng trình duyệt!</h3>";

$conn->begin_transaction();

try {
    while (($row = fgetcsv($file)) !== FALSE && $count < $limit) {
        $visitorId = (int)$row[1];
        $event = $row[2];
        $itemId = (int)$row[3];

        // 1. Quy đổi sự kiện thành sao
        $soSao = 1;
        if ($event == 'addtocart') $soSao = 3;
        if ($event == 'transaction') $soSao = 5;

        // 2. Tạo User Ảo
        $emailFake = "user_" . $visitorId . "@kaggle.com";
        $sqlUser = "INSERT IGNORE INTO TaiKhoan (IdTaiKhoan, TenTK, Email, Sdt, MatKhau, VaiTro, TrangThaiBanHang) 
                    VALUES ($visitorId, 'Khách $visitorId', '$emailFake', '0000000000', '123456', 0, 'ChuaKichHoat')";
        $conn->query($sqlUser);

        // 3. Tạo Sản Phẩm Ảo
        $tenSPFake = "Sản phẩm Kaggle #" . $itemId;
        $giaFake = rand(50, 500) * 1000;
        $sqlItem = "INSERT IGNORE INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, ChatLuongHang, TinhTrangHang, TrangThaiDuyet) 
                    VALUES ($itemId, $idNguoiBanMacDinh, 8, '$tenSPFake', 10, $giaFake, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet')";
        $conn->query($sqlItem);

        // 4. Lưu Đánh Giá Sao
        $sqlRating = "INSERT INTO DanhGiaSao (IdTaiKhoan, MaHH, SoSao, TrangThai) 
                      VALUES ($visitorId, $itemId, $soSao, 'Hiển thị') 
                      ON DUPLICATE KEY UPDATE SoSao = GREATEST(SoSao, VALUES(SoSao))";
        $conn->query($sqlRating);

        $count++;
        $insertedRatings++;
    }

    $conn->commit();
    echo "<p style='color:green; font-weight:bold;'>Thành công! Đã xử lý $count dòng và đồng bộ $insertedRatings lượt tương tác vào CSDL.</p>";
} catch (Exception $e) {
    $conn->rollback();
    echo "<p style='color:red;'>Lỗi: " . $e->getMessage() . "</p>";
}

fclose($file);
