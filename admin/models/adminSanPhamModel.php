<?php

class AdminSanPhamModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function countTatCaSanPham($keyword, $madm, $idNguoiBan, $tinhtrang, $trangthaiduyet)
    {
        $sql = "SELECT COUNT(*) as total FROM HangHoa hh JOIN TaiKhoan tk ON hh.IdNguoiBan = tk.IdTaiKhoan WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (hh.TenHH LIKE ? OR tk.TenTK LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "ss";
        }
        if (!empty($madm)) {
            $sql .= " AND hh.MaDM = ?";
            $params[] = $madm;
            $types .= "i";
        }
        if (!empty($idNguoiBan)) {
            $sql .= " AND hh.IdNguoiBan = ?";
            $params[] = $idNguoiBan;
            $types .= "i";
        }
        if (!empty($tinhtrang)) {
            $sql .= " AND hh.TinhTrangHang = ?";
            $params[] = $tinhtrang;
            $types .= "s";
        }
        if (!empty($trangthaiduyet)) {
            $sql .= " AND hh.TrangThaiDuyet = ?";
            $params[] = $trangthaiduyet;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }



    //Lấy tất cả sản phẩm 

    public function getTatCaSanPham($keyword, $madm, $idNguoiBan, $tinhtrang, $trangthaiduyet, $sort, $limit, $offset)
    {
        $sql = "SELECT hh.*, dm.TenDM, tk.TenTK as NguoiBan, tk.IdTaiKhoan,
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien
                FROM HangHoa hh
                LEFT JOIN DanhMuc dm ON hh.MaDM = dm.MaDM
                LEFT JOIN TaiKhoan tk ON hh.IdNguoiBan = tk.IdTaiKhoan
                WHERE 1=1";

        $params = [];
        $types = "";

        // Điều kiện lọc giống hàm count ở trên
        if (!empty($keyword)) {
            $sql .= " AND (hh.TenHH LIKE ? OR tk.TenTK LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $types .= "ss";
        }
        if (!empty($madm)) {
            $sql .= " AND hh.MaDM = ?";
            $params[] = $madm;
            $types .= "i";
        }
        if (!empty($idNguoiBan)) {
            $sql .= " AND hh.IdNguoiBan = ?";
            $params[] = $idNguoiBan;
            $types .= "i";
        }
        if (!empty($tinhtrang)) {
            $sql .= " AND hh.TinhTrangHang = ?";
            $params[] = $tinhtrang;
            $types .= "s";
        }
        if (!empty($trangthaiduyet)) {
            $sql .= " AND hh.TrangThaiDuyet = ?";
            $params[] = $trangthaiduyet;
            $types .= "s";
        }

        // Sắp xếp
        if ($sort == 'gia_asc') $sql .= " ORDER BY hh.Gia ASC";
        elseif ($sort == 'gia_desc') $sql .= " ORDER BY hh.Gia DESC";
        elseif ($sort == 'tonkho_desc') $sql .= " ORDER BY hh.SoLuongHH DESC";
        else $sql .= " ORDER BY CASE WHEN hh.TrangThaiDuyet = 'ChoDuyet' THEN 1 ELSE 2 END, hh.MaHH DESC";

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function layDanhSachNguoiBan()
    {
        $res = $this->conn->query("SELECT IdTaiKhoan, TenTK FROM TaiKhoan WHERE TrangThaiBanHang != 'ChuaKichHoat'");
        return $res->fetch_all(MYSQLI_ASSOC);
    }


    //Lấy một sản phẩm bằng ID

    public function getSanPhamTheoId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM HangHoa WHERE MaHH = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    //Lấy ảnh của sản phẩm

    public function getAnhSanPham($id)
    {
        $stmt = $this->conn->prepare("SELECT URL FROM HinhAnh WHERE MaHH = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    //Thêm sản phẩm mới

    public function themSanPham($ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota)
    {
        $stmt = $this->conn->prepare("INSERT INTO HangHoa (TenHH, MaDM, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("siiddsss", $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota);
        if ($stmt->execute()) {
            return $this->conn->insert_id; // Trả về ID sản phẩm mới
        }
        return false;
    }


    //   Cập nhật sản phẩm

    public function suaSanPham($id, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota)
    {
        $stmt = $this->conn->prepare("UPDATE HangHoa SET TenHH=?, MaDM=?, SoLuongHH=?, Gia=?, GiaThiTruong=?, ChatLuongHang=?, TinhTrangHang=?, MoTa=? WHERE MaHH=?");
        $stmt->bind_param("siiddsssi", $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota, $id);
        return $stmt->execute();
    }

    //   Xóa ảnh cũ của sản phẩm
    public function xoaAnhCu($mahh)
    {
        //  Lấy danh sách ảnh
        $urls_to_delete = [];
        $stmt_img = $this->conn->prepare("SELECT URL FROM HinhAnh WHERE MaHH = ?");
        $stmt_img->bind_param("i", $mahh);
        $stmt_img->execute();
        $img_res = $stmt_img->get_result();
        while ($img_row = $img_res->fetch_assoc()) {
            $urls_to_delete[] = $img_row['URL'];
        }
        $stmt_img->close();

        //  Xóa bản ghi CSDL
        $stmt_del = $this->conn->prepare("DELETE FROM HinhAnh WHERE MaHH = ?");
        $stmt_del->bind_param("i", $mahh);
        $stmt_del->execute();
        $stmt_del->close();

        //  Xóa file vật lý
        foreach ($urls_to_delete as $url) {
            $file_to_delete = realpath(__DIR__ . "/../../" . $url);
            if ($file_to_delete && strpos($url, 'assets/images/products/uploaded/') === 0 && file_exists($file_to_delete)) {
                @unlink($file_to_delete);
            }
        }
    }

    //   Thêm ảnh mới cho sản phẩm
    public function themNhieuAnh($mahh, $danh_sach_anh)
    {
        $stmt = $this->conn->prepare("INSERT INTO HinhAnh (URL, MaHH) VALUES (?, ?)");
        foreach ($danh_sach_anh as $image_path_db) {
            $stmt->bind_param("si", $image_path_db, $mahh);
            $stmt->execute();
        }
        $stmt->close();
        return true;
    }
    // duyệt sản phẩm
    public function duyetSanPham($mahh, $trangThai, $lyDo = null)
    {
        $sql = "UPDATE HangHoa SET TrangThaiDuyet = ?, LyDoTuChoi = ? WHERE MaHH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $trangThai, $lyDo, $mahh);
        return $stmt->execute();
    }

    // bật/tắt hiển thị
    public function capNhatHienThi($mahh, $trangThai)
    {
        // $trangThai: 0 là Ẩn, 1 là Hiện
        $stmt = $this->conn->prepare("UPDATE HangHoa SET HienThi = ? WHERE MaHH = ?");
        $stmt->bind_param("ii", $trangThai, $mahh);
        return $stmt->execute();
    }
    // --- Lấy thông tin chi tiết cho Admin (Kèm thông tin người bán) ---
    public function getChiTietSanPhamAdmin($id)
    {
        $sql = "SELECT hh.*, dm.TenDM, tk.TenTK as NguoiBan, tk.Email, tk.Sdt, tk.Avatar 
                FROM HangHoa hh
                LEFT JOIN DanhMuc dm ON hh.MaDM = dm.MaDM
                LEFT JOIN TaiKhoan tk ON hh.IdNguoiBan = tk.IdTaiKhoan
                WHERE hh.MaHH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    // Lấy danh sách SP đang chờ duyệt để gửi thông báo
    public function getTatCaSanPhamChoDuyet()
    {
        $sql = "SELECT MaHH, IdNguoiBan, TenHH FROM HangHoa WHERE TrangThaiDuyet = 'ChoDuyet'";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //  Cập nhật trạng thái duyệt tất cả
    public function duyetTatCaSanPham()
    {
        $sql = "UPDATE HangHoa SET TrangThaiDuyet = 'DaDuyet' WHERE TrangThaiDuyet = 'ChoDuyet'";
        return $this->conn->query($sql);
    }
}
