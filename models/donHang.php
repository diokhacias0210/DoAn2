<?php
class DonHang
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Lấy tất cả đơn hàng (Admin)
    public function getDonHang()
    {
        $sql = "SELECT dh.MaDH, dh.TongTien, dh.NgayDat, dh.TrangThai, dh.DiaChiGiao, dh.GhiChu,
                       u.TenTK, u.Email, u.Sdt,
                       tt.MaThanhToan, tt.PhuongThuc as PhuongThucTT, tt.TrangThai as TrangThaiTT
                FROM DonHang dh
                JOIN TaiKhoan u ON dh.IdTaiKhoan = u.IdTaiKhoan
                LEFT JOIN ThanhToan tt ON dh.MaDH = tt.MaDH
                ORDER BY dh.NgayDat DESC";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy đơn hàng theo khách hàng
    public function getDonHangTheoKhachHang($idTaiKhoan)
    {
        $sql = "SELECT dh.MaDH, dh.NgayDat, dh.DiaChiGiao, dh.TongTien, dh.TrangThai, 
                       tt.MaThanhToan
                FROM DonHang dh
                LEFT JOIN ThanhToan tt ON dh.MaDH = tt.MaDH
                WHERE dh.IdTaiKhoan = ?
                ORDER BY dh.NgayDat DESC";

        $stmt = $this->conn->prepare($sql);

        // Nếu $stmt là false, nghĩa là câu SQL bị lỗi
        if ($stmt === false) {
            throw new Exception("Lỗi CSDL (prepare failed): " . $this->conn->error);
        }
        // =========================

        $stmt->bind_param("i", $idTaiKhoan);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy chi tiết đơn hàng
    public function getChiTietDonHang($maDH)
    {
        $sql = "SELECT ctdh.SoLuongSanPham, ctdh.DonGia, ctdh.GiamGia,
                       hh.TenHH, hh.MaHH,
                       (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh
                JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH
                WHERE ctdh.MaDH = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLichSuDonHang($maDH)
    {
        $sql = "SELECT NgayThayDoi, TrangThai, GhiChu
                FROM LichSuDonHang
                WHERE MaDH = ?
                ORDER BY NgayThayDoi DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Kiểm tra quyền truy cập
    public function kiemTraQuyenTruyCap($maDH, $idTaiKhoan)
    {
        $sql = "SELECT TrangThai FROM DonHang WHERE MaDH = ? AND IdTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maDH, $idTaiKhoan);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Hủy đơn hàng
    public function huyDonHang($maDH, $idTaiKhoan, $lyDo = "")
    {
        $this->conn->begin_transaction();
        try {
            // Kiểm tra đơn hàng
            $order = $this->kiemTraQuyenTruyCap($maDH, $idTaiKhoan);
            if (!$order) {
                throw new Exception("Đơn hàng không tồn tại hoặc không thuộc về bạn.");
            }
            if ($order['TrangThai'] !== 'Chờ xử lý') {
                throw new Exception("Chỉ có thể hủy đơn hàng đang ở trạng thái 'Chờ xử lý'.");
            }

            // Lấy chi tiết để hoàn trả số lượng
            $stmt = $this->conn->prepare("SELECT MaHH, SoLuongSanPham FROM ChiTietDonHang WHERE MaDH = ?");
            $stmt->bind_param("i", $maDH);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);

            // Cập nhật trạng thái đơn hàng và ghi chú
            $stmt = $this->conn->prepare("UPDATE DonHang SET TrangThai = 'Đã hủy', GhiChu = ? WHERE MaDH = ?");
            $stmt->bind_param("si", $lyDo, $maDH);
            $stmt->execute();

            // Hoàn lại số lượng
            $stmt = $this->conn->prepare("UPDATE HangHoa SET SoLuongHH = SoLuongHH + ? WHERE MaHH = ?");
            foreach ($items as $item) {
                $stmt->bind_param("ii", $item['SoLuongSanPham'], $item['MaHH']);
                $stmt->execute();
            }

            // CẬP NHẬT lịch sử thay vì INSERT
            $stmt = $this->conn->prepare("SELECT MaLichSu FROM LichSuDonHang WHERE MaDH = ? LIMIT 1");
            $stmt->bind_param("i", $maDH);
            $stmt->execute();
            $result = $stmt->get_result();

            $ghiChuLichSu = "Khách huỷ: " . $lyDo;

            if ($result->num_rows > 0) {
                // Đã có -> UPDATE
                $stmt = $this->conn->prepare("UPDATE LichSuDonHang SET TrangThai = 'Đã hủy', GhiChu = ?, NgayThayDoi = NOW() WHERE MaDH = ?");
                $stmt->bind_param("si", $ghiChuLichSu, $maDH);
            } else {
                // Chưa có -> INSERT
                $stmt = $this->conn->prepare("INSERT INTO LichSuDonHang (MaDH, TrangThai, GhiChu) VALUES (?, 'Đã hủy', ?)");
                $stmt->bind_param("is", $maDH, $ghiChuLichSu);
            }
            $stmt->execute();

            $this->conn->commit();
            return ["success" => true, "message" => "Hủy đơn hàng thành công."];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Xóa đơn hàng
    public function xoaDonHang($maDH, $idTaiKhoan)
    {
        $this->conn->begin_transaction();
        try {
            // Kiểm tra
            $order = $this->kiemTraQuyenTruyCap($maDH, $idTaiKhoan);
            if (!$order) {
                throw new Exception("Đơn hàng không tồn tại hoặc bạn không có quyền xóa.");
            }
            if ($order['TrangThai'] !== 'Đã hủy') {
                throw new Exception("Chỉ có thể xóa các đơn hàng đã ở trạng thái 'Đã hủy'.");
            }

            // Xóa
            $stmt = $this->conn->prepare("DELETE FROM DonHang WHERE MaDH = ?");
            $stmt->bind_param("i", $maDH);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Admin duyệt đơn hàng - CẬP NHẬT cả bảng DonHang và LichSuDonHang
    public function duyetDonHang($maDH, $trangThaiMoi = 'Đã xác nhận')
    {
        $this->conn->begin_transaction();
        try {
            // 1. Kiểm tra đơn hàng tồn tại
            $stmt = $this->conn->prepare("SELECT TrangThai FROM DonHang WHERE MaDH = ?");
            $stmt->bind_param("i", $maDH);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Đơn hàng không tồn tại.");
            }

            $trangThaiHienTai = $result->fetch_assoc()['TrangThai'];

            // 2. Kiểm tra trạng thái hợp lệ
            $trangThaiHopLe = [
                'Chờ xử lý' => ['Đã xác nhận', 'Đã hủy'],
                'Đã xác nhận' => ['Đang giao', 'Đã hủy'],
                'Đang giao' => ['Hoàn tất', 'Đã hủy'],
            ];

            if (
                !isset($trangThaiHopLe[$trangThaiHienTai]) ||
                !in_array($trangThaiMoi, $trangThaiHopLe[$trangThaiHienTai])
            ) {
                throw new Exception("Trạng thái mới không hợp lệ từ trạng thái hiện tại.");
            }

            // 3. CẬP NHẬT bảng DonHang
            $stmt = $this->conn->prepare("UPDATE DonHang SET TrangThai = ? WHERE MaDH = ?");
            $stmt->bind_param("si", $trangThaiMoi, $maDH);
            $stmt->execute();

            // 4. CẬP NHẬT hoặc INSERT vào LichSuDonHang
            $stmt = $this->conn->prepare("SELECT MaLichSu FROM LichSuDonHang WHERE MaDH = ? LIMIT 1");
            $stmt->bind_param("i", $maDH);
            $stmt->execute();
            $result = $stmt->get_result();


            if ($result->num_rows > 0) {
                // Đã có -> UPDATE
                $ghiChu = "Cập nhật trạng thái đơn hàng";
                $stmt = $this->conn->prepare("UPDATE LichSuDonHang SET TrangThai = ?, GhiChu = ?, NgayThayDoi = NOW() WHERE MaDH = ?");
                $stmt->bind_param("ssi", $trangThaiMoi, $ghiChu, $maDH);
            } else {
                // Chưa có -> INSERT
                $ghiChu = "Thêm mới trạng thái đơn hàng";
                $stmt = $this->conn->prepare("INSERT INTO LichSuDonHang (MaDH, TrangThai, GhiChu) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $maDH, $trangThaiMoi, $ghiChu);
            }
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Lấy thông tin đơn hàng
    public function getThongTinDonHang($maDH)
    {
        $sql = "SELECT dh.*, tt.MaThanhToan, tt.PhuongThuc, tt.TrangThai as TrangThaiTT,
                       tk.TenTK, tk.Email, tk.Sdt
                FROM DonHang dh
                LEFT JOIN ThanhToan tt ON dh.MaDH = tt.MaDH
                LEFT JOIN TaiKhoan tk ON dh.IdTaiKhoan = tk.IdTaiKhoan
                WHERE dh.MaDH = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    // hàm đếm các đơn hàng đang chờ duỵet
    public function duyetTatCa()
    {
        // lấy đơn đang đang chờ
        $sql = "select MaDH from DonHang where TrangThai = 'chờ xử lý'";
        $result = $this->conn->query($sql);
        $dsDonHang = $result->fetch_all(MYSQLI_ASSOC);

        $dem = 0;

        foreach ($dsDonHang as $dh) {
            try {
                $this->duyetDonHang($dh['MaDH'], 'Đã xác nhận');
                $dem++;
            } catch (Exception $e) {
                continue;
            }
        }
        return $dem;
    }

    public function timKiemDonHang($keyword)
    {
        $sql = "SELECT dh.MaDH, dh.TongTien, dh.NgayDat, dh.TrangThai, dh.DiaChiGiao, 
                   u.TenTK, u.Email, u.Sdt,
                   tt.MaThanhToan, tt.PhuongThuc as PhuongThucTT, tt.TrangThai as TrangThaiTT
            FROM DonHang dh
            JOIN TaiKhoan u ON dh.IdTaiKhoan = u.IdTaiKhoan
            LEFT JOIN ThanhToan tt ON dh.MaDH = tt.MaDH
            WHERE u.TenTK LIKE ? 
 
            ORDER BY dh.NgayDat DESC";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception("Lỗi CSDL (prepare failed): " . $this->conn->error);
        }

        $searchTerm = "%" . $keyword . "%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
