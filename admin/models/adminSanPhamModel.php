<?php

class AdminSanPhamModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }


    //Lấy tất cả sản phẩm 

    public function getTatCaSanPham($keyword = '')
    {
        // Câu lệnh SQL chung cho cả tìm kiếm và lấy tất cả, chỉ khác ở mệnh đề WHERE
        $sql_template = "
            SELECT 
                hh.*, 
                dm.TenDM, 
                tk.TenTK as NguoiBan,
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien,
                CASE 
                    WHEN hh.TinhTrangHang = 'Ngưng kinh doanh' THEN 'Ngưng kinh doanh'
                    WHEN hh.SoLuongHH <= 0 THEN 'Hết hàng'
                    ELSE 'Còn hàng'
                END AS TinhTrangHang
            FROM 
                HangHoa hh
            LEFT JOIN   DanhMuc dm ON hh.MaDM = dm.MaDM
            LEFT JOIN   TaiKhoan tk ON hh.IdNguoiBan = tk.IdTaiKhoan
        ";

        $result = null;

        if (!empty($keyword)) {
            // Tìm kiếm theo tên sản phẩm hoặc người bán (dùng prepared statement)
            $sql = $sql_template . " WHERE hh.TenHH LIKE ? OR tk.TenTK LIKE ? ORDER BY hh.MaHH DESC";
            $stmt = $this->conn->prepare($sql);
            $searchTerm = "%" . $keyword . "%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            // Lấy tất cả (không tìm kiếm)
            $sql = $sql_template . " ORDER BY hh.TrangThaiDuyet ASC, hh.MaHH DESC";
            $result = $this->conn->query($sql);
        }

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            // Xử lý trường hợp truy vấn thất bại (tùy chọn)
            return [];
        }
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

    //Xóa sản phẩm (bao gồm cả ảnh)
    public function xoaSanPham($id)
    {
        // 1. Lấy danh sách ảnh để xóa file
        $urls_to_delete = [];
        $stmt_img = $this->conn->prepare("SELECT URL FROM HinhAnh WHERE MaHH = ?");
        $stmt_img->bind_param("i", $id);
        $stmt_img->execute();
        $img_res = $stmt_img->get_result();
        while ($img_row = $img_res->fetch_assoc()) {
            $urls_to_delete[] = $img_row['URL'];
        }
        $stmt_img->close();

        // Xóa bản ghi trong CSDL (sẽ tự động xóa HinhAnh nhờ CASCADE)
        $stmt_del_hh = $this->conn->prepare("DELETE FROM HangHoa WHERE MaHH=?");
        $stmt_del_hh->bind_param("i", $id);
        if ($stmt_del_hh->execute() && $stmt_del_hh->affected_rows > 0) {
            //  Xóa file vật lý
            foreach ($urls_to_delete as $url) {
                // Chỉ xóa file do chúng ta upload (trong 'uploaded/')
                $file_to_delete = realpath(__DIR__ . "/../../" . $url);
                if ($file_to_delete && strpos($url, 'assets/images/products/uploaded/') === 0 && file_exists($file_to_delete)) {
                    @unlink($file_to_delete);
                }
            }
            return true;
        }
        $stmt_del_hh->close();
        return false;
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
}
