<?php
class TaiKhoanModel
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function getUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT IdTaiKhoan, TenTK, MatKhau, VaiTro FROM TaiKhoan WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT TenTK, Email, Sdt FROM TaiKhoan WHERE IdTaiKhoan = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }
    public function getThongTin($idUser)
    {
        $stmt = $this->conn->prepare("SELECT TenTK, Email, Sdt FROM TaiKhoan WHERE IdTaiKhoan = ?");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // hàm để bỏ trạng thái mặc định của các địa chỉ cũ và thiết lập địa chỉ mới là mặc định
    public function getDanhSachDiaChi($idUser)
    {
        $stmt = $this->conn->prepare("SELECT MaDC, DiaChiChiTiet, MacDinh, ViDo, KinhDo FROM DiaChi WHERE IdTaiKhoan = ?");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function themDiaChi($idUser, $diaChiMoi, $viDo = null, $kinhDo = null)
    {
        $count_stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM DiaChi WHERE IdTaiKhoan = ?");
        $count_stmt->bind_param("i", $idUser);
        $count_stmt->execute();
        $address_count = $count_stmt->get_result()->fetch_assoc()['count'];

        if ($address_count >= 5) {
            return "max";
        }

        $is_default = 0;
        // Thêm ViDo và KinhDo vào câu lệnh INSERT
        $stmt = $this->conn->prepare("INSERT INTO DiaChi (IdTaiKhoan, DiaChiChiTiet, MacDinh, ViDo, KinhDo) VALUES (?, ?, ?, ?, ?)");
        // Chữ isidd: i=int, s=string, i=int, d=double(Vĩ độ), d=double(Kinh độ)
        $stmt->bind_param("isidd", $idUser, $diaChiMoi, $is_default, $viDo, $kinhDo);
        return $stmt->execute() ? "ok" : "fail";
    }

    public function xoaDiaChi($idUser, $maDC)
    {
        $stmt = $this->conn->prepare("SELECT MacDinh FROM DiaChi WHERE MaDC = ? AND IdTaiKhoan = ?");
        $stmt->bind_param("ii", $maDC, $idUser);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) return "invalid";
        $row = $result->fetch_assoc();
        if ($row['MacDinh'] == 1) return "default";

        $delete = $this->conn->prepare("DELETE FROM DiaChi WHERE MaDC = ?");
        $delete->bind_param("i", $maDC);
        return $delete->execute() ? "ok" : "fail";
    }
 
    public function setDiaChiMacDinh($idUser, $maDC)
    {
        $this->conn->begin_transaction();
        try {
            // 1. Chuyển tất cả địa chỉ của user này về KHÔNG mặc định (MacDinh = 0)
            $stmt1 = $this->conn->prepare("UPDATE DiaChi SET MacDinh = 0 WHERE IdTaiKhoan = ?");
            $stmt1->bind_param("i", $idUser);
            $stmt1->execute();

            // 2. Chuyển địa chỉ được chọn thành mặc định (MacDinh = 1)
            $stmt2 = $this->conn->prepare("UPDATE DiaChi SET MacDinh = 1 WHERE MaDC = ? AND IdTaiKhoan = ?");
            $stmt2->bind_param("ii", $maDC, $idUser);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    public function getDiaChiMacDinh($idUser) {
        $stmt = $this->conn->prepare("SELECT ViDo, KinhDo, DiaChiChiTiet FROM DiaChi WHERE IdTaiKhoan = ? AND MacDinh = 1 LIMIT 1");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Trả về 1 dòng duy nhất hoặc null
    }
}
