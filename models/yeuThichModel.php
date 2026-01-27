<?php
class YeuThichModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }


    // Kiểm tra xem sản phẩm đã được yêu thích chưa

    public function kiemTraYeuThich($idUser, $maHH)
    {
        $stmt = $this->conn->prepare("SELECT MaYT FROM YeuThich WHERE IdTaiKhoan = ? AND MaHH = ?");
        $stmt->bind_param("ii", $idUser, $maHH);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }


    // Thêm vào danh sách yêu thích

    public function themYeuThich($idUser, $maHH)
    {
        // Kiểm tra sản phẩm tồn tại trước (để tránh lỗi khóa ngoại nếu CSDL chặt chẽ)
        $stmtCheck = $this->conn->prepare("SELECT MaHH FROM HangHoa WHERE MaHH = ?");
        $stmtCheck->bind_param("i", $maHH);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows === 0) return false;

        $stmt = $this->conn->prepare("INSERT INTO YeuThich (IdTaiKhoan, MaHH) VALUES (?, ?)");
        $stmt->bind_param("ii", $idUser, $maHH);
        return $stmt->execute();
    }


    //  Xóa khỏi danh sách yêu thích

    public function xoaYeuThich($idUser, $maHH)
    {
        $stmt = $this->conn->prepare("DELETE FROM YeuThich WHERE IdTaiKhoan = ? AND MaHH = ?");
        $stmt->bind_param("ii", $idUser, $maHH);
        return $stmt->execute();
    }


    //  Lấy danh sách sản phẩm yêu thích của user

    public function getDanhSachYeuThich($idUser)
    {
        $sql = "
            SELECT h.MaHH, h.TenHH, h.Gia, h.SoLuongHH, 
                   (SELECT URL FROM HinhAnh WHERE MaHH = h.MaHH LIMIT 1) AS AnhDaiDien,
                   MAX(mg.GiaTri) AS GiaTri,
                   IFNULL(ROUND(AVG(dg.SoSao), 1), 0) AS Rating
            FROM YeuThich y
            JOIN HangHoa h ON y.MaHH = h.MaHH
            LEFT JOIN MaGiamGiaDanhMuc mgdm ON h.MaDM = mgdm.MaDM
            LEFT JOIN MaGiamGia mg ON mg.MaGG = mgdm.MaGG 
                AND mg.TrangThai = 'Hoạt động'
                AND (mg.NgayKetThuc IS NULL OR mg.NgayKetThuc >= NOW())
            LEFT JOIN DanhGiaSao dg ON h.MaHH = dg.MaHH
            WHERE y.IdTaiKhoan = ?
            GROUP BY h.MaHH
            ORDER BY max(y.NgayLuu) DESC";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            // In ra lỗi SQL trực tiếp để bạn biết sai ở đâu
            die("Lỗi SQL: " . $this->conn->error);
        }
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
