<?php

class AdminDanhMucModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Lấy tất cả danh mục
    public function getTatCaDanhMuc($keyword = '')
    {
        if (!empty($keyword)) {
            $keyword = '%' . $keyword . '%';
            $stmt = $this->conn->prepare("SELECT * FROM DanhMuc WHERE TenDM LIKE ? ORDER BY MaDM DESC");
            $stmt->bind_param("s", $keyword);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $sql = "SELECT * FROM DanhMuc ORDER BY MaDM DESC";
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    // Thêm một danh mục mới
    public function themDanhMuc($ten)
    {
        $ten = trim($ten);
        if (empty($ten)) return false;

        $stmt = $this->conn->prepare("INSERT INTO DanhMuc (TenDM) VALUES (?)");
        $stmt->bind_param("s", $ten);
        return $stmt->execute();
    }


    // Sửa một danh mục

    public function suaDanhMuc($id, $ten)
    {
        $id = intval($id);
        $ten = trim($ten);
        if (empty($ten) || $id <= 0) return false;

        $stmt = $this->conn->prepare("UPDATE DanhMuc SET TenDM = ? WHERE MaDM = ?");
        $stmt->bind_param("si", $ten, $id);
        return $stmt->execute();
    }

    public function layIdDanhMucMacDinh()
    {
        // Tìm xem đã có danh mục "Chưa phân loại" chưa
        $tenMacDinh = "Chưa phân loại";
        $stmt = $this->conn->prepare("SELECT MaDM FROM DanhMuc WHERE TenDM = ?");
        $stmt->bind_param("s", $tenMacDinh);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['MaDM']; // Đã có, trả về ID
        }

        // Nếu chưa có, tạo mới
        $stmt = $this->conn->prepare("INSERT INTO DanhMuc (TenDM) VALUES (?)");
        $stmt->bind_param("s", $tenMacDinh);
        if ($stmt->execute()) {
            return $this->conn->insert_id; // Trả về ID vừa tạo
        }

        return false; // Lỗi
    }
    public function chuyenSanPhamSangDanhMucMoi($maDMCu, $maDMMoi)
    {
        $stmt = $this->conn->prepare("UPDATE HangHoa SET MaDM = ? WHERE MaDM = ?");
        $stmt->bind_param("ii", $maDMMoi, $maDMCu);
        $stmt->execute();
        // Trả về số lượng sản phẩm đã được chuyển
        return $stmt->affected_rows;
    }

    // Xóa một danh mục

    public function xoaDanhMuc($id)
    {
        $id = intval($id);
        if ($id <= 0) return false;

        $stmt = $this->conn->prepare("DELETE FROM DanhMuc WHERE MaDM = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
