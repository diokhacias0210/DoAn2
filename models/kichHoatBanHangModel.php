<?php
class kichHoatBanHangModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Kiểm tra trạng thái từ bảng TaiKhoan
    public function getTrangThai($idUser) {
        $stmt = $this->conn->prepare("SELECT TrangThaiBanHang FROM TaiKhoan WHERE IdTaiKhoan = ?");
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result ? $result['TrangThaiBanHang'] : 'ChuaKichHoat';
    }

    // Kích hoạt bán hàng (Thêm hồ sơ + Đổi trạng thái)
    public function kichHoat($idUser, $tenCuaHang, $soCCCD, $diaChiKhoHang, $tenNganHang, $soTaiKhoan, $tenChuTaiKhoan) {
        
        // Bước 1: Cập nhật trạng thái thành DangHoatDong trong bảng TaiKhoan
        $stmt1 = $this->conn->prepare("UPDATE TaiKhoan SET TrangThaiBanHang = 'DangHoatDong' WHERE IdTaiKhoan = ?");
        $stmt1->bind_param("i", $idUser);
        $stmt1->execute();

        // Bước 2: Thêm thông tin vào bảng HoSoNguoiBan, bao gồm cả Ngân Hàng
        $sql2 = "INSERT INTO HoSoNguoiBan (IdTaiKhoan, SoCCCD, DiaChiKhoHang, TenCuaHang, TenNganHang, SoTaiKhoanNganHang, TenChuTaiKhoan, NgayDuyet) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE 
                    SoCCCD = VALUES(SoCCCD), 
                    DiaChiKhoHang = VALUES(DiaChiKhoHang), 
                    TenCuaHang = VALUES(TenCuaHang),
                    TenNganHang = VALUES(TenNganHang),
                    SoTaiKhoanNganHang = VALUES(SoTaiKhoanNganHang),
                    TenChuTaiKhoan = VALUES(TenChuTaiKhoan)";
        
        $stmt2 = $this->conn->prepare($sql2);
        
        // "issssss" tương ứng: 1 biến kiểu INT ($idUser) và 6 biến kiểu STRING cho các trường còn lại
        $stmt2->bind_param("issssss", $idUser, $soCCCD, $diaChiKhoHang, $tenCuaHang, $tenNganHang, $soTaiKhoan, $tenChuTaiKhoan);
        
        return $stmt2->execute();
    }

    // Hủy kích hoạt
    public function huy($idUser) {
        $stmt = $this->conn->prepare("UPDATE TaiKhoan SET TrangThaiBanHang = 'ChuaKichHoat' WHERE IdTaiKhoan = ?");
        $stmt->bind_param("i", $idUser);
        return $stmt->execute();
    }
}
?>