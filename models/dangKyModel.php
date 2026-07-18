<?php
class DangKyModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function emailDaTonTai($email)
    {
        $stmt = $this->conn->prepare("SELECT IdTaiKhoan FROM TaiKhoan WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Thêm ViDo, KinhDo và DiaChi vào hàm đăng ký
    public function themTaiKhoan($tentk, $email, $phone, $password, $vido, $kinhdo, $diachi)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Dùng Transaction để đảm bảo lưu cả 2 bảng thành công
        $this->conn->begin_transaction();
        try {
            // Lưu vào bảng TaiKhoan
            $stmt = $this->conn->prepare("INSERT INTO TaiKhoan (TenTK, Email, Sdt, MatKhau, ViDo, KinhDo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssdd", $tentk, $email, $phone, $hash, $vido, $kinhdo);
            $stmt->execute();
            $idTaiKhoan = $stmt->insert_id; // Lấy ID vừa tạo
            $stmt->close();

            // Lưu địa chỉ mặc định vào bảng DiaChi
            $is_default = 1;
            $stmt2 = $this->conn->prepare("INSERT INTO DiaChi (IdTaiKhoan, DiaChiChiTiet, MacDinh) VALUES (?, ?, ?)");
            $stmt2->bind_param("isi", $idTaiKhoan, $diachi, $is_default);
            $stmt2->execute();
            $stmt2->close();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>