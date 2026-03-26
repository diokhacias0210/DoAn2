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
    public function kichHoat($idUser, $tenCuaHang, $soCCCD, $diaChiKhoHang, $tenNganHang, $soTaiKhoan, $tenChuTaiKhoan, $viDo, $kinhDo) {
        
        // Bước 1: Cập nhật trạng thái thành DangHoatDong trong bảng TaiKhoan
        $stmt1 = $this->conn->prepare("UPDATE TaiKhoan SET TrangThaiBanHang = 'DangHoatDong' WHERE IdTaiKhoan = ?");
        $stmt1->bind_param("i", $idUser);
        $stmt1->execute();

        // Bước 2: Thêm thông tin vào bảng HoSoNguoiBan, bao gồm Ngân Hàng VÀ Tọa độ
        $sql2 = "INSERT INTO HoSoNguoiBan (IdTaiKhoan, SoCCCD, DiaChiKhoHang, TenCuaHang, TenNganHang, SoTaiKhoanNganHang, TenChuTaiKhoan, ViDo, KinhDo, NgayDuyet) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE 
                    SoCCCD = VALUES(SoCCCD), 
                    DiaChiKhoHang = VALUES(DiaChiKhoHang), 
                    TenCuaHang = VALUES(TenCuaHang),
                    TenNganHang = VALUES(TenNganHang),
                    SoTaiKhoanNganHang = VALUES(SoTaiKhoanNganHang),
                    TenChuTaiKhoan = VALUES(TenChuTaiKhoan),
                    ViDo = VALUES(ViDo),
                    KinhDo = VALUES(KinhDo)";
        
        $stmt2 = $this->conn->prepare($sql2);
        
        // "issssssdd" tương ứng: 1 INT ($idUser), 6 STRING (các thông tin chữ), 2 DOUBLE ($viDo, $kinhDo)
        $stmt2->bind_param("issssssdd", $idUser, $soCCCD, $diaChiKhoHang, $tenCuaHang, $tenNganHang, $soTaiKhoan, $tenChuTaiKhoan, $viDo, $kinhDo);
        
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