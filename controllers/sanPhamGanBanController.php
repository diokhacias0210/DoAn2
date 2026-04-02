<?php
session_start();
require_once __DIR__ . '/../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

// Nhận tọa độ từ URL do nút "Xem tất cả" gửi sang
$lat = isset($_GET['lat']) ? (float)$_GET['lat'] : 0;
$lng = isset($_GET['lng']) ? (float)$_GET['lng'] : 0;
$banKinh = 1000; // Tìm trong bán kính 10km

// Nếu URL không có tọa độ, thử lấy tọa độ lưu trong CSDL của user
if ($lat == 0 || $lng == 0) {
    $id = $_SESSION['IdTaiKhoan'];
    $sqlUser = "SELECT ViDo, KinhDo FROM TaiKhoan WHERE IdTaiKhoan = $id";
    $resUser = $conn->query($sqlUser);
    if ($row = $resUser->fetch_assoc()) {
        $lat = $row['ViDo'] ?? 0;
        $lng = $row['KinhDo'] ?? 0;
    }
}

$danhSachSanPham = [];

// ... code ở trên giữ nguyên ...
if ($lat != 0 && $lng != 0) {
    $idHienTai = $_SESSION['IdTaiKhoan']; // Lấy ID

    $sql = "SELECT 
                hh.MaHH, 
                hh.TenHH, 
                hh.Gia, 
                hs.TenCuaHang, 
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) AS HinhAnh,
                ( 6371 * acos( cos( radians($lat) ) * cos( radians( hs.ViDo ) ) 
                * cos( radians( hs.KinhDo ) - radians($lng) ) + sin( radians($lat) ) 
                * sin( radians( hs.ViDo ) ) ) ) AS KhoangCachKm
            FROM HangHoa hh
            INNER JOIN HoSoNguoiBan hs ON hh.IdNguoiBan = hs.IdTaiKhoan
            INNER JOIN TaiKhoan tk ON tk.IdTaiKhoan = hs.IdTaiKhoan
            WHERE tk.TrangThaiBanHang = 'DangHoatDong' 
              AND hh.TrangThaiDuyet = 'DaDuyet'
              AND hh.HienThi = 1
              AND hs.ViDo IS NOT NULL 
              AND hs.KinhDo IS NOT NULL
              AND hs.IdTaiKhoan != $idHienTai -- ĐÂY LÀ DÒNG MỚI THÊM
            HAVING KhoangCachKm <= $banKinh
            ORDER BY KhoangCachKm ASC, hh.MaHH DESC
            LIMIT 100";


    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['KhoangCachKm'] = round($row['KhoangCachKm'], 1);
            $row['GiaFormat'] = number_format($row['Gia'], 0, ',', '.') . ' đ';
            if (empty($row['HinhAnh'])) {
                $row['HinhAnh'] = 'assets/images/no-image.png';
            }
            $danhSachSanPham[] = $row;
        }
    }
}

// Gọi file View ra để hiển thị
include_once __DIR__ . '/../views/sanPhamGanBan.php';
?>