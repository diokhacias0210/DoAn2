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

    // Lấy danh sách các phòng chat của một người dùng (Cột trái giao diện)
    public function layDanhSachPhongCuaNguoiDung($idNguoiDung)
    {
        // Sử dụng CASE WHEN để xác định tên đối phương đang chat với mình
        $sql = "SELECT p.MaPhong, h.TenHH, 
                       CASE 
                           WHEN p.IdNguoiMua = ? THEN tk_ban.TenTK
                           ELSE tk_mua.TenTK
                       END as TenNguoiChat
                FROM PhongChat p 
                JOIN HangHoa h ON p.MaHH = h.MaHH 
                JOIN TaiKhoan tk_ban ON p.IdNguoiBan = tk_ban.IdTaiKhoan
                JOIN TaiKhoan tk_mua ON p.IdNguoiMua = tk_mua.IdTaiKhoan
                WHERE p.IdNguoiMua = ? OR p.IdNguoiBan = ?";
                
        $stmt = $this->conn->prepare($sql);
        // Bind 3 tham số $idNguoiDung
        $stmt->bind_param("iii", $idNguoiDung, $idNguoiDung, $idNguoiDung);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy toàn bộ tin nhắn của một phòng
    public function layDanhSachTinNhan($maPhong)
    {
        $sql = "SELECT IdNguoiGui, NoiDung, NgayGui FROM TinNhan WHERE MaPhong = ? ORDER BY NgayGui ASC";
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
    // Hàm lưu tin nhắn vào bảng TinNhan
    public function guiTinNhan($maPhong, $idNguoiGui, $noiDung) {
        $sql = "INSERT INTO TinNhan (MaPhong, IdNguoiGui, NoiDung) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $maPhong, $idNguoiGui, $noiDung);
        return $stmt->execute();
    }

    // Lấy thông tin chi tiết của 1 phòng chat (bao gồm Tên sản phẩm và Tên đối phương)
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
}
?>