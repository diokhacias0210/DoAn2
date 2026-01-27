<?php
class DanhGiaModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    /**
     * Kiểm tra xem người dùng có đơn hàng với mã thanh toán cho sản phẩm này không
     * (CHỈ CẦN CÓ MÃ TT, KHÔNG CẦN KIỂM TRA TRẠNG THÁI)
     * @param int $idTaiKhoan
     * @param int $maHH
     * @return bool
     */
    public function kiemTraCoMaThanhToan($idTaiKhoan, $maHH)
    {
        $sql = "
            SELECT COUNT(*) as SoLuong
            FROM DonHang dh
            INNER JOIN ChiTietDonHang ctdh ON dh.MaDH = ctdh.MaDH
            INNER JOIN ThanhToan tt ON dh.MaDH = tt.MaDH
            WHERE dh.IdTaiKhoan = ? 
            AND ctdh.MaHH = ?
            AND tt.MaTT IS NOT NULL
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idTaiKhoan, $maHH);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['SoLuong'] > 0;
    }

    /**
     * Phương thức cũ - GIỮ LẠI ĐỂ TƯƠNG THÍCH NGƯỢC
     * (Nếu có file khác vẫn đang dùng tên này)
     * @deprecated Sử dụng kiemTraCoMaThanhToan() thay thế
     */
    public function kiemTraDaMuaHang($idTaiKhoan, $maHH)
    {
        return $this->kiemTraCoMaThanhToan($idTaiKhoan, $maHH);
    }

    /**
     * Kiểm tra xem người dùng đã đánh giá sản phẩm chưa
     * @param int $idTaiKhoan
     * @param int $maHH
     * @return bool
     */
    public function kiemTraDaDanhGia($idTaiKhoan, $maHH)
    {
        $sql = "SELECT MaDG FROM DanhGiaSao WHERE IdTaiKhoan = ? AND MaHH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idTaiKhoan, $maHH);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
    public function themDanhGiaVaBinhLuan($idTaiKhoan, $maHH, $soSao, $noiDung)
    {
        // 1. Kiểm tra đã mua (dùng hàm có sẵn)
        if (!$this->kiemTraCoMaThanhToan($idTaiKhoan, $maHH)) {
            return ['success' => false, 'message' => 'Bạn cần mua sản phẩm này và có mã thanh toán trước khi đánh giá.'];
        }

        // 2. Kiểm tra đã đánh giá chưa (dùng hàm có sẵn)
        if ($this->kiemTraDaDanhGia($idTaiKhoan, $maHH)) {
            return ['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi.'];
        }

        // 3. Bắt đầu transaction
        $this->conn->begin_transaction();

        try {
            // Thêm đánh giá (sao)
            $sqlInsertRating = "INSERT INTO DanhGiaSao (IdTaiKhoan, MaHH, SoSao) VALUES (?, ?, ?)";
            $stmtRating = $this->conn->prepare($sqlInsertRating);
            if (!$stmtRating) throw new Exception('Lỗi chuẩn bị câu lệnh thêm đánh giá: ' . $this->conn->error);
            $stmtRating->bind_param("iii", $idTaiKhoan, $maHH, $soSao);
            if (!$stmtRating->execute()) throw new Exception('Lỗi khi lưu đánh giá: ' . $stmtRating->error);

            // Thêm bình luận
            $sqlInsertComment = "INSERT INTO BinhLuan (IdTaiKhoan, MaHH, NoiDung) VALUES (?, ?, ?)";
            $stmtComment = $this->conn->prepare($sqlInsertComment);
            if (!$stmtComment) throw new Exception('Lỗi chuẩn bị câu lệnh thêm bình luận: ' . $this->conn->error);
            $stmtComment->bind_param("iis", $idTaiKhoan, $maHH, $noiDung);
            if (!$stmtComment->execute()) throw new Exception('Lỗi khi lưu bình luận: ' . $stmtComment->error);

            // Hoàn tất
            $this->conn->commit();

            return ['success' => true, 'message' => 'Đánh giá của bạn đã được gửi thành công'];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()];
        }
    }
    /**
     * Lấy đánh giá của người dùng cho sản phẩm
     * @param int $idTaiKhoan
     * @param int $maHH
     * @return array|null
     */
    public function getDanhGiaCuaNguoiDung($idTaiKhoan, $maHH)
    {
        $sql = "
            SELECT dg.SoSao, dg.NgayDG
            FROM DanhGiaSao dg
            WHERE dg.IdTaiKhoan = ? AND dg.MaHH = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idTaiKhoan, $maHH);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Lấy số lượng bình luận của người dùng cho sản phẩm
     * @param int $idTaiKhoan
     * @param int $maHH
     * @return int
     */
    public function getSoLuongBinhLuan($idTaiKhoan, $maHH)
    {
        $sql = "SELECT COUNT(*) as SoLuong FROM BinhLuan WHERE IdTaiKhoan = ? AND MaHH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idTaiKhoan, $maHH);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['SoLuong'];
    }

    /**
     * Lấy thông tin mã thanh toán của người dùng cho sản phẩm
     * @param int $idTaiKhoan
     * @param int $maHH
     * @return array|null
     */
    public function getThongTinThanhToan($idTaiKhoan, $maHH)
    {
        $sql = "
            SELECT tt.MaTT, tt.MaThanhToan, tt.NgayThanhToan, dh.MaDH
            FROM DonHang dh
            INNER JOIN ChiTietDonHang ctdh ON dh.MaDH = ctdh.MaDH
            INNER JOIN ThanhToan tt ON dh.MaDH = tt.MaDH
            WHERE dh.IdTaiKhoan = ? 
            AND ctdh.MaHH = ?
            AND tt.MaTT IS NOT NULL
            ORDER BY tt.NgayThanhToan DESC
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idTaiKhoan, $maHH);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
}
