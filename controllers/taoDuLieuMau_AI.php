<?php
session_start();
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/aiGenerate.php';

// ====================== CẤU HÌNH DỄ CHỈNH ======================
$soNguoiBan = 5;      // Số người bán
$soSanPham  = 20;     // Số sản phẩm
$soDanhGia  = 15;     // Số đánh giá + bình luận
// ============================================================

// Lấy danh mục
$categories = [];
$res = $conn->query("SELECT MaDM FROM DanhMuc");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row['MaDM'];
}
if (empty($categories)) die("❌ Chưa có danh mục trong bảng DanhMuc");

// ====================== PROMPT AI ======================
$prompt = 'Bạn là chuyên gia content cho sàn second-hand TwoHand Việt Nam.

Tạo **ngẫu nhiên một sản phẩm second-hand** chất lượng cao.

Chỉ trả về đúng một object JSON, KHÔNG thêm bất kỳ ký tự nào khác.

{
  "ten": "Tên sản phẩm ngắn gọn, hấp dẫn",
  "gia": number,
  "gia_thi_truong": number,
  "mo_ta": "Mô tả chi tiết 120-200 từ, tiếng Việt tự nhiên, thuyết phục",
  "chat_luong": "Mới" | "Gần như mới" | "Đã qua sử dụng"
}

Yêu cầu: tên hay, giá hợp lý, mô tả chân thực second-hand.';

// ====================== MẢNG ẢNH CỐ ĐỊNH ======================
$imagePool = [
    'https://picsum.photos/id/1015/800/800',
    'https://picsum.photos/id/201/800/800',
    'https://picsum.photos/id/251/800/800',
    'https://picsum.photos/id/180/800/800',
    'https://picsum.photos/id/1005/800/800',
    'https://picsum.photos/id/133/800/800',
    'https://picsum.photos/id/160/800/800',
    'https://picsum.photos/id/1009/800/800',
    'https://picsum.photos/id/29/800/800',
];

// ====================== TẠO NGƯỜI BÁN ======================
$sellerIds = [];
echo "<h2>🔨 Đang tạo $soNguoiBan người bán + $soSanPham sản phẩm...</h2>";

$nganHangList = ['Vietcombank', 'BIDV', 'Techcombank', 'MB Bank', 'TPBank', 'Agribank'];
$diaChiMau = ['Cần Thơ', 'Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Hải Phòng'];

for ($i = 0; $i < $soNguoiBan; $i++) {
    $timestamp = time();
    $email = "seller_ai_{$timestamp}_{$i}@gmail.com";
    $sdt = '09' . rand(10000000, 99999999);
    $pass = password_hash("123456", PASSWORD_DEFAULT);
    $tenTK = "Seller AI " . ($i + 1);

    $stmt = $conn->prepare("INSERT INTO TaiKhoan (TenTK, Email, Sdt, MatKhau, VaiTro, TrangThaiBanHang) 
                            VALUES (?, ?, ?, ?, 0, 'DangHoatDong')");
    $stmt->bind_param("ssss", $tenTK, $email, $sdt, $pass);
    $stmt->execute();
    $sellerId = $conn->insert_id;
    $sellerIds[] = $sellerId;

    $tenCuaHang = "Cửa Hàng AI " . ($i + 1);
    $diaChiKho = $diaChiMau[array_rand($diaChiMau)] . ", Việt Nam";
    $viDo = 10.0 + (rand(0, 1200) / 1000);
    $kinhDo = 105.0 + (rand(0, 1500) / 1000);
    $soCCCD = '0' . rand(100000000, 999999999);
    $nganHang = $nganHangList[array_rand($nganHangList)];
    $soTK = rand(100000000, 999999999) . rand(0, 99);
    $chuTK = strtoupper($tenTK);

    $stmt2 = $conn->prepare("INSERT INTO HoSoNguoiBan 
            (IdTaiKhoan, TenCuaHang, DiaChiKhoHang, ViDo, KinhDo, SoCCCD, 
             TenNganHang, SoTaiKhoanNganHang, TenChuTaiKhoan, NgayDuyet, SoDu) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 0)");
    $stmt2->bind_param("isddsssss", 
        $sellerId, $tenCuaHang, $diaChiKho, $viDo, $kinhDo, 
        $soCCCD, $nganHang, $soTK, $chuTK);
    $stmt2->execute();

    echo "✅ Người bán: <strong>$tenTK</strong> (ID: $sellerId)<br>";
}

// ====================== TẠO SẢN PHẨM ======================
for ($i = 0; $i < $soSanPham; $i++) {
    $json = goiAI($prompt);
    $data = json_decode($json, true);

    if (!$data || !isset($data['ten'])) {
        $data = [
            "ten" => "Sản phẩm Second-hand " . rand(100, 999),
            "gia" => rand(150000, 1200000),
            "gia_thi_truong" => rand(300000, 2000000),
            "mo_ta" => "Sản phẩm second-hand chất lượng cao, còn tốt.",
            "chat_luong" => "Gần như mới"
        ];
    }

    $tenHH = $conn->real_escape_string($data['ten']);
    $gia = intval($data['gia']);
    $giaThiTruong = intval($data['gia_thi_truong'] ?? $gia * 1.6);
    $moTa = $conn->real_escape_string($data['mo_ta'] ?? '');
    $chatLuong = in_array($data['chat_luong'] ?? '', ['Mới','Gần như mới','Đã qua sử dụng']) 
                 ? $data['chat_luong'] : 'Gần như mới';

    $seller = $sellerIds[array_rand($sellerIds)];
    $maDM = $categories[array_rand($categories)];
    $soLuong = rand(1, 8);

    $stmt = $conn->prepare("INSERT INTO HangHoa 
            (IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, 
             TinhTrangHang, TrangThaiDuyet, MoTa, HienThi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Còn hàng', 'DaDuyet', ?, 1)");
    $stmt->bind_param("iisddsss", $seller, $maDM, $tenHH, $soLuong, $gia, $giaThiTruong, $chatLuong, $moTa);
    $stmt->execute();
    $maHH = $conn->insert_id;

    $numImg = rand(2, 3);
    for ($j = 0; $j < $numImg; $j++) {
        $url = $imagePool[array_rand($imagePool)];
        $conn->query("INSERT INTO HinhAnh (MaHH, URL) VALUES ($maHH, '$url')");
    }

    echo "✔️ Sản phẩm: <strong>$tenHH</strong> (ID: $maHH) - " . number_format($gia) . "đ<br>";
}

// ====================== TẠO ĐÁNH GIÁ + BÌNH LUẬN (ĐÃ SỬA) ======================
echo "<h3>📝 Đang tạo $soDanhGia đánh giá & bình luận...</h3>";

$buyers = [6,7,8,9,10,11];

$commentList = [                                      // ← Đổi tên biến để tránh lỗi
    'Sản phẩm rất đẹp, đúng mô tả, giao hàng nhanh!',
    'Chất lượng tốt hơn mong đợi, sẽ ủng hộ shop lần sau.',
    'Hàng second-hand nhưng còn rất mới, đáng tiền.',
    'Shop đóng gói cẩn thận, tư vấn nhiệt tình.',
    'Đúng như hình, dùng rất ổn.'
];

for ($i = 0; $i < $soDanhGia; $i++) {
    $buyer = $buyers[array_rand($buyers)];

    $res = $conn->query("SELECT MaHH FROM HangHoa ORDER BY RAND() LIMIT 1");
    $row = $res->fetch_assoc();
    $maHH = $row['MaHH'];

    $soSao = rand(4, 5);
    $noiDung = $commentList[array_rand($commentList)];   // ← ĐÃ SỬA

    // Đánh giá sao
    $conn->query("INSERT IGNORE INTO DanhGiaSao (IdTaiKhoan, MaHH, SoSao) 
                  VALUES ($buyer, $maHH, $soSao)");

    // Bình luận
    $noiDungSafe = $conn->real_escape_string($noiDung);
    $conn->query("INSERT INTO BinhLuan (IdTaiKhoan, MaHH, NoiDung) 
                  VALUES ($buyer, $maHH, '$noiDungSafe')");
}

echo "<h2 style='color:green'>🎉 HOÀN TẤT! Lỗi đã được sửa.</h2>";
echo "<p>✅ $soNguoiBan người bán (đầy đủ thông tin + tọa độ)<br>";
echo "✅ $soSanPham sản phẩm<br>";
echo "✅ $soDanhGia đánh giá + bình luận</p>";
?>