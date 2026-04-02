<?php
session_start(); // Đảm bảo đã khởi tạo session
require_once __DIR__ . '/../includes/ketnoi.php';

header('Content-Type: application/json; charset=utf-8');

// KIỂM TRA: Nếu không có session thì không cho phép lấy dữ liệu cửa hàng
if (!isset($_SESSION['IdTaiKhoan'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để sử dụng tính năng này.']);
    exit;
}

// Nhận tọa độ từ JS (Trang Chủ) gửi lên qua phương thức POST
$lat = isset($_POST['lat']) ? (float)$_POST['lat'] : 0;
$lng = isset($_POST['lng']) ? (float)$_POST['lng'] : 0;
$banKinh = 10000; // Giới hạn tìm kiếm: 10 km 

// Nếu không nhận được tọa độ hợp lệ thì báo lỗi luôn, không truy vấn CSDL
if ($lat == 0 || $lng == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Không nhận được tọa độ hợp lệ từ trình duyệt.']);
    exit;
}

// CÂU LỆNH SQL SỬ DỤNG CÔNG THỨC HAVERSINE ĐỂ TÍNH KHOẢNG CÁCH GIỮA TỌA ĐỘ NGƯỜI DÙNG VÀ CÁC CỬA HÀNG TRONG BẢN ĐỒ
// Lấy ID của người đang dùng app
$idHienTai = $_SESSION['IdTaiKhoan'];

$sql = "SELECT 
            tk.IdTaiKhoan, 
            hs.TenCuaHang, 
            hs.DiaChiKhoHang AS DiaChi, 
            hs.ViDo, 
            hs.KinhDo,
           ( 6371 * acos( cos( radians($lat) ) * cos( radians( hs.ViDo ) ) 
           * cos( radians( hs.KinhDo ) - radians($lng) ) + sin( radians($lat) ) 
           * sin( radians( hs.ViDo ) ) ) ) AS KhoangCachKm
        FROM TaiKhoan tk
        INNER JOIN HoSoNguoiBan hs ON tk.IdTaiKhoan = hs.IdTaiKhoan
        WHERE tk.TrangThaiBanHang = 'DangHoatDong'
          AND hs.ViDo IS NOT NULL 
          AND hs.KinhDo IS NOT NULL
          AND tk.IdTaiKhoan != $idHienTai  -- ĐÂY LÀ DÒNG MỚI THÊM: Loại bỏ cửa hàng của chính mình
        HAVING KhoangCachKm <= $banKinh
        ORDER BY KhoangCachKm ASC";

$result = $conn->query($sql);
$danhSachCuaHang = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Làm tròn khoảng cách còn 1 chữ số thập phân (VD: 2.456 km -> 2.5 km)
            $row['KhoangCachKm'] = round($row['KhoangCachKm'], 1);
            $danhSachCuaHang[] = $row;
        }
        // Trả về thành công kèm danh sách
        echo json_encode(['status' => 'success', 'data' => $danhSachCuaHang]);
    } else {
        // Trả về mảng rỗng nếu không có ai trong bán kính
        echo json_encode(['status' => 'success', 'data' => []]);
    }
} else {
    // Trả về lỗi nếu câu lệnh SQL bị sai
    echo json_encode([
        'status' => 'success',
        'data' => $danhSachCuaHang // Mảng này chứa các shop kèm theo biến KhoangCachKm
    ]);
}
?>