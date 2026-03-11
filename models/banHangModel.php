<?php
class banHangModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // kiểm tra đã kích hoạt chưa
    public function getTrangThai($idUser) {
        $stmt = $this->conn->prepare(
            "SELECT TrangThai FROM NguoiBan WHERE IdTaiKhoan = ?"
        );
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // kích hoạt bán hàng
    public function kichHoat($idUser, $cccd, $sdt, $diachi) {
        $stmt = $this->conn->prepare(
            "INSERT INTO NguoiBan (IdTaiKhoan, SoCCCD, SDT, DiaChi, TrangThai)
             VALUES (?, ?, ?, ?, 1)"
        );
        $stmt->bind_param("isss", $idUser, $cccd, $sdt, $diachi);
        return $stmt->execute();
    }

    // hủy kích hoạt
    public function huy($idUser) {
        $stmt = $this->conn->prepare(
            "UPDATE NguoiBan SET TrangThai = 0 WHERE IdTaiKhoan = ?"
        );
        $stmt->bind_param("i", $idUser);
        return $stmt->execute();
    }
}
