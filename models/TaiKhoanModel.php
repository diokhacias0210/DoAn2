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

    public function getDanhSachDiaChi($idUser)
    {
        $stmt = $this->conn->prepare("SELECT MaDC, DiaChiChiTiet, MacDinh FROM DiaChi WHERE IdTaiKhoan = ?");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function themDiaChi($idUser, $diaChiMoi)
    {
        $count_stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM DiaChi WHERE IdTaiKhoan = ?");
        $count_stmt->bind_param("i", $idUser);
        $count_stmt->execute();
        $address_count = $count_stmt->get_result()->fetch_assoc()['count'];

        if ($address_count >= 5) {
            return "max";
        }

        $is_default = 0;
        $stmt = $this->conn->prepare("INSERT INTO DiaChi (IdTaiKhoan, DiaChiChiTiet, MacDinh) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $idUser, $diaChiMoi, $is_default);
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
}
