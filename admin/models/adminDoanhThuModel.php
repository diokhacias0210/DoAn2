<?php
class AdminDoanhThuModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Thống kê tổng quan Sàn
    public function getThongKeTongQuan()
    {
        // Tổng lợi nhuận sàn (Tổng phí sàn của các đơn Hoàn tất)
        $sql1 = "SELECT SUM(PhiSan) as TongLoiNhuan FROM DonHang WHERE TrangThai = 'Hoàn tất'";
        $loiNhuan = $this->conn->query($sql1)->fetch_assoc()['TongLoiNhuan'] ?? 0;

        // Tổng tiền chờ rút
        $sql2 = "SELECT SUM(SoTien) as TienChoRut FROM YeuCauRutTien WHERE TrangThai = 'ChoDuyet'";
        $choRut = $this->conn->query($sql2)->fetch_assoc()['TienChoRut'] ?? 0;

        // Tổng tiền đã thanh toán cho người bán
        $sql3 = "SELECT SUM(SoTien) as DaThanhToan FROM YeuCauRutTien WHERE TrangThai = 'DaChuyen'";
        $daThanhToan = $this->conn->query($sql3)->fetch_assoc()['DaThanhToan'] ?? 0;

        return [
            'TongLoiNhuan' => $loiNhuan,
            'TienChoRut' => $choRut,
            'DaThanhToan' => $daThanhToan
        ];
    }

    // Quản lý Phí Sàn
    public function getPhiSan()
    {
        $sql = "SELECT GiaTri FROM CauHinhHeThong WHERE TenCauHinh = 'PhiSan'";
        $res = $this->conn->query($sql)->fetch_assoc();
        return $res ? floatval($res['GiaTri']) : 5;
    }

    public function updatePhiSan($phanTram)
    {
        $sql = "UPDATE CauHinhHeThong SET GiaTri = ? WHERE TenCauHinh = 'PhiSan'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("d", $phanTram);
        return $stmt->execute();
    }

    // Lấy danh sách Yêu cầu rút tiền
    public function getDanhSachRutTien($trangThai = '')
    {
        $sql = "SELECT yc.*, tk.TenTK, tk.Email, hs.TenCuaHang 
                FROM YeuCauRutTien yc 
                JOIN TaiKhoan tk ON yc.IdTaiKhoan = tk.IdTaiKhoan 
                LEFT JOIN HoSoNguoiBan hs ON tk.IdTaiKhoan = hs.IdTaiKhoan 
                WHERE 1=1 ";
        if (!empty($trangThai)) {
            $sql .= " AND yc.TrangThai = '$trangThai' ";
        }
        $sql .= " ORDER BY yc.NgayYeuCau ASC"; // Ưu tiên xử lý người yêu cầu trước
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    //Lấy chi tiết Doanh thu của từng người bán
    public function getDoanhThuNguoiBan()
    {
        $sql = "SELECT tk.IdTaiKhoan, tk.TenTK, hs.TenCuaHang, hs.SoDu,
                       COUNT(dh.MaDH) as SoDonHoanTat,
                       SUM(dh.TienNguoiBanNhan) as TongDoanhThu,
                       SUM(dh.PhiSan) as PhiSanThuDuoc
                FROM TaiKhoan tk
                JOIN HoSoNguoiBan hs ON tk.IdTaiKhoan = hs.IdTaiKhoan
                LEFT JOIN DonHang dh ON tk.IdTaiKhoan = dh.IdNguoiBan AND dh.TrangThai = 'Hoàn tất'
                WHERE tk.VaiTro = 0 AND tk.TrangThaiBanHang != 'ChuaKichHoat'
                GROUP BY tk.IdTaiKhoan
                ORDER BY TongDoanhThu DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Duyệt rút tiền thành công
    public function duyetRutTien($maYC)
    {
        $sql = "UPDATE YeuCauRutTien SET TrangThai = 'DaChuyen', NgayXuLy = NOW() WHERE MaYC = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maYC);
        return $stmt->execute();
    }

    // Từ chối rút tiền (Hoàn lại tiền vào Số dư)
    public function tuChoiRutTien($maYC, $lyDo)
    {
        $this->conn->begin_transaction();
        try {
            // Lấy thông tin yêu cầu
            $sqlYC = "SELECT IdTaiKhoan, SoTien FROM YeuCauRutTien WHERE MaYC = ? FOR UPDATE";
            $stmtYC = $this->conn->prepare($sqlYC);
            $stmtYC->bind_param("i", $maYC);
            $stmtYC->execute();
            $yc = $stmtYC->get_result()->fetch_assoc();

            if (!$yc) throw new Exception("Không tìm thấy lệnh rút.");

            // Cập nhật trạng thái
            $sqlUpd = "UPDATE YeuCauRutTien SET TrangThai = 'TuChoi', LyDoTuChoi = ?, NgayXuLy = NOW() WHERE MaYC = ?";
            $stmtUpd = $this->conn->prepare($sqlUpd);
            $stmtUpd->bind_param("si", $lyDo, $maYC);
            $stmtUpd->execute();

            // Hoàn tiền vào Số dư
            $sqlHoan = "UPDATE HoSoNguoiBan SET SoDu = SoDu + ? WHERE IdTaiKhoan = ?";
            $stmtHoan = $this->conn->prepare($sqlHoan);
            $stmtHoan->bind_param("di", $yc['SoTien'], $yc['IdTaiKhoan']);
            $stmtHoan->execute();

            // Lấy số dư mới để ghi Log
            $soDuMoi = $this->conn->query("SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan = " . $yc['IdTaiKhoan'])->fetch_assoc()['SoDu'];

            // Ghi log biến động
            $sqlLog = "INSERT INTO BienDongSoDu (IdTaiKhoan, LoaiGiaoDich, SoTien, SoDuSauGiaoDich, NoiDung) VALUES (?, 'HoanTien', ?, ?, 'Hoàn tiền do lệnh rút bị từ chối')";
            $stmtLog = $this->conn->prepare($sqlLog);
            $stmtLog->bind_param("idd", $yc['IdTaiKhoan'], $yc['SoTien'], $soDuMoi);
            $stmtLog->execute();

            $this->conn->commit();
            return ['status' => true, 'IdTaiKhoan' => $yc['IdTaiKhoan'], 'SoTien' => $yc['SoTien']];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => false];
        }
    }

    //Lấy danh sách ID người bán đang hoạt động để gửi thông báo phí sàn
    public function getTatCaIdNguoiBanActive()
    {
        $sql = "SELECT IdTaiKhoan FROM TaiKhoan WHERE VaiTro = 0 AND TrangThaiBanHang != 'ChuaKichHoat'";
        $res = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        $ids = [];
        foreach ($res as $r) $ids[] = $r['IdTaiKhoan'];
        return $ids;
    }
    public function layDanhSachIdChoDuyet()
    {
        $sql = "SELECT DISTINCT IdTaiKhoan FROM YeuCauRutTien WHERE TrangThai = 'ChoDuyet'";
        $res = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        $ids = [];
        foreach ($res as $r) $ids[] = $r['IdTaiKhoan'];
        return $ids;
    }

    // Duyệt tất cả các lệnh đang chờ
    public function duyetTatCaRutTien()
    {
        $sql = "UPDATE YeuCauRutTien SET TrangThai = 'DaChuyen', NgayXuLy = NOW() WHERE TrangThai = 'ChoDuyet'";
        return $this->conn->execute_query($sql); // Dùng cho PHP 8.2+ hoặc $this->conn->query($sql)
    }
}
