<?php
class BaoCaoModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function guiBaoCao($idNguoiBaoCao, $idBiBaoCao, $maHH, $loaiBaoCao, $lyDoChinh, $chiTiet)
    {
        $this->conn->begin_transaction();
        try {
            //Thêm báo cáo vào CSDL
            $sql = "INSERT INTO BaoCao (IdNguoiBaoCao, IdDoiTuongBiBaoCao, MaHH, LoaiBaoCao, LyDoChinh, ChiTiet) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiisss", $idNguoiBaoCao, $idBiBaoCao, $maHH, $loaiBaoCao, $lyDoChinh, $chiTiet);
            $stmt->execute();

            //tự động: Nếu là báo cáo sản phẩm
            if ($loaiBaoCao == 'SanPham' && $maHH != null) {
                // Đếm số người KHÁC NHAU báo cáo sản phẩm này trong 24h qua
                $sql_count = "SELECT COUNT(DISTINCT IdNguoiBaoCao) as SoNguoi 
                              FROM BaoCao 
                              WHERE MaHH = ? AND NgayTao >= NOW() - INTERVAL 1 DAY";
                $stmt_count = $this->conn->prepare($sql_count);
                $stmt_count->bind_param("i", $maHH);
                $stmt_count->execute();
                $count = $stmt_count->get_result()->fetch_assoc()['SoNguoi'];

                // Nếu >= 3 người báo cáo thì Tạm ẩn sản phẩm
                if ($count >= 3) {
                    $sql_hide = "UPDATE HangHoa SET HienThi = 0 WHERE MaHH = ?";
                    $stmt_hide = $this->conn->prepare($sql_hide);
                    $stmt_hide->bind_param("i", $maHH);
                    $stmt_hide->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
