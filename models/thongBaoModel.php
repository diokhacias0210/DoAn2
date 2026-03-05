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
    public function getTatCaThongBao($loaiFilter = '', $doiTuongFilter = '')
    {
        // Dùng LEFT JOIN để đếm số lượng người nhận và lấy tên nếu gửi riêng lẻ
        $sql = "SELECT tb.*, 
                       COUNT(tbn.IdNhan) as SoNguoiNhan, 
                       MIN(tk.TenTK) as TenNguoiNhan 
                FROM ThongBao tb 
                LEFT JOIN ThongBaoNguoiDung tbn ON tb.MaTB = tbn.MaTB 
                LEFT JOIN TaiKhoan tk ON tbn.IdNhan = tk.IdTaiKhoan 
                WHERE 1=1 ";

        $params = [];
        $types = "";

        // Lọc theo loại thông báo
        if (!empty($loaiFilter)) {
            $sql .= " AND tb.LoaiTB = ? ";
            $params[] = $loaiFilter;
            $types .= "s";
        }

        $sql .= " GROUP BY tb.MaTB ";

        // Lọc theo đối tượng nhận
        if ($doiTuongFilter === 'all') {
            $sql .= " HAVING SoNguoiNhan > 1 "; // Gửi cho nhiều người
        } elseif ($doiTuongFilter === 'single') {
            $sql .= " HAVING SoNguoiNhan = 1 "; // Gửi cho 1 người
        }

        $sql .= " ORDER BY tb.NgayTao DESC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    // Gửi thông báo chung cho tất cả hoặc theo nhóm
    public function guiThongBaoNangCao($tieuDe, $noiDung, $loaiTB, $idAdmin, $cheDo, $danhSachId = [])
    {
        $this->conn->begin_transaction();
        try {
            // Thêm vào bảng ThongBao gốc
            $sql = "INSERT INTO ThongBao (TieuDe, NoiDung, LoaiTB, NguoiGui, NgayTao) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $tieuDe, $noiDung, $loaiTB, $idAdmin);
            $stmt->execute();
            $maTB = $this->conn->insert_id;

            // Map vào bảng ThongBaoNguoiDung tùy theo chế độ
            if ($cheDo == 'custom' && !empty($danhSachId)) {
                // Gửi cho 1 danh sách người được chọn cụ thể
                $sql_map = "INSERT INTO ThongBaoNguoiDung (MaTB, IdNhan) VALUES (?, ?)";
                $stmt_map = $this->conn->prepare($sql_map);
                foreach ($danhSachId as $idNhan) {
                    $id = intval($idNhan);
                    $stmt_map->bind_param("ii", $maTB, $id);
                    $stmt_map->execute();
                }
            } else {
                // Gửi theo nhóm
                $sql_cond = "VaiTro = 0"; // Mặc định: Tất cả hệ thống (Trừ admin)

                if ($cheDo == 'all_sellers') {
                    $sql_cond = "VaiTro = 0 AND TrangThaiBanHang = 'DangHoatDong'"; // Chỉ người bán
                } elseif ($cheDo == 'all_users') {
                    $sql_cond = "VaiTro = 0 AND TrangThaiBanHang != 'DangHoatDong'"; // Chỉ người mua
                }

                $sql_map = "INSERT INTO ThongBaoNguoiDung (MaTB, IdNhan) SELECT ?, IdTaiKhoan FROM TaiKhoan WHERE $sql_cond";
                $stmt_map = $this->conn->prepare($sql_map);
                $stmt_map->bind_param("i", $maTB);
                $stmt_map->execute();
            }

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
