<?php
class AdminDonHangModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Đếm tổng số đơn hàng để phân trang
    public function countDonHang($keyword, $trangThai, $tuNgay, $denNgay)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM DonHang dh 
                JOIN TaiKhoan mua ON dh.IdTaiKhoan = mua.IdTaiKhoan 
                JOIN TaiKhoan ban ON dh.IdNguoiBan = ban.IdTaiKhoan 
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (dh.MaDH LIKE ? OR mua.TenTK LIKE ? OR ban.TenTK LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "sss";
        }
        if (!empty($trangThai)) {
            $sql .= " AND dh.TrangThai = ?";
            $params[] = $trangThai;
            $types .= "s";
        }
        if (!empty($tuNgay)) {
            $sql .= " AND DATE(dh.NgayDat) >= ?";
            $params[] = $tuNgay;
            $types .= "s";
        }
        if (!empty($denNgay)) {
            $sql .= " AND DATE(dh.NgayDat) <= ?";
            $params[] = $denNgay;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Lấy danh sách có Lọc, Sắp xếp và Phân trang
    public function getDanhSachDonHang($keyword, $trangThai, $tuNgay, $denNgay, $sort, $limit, $offset)
    {
        $sql = "SELECT dh.*, mua.TenTK as NguoiMua, mua.Sdt as SdtMua, ban.TenTK as NguoiBan 
                FROM DonHang dh 
                JOIN TaiKhoan mua ON dh.IdTaiKhoan = mua.IdTaiKhoan 
                JOIN TaiKhoan ban ON dh.IdNguoiBan = ban.IdTaiKhoan 
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (dh.MaDH LIKE ? OR mua.TenTK LIKE ? OR ban.TenTK LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "sss";
        }
        if (!empty($trangThai)) {
            $sql .= " AND dh.TrangThai = ?";
            $params[] = $trangThai;
            $types .= "s";
        }
        if (!empty($tuNgay)) {
            $sql .= " AND DATE(dh.NgayDat) >= ?";
            $params[] = $tuNgay;
            $types .= "s";
        }
        if (!empty($denNgay)) {
            $sql .= " AND DATE(dh.NgayDat) <= ?";
            $params[] = $denNgay;
            $types .= "s";
        }

        // Sắp xếp
        if ($sort == 'giacao') $sql .= " ORDER BY dh.TongTien DESC";
        elseif ($sort == 'giathap') $sql .= " ORDER BY dh.TongTien ASC";
        elseif ($sort == 'cu') $sql .= " ORDER BY dh.NgayDat ASC";
        else $sql .= " ORDER BY dh.NgayDat DESC"; // Mặc định mới nhất

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy thông tin tổng quan của 1 đơn hàng (Thông tin người mua, người bán)
    public function getThongTinDonHang($maDH)
    {
        $sql = "SELECT dh.*, 
                mua.TenTK as NguoiMua, mua.Sdt as SdtMua, mua.Email as EmailMua,
                ban.TenTK as NguoiBan, ban.Sdt as SdtBan, ban.Email as EmailBan, hs.TenCuaHang
                FROM DonHang dh
                JOIN TaiKhoan mua ON dh.IdTaiKhoan = mua.IdTaiKhoan
                JOIN TaiKhoan ban ON dh.IdNguoiBan = ban.IdTaiKhoan
                LEFT JOIN HoSoNguoiBan hs ON ban.IdTaiKhoan = hs.IdTaiKhoan
                WHERE dh.MaDH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDH);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy chi tiết sản phẩm trong đơn hàng
    public function getChiTietDonHang($maDH)
    {
        $sql = "SELECT ctdh.*, hh.TenHH, hh.MaHH, 
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh
                JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH
                WHERE ctdh.MaDH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDH);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
