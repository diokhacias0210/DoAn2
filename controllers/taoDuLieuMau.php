<?php
session_start();
require_once __DIR__ . '/../includes/ketnoi.php';

// ✅ File này nằm ở thư mục gốc C:\DoAn2\
// Đường dẫn vật lý để PHP đọc file ảnh
$thuMucAnhVatLy = __DIR__ . '/../assets/images/mock/';

// ✅ Đường dẫn web để lưu vào CSDL và hiển thị lên thẻ <img src="...">
// Phù hợp với cấu trúc: localhost/DoAn2/assets/images/mock/
$duongDanWeb = '/../assets/images/mock/';

// Quét toàn bộ ảnh trong thư mục
$danhSachAnhMau = glob($thuMucAnhVatLy . "*.{jpg,jpeg,png,webp}", GLOB_BRACE);

if (empty($danhSachAnhMau)) {
    die("<b style='color:red;'>Lỗi: Thư mục assets/images/mock/ đang trống. Hãy chạy taiAnhMau.php trước!</b>");
}

echo "<h2>Đang tiến hành tạo 10 Người Bán mới tại Kiên Giang và 60 Sản phẩm...</h2>";

// 1. KIỂM TRA DANH MỤC CÓ SẴN
$sqlCats = "SELECT MaDM FROM DanhMuc";
$resCats = $conn->query($sqlCats);
$categories = [];
while ($row = $resCats->fetch_assoc()) {
    $categories[] = $row['MaDM'];
}
if (empty($categories)) {
    die("<b style='color:red;'>Lỗi: Vui lòng tạo ít nhất 1 Danh mục trong bảng DanhMuc trước khi chạy!</b>");
}

// 2. KHỞI TẠO 10 NGƯỜI BÁN MỚI Ở KIÊN GIANG
$storeNames = [
    "Shop Đồ Cũ Kiên Giang", "Tiệm Đồ Xịn Rạch Giá", "Kho Hàng Rẻ Hà Tiên",
    "Thanh Lý Giá Tốt", "Secondhand Chợ Đêm", "Đồ Cũ Sinh Viên KG",
    "Cửa Hàng 2Hand", "Chuyên Đồ Cũ Phú Quốc", "Shop Thanh Lý Mới", "Góc Đồ Cũ Kiên Lương"
];

$baseLat = 9.9590;
$baseLng = 105.0560;

$newSellerIds = [];

for ($i = 0; $i < 10; $i++) {
    // ✅ Dùng microtime() thay time() để tránh email trùng khi chạy nhanh
    $email    = "seller_ai_" . str_replace('.', '', microtime(true)) . "_$i@gmail.com";
    $sdt      = "09" . rand(10000000, 99999999);
    $tenTK    = "Người bán AI " . ($i + 1);
    $matKhau  = password_hash("123456", PASSWORD_DEFAULT);

    $sqlTK = "INSERT INTO TaiKhoan (TenTK, Email, Sdt, MatKhau, TrangThaiBanHang) 
              VALUES ('$tenTK', '$email', '$sdt', '$matKhau', 'DangHoatDong')";

    if ($conn->query($sqlTK)) {
        $idTaiKhoan    = $conn->insert_id;
        $newSellerIds[] = $idTaiKhoan;

        $tenCuaHang = $storeNames[$i];
        $lat = $baseLat + (rand(-60, 60) / 1000);
        $lng = $baseLng + (rand(-60, 60) / 1000);

        $sqlHS = "INSERT INTO HoSoNguoiBan (IdTaiKhoan, TenCuaHang, DiaChiKhoHang, ViDo, KinhDo) 
                  VALUES ($idTaiKhoan, '$tenCuaHang', 'Khu vực Kiên Giang', $lat, $lng)";
        $conn->query($sqlHS);
    } else {
        echo "<p style='color:red;'>⚠️ Không tạo được seller $i: " . $conn->error . "</p>";
    }
}

echo "<p>✔️ Đã tạo thành công " . count($newSellerIds) . " Cửa hàng mới tại Kiên Giang.</p>";

// 3. KHO DỮ LIỆU 60 SẢN PHẨM
$khoDuLieuAI = [
    // Đồ công nghệ
    ["iPhone 11 64GB cũ pin 82%", 4500000, 6000000, "Đã qua sử dụng"],
    ["Macbook Pro 2015 15 inch", 7000000, 9500000, "Đã qua sử dụng"],
    ["Tai nghe AirPods 2 xước case", 1200000, 2500000, "Đã qua sử dụng"],
    ["Chuột Logitech G102 như mới", 250000, 450000, "Gần như mới"],
    ["Bàn phím cơ Akko 3087", 600000, 1100000, "Gần như mới"],
    ["Màn hình Dell Ultrasharp 24 inch", 2500000, 4000000, "Đã qua sử dụng"],
    ["Máy tính bảng iPad Air 2", 2100000, 3500000, "Đã qua sử dụng"],
    ["Đồng hồ Apple Watch Series 4", 1800000, 3000000, "Đã qua sử dụng"],
    ["Sạc dự phòng Xiaomi 10000mAh", 150000, 350000, "Gần như mới"],
    ["Ốp lưng iPhone 13 Pro Max xịn", 50000, 200000, "Mới"],
    // Thời trang nam
    ["Áo khoác da thật nam Vintage", 850000, 2000000, "Đã qua sử dụng"],
    ["Giày Nike Air Force 1 size 42", 900000, 2500000, "Gần như mới"],
    ["Áo sơ mi nam Owen caro", 150000, 450000, "Đã qua sử dụng"],
    ["Quần Jean nam Levis 501", 350000, 1200000, "Đã qua sử dụng"],
    ["Đồng hồ cơ nam Casio", 600000, 1500000, "Gần như mới"],
    ["Balo nam The North Face", 300000, 800000, "Đã qua sử dụng"],
    ["Thắt lưng da bò thật", 120000, 300000, "Gần như mới"],
    ["Giày tây nam công sở", 450000, 1100000, "Đã qua sử dụng"],
    ["Áo thun MLB auth form rộng", 250000, 900000, "Gần như mới"],
    ["Kính mát nam Rayban", 700000, 2200000, "Đã qua sử dụng"],
    // Thời trang nữ
    ["Túi xách Charles & Keith", 450000, 1500000, "Gần như mới"],
    ["Váy thiết kế dự tiệc cao cấp", 350000, 1200000, "Đã qua sử dụng"],
    ["Giày cao gót Zara size 37", 250000, 800000, "Đã qua sử dụng"],
    ["Áo blazer nữ form Hàn Quốc", 200000, 600000, "Gần như mới"],
    ["Son MAC Ruby Woo pass nhanh", 150000, 450000, "Mới"],
    ["Áo len nữ Cardigan", 120000, 350000, "Đã qua sử dụng"],
    ["Chân váy xếp ly dài", 90000, 250000, "Gần như mới"],
    ["Mũ bucket nữ viền lông", 50000, 150000, "Đã qua sử dụng"],
    ["Đồng hồ nữ Daniel Wellington", 550000, 1800000, "Đã qua sử dụng"],
    ["Dây chuyền bạc 925", 150000, 400000, "Gần như mới"],
    // Gia dụng & Nội thất
    ["Lò vi sóng Sharp 20L cũ", 600000, 1500000, "Đã qua sử dụng"],
    ["Nồi chiên không dầu Philips", 900000, 2200000, "Gần như mới"],
    ["Ghế xoay văn phòng Hòa Phát", 350000, 850000, "Đã qua sử dụng"],
    ["Bàn học gấp gọn gỗ ép", 100000, 250000, "Gần như mới"],
    ["Máy xay sinh tố Sunhouse", 150000, 400000, "Đã qua sử dụng"],
    ["Đèn bàn học rạng đông", 80000, 200000, "Gần như mới"],
    ["Bếp từ đơn Kangaroo", 250000, 600000, "Đã qua sử dụng"],
    ["Bộ nồi Inox 3 đáy", 300000, 800000, "Gần như mới"],
    ["Tủ quần áo vải sinh viên", 120000, 300000, "Đã qua sử dụng"],
    ["Kệ sách gỗ 3 tầng", 150000, 350000, "Đã qua sử dụng"],
    // Phương tiện & Thể thao
    ["Xe đạp địa hình Asama cũ", 1200000, 3000000, "Đã qua sử dụng"],
    ["Mũ bảo hiểm 3/4 Royal", 150000, 450000, "Gần như mới"],
    ["Vợt cầu lông Yonex xước nhẹ", 400000, 1100000, "Đã qua sử dụng"],
    ["Thảm tập Yoga TPE", 80000, 250000, "Gần như mới"],
    ["Giày đá bóng Mizuno fake 1", 200000, 600000, "Đã qua sử dụng"],
    ["Quả bóng rổ Spalding", 150000, 400000, "Đã qua sử dụng"],
    ["Xe máy Honda Wave Alpha 2018", 9500000, 18000000, "Đã qua sử dụng"],
    ["Găng tay tập gym", 50000, 150000, "Mới"],
    ["Ván trượt Skateboard", 250000, 700000, "Đã qua sử dụng"],
    ["Dây nhảy thể dục xịn", 30000, 100000, "Mới"],
    // Sở thích, Sách
    ["Sách Đắc Nhân Tâm bản cũ", 30000, 85000, "Đã qua sử dụng"],
    ["Bộ truyện Conan 50 cuốn", 500000, 1000000, "Đã qua sử dụng"],
    ["Đàn Guitar Acoustic gỗ hồng đào", 700000, 1500000, "Gần như mới"],
    ["Máy ảnh Film Canon AE-1", 2500000, 4000000, "Đã qua sử dụng"],
    ["Figure One Piece Luffy real", 350000, 900000, "Gần như mới"],
    ["Bàn cờ vua gỗ tự nhiên", 150000, 400000, "Gần như mới"],
    ["Sách Lập trình PHP căn bản", 40000, 120000, "Đã qua sử dụng"],
    ["Rubik 3x3 Gan xịn", 120000, 350000, "Gần như mới"],
    ["Đĩa than Vinyl nhạc Trịnh", 450000, 1000000, "Đã qua sử dụng"],
    ["Tranh sơn dầu phong cảnh", 600000, 1500000, "Gần như mới"]
];

// ✅ KHÔNG shuffle - giữ nguyên thứ tự để ảnh khớp với sản phẩm
$count    = 0;
$itemIndex = 0;

// 4. CHIA ĐỀU MỖI NGƯỜI BÁN 6 SẢN PHẨM
foreach ($newSellerIds as $idNguoiBan) {
    for ($j = 0; $j < 6; $j++) {
        if (!isset($khoDuLieuAI[$itemIndex])) break;

        $item      = $khoDuLieuAI[$itemIndex];
        $maDM      = $categories[array_rand($categories)];
        $tenHH     = $conn->real_escape_string($item[0]);
        $gia       = $item[1];
        $giaTT     = $item[2];
        $chatLuong = $item[3];
        $soLuong   = rand(1, 3);
        $moTa      = $conn->real_escape_string("Đây là sản phẩm '$item[0]' sinh ra để test giao diện TwoHand.");

        $sqlHangHoa = "INSERT INTO HangHoa (IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa, HienThi) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'Còn hàng', 'DaDuyet', ?, 1)";
        $stmt = $conn->prepare($sqlHangHoa);
        $stmt->bind_param("iisiiiss", $idNguoiBan, $maDM, $tenHH, $soLuong, $gia, $giaTT, $chatLuong, $moTa);

        if ($stmt->execute()) {
            $maHH_moi_tao = $conn->insert_id;

            // ✅ Tìm đúng ảnh theo số thứ tự itemIndex (VD: 0_iphone.jpg, 1_macbook.jpg)
            $anhChinhXac = glob($thuMucAnhVatLy . $itemIndex . "_*.{jpg,jpeg,png,webp}", GLOB_BRACE);

            if (!empty($anhChinhXac)) {
                $tenFileAnh = basename($anhChinhXac[0]);
            } else {
                // Dự phòng: lấy ảnh bất kỳ nếu không tìm thấy đúng số
                $tenFileAnh = basename($danhSachAnhMau[array_rand($danhSachAnhMau)]);
            }

            // ✅ URL lưu vào DB: assets/images/mock/0_iphone.jpg
            // Khi hiển thị từ views/ thì thêm ../ phía trước, từ thư mục gốc thì dùng thẳng
            $urlAnh = $duongDanWeb . $tenFileAnh;

            $sqlHinhAnh = "INSERT INTO HinhAnh (MaHH, URL) VALUES (?, ?)";
            $stmtAnh    = $conn->prepare($sqlHinhAnh);
            $stmtAnh->bind_param("is", $maHH_moi_tao, $urlAnh);
            $stmtAnh->execute();

            $count++;
        } else {
            echo "<p style='color:red;'>⚠️ Lỗi insert sản phẩm $itemIndex: " . $stmt->error . "</p>";
        }

        $itemIndex++; // ✅ Luôn tăng dù thành công hay thất bại để ảnh không bị lệch
    }
}

echo "<h3 style='color:green;'>✔️ Đã rải thành công $count sản phẩm cho " . count($newSellerIds) . " cửa hàng tại Kiên Giang!</h3>";
echo "<a href='trangChuController.php'>Về trang chủ xem thử ngay →</a>";
?>
