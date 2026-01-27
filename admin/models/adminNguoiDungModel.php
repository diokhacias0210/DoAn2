<?php

class AdminNguoiDungModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Lấy tất cả người dùng, có thể lọc theo từ khóa
     * @param string $keyword Từ khóa để tìm kiếm (Tên, Email, SĐT)
     * @return array Danh sách người dùng
     */
    public function getTatCaNguoiDung($keyword = '')
    {
        if (!empty($keyword)) {
            // Nếu có từ khóa -> tìm kiếm
            $sql = "
                SELECT IdTaiKhoan, TenTK, Email, Sdt, VaiTro 
                FROM TaiKhoan 
                WHERE TenTK LIKE ? OR Email LIKE ? OR Sdt LIKE ?
                ORDER BY IdTaiKhoan DESC
            ";
            $stmt = $this->conn->prepare($sql);
            $searchTerm = "%" . $keyword . "%";
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            // Nếu không có từ khóa -> lấy tất cả
            $sql = "
                SELECT IdTaiKhoan, TenTK, Email, Sdt, VaiTro 
                FROM TaiKhoan 
                ORDER BY IdTaiKhoan DESC
            ";
            $res = $this->conn->query($sql);
        }

        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
