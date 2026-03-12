<?php
class AdminNguoiDungModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Đếm tổng số người dùng để chia trang
    public function countTatCaNguoiDung($keyword, $vaitro, $trangthai)
    {
        $sql = "SELECT COUNT(*) as total FROM TaiKhoan WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (TenTK LIKE ? OR Email LIKE ? OR Sdt LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "sss";
        }
        if ($vaitro !== '') {
            $sql .= " AND VaiTro = ?";
            $params[] = $vaitro;
            $types .= "i";
        }
        if (!empty($trangthai)) {
            $sql .= " AND TrangThaiBanHang = ?";
            $params[] = $trangthai;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Lấy danh sách có Lọc và Phân trang
    public function getDanhSachNguoiDung($keyword, $vaitro, $trangthai, $limit, $offset)
    {
        $sql = "SELECT IdTaiKhoan, TenTK, Email, Sdt, VaiTro, TrangThaiBanHang, DiemViPham, ThoiGianTao, Avatar 
                FROM TaiKhoan WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (TenTK LIKE ? OR Email LIKE ? OR Sdt LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "sss";
        }
        if ($vaitro !== '') {
            $sql .= " AND VaiTro = ?";
            $params[] = $vaitro;
            $types .= "i";
        }
        if (!empty($trangthai)) {
            $sql .= " AND TrangThaiBanHang = ?";
            $params[] = $trangthai;
            $types .= "s";
        }

        $sql .= " ORDER BY ThoiGianTao DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Cập nhật trạng thái Khóa / Mở khóa
    public function capNhatTrangThaiKhoa($idTaiKhoan, $trangThaiMoi)
    {
        $sql = "UPDATE TaiKhoan SET TrangThaiBanHang = ? WHERE IdTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $trangThaiMoi, $idTaiKhoan);
        return $stmt->execute();
    }

    // Lấy thông tin chi tiết Tài khoản + Cửa hàng
    public function getChiTietNguoiDung($id)
    {
        $sql = "SELECT tk.*, hs.TenCuaHang, hs.DiaChiKhoHang, hs.SoCCCD, hs.TenNganHang, hs.SoTaiKhoanNganHang, hs.TenChuTaiKhoan
                FROM TaiKhoan tk
                LEFT JOIN HoSoNguoiBan hs ON tk.IdTaiKhoan = hs.IdTaiKhoan
                WHERE tk.IdTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy danh sách sản phẩm CỦA người dùng này (dành cho trang chi tiết)
    public function getSanPhamCuaNguoiDung($idNguoiBan)
    {
        $sql = "SELECT hh.MaHH, hh.TenHH, hh.Gia, hh.SoLuongHH, hh.TrangThaiDuyet, hh.HienThi, dm.TenDM,
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM HangHoa hh
                LEFT JOIN DanhMuc dm ON hh.MaDM = dm.MaDM
                WHERE hh.IdNguoiBan = ? ORDER BY hh.MaHH DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idNguoiBan);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
