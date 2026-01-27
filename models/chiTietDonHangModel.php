<?php

class chiTietDonHangModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    //   Lấy thông tin cơ bản của đơn hàng VÀ thông tin người nhận
    public function layThongTinDonHang($maDH, $idUser)
    {
        $sql_order = "
            SELECT dh.MaDH, dh.NgayDat, dh.DiaChiGiao, dh.TongTien, dh.TrangThai, dh.GhiChu,
                   tk.TenTK, tk.Email, tk.Sdt
            FROM DonHang dh
            JOIN TaiKhoan tk ON dh.IdTaiKhoan = tk.IdTaiKhoan
            WHERE dh.MaDH = ? AND dh.IdTaiKhoan = ?
        ";

        $stmt = $this->conn->prepare($sql_order);
        if (!$stmt) throw new Exception("Lỗi prepare SQL: " . $this->conn->error);

        $stmt->bind_param("ii", $maDH, $idUser);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    //  Lấy danh sách sản phẩm trong đơn hàng
    public function layChiTietSanPham($maDH)
    {
        $sql_items = "
            SELECT 
                ct.SoLuongSanPham,
                ct.DonGia,
                h.TenHH,
                h.MaHH,
                (SELECT URL FROM HinhAnh WHERE MaHH = h.MaHH LIMIT 1) as AnhDaiDien
            FROM ChiTietDonHang ct
            JOIN HangHoa h ON ct.MaHH = h.MaHH
            WHERE ct.MaDH = ?
        ";

        $stmt_items = $this->conn->prepare($sql_items);
        if (!$stmt_items) throw new Exception("Lỗi prepare SQL: " . $this->conn->error);

        $stmt_items->bind_param("i", $maDH);
        $stmt_items->execute();

        return $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    //   Lấy thông tin thanh toán của đơn hàng
    public function layThongTinThanhToan($maDH)
    {
        $sql_payment = "SELECT * FROM ThanhToan WHERE MaDH = ? LIMIT 1";

        $stmt_payment = $this->conn->prepare($sql_payment);
        if (!$stmt_payment) throw new Exception("Lỗi prepare SQL: " . $this->conn->error);

        $stmt_payment->bind_param("i", $maDH);
        $stmt_payment->execute();
        return $stmt_payment->get_result()->fetch_assoc();
    }
}
