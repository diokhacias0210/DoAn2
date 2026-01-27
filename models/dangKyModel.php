<?php
class DangKyModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Kiểm tra email đã tồn tại
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

    // Thêm tài khoản mới
    public function themTaiKhoan($tentk, $email, $phone, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO TaiKhoan (TenTK, Email, Sdt, MatKhau) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $tentk, $email, $phone, $hash);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
