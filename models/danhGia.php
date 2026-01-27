<?php
class DanhGia
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Lấy thông tin người dùng và sản phẩm từ mã bình luận
    private function getThongTinTuBinhLuan($maBL)
    {
        $stmt = $this->conn->prepare("SELECT IdTaiKhoan, MaHH FROM BinhLuan WHERE MaBL = ?");
        $stmt->bind_param("i", $maBL);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Cập nhật trạng thái (Ẩn/Hiện) cho cả Bình luận và Đánh giá sao
    public function capNhatTrangThaiBinhLuan($maBL, $trangThai)
    {
        // Lấy thông tin người dùng
        $info = $this->getThongTinTuBinhLuan($maBL);
        if (!$info) return false;

        $idTaiKhoan = $info['IdTaiKhoan'];
        $maHH = $info['MaHH'];

        $this->conn->begin_transaction();
        try {
            // Cập nhật bảng Bình luận
            $sqlBL = "UPDATE BinhLuan SET TrangThai = ? WHERE MaBL = ?";
            $stmtBL = $this->conn->prepare($sqlBL);
            $stmtBL->bind_param("si", $trangThai, $maBL);
            $stmtBL->execute();

            // Cập nhật bảng Đánh giá sao (Dựa theo User và Sản phẩm)
            $sqlSao = "UPDATE DanhGiaSao SET TrangThai = ? WHERE IdTaiKhoan = ? AND MaHH = ?";
            $stmtSao = $this->conn->prepare($sqlSao);
            $stmtSao->bind_param("sii", $trangThai, $idTaiKhoan, $maHH);
            $stmtSao->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Xóa Bình luận và Đánh giá sao
    public function xoaBinhLuan($maBL)
    {
        //  Lấy thông tin người dùng
        $info = $this->getThongTinTuBinhLuan($maBL);
        if (!$info) return false;

        $idTaiKhoan = $info['IdTaiKhoan'];
        $maHH = $info['MaHH'];

        $this->conn->begin_transaction();
        try {
            //  Xóa Bình luận
            $sqlBL = "DELETE FROM BinhLuan WHERE MaBL = ?";
            $stmtBL = $this->conn->prepare($sqlBL);
            $stmtBL->bind_param("i", $maBL);
            $stmtBL->execute();

            //  Xóa Đánh giá sao
            $sqlSao = "DELETE FROM DanhGiaSao WHERE IdTaiKhoan = ? AND MaHH = ?";
            $stmtSao = $this->conn->prepare($sqlSao);
            $stmtSao->bind_param("ii", $idTaiKhoan, $maHH);
            $stmtSao->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getDanhGiaSaoTheoSaPham($maHH)
    {
        $sql = "SELECT dg.MaDG, dg.SoSao, dg.NgayDG, tk.TenTK
                FROM DanhGiaSao dg
                JOIN TaiKhoan tk ON dg.IdTaiKhoan = tk.IdTaiKhoan
                WHERE dg.MaHH = ? AND dg.TrangThai = 'Hiển thị' 
                ORDER BY dg.NgayDG DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHH);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBinhLuanTheoSanPham($maHH)
    {
        $sql = "SELECT bl.MaBL, bl.NoiDung, bl.NgayBL, tk.TenTK
                FROM BinhLuan bl
                JOIN TaiKhoan tk ON bl.IdTaiKhoan = tk.IdTaiKhoan
                WHERE bl.MaHH = ? AND bl.TrangThai = 'Hiển thị'
                ORDER BY bl.NgayBL DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHH);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function     getSanPhamCoDanhGia()
    {
        $sql = "SELECT hh.MaHH, hh.TenHH, COUNT(DISTINCT dg.MaDG) AS SoDanhGia, COUNT(DISTINCT bl.MaBL) AS SoBinhLuan
                FROM HangHoa hh
                LEFT JOIN DanhGiaSao dg ON hh.MaHH = dg.MaHH
                LEFT JOIN BinhLuan bl ON hh.MaHH = bl.MaHH
                WHERE dg.MaDG IS NOT NULL OR bl.MaBL IS NOT NULL
                GROUP BY hh.MaHH, hh.TenHH
                ORDER BY hh.MaHH DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getChiTietDanhGia($maHH)
    {
        $sql = "SELECT tk.TenTK, dg.SoSao, bl.MaBL, bl.NoiDung, bl.NgayBL, bl.TrangThai
                FROM TaiKhoan tk
                LEFT JOIN DanhGiaSao dg ON tk.IdTaiKhoan = dg.IdTaiKhoan AND dg.MaHH = ?
                LEFT JOIN BinhLuan bl ON tk.IdTaiKhoan = bl.IdTaiKhoan AND bl.MaHH = ?
                WHERE dg.MaDG IS NOT NULL OR bl.MaBL IS NOT NULL
                ORDER BY bl.NgayBL DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maHH, $maHH);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
