<?php
class ThongBaoModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // CÁC HÀM DÀNH CHO ADMIN

    // Lấy tất cả thông báo Admin đã tạo
    public function getTatCaThongBao()
    {
        $sql = "SELECT * FROM ThongBao ORDER BY NgayTao DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    //Gửi thông báo cho TẤT CẢ người dùng
    public function guiThongBaoChung($tieuDe, $noiDung, $loaiTB, $idAdmin)
    {
        $this->conn->begin_transaction();
        try {
            // Thêm vào bảng ThongBao
            $sql = "INSERT INTO ThongBao (TieuDe, NoiDung, LoaiTB, NguoiGui, NgayTao) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $tieuDe, $noiDung, $loaiTB, $idAdmin);
            $stmt->execute();

            $maTB = $this->conn->insert_id;

            // Thêm vào ThongBaoNguoiDung cho TẤT CẢ tài khoản (trừ Admin)
            $sql_mapping = "INSERT INTO ThongBaoNguoiDung (MaTB, IdNhan) 
                            SELECT ?, IdTaiKhoan FROM TaiKhoan WHERE VaiTro = 0";
            $stmt_mapping = $this->conn->prepare($sql_mapping);
            $stmt_mapping->bind_param("i", $maTB);
            $stmt_mapping->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    // Gửi thông báo cho MỘT người dùng cụ thể
    public function guiThongBaoRieng($idNhan, $tieuDe, $noiDung, $loaiTB, $idAdmin)
    {
        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO ThongBao (TieuDe, NoiDung, LoaiTB, NguoiGui, NgayTao) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $tieuDe, $noiDung, $loaiTB, $idAdmin);
            $stmt->execute();

            $maTB = $this->conn->insert_id;

            $sql_mapping = "INSERT INTO ThongBaoNguoiDung (MaTB, IdNhan) VALUES (?, ?)";
            $stmt_mapping = $this->conn->prepare($sql_mapping);
            $stmt_mapping->bind_param("ii", $maTB, $idNhan);
            $stmt_mapping->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Xóa thông báo
    public function xoaThongBao($maTB)
    {
        $sql = "DELETE FROM ThongBao WHERE MaTB = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTB);
        return $stmt->execute();
    }

    // CÁC HÀM DÀNH CHO USER
    public function getThongBaoCuaToi($idTaiKhoan)
    {
        $sql = "SELECT tb.*, tbn.DaXem, tbn.Id 
                FROM ThongBao tb 
                JOIN ThongBaoNguoiDung tbn ON tb.MaTB = tbn.MaTB 
                WHERE tbn.IdNhan = ? 
                ORDER BY tb.NgayTao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function demThongBaoChuaDoc($idTaiKhoan)
    {
        $sql = "SELECT COUNT(*) as SoLuong FROM ThongBaoNguoiDung WHERE IdNhan = ? AND DaXem = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['SoLuong'];
    }

    public function danhDauDaDoc($idTaiKhoan, $maTB)
    {
        $sql = "UPDATE ThongBaoNguoiDung SET DaXem = 1, NgayXem = NOW() WHERE IdNhan = ? AND MaTB = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $idTaiKhoan, $maTB);
        return $stmt->execute();
    }
}
