<?php
class SellerDonHangModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Đếm tổng số đơn để phân trang
    public function countDonHang($idNguoiBan, $keyword, $trangThai, $tuNgay, $denNgay)
    {
        $sql = "SELECT COUNT(*) as total FROM DonHang dh JOIN TaiKhoan tk ON dh.IdTaiKhoan = tk.IdTaiKhoan WHERE dh.IdNguoiBan = ?";
        $params = [$idNguoiBan];
        $types = "i";

        if (!empty($keyword)) {
            $sql .= " AND (dh.MaDH LIKE ? OR tk.TenTK LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "ss";
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

    // Lấy danh sách đơn hàng có Lọc & Phân trang
    public function getDanhSachDonHang($idNguoiBan, $keyword, $trangThai, $tuNgay, $denNgay, $limit, $offset)
    {
        $sql = "SELECT dh.*, tk.TenTK as NguoiMua, tk.Sdt 
                FROM DonHang dh JOIN TaiKhoan tk ON dh.IdTaiKhoan = tk.IdTaiKhoan 
                WHERE dh.IdNguoiBan = ?";
        $params = [$idNguoiBan];
        $types = "i";

        if (!empty($keyword)) {
            $sql .= " AND (dh.MaDH LIKE ? OR tk.TenTK LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "ss";
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

        $sql .= " ORDER BY dh.NgayDat DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy chi tiết đơn hàng
    public function getChiTietDonHang($maDH, $idNguoiBan)
    {
        $sql = "SELECT ctdh.*, hh.TenHH, hh.MaHH, 
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH JOIN DonHang dh ON ctdh.MaDH = dh.MaDH
                WHERE ctdh.MaDH = ? AND dh.IdNguoiBan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maDH, $idNguoiBan);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy trạng thái hiện tại của 1 đơn hàng
    public function getTrangThaiDonHang($maDH, $idNguoiBan)
    {
        $sql = "SELECT TrangThai FROM DonHang WHERE MaDH = ? AND IdNguoiBan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maDH, $idNguoiBan);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['TrangThai'] : null;
    }

    // Cập nhật trạng thái
    public function capNhatTrangThai($maDH, $idNguoiBan, $trangThaiMoi)
    {
        $sql = "UPDATE DonHang SET TrangThai = ? WHERE MaDH = ? AND IdNguoiBan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $trangThaiMoi, $maDH, $idNguoiBan);
        return $stmt->execute();
    }
    public function getThongTinDonHang($maDH, $idNguoiBan)
    {
        $sql = "SELECT dh.*, tk.TenTK as NguoiMua, tk.Sdt, tk.Email
                FROM DonHang dh
                JOIN TaiKhoan tk ON dh.IdTaiKhoan = tk.IdTaiKhoan
                WHERE dh.MaDH = ? AND dh.IdNguoiBan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maDH, $idNguoiBan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
