<?php
class SanPham
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Lấy 12 sản phẩm mới nhất
    public function getSanPham($limit = 12)
    {
        $sql = "SELECT h.MaHH, h.TenHH, h.Gia, h.SoLuongHH, MIN(ha.URL) AS URL,
                   IFNULL(ROUND(AVG(d.SoSao), 1), 0) AS Rating,
                   MAX(mg.GiaTri) AS GiaTri
            FROM HangHoa h
            LEFT JOIN HinhAnh ha ON h.MaHH = ha.MaHH
            LEFT JOIN DanhGiaSao d ON h.MaHH = d.MaHH
            LEFT JOIN MaGiamGiaDanhMuc mgdm ON h.MaDM = mgdm.MaDM
            LEFT JOIN MaGiamGia mg ON mg.MaGG = mgdm.MaGG 
                AND mg.TrangThai = 'Hoạt động'
                AND (mg.NgayKetThuc IS NULL OR mg.NgayKetThuc >= NOW())
            WHERE h.TrangThaiDuyet = 'DaDuyet' AND h.HienThi = 1
            GROUP BY h.MaHH
            ORDER BY h.NgayThem DESC
            LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy sản phẩm đang được giảm giá
    public function getSanPhamGiamGia($limit = 12)
    {
        $sql = "SELECT sp.*, IFNULL(ROUND(AVG(d.SoSao), 1), 0) AS Rating
            FROM (
                SELECT h.MaHH, h.TenHH, h.Gia, h.SoLuongHH, h.NgayThem,
                       MIN(ha.URL) AS URL,
                       MAX(mg.GiaTri) AS GiaTri
                FROM HangHoa h
                LEFT JOIN HinhAnh ha ON h.MaHH = ha.MaHH
                LEFT JOIN MaGiamGiaDanhMuc mgdm ON h.MaDM = mgdm.MaDM
                LEFT JOIN MaGiamGia mg ON mg.MaGG = mgdm.MaGG 
                    AND mg.TrangThai = 'Hoạt động'
                    AND (mg.NgayKetThuc IS NULL OR mg.NgayKetThuc >= NOW())
                WHERE mg.GiaTri IS NOT NULL and h.TrangThaiDuyet = 'DaDuyet' AND h.HienThi = 1
                GROUP BY h.MaHH, h.TenHH, h.Gia, h.SoLuongHH, h.NgayThem
            ) sp
            LEFT JOIN DanhGiaSao d ON sp.MaHH = d.MaHH
            GROUP BY sp.MaHH
            ORDER BY sp.GiaTri DESC, sp.NgayThem DESC
            LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Lỗi SQL: " . $this->conn->error);
        }
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy sản phẩm phân trang với lọc
    public function getSanPhamPhanTrang($offset = 0, $limit = 12, $sapxep = 'new', $iddanhmuc = null, $tukhoa = null)
    {
        $sql = "SELECT h.MaHH, h.TenHH, h.Gia, h.SoLuongHH, h.NgayThem, MIN(ha.URL) AS URL, 
                       IFNULL(ROUND(AVG(d.SoSao), 1), 0) AS Rating,
                       MAX(mg.GiaTri) AS GiaTri
                FROM HangHoa h
                LEFT JOIN HinhAnh ha ON h.MaHH = ha.MaHH
                LEFT JOIN DanhGiaSao d ON h.MaHH = d.MaHH
                LEFT JOIN MaGiamGiaDanhMuc mgdm ON h.MaDM = mgdm.MaDM
                LEFT JOIN MaGiamGia mg ON mg.MaGG = mgdm.MaGG AND mg.TrangThai = 'Hoạt động'";

        $dieukien = [];
        $thongso = [];
        $loai = "";

        // Lọc theo danh mục
        if ($iddanhmuc !== null && $iddanhmuc !== "") {
            $dieukien[] = "h.MaDM = ?";
            $thongso[] = $iddanhmuc;
            $loai .= "i";
        }

        // Tìm kiếm theo từ khóa
        if ($tukhoa !== null && $tukhoa !== "") {
            $dieukien[] = "h.TenHH LIKE ?";
            $thongso[] = "%{$tukhoa}%";
            $loai .= "s";
        }

        if (!empty($dieukien)) {
            $sql .= " WHERE " . implode(" AND ", $dieukien);
        }

        $sql .= " GROUP BY h.MaHH";

        // Sắp xếp
        switch ($sapxep) {
            case 'new':
                $sql .= " ORDER BY h.NgayThem DESC";
                break;
            case 'old':
                $sql .= " ORDER BY h.NgayThem ASC";
                break;
            case 'az':
                $sql .= " ORDER BY h.TenHH ASC";
                break;
            case 'za':
                $sql .= " ORDER BY h.TenHH DESC";
                break;
            case 'giacao':
                $sql .= " ORDER BY h.Gia DESC";
                break;
            case 'giathap':
                $sql .= " ORDER BY h.Gia ASC";
                break;
            default:
                $sql .= " ORDER BY h.NgayThem DESC";
                break;
        }

        $sql .= " LIMIT ?, ?";
        $thongso[] = $offset;
        $thongso[] = $limit;
        $loai .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Lỗi SQL: " . $this->conn->error);
        }

        if (!empty($thongso)) {
            $stmt->bind_param($loai, ...$thongso);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Tìm kiếm sản phẩm
    public function timKiemSanPham($keyword)
    {
        $keyword = trim($keyword);
        if (strlen($keyword) < 2) return [];

        $likeKeyword = '%' . $keyword . '%';
        $sql = "SELECT h.MaHH, h.TenHH, h.Gia, h.SoLuongHH,
                   CONCAT('../', MIN(ha.URL)) AS URL,
                   IFNULL(ROUND(AVG(d.SoSao), 1), 0) AS Rating, 
                   MAX(mg.GiaTri) as GiaTri
            FROM HangHoa h
            LEFT JOIN HinhAnh ha ON h.MaHH = ha.MaHH
            LEFT JOIN DanhGiaSao d ON h.MaHH = d.MaHH
            LEFT JOIN MaGiamGiaDanhMuc mgdm ON h.MaDM = mgdm.MaDM
            LEFT JOIN MaGiamGia mg ON mg.MaGG = mgdm.MaGG 
                AND mg.TrangThai = 'Hoạt động'
                AND (mg.NgayKetThuc IS NULL OR mg.NgayKetThuc >= NOW())
            WHERE h.TenHH LIKE ? and h.TrangThaiDuyet = 'DaDuyet' AND h.HienThi = 1
            GROUP BY h.MaHH
            ORDER BY h.NgayThem DESC
            LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $likeKeyword);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Đếm số sản phẩm
    public function demSanPham($iddanhmuc = null, $tukhoa = null)
    {
        $sql = "SELECT COUNT(*) AS dem FROM HangHoa h";
        $dieukien = [];
        $thongso = [];
        $loai = "";

        if ($iddanhmuc !== null && $iddanhmuc !== "") {
            $dieukien[] = "h.MaDM = ?";
            $thongso[] = $iddanhmuc;
            $loai .= "i";
        }

        if ($tukhoa !== null && $tukhoa !== "") {
            $dieukien[] = "h.TenHH LIKE ?";
            $thongso[] = "%{$tukhoa}%";
            $loai .= "s";
        }

        if (!empty($dieukien)) {
            $sql .= " WHERE " . implode(" AND ", $dieukien) . " AND h.TrangThaiDuyet = 'DaDuyet' AND h.HienThi = 1";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Lỗi SQL: " . $this->conn->error . " --- Query: " . $sql);
        }

        if (!empty($thongso)) {
            $stmt->bind_param($loai, ...$thongso);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['dem'];
        }

        return 0;
    }
    //lấy sản phẩm theo người bán
    public function getSanPhamCuaNguoiBan($idNguoiBan, $offset, $limit, $sapxep, $iddanhmuc = null, $tukhoa = null)
    {
        $sql = "SELECT h.MaHH, h.TenHH, h.Gia, h.SoLuongHH, 
                       (SELECT URL FROM HinhAnh WHERE MaHH = h.MaHH LIMIT 1) AS URL,
                       IFNULL(ROUND(AVG(d.SoSao), 1), 0) AS Rating,
                       MAX(mg.GiaTri) AS GiaTri
                FROM HangHoa h
                LEFT JOIN DanhGiaSao d ON h.MaHH = d.MaHH
                LEFT JOIN MaGiamGiaDanhMuc mgdm ON h.MaDM = mgdm.MaDM
                LEFT JOIN MaGiamGia mg ON mg.MaGG = mgdm.MaGG 
                    AND mg.TrangThai = 'Hoạt động'
                    AND (mg.NgayKetThuc IS NULL OR mg.NgayKetThuc >= NOW())
                WHERE h.IdNguoiBan = ? 
                AND h.TrangThaiDuyet = 'DaDuyet' 
                AND h.HienThi = 1";

        $params = [$idNguoiBan];
        $types = "i";

        // Lọc danh mục
        if ($iddanhmuc) {
            $sql .= " AND h.MaDM = ?";
            $params[] = $iddanhmuc;
            $types .= "i";
        }

        // Tìm kiếm
        if ($tukhoa) {
            $sql .= " AND h.TenHH LIKE ?";
            $params[] = "%" . $tukhoa . "%";
            $types .= "s";
        }

        $sql .= " GROUP BY h.MaHH";

        // Sắp xếp
        switch ($sapxep) {
            case 'old':
                $sql .= " ORDER BY h.NgayThem ASC";
                break;
            case 'az':
                $sql .= " ORDER BY h.TenHH ASC";
                break;
            case 'za':
                $sql .= " ORDER BY h.TenHH DESC";
                break;
            case 'giacao':
                $sql .= " ORDER BY h.Gia DESC";
                break;
            case 'giathap':
                $sql .= " ORDER BY h.Gia ASC";
                break;
            default:
                $sql .= " ORDER BY h.NgayThem DESC";
                break; // Mặc định mới nhất
        }

        $sql .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Đếm tổng sản phẩm của người bán (để tính xem còn nút "Xem thêm" không)
     */
    public function demSanPhamCuaNguoiBan($idNguoiBan, $iddanhmuc = null, $tukhoa = null)
    {
        $sql = "SELECT COUNT(*) as dem FROM HangHoa 
                WHERE IdNguoiBan = ? AND TrangThaiDuyet = 'DaDuyet' AND HienThi = 1";
        $params = [$idNguoiBan];
        $types = "i";

        if ($iddanhmuc) {
            $sql .= " AND MaDM = ?";
            $params[] = $iddanhmuc;
            $types .= "i";
        }
        if ($tukhoa) {
            $sql .= " AND TenHH LIKE ?";
            $params[] = "%" . $tukhoa . "%";
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['dem'];
    }
}
