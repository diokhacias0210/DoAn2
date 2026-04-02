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
$sql = "WITH RankedProducts AS (
            SELECT 
                hh.MaHH, 
                hh.TenHH, 
                hh.Gia, 
                hs.TenCuaHang, 
                hs.IdTaiKhoan AS IdNguoiBan,
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) AS HinhAnh,
                ( 6371 * acos( cos( radians($lat) ) * cos( radians( hs.ViDo ) ) 
                * cos( radians( hs.KinhDo ) - radians($lng) ) + sin( radians($lat) ) 
                * sin( radians( hs.ViDo ) ) ) ) AS KhoangCachKm,
                -- Vẫn đếm thứ tự sản phẩm của từng cửa hàng
                ROW_NUMBER() OVER(PARTITION BY hs.IdTaiKhoan ORDER BY hh.MaHH DESC) as rn
            FROM HangHoa hh
            INNER JOIN HoSoNguoiBan hs ON hh.IdNguoiBan = hs.IdTaiKhoan
            INNER JOIN TaiKhoan tk ON tk.IdTaiKhoan = hs.IdTaiKhoan
            WHERE tk.TrangThaiBanHang = 'DangHoatDong' 
              AND hh.TrangThaiDuyet = 'DaDuyet'
              AND hh.HienThi = 1
              AND hs.ViDo IS NOT NULL 
              AND hs.KinhDo IS NOT NULL
        )
        SELECT * FROM RankedProducts 
        WHERE KhoangCachKm <= $banKinh 
        -- ĐÃ XÓA ĐIỀU KIỆN 'rn <= 2' Ở ĐÂY ĐỂ KHÔNG BỊ MẤT DỮ LIỆU
        
        ORDER BY 
            -- Thuật toán linh hoạt: 
            -- Nhóm 0: Lấy tối đa 2 sản phẩm đầu của tất cả các shop (Ưu tiên nhất)
            -- Nhóm 1: Các sản phẩm thứ 3, 4, 5... của các shop (Chỉ được xuất hiện nếu Nhóm 0 không đủ 20 cái)
            CASE WHEN rn <= 2 THEN 0 ELSE 1 END ASC, 
            KhoangCachKm ASC,  -- Cùng nhóm thì shop nào gần hơn xếp trước
            MaHH DESC          -- Cuối cùng là ưu tiên sản phẩm mới đăng
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