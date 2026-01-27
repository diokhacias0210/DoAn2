<?php
class DanhMuc
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Lấy 8 sản phẩm mới nhất
    public function getDanhMuc()
    {
        $sql = "SELECT MaDM,TenDM FROM DanhMuc";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
