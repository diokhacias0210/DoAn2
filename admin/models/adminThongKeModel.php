<?php
// File: models/adminThongKeModel.php

class ThongKe
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // ========== THỐNG KÊ TỔNG QUAN ==========
    
    public function getTongDoanhThu()
    {
        $sql = "SELECT COALESCE(SUM(TongTien), 0) as TongDoanhThu 
                FROM DonHang 
                WHERE TrangThai = 'Hoàn tất'";
        
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['TongDoanhThu'];
    }

    public function getTongDonHang()
    {
        $sql = "SELECT COUNT(*) as TongDonHang FROM DonHang";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['TongDonHang'];
    }

    public function getTongNguoiDung()
    {
        $sql = "SELECT COUNT(*) as TongNguoiDung FROM TaiKhoan WHERE VaiTro = 0";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['TongNguoiDung'];
    }

    public function getTongSanPham()
    {
        $sql = "SELECT COUNT(*) as TongSanPham FROM HangHoa";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['TongSanPham'];
    }

    // ========== THỐNG KÊ THEO THỜI GIAN CỐ ĐỊNH ==========
    
    public function getDoanhThuTheoNgay($ngay = null)
    {
        if ($ngay === null) {
            $ngay = date('Y-m-d');
        }
        
        $sql = "SELECT COALESCE(SUM(TongTien), 0) as DoanhThu,
                       COUNT(*) as SoDonHang
                FROM DonHang 
                WHERE DATE(NgayDat) = ? 
                AND TrangThai = 'Hoàn tất'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ngay);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getDoanhThuTheoThang($thang = null, $nam = null)
    {
        if ($thang === null) {
            $thang = date('m');
        }
        if ($nam === null) {
            $nam = date('Y');
        }
        
        $sql = "SELECT COALESCE(SUM(TongTien), 0) as DoanhThu,
                       COUNT(*) as SoDonHang
                FROM DonHang 
                WHERE MONTH(NgayDat) = ? 
                AND YEAR(NgayDat) = ?
                AND TrangThai = 'Hoàn tất'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $thang, $nam);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getDoanhThuTheoNam($nam = null)
    {
        if ($nam === null) {
            $nam = date('Y');
        }
        
        $sql = "SELECT COALESCE(SUM(TongTien), 0) as DoanhThu,
                       COUNT(*) as SoDonHang
                FROM DonHang 
                WHERE YEAR(NgayDat) = ?
                AND TrangThai = 'Hoàn tất'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $nam);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // ========== THỐNG KÊ THEO BỘ LỌC ==========
    
    public function getDoanhThuTheoKhoangThoiGian($tuNgay, $denNgay)
    {
        $sql = "SELECT DATE(NgayDat) as Ngay,
                       COALESCE(SUM(TongTien), 0) as DoanhThu,
                       COUNT(*) as SoDonHang
                FROM DonHang
                WHERE DATE(NgayDat) BETWEEN ? AND ?
                AND TrangThai = 'Hoàn tất'
                GROUP BY DATE(NgayDat)
                ORDER BY Ngay ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $tuNgay, $denNgay);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDoanhThuTheoThangTrongNam($nam)
    {
        $sql = "SELECT MONTH(NgayDat) as Thang,
                       COALESCE(SUM(TongTien), 0) as DoanhThu,
                       COUNT(*) as SoDonHang
                FROM DonHang
                WHERE YEAR(NgayDat) = ?
                AND TrangThai = 'Hoàn tất'
                GROUP BY MONTH(NgayDat)
                ORDER BY Thang ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $nam);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ========== TOP SẢN PHẨM BÁN CHẠY ==========
    
    public function getTopSanPhamBanChay($limit = 5)
    {
        $sql = "SELECT hh.MaHH, hh.TenHH,
                       SUM(ctdh.SoLuongSanPham) as TongSoLuongBan,
                       SUM(ctdh.SoLuongSanPham * ctdh.DonGia) as TongDoanhThu,
                       (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh
                JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH
                JOIN DonHang dh ON ctdh.MaDH = dh.MaDH
                WHERE dh.TrangThai = 'Hoàn tất'
                GROUP BY hh.MaHH
                ORDER BY TongSoLuongBan DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopSanPhamBanChayTheoKhoangThoiGian($tuNgay, $denNgay, $limit = 5)
    {
        $sql = "SELECT hh.MaHH, hh.TenHH,
                       SUM(ctdh.SoLuongSanPham) as TongSoLuongBan,
                       SUM(ctdh.SoLuongSanPham * ctdh.DonGia) as TongDoanhThu,
                       (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh
                JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH
                JOIN DonHang dh ON ctdh.MaDH = dh.MaDH
                WHERE DATE(dh.NgayDat) BETWEEN ? AND ?
                AND dh.TrangThai = 'Hoàn tất'
                GROUP BY hh.MaHH
                ORDER BY TongSoLuongBan DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $tuNgay, $denNgay, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopSanPhamBanChayTheoThang($thang, $nam, $limit = 5)
    {
        $sql = "SELECT hh.MaHH, hh.TenHH,
                       SUM(ctdh.SoLuongSanPham) as TongSoLuongBan,
                       SUM(ctdh.SoLuongSanPham * ctdh.DonGia) as TongDoanhThu,
                       (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh
                JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH
                JOIN DonHang dh ON ctdh.MaDH = dh.MaDH
                WHERE MONTH(dh.NgayDat) = ? 
                AND YEAR(dh.NgayDat) = ?
                AND dh.TrangThai = 'Hoàn tất'
                GROUP BY hh.MaHH
                ORDER BY TongSoLuongBan DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $thang, $nam, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopSanPhamBanChayTheoNam($nam, $limit = 5)
    {
        $sql = "SELECT hh.MaHH, hh.TenHH,
                       SUM(ctdh.SoLuongSanPham) as TongSoLuongBan,
                       SUM(ctdh.SoLuongSanPham * ctdh.DonGia) as TongDoanhThu,
                       (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM ChiTietDonHang ctdh
                JOIN HangHoa hh ON ctdh.MaHH = hh.MaHH
                JOIN DonHang dh ON ctdh.MaDH = dh.MaDH
                WHERE YEAR(dh.NgayDat) = ?
                AND dh.TrangThai = 'Hoàn tất'
                GROUP BY hh.MaHH
                ORDER BY TongSoLuongBan DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $nam, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ========== THỐNG KÊ BỔ SUNG ==========
    
    public function getThongKeSanPhamTheoDanhMuc()
    {
        $sql = "SELECT dm.TenDM,
                       COUNT(hh.MaHH) as SoLuongSanPham,
                       SUM(hh.SoLuongHH) as TongTonKho
                FROM DanhMuc dm
                LEFT JOIN HangHoa hh ON dm.MaDM = hh.MaDM
                GROUP BY dm.MaDM
                ORDER BY SoLuongSanPham DESC";
        
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>