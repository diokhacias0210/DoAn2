<?php

class AdminDanhGiaModel
{

    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // LẤY DANH SÁCH SẢN PHẨM CÓ ĐÁNH GIÁ / BÌNH LUẬN
    public function getDanhSachSanPham()
    {
        $sql = "
            SELECT hh.MaHH, hh.TenHH,
                (SELECT COUNT(*) FROM DanhGiaSao WHERE DanhGiaSao.MaHH = hh.MaHH) AS SoDanhGia,
                (SELECT COUNT(*) FROM BinhLuan WHERE BinhLuan.MaHH = hh.MaHH) AS SoBinhLuan
            FROM HangHoa hh
            ORDER BY hh.MaHH DESC
        ";

        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // LẤY CHI TIẾT ĐÁNH GIÁ + BÌNH LUẬN CỦA 1 SẢN PHẨM
    public function getChiTiet($maHH)
    {
        $sql = "
            SELECT 
                'sao' AS Loai,
                dg.MaDG AS Ma,
                tk.TenTK,
                dg.SoSao,
                NULL AS BinhLuan,
                dg.NgayDG AS NgayBL,
                dg.TrangThai
            FROM DanhGiaSao dg
            LEFT JOIN TaiKhoan tk ON dg.IdTaiKhoan = tk.IdTaiKhoan
            WHERE dg.MaHH = $maHH

            UNION

            SELECT 
                'binhluan' AS Loai,
                bl.MaBL AS Ma,
                tk.TenTK AS NguoiDung,
                NULL AS SoSao,
                bl.NoiDung AS BinhLuan,
                bl.NgayBL AS Ngay,
                bl.TrangThai
            FROM BinhLuan bl
            LEFT JOIN TaiKhoan tk ON bl.IdTaiKhoan = tk.IdTaiKhoan
            WHERE bl.MaHH = $maHH

            ORDER BY Ngay DESC
        ";

        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // ẨN RECORD (SAO HOẶC BÌNH LUẬN)
    public function capNhatTrangThai($loai, $id, $trangThaiMoi)
    {
        if ($loai === 'sao') {
            $sql = "UPDATE DanhGiaSao SET TrangThai=? WHERE MaDG=?";
        } else {
            $sql = "UPDATE BinhLuan SET TrangThai=? WHERE MaBL=?";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $trangThaiMoi, $id);
        return $stmt->execute();
    }

    // XÓA RECORD (SAO HOẶC BÌNH LUẬN)
    public function xoaRecord($loai, $id)
    {
        if ($loai === 'sao') {
            $sql = "DELETE FROM DanhGiaSao WHERE MaDG=?";
        } else {
            $sql = "DELETE FROM BinhLuan WHERE MaBL=?";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
