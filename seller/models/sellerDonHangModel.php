<?php
// File: seller/models/sellerDonHangModel.php

class SellerDonHangModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // 1. Lấy danh sách đơn hàng của Shop
    public function getDonHangCuaToi($idNguoiBan, $keyword = '')
    {
        $sql = "SELECT dh.*, tk.TenTK as NguoiMua, tk.Sdt 
                FROM DonHang dh
                JOIN TaiKhoan tk ON dh.IdTaiKhoan = tk.IdTaiKhoan
                WHERE dh.IdNguoiBan = ?";

        if (!empty($keyword)) {
            $sql .= " AND (dh.MaDH LIKE ? OR tk.TenTK LIKE ?)";
            $sql .= " ORDER BY dh.NgayDat DESC";
            $stmt = $this->conn->prepare($sql);
            $search = "%$keyword%";
            $stmt->bind_param("iss", $idNguoiBan, $search, $search);
        } else {
            $sql .= " ORDER BY dh.NgayDat DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $idNguoiBan);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 2. Lấy chi tiết 1 đơn hàng (Để xem sản phẩm bên trong)
    public function getChiTietDonHang($maDH, $idNguoiBan)
    {
        // Phải check IdNguoiBan để đảm bảo shop này có quyền xem đơn này
        $sql = "SELECT ctdh.*, hh.TenHH, hh.MaHH, 
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh
                JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH
                JOIN DonHang dh ON ctdh.MaDH = dh.MaDH
                WHERE ctdh.MaDH = ? AND dh.IdNguoiBan = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maDH, $idNguoiBan);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 3. Cập nhật trạng thái đơn hàng
    public function capNhatTrangThai($maDH, $idNguoiBan, $trangThaiMoi)
    {
        $sql = "UPDATE DonHang SET TrangThai = ? WHERE MaDH = ? AND IdNguoiBan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $trangThaiMoi, $maDH, $idNguoiBan);
        return $stmt->execute();
    }
}
