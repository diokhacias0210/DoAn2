<?php
// File: models/DiaChiModel.php
class DiaChiModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Lấy địa chỉ mặc định
    public function getDefaultAddress($idUser)
    {
        $stmt_addr = $this->conn->prepare("SELECT DiaChiChiTiet FROM DiaChi WHERE IdTaiKhoan = ? AND MacDinh = 1 LIMIT 1");
        $stmt_addr->bind_param("i", $idUser);
        $stmt_addr->execute();
        $addr_result = $stmt_addr->get_result();
        $default_address = $addr_result->num_rows > 0 ? $addr_result->fetch_assoc()['DiaChiChiTiet'] : '';
        $stmt_addr->close();
        return $default_address;
    }

    // Lấy tất cả địa chỉ
    public function getAllAddresses($idUser)
    {
        $stmt_all_addr = $this->conn->prepare("SELECT MaDC, DiaChiChiTiet, MacDinh FROM DiaChi WHERE IdTaiKhoan = ?");
        $stmt_all_addr->bind_param("i", $idUser);
        $stmt_all_addr->execute();
        $all_addresses = $stmt_all_addr->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_all_addr->close();
        return $all_addresses;
    }
}
