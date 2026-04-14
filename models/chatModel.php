<?php
class chatModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Lấy ID người bán của một sản phẩm
    public function layIdNguoiBanTuSanPham($maHH)
    {
        $sql = "SELECT IdNguoiBan FROM HangHoa WHERE MaHH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHH);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['IdNguoiBan'];
        }
        return 0;
    }

    // Kiểm tra phòng chat đã tồn tại chưa
    public function kiemTraPhongTonTai($idNguoiMua, $idNguoiBan, $maHH)
    {
        $sql = "SELECT MaPhong FROM PhongChat WHERE IdNguoiMua = ? AND IdNguoiBan = ? AND MaHH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $idNguoiMua, $idNguoiBan, $maHH);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['MaPhong'];
        }
        return 0;
    }

    // Tạo phòng chat mới
    public function taoPhongChat($idNguoiMua, $idNguoiBan, $maHH)
    {
        $sql = "INSERT INTO PhongChat (IdNguoiMua, IdNguoiBan, MaHH) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $idNguoiMua, $idNguoiBan, $maHH);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return 0;
    }

    // Lấy danh sách các phòng chat của một người dùng (ĐÃ BỔ SUNG SỐ TIN NHẮN CHƯA ĐỌC)
    public function layDanhSachPhongCuaNguoiDung($idNguoiDung)
    {
        $sql = "SELECT p.MaPhong, h.TenHH, 
                       CASE 
                           WHEN p.IdNguoiMua = ? THEN tk_ban.TenTK
                           ELSE tk_mua.TenTK
                       END as TenNguoiChat,
                       (SELECT COUNT(tn.MaTN) 
                        FROM TinNhan tn 
                        WHERE tn.MaPhong = p.MaPhong 
                        AND tn.IdNguoiGui != ? 
                        AND tn.DaXem = 0) as SoTinNhanMoi
                FROM PhongChat p 
                JOIN HangHoa h ON p.MaHH = h.MaHH 
                JOIN TaiKhoan tk_ban ON p.IdNguoiBan = tk_ban.IdTaiKhoan
                JOIN TaiKhoan tk_mua ON p.IdNguoiMua = tk_mua.IdTaiKhoan
                WHERE p.IdNguoiMua = ? OR p.IdNguoiBan = ?";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $idNguoiDung, $idNguoiDung, $idNguoiDung, $idNguoiDung);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy toàn bộ tin nhắn của một phòng (ĐÃ BỔ SUNG THÊM CÁC TRƯỜNG CHO SỬA/THU HỒI)
    public function layDanhSachTinNhan($maPhong)
    {
        $sql = "SELECT MaTN, IdNguoiGui, NoiDung, NgayGui, DaChinhSua, TrangThai 
                FROM TinNhan WHERE MaPhong = ? ORDER BY NgayGui ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maPhong);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lưu tin nhắn mới vào CSDL
    public function themTinNhan($maPhong, $idNguoiGui, $noiDung)
    {
        $sql = "INSERT INTO TinNhan (MaPhong, IdNguoiGui, NoiDung) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $maPhong, $idNguoiGui, $noiDung);
        return $stmt->execute();
    }
    public function guiTinNhan($maPhong, $idNguoiGui, $noiDung) {
        $sql = "INSERT INTO TinNhan (MaPhong, IdNguoiGui, NoiDung) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $maPhong, $idNguoiGui, $noiDung);
        return $stmt->execute();
    }

    // Lấy thông tin chi tiết của 1 phòng chat
    public function layChiTietPhongChat($maPhong, $idNguoiDung)
    {
        $sql = "SELECT p.MaPhong, h.TenHH, 
                       CASE 
                           WHEN p.IdNguoiMua = ? THEN tk_ban.TenTK
                           ELSE tk_mua.TenTK
                       END as TenNguoiChat
                FROM PhongChat p 
                JOIN HangHoa h ON p.MaHH = h.MaHH 
                JOIN TaiKhoan tk_ban ON p.IdNguoiBan = tk_ban.IdTaiKhoan
                JOIN TaiKhoan tk_mua ON p.IdNguoiMua = tk_mua.IdTaiKhoan
                WHERE p.MaPhong = ?";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idNguoiDung, $maPhong);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // ==================== THÊM MỚI: SỬA & THU HỒI TIN NHẮN ====================
    public function suaTinNhan($maTN, $noiDungMoi, $idNguoiGui)
    {
        // Lấy nội dung cũ để lưu lịch sử
        $sqlOld = "SELECT NoiDung FROM TinNhan WHERE MaTN = ? AND IdNguoiGui = ?";
        $stmtOld = $this->conn->prepare($sqlOld);
        $stmtOld->bind_param("ii", $maTN, $idNguoiGui);
        $stmtOld->execute();
        $result = $stmtOld->get_result();
        if ($result->num_rows === 0) return false;
        $oldContent = $result->fetch_assoc()['NoiDung'];

        // Lưu lịch sử
        $sqlHistory = "INSERT INTO LichSuTinNhan (MaTN, NoiDungCu, LoaiThayDoi) VALUES (?, ?, 'Sua')";
        $stmtH = $this->conn->prepare($sqlHistory);
        $stmtH->bind_param("is", $maTN, $oldContent);
        $stmtH->execute();

        // Cập nhật tin nhắn
        $sql = "UPDATE TinNhan SET NoiDung = ?, DaChinhSua = 1 WHERE MaTN = ? AND IdNguoiGui = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $noiDungMoi, $maTN, $idNguoiGui);
        return $stmt->execute();
    }

    public function thuHoiTinNhan($maTN, $idNguoiGui)
    {
        // Lấy nội dung cũ để lưu lịch sử
        $sqlOld = "SELECT NoiDung FROM TinNhan WHERE MaTN = ? AND IdNguoiGui = ?";
        $stmtOld = $this->conn->prepare($sqlOld);
        $stmtOld->bind_param("ii", $maTN, $idNguoiGui);
        $stmtOld->execute();
        $result = $stmtOld->get_result();
        if ($result->num_rows === 0) return false;
        $oldContent = $result->fetch_assoc()['NoiDung'];

        // Lưu lịch sử
        $sqlHistory = "INSERT INTO LichSuTinNhan (MaTN, NoiDungCu, LoaiThayDoi) VALUES (?, ?, 'ThuHoi')";
        $stmtH = $this->conn->prepare($sqlHistory);
        $stmtH->bind_param("is", $maTN, $oldContent);
        $stmtH->execute();

        // Thu hồi tin nhắn
        $sql = "UPDATE TinNhan SET NoiDung = '[Tin nhắn đã được thu hồi]', DaChinhSua = 1, TrangThai = 1 
                WHERE MaTN = ? AND IdNguoiGui = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maTN, $idNguoiGui);
        return $stmt->execute();
    }
}
?>