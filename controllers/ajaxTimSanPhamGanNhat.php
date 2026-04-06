<?php
session_start();
require_once __DIR__ . '/../includes/ketnoi.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['IdTaiKhoan'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập.']);
    exit;
}

$lat = isset($_POST['lat']) ? (float)$_POST['lat'] : 0;
$lng = isset($_POST['lng']) ? (float)$_POST['lng'] : 0;
$banKinh = 10000; // Tìm sản phẩm trong bán kính 10km

if ($lat == 0 || $lng == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi tọa độ.']);
    exit;
}

// SQL MỚI: Rút trích tối đa 2 sản phẩm mỗi cửa hàng để đảm bảo đa dạng
// SQL MỚI CẬP NHẬT: Ưu tiên đa dạng shop trước, nếu thiếu sẽ tự động bù sản phẩm khác vào cho đủ 20
// Lấy ID của người đang dùng app
$idHienTai = $_SESSION['IdTaiKhoan'];

$sql = "WITH RankedProducts AS (
            SELECT 
                hh.MaHH, 
                hh.TenHH, 
                hh.Gia, 
                (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) AS HinhAnh,
                hs.TenCuaHang, 
                hs.IdTaiKhoan AS IdNguoiBan,
                ( 6371 * acos( cos( radians($lat) ) * cos( radians( hs.ViDo ) ) 
                * cos( radians( hs.KinhDo ) - radians($lng) ) + sin( radians($lat) ) 
                * sin( radians( hs.ViDo ) ) ) ) AS KhoangCachKm,
                ROW_NUMBER() OVER(PARTITION BY hs.IdTaiKhoan ORDER BY hh.MaHH DESC) as rn
            FROM HangHoa hh
            INNER JOIN HoSoNguoiBan hs ON hh.IdNguoiBan = hs.IdTaiKhoan
            INNER JOIN TaiKhoan tk ON tk.IdTaiKhoan = hs.IdTaiKhoan
            WHERE tk.TrangThaiBanHang = 'DangHoatDong' 
              AND hh.TrangThaiDuyet = 'DaDuyet'
              AND hh.HienThi = 1
              AND hs.ViDo IS NOT NULL 
              AND hs.KinhDo IS NOT NULL
              AND hs.IdTaiKhoan != $idHienTai
        )
        SELECT * FROM RankedProducts 
        WHERE KhoangCachKm <= $banKinh 
        ORDER BY 
            CASE WHEN rn <= 2 THEN 0 ELSE 1 END ASC, 
            KhoangCachKm ASC,  
            MaHH DESC          
        LIMIT 20";  

$result = $conn->query($sql);
$data = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['KhoangCachKm'] = round($row['KhoangCachKm'], 1);
            $row['GiaFormat'] = number_format($row['Gia'], 0, ',', '.') . ' đ';
            
            // Xử lý nếu sản phẩm chưa có hình ảnh nào
            if (empty($row['HinhAnh'])) {
                $row['HinhAnh'] = 'assets/images/no-image.png'; // Đường dẫn ảnh mặc định
            }
            
            $data[] = $row;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    // Nếu câu SQL bị lỗi, trả về lỗi để JS biết
    echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn SQL: ' . $conn->error]);
}
?>