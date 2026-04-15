<?php
// File: seller/models/sellerSanPhamModel.php

class SellerSanPhamModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Lấy tổng số sản phẩm để chia trang

    public function countSanPhamCuaToi($idNguoiBan, $keyword, $madm, $tinhtrang, $trangthaiduyet)
    {
        $sql = "SELECT COUNT(*) as total FROM HangHoa WHERE IdNguoiBan = ?";
        $params = [$idNguoiBan];
        $types = "i";

        if (!empty($keyword)) {
            $sql .= " AND TenHH LIKE ?";
            $params[] = "%$keyword%";
            $types .= "s";
        }
        if (!empty($madm)) {
            $sql .= " AND MaDM = ?";
            $params[] = $madm;
            $types .= "i";
        }
        if (!empty($tinhtrang)) {
            $sql .= " AND TinhTrangHang = ?";
            $params[] = $tinhtrang;
            $types .= "s";
        }
        if (!empty($trangthaiduyet)) {
            $sql .= " AND TrangThaiDuyet = ?";
            $params[] = $trangthaiduyet;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
    // Lấy danh sách có Lọc, Sắp xếp và Phân trang
    public function getSanPhamCuaToi($idNguoiBan, $keyword, $madm, $tinhtrang, $trangthaiduyet, $sort, $limit, $offset)
    {
        // Câu SQL có tính luôn "Số lượng đã bán" từ bảng ChiTietDonHang
        $sql = "SELECT hh.*, dm.TenDM, 
                (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as AnhDaiDien,
                COALESCE((SELECT SUM(ct.SoLuongSanPham) FROM ChiTietDonHang ct JOIN DonHang dh ON ct.MaDH = dh.MaDH WHERE ct.MaHH = hh.MaHH AND dh.TrangThai = 'Hoàn tất'), 0) as DaBan
                FROM HangHoa hh
                LEFT JOIN DanhMuc dm ON hh.MaDM = dm.MaDM
                WHERE hh.IdNguoiBan = ?";

        $params = [$idNguoiBan];
        $types = "i";

        if (!empty($keyword)) {
            $sql .= " AND hh.TenHH LIKE ?";
            $params[] = "%$keyword%";
            $types .= "s";
        }
        if (!empty($madm)) {
            $sql .= " AND hh.MaDM = ?";
            $params[] = $madm;
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
        elseif ($sort == 'tonkho_asc') $sql .= " ORDER BY hh.SoLuongHH ASC";
        elseif ($sort == 'banchay') $sql .= " ORDER BY DaBan DESC";
        else $sql .= " ORDER BY hh.MaHH DESC"; // Mặc định mới nhất

        // Phân trang
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Thêm sản phẩm mới (Mặc định ChoDuyet)
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

    // Sửa sản phẩm (Quan trọng: Phải check IdNguoiBan để không sửa hàng người khác)
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

    // Thêm ảnh (Dùng chung logic)
    public function themNhieuAnh($mahh, $danh_sach_anh)
    {
        $stmt = $this->conn->prepare("INSERT INTO HinhAnh (URL, MaHH) VALUES (?, ?)");
        foreach ($danh_sach_anh as $url) {
            $stmt->bind_param("si", $url, $mahh);
            $stmt->execute();
        }
        return true;
    }

    // Lấy thông tin 1 sản phẩm để sửa
    public function getSanPhamById($idNguoiBan, $mahh)
    {
        $stmt = $this->conn->prepare("SELECT * FROM HangHoa WHERE MaHH = ? AND IdNguoiBan = ?");
        $stmt->bind_param("ii", $mahh, $idNguoiBan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy ảnh của sản phẩm
    public function getAnhSanPham($mahh)
    {
        $stmt = $this->conn->prepare("SELECT URL FROM HinhAnh WHERE MaHH = ?");
        $stmt->bind_param("i", $mahh);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
