<?php
// File: seller/models/sellerSanPhamModel.php

class SellerSanPhamModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // 1. Lấy danh sách sản phẩm CỦA TÔI (người đang đăng nhập)
    public function getSanPhamCuaToi($idNguoiBan, $keyword = '')
    {
        // Chỉ lấy sản phẩm có IdNguoiBan trùng với Session
        $sql = "SELECT hh.*, dm.TenDM, 
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM HangHoa hh
                LEFT JOIN DanhMuc dm ON hh.MaDM = dm.MaDM
                WHERE hh.IdNguoiBan = ?";

        if (!empty($keyword)) {
            $sql .= " AND hh.TenHH LIKE ?";
            $sql .= " ORDER BY hh.MaHH DESC";
            $stmt = $this->conn->prepare($sql);
            $search = "%" . $keyword . "%";
            $stmt->bind_param("is", $idNguoiBan, $search);
        } else {
            $sql .= " ORDER BY hh.MaHH DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $idNguoiBan);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 2. Thêm sản phẩm mới (Mặc định ChoDuyet)
    public function themSanPham($idNguoiBan, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota)
    {
        // Câu lệnh INSERT có thêm cột IdNguoiBan và set cứng TrangThaiDuyet = 'ChoDuyet'
        $sql = "INSERT INTO HangHoa (IdNguoiBan, TenHH, MaDM, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa, TrangThaiDuyet, NgayThem) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'ChoDuyet', NOW())";

        $stmt = $this->conn->prepare($sql);
        // i=int, s=string, d=double. Thứ tự: IdNguoiBan(i), Ten(s), MaDM(i), SL(i), Gia(d), GiaTT(d), ChatLuong(s), TinhTrang(s), MoTa(s)
        $stmt->bind_param("isiiddsss", $idNguoiBan, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota);

        if ($stmt->execute()) {
            return $this->conn->insert_id; // Trả về ID vừa tạo để lát up ảnh
        }
        return false;
    }

    // 3. Sửa sản phẩm (Quan trọng: Phải check IdNguoiBan để không sửa hàng người khác)
    public function suaSanPham($idNguoiBan, $mahh, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota)
    {
        $sql = "UPDATE HangHoa 
                SET TenHH=?, MaDM=?, SoLuongHH=?, Gia=?, GiaThiTruong=?, ChatLuongHang=?, TinhTrangHang=?, MoTa=?, TrangThaiDuyet='ChoDuyet' 
                WHERE MaHH=? AND IdNguoiBan=?";
        // Lưu ý: Khi sửa xong thì reset về 'ChoDuyet' để Admin kiểm tra lại

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siiddsssii", $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota, $mahh, $idNguoiBan);
        return $stmt->execute();
    }

    // 4. Xóa sản phẩm
    public function xoaSanPham($idNguoiBan, $mahh)
    {
        // Logic xóa file ảnh vật lý giữ nguyên, nhưng phải select kiểm tra quyền trước

        // B1: Lấy danh sách ảnh để xóa file (Chỉ lấy nếu đúng là hàng của người này)
        $urls_to_delete = [];
        $stmt_img = $this->conn->prepare("SELECT ha.URL FROM HinhAnh ha JOIN HangHoa hh ON ha.MaHH = hh.MaHH WHERE hh.MaHH = ? AND hh.IdNguoiBan = ?");
        $stmt_img->bind_param("ii", $mahh, $idNguoiBan);
        $stmt_img->execute();
        $res = $stmt_img->get_result();
        while ($row = $res->fetch_assoc()) {
            $urls_to_delete[] = $row['URL'];
        }

        // B2: Xóa trong DB
        $sql = "DELETE FROM HangHoa WHERE MaHH=? AND IdNguoiBan=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $mahh, $idNguoiBan);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            // B3: Xóa file vật lý
            foreach ($urls_to_delete as $url) {
                // Đường dẫn tương đối từ file Controller ra thư mục assets
                // Vì controller nằm ở seller/controllers/ -> ra ngoài 2 cấp là root
                $file_to_delete = realpath(__DIR__ . "/../../" . $url);
                if ($file_to_delete && file_exists($file_to_delete)) {
                    @unlink($file_to_delete);
                }
            }
            return true;
        }
        return false;
    }

    // 5. Thêm ảnh (Dùng chung logic)
    public function themNhieuAnh($mahh, $danh_sach_anh)
    {
        $stmt = $this->conn->prepare("INSERT INTO HinhAnh (URL, MaHH) VALUES (?, ?)");
        foreach ($danh_sach_anh as $url) {
            $stmt->bind_param("si", $url, $mahh);
            $stmt->execute();
        }
        return true;
    }

    // 6. Lấy thông tin 1 sản phẩm để sửa
    public function getSanPhamById($idNguoiBan, $mahh)
    {
        $stmt = $this->conn->prepare("SELECT * FROM HangHoa WHERE MaHH = ? AND IdNguoiBan = ?");
        $stmt->bind_param("ii", $mahh, $idNguoiBan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 7. Lấy ảnh của sản phẩm
    public function getAnhSanPham($mahh)
    {
        $stmt = $this->conn->prepare("SELECT URL FROM HinhAnh WHERE MaHH = ?");
        $stmt->bind_param("i", $mahh);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
