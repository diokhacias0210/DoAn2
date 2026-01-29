<?php
class SanPhamChiTiet
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Lấy chi tiết sản phẩm theo mã sản phẩm
    public function getChiTietSanPham($maHH)
    {
        $sql = "
            select h.MaHH,h.IdNguoiBan, h.TenHH, h.SoLuongHH, h.Gia,h.GiaThiTruong, h.NgayThem, h.ChatLuongHang, h.TinhTrangHang, h.MoTa, d.TenDM
            ,avg(ifnull(dg.SoSao, 0)) as Rating, count(distinct dg.MaDG) as SoLuotDanhGia, mg.GiaTri as GiamGia
            from HangHoa h
            left join DanhMuc d on h.MaDM = d.MaDM
            left join DanhGiaSao dg on h.MaHH = dg.MaHH
            left join MaGiamGiaDanhMuc mgdm on h.MaDM = mgdm.MaDM
            left join MaGiamGia mg on mgdm.MaGG = mg.MaGG and mg.TrangThai = 'Hoạt động' and (mg.NgayKetThuc is null or mg.NgayKetThuc >= NOW())
            where h.MaHH = ? 
            group by h.MaHH,h.IdNguoiBan, h.TenHH, h.SoLuongHH, h.Gia, h.NgayThem, h.ChatLuongHang, h.TinhTrangHang, h.MoTa, d.TenDM, mg.GiaTri;
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    // Lấy hình ảnh 
    public function getHinhAnhSanPham($maHH)
    {
        $sql = "
        select URL 
        from HinhAnh 
        where MaHH = ? 
        order by IDHinhAnh";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    // lấy bình luận
    public function getBinhLuanSanPham($maHH)
    {
        $sql = "
            SELECT bl.NoiDung, bl.NgayBL, tk.TenTK, dg.SoSao
            FROM BinhLuan bl
            JOIN TaiKhoan tk ON bl.IdTaiKhoan = tk.IdTaiKhoan
            LEFT JOIN DanhGiaSao dg ON bl.IdTaiKhoan = dg.IdTaiKhoan 
                AND bl.MaHH = dg.MaHH
            WHERE bl.MaHH = ? 
            AND bl.TrangThai = 'Hiển thị'  -- THÊM DÒNG NÀY
            ORDER BY bl.NgayBL DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHH);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy thông tin người bán dựa trên IdTaiKhoan của người bán
    public function getThongTinNguoiBan($idNguoiBan)
    {
        $sql = "
            SELECT 
                tk.IdTaiKhoan, 
                tk.TenTK, 
                tk.Avatar, 
                tk.Sdt,
                hs.TenCuaHang, 
                hs.DiaChiKhoHang
            FROM TaiKhoan tk
            LEFT JOIN HoSoNguoiBan hs ON tk.IdTaiKhoan = hs.IdTaiKhoan
            WHERE tk.IdTaiKhoan = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idNguoiBan);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
