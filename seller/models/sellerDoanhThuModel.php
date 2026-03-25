<?php
class SellerDoanhThuModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Lấy số dư hiện tại của người bán
    public function getSoDu($idTaiKhoan)
    {
        // Nếu người bán chưa có hồ sơ thì trả về 0
        $sql = "SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTaiKhoan);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['SoDu'] : 0;
    }

    //  Lấy thống kê doanh thu trong khoảng thời gian (Chỉ tính đơn 'Hoàn tất')
    public function getThongKeTrongKy($idTaiKhoan, $tuNgay, $denNgay)
    {
        $sql = "SELECT SUM(TienNguoiBanNhan) as TongDoanhThu, COUNT(MaDH) as SoDon
                FROM DonHang
                WHERE IdNguoiBan = ? AND TrangThai = 'Hoàn tất' 
                AND DATE(NgayDat) >= ? AND DATE(NgayDat) <= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $idTaiKhoan, $tuNgay, $denNgay);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy Top sản phẩm bán chạy trong kỳ (Chỉ tính đơn 'Hoàn tất')
    public function getTopSanPhamBanChay($idTaiKhoan, $tuNgay, $denNgay)
    {
        $sql = "SELECT hh.MaHH, hh.TenHH, hh.Gia, SUM(ct.SoLuongSanPham) as TongDaBan,
                       (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ct
                JOIN DonHang dh ON ct.MaDH = dh.MaDH
                JOIN HangHoa hh ON ct.MaHH = hh.MaHH
                WHERE dh.IdNguoiBan = ? AND dh.TrangThai = 'Hoàn tất'
                AND DATE(dh.NgayDat) >= ? AND DATE(dh.NgayDat) <= ?
                GROUP BY hh.MaHH
                ORDER BY TongDaBan DESC LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $idTaiKhoan, $tuNgay, $denNgay);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tạo yêu cầu rút tiền (Giao dịch DB an toàn)
    public function taoYeuCauRutTien($idTaiKhoan, $soTien, $nganHang, $stk, $tenChuTk)
    {
        $this->conn->begin_transaction();
        try {
            // Lấy số dư và khóa dòng (tránh rút 2 lần cùng lúc)
            $sqlCheck = "SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan = ? FOR UPDATE";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->bind_param("i", $idTaiKhoan);
            $stmtCheck->execute();
            $soDuHienTai = $stmtCheck->get_result()->fetch_assoc()['SoDu'] ?? 0;

            if ($soDuHienTai < $soTien) {
                throw new Exception("Số dư không đủ.");
            }

            // Trừ tiền
            $sqlTruTien = "UPDATE HoSoNguoiBan SET SoDu = SoDu - ? WHERE IdTaiKhoan = ?";
            $stmtTru = $this->conn->prepare($sqlTruTien);
            $stmtTru->bind_param("di", $soTien, $idTaiKhoan);
            $stmtTru->execute();

            // Tạo yêu cầu
            $sqlYeuCau = "INSERT INTO YeuCauRutTien (IdTaiKhoan, SoTien, NganHang, SoTaiKhoan, TenChuTaiKhoan) VALUES (?, ?, ?, ?, ?)";
            $stmtYeuCau = $this->conn->prepare($sqlYeuCau);
            $stmtYeuCau->bind_param("idsss", $idTaiKhoan, $soTien, $nganHang, $stk, $tenChuTk);
            $stmtYeuCau->execute();

            // Ghi log biến động
            $soDuMoi = $soDuHienTai - $soTien;
            $sqlLog = "INSERT INTO BienDongSoDu (IdTaiKhoan, LoaiGiaoDich, SoTien, SoDuSauGiaoDich, NoiDung) VALUES (?, 'RutTien', ?, ?, 'Yêu cầu rút tiền về ngân hàng')";
            $stmtLog = $this->conn->prepare($sqlLog);
            $stmtLog->bind_param("idd", $idTaiKhoan, $soTien, $soDuMoi);
            $stmtLog->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Lịch sử rút tiền
    public function getLichSuRutTien($idTaiKhoan)
    {
        $sql = "SELECT * FROM YeuCauRutTien WHERE IdTaiKhoan = ? ORDER BY NgayYeuCau DESC LIMIT 20";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
