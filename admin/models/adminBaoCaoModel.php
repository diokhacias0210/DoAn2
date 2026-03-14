<?php
class AdminBaoCaoModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Lấy danh sách tất cả báo cáo
    // Lấy danh sách tất cả báo cáo (Có kèm nội dung kháng cáo nếu có)
    public function getDanhSachBaoCao($loai = '', $trangThai = '', $search = '')
    {
        $sql = "SELECT bc.*, 
                nguoibc.TenTK as TenNguoiBaoCao, 
                nguoibi.TenTK as TenBiBaoCao, 
                nguoibi.DiemViPham,
                kc.NoiDung as NoiDungKhangCao
                FROM BaoCao bc
                JOIN TaiKhoan nguoibc ON bc.IdNguoiBaoCao = nguoibc.IdTaiKhoan
                JOIN TaiKhoan nguoibi ON bc.IdDoiTuongBiBaoCao = nguoibi.IdTaiKhoan
                LEFT JOIN KhangCao kc ON bc.MaBC = kc.MaBC
                WHERE 1=1 ";

        $params = [];
        $types = "";

        if (!empty($loai)) {
            $sql .= " AND bc.LoaiBaoCao = ?";
            $params[] = $loai;
            $types .= "s";
        }
        if (!empty($trangThai)) {
            $sql .= " AND bc.TrangThai = ?";
            $params[] = $trangThai;
            $types .= "s";
        }
        if (!empty($search)) {
            $sql .= " AND (nguoibc.TenTK LIKE ? OR nguoibi.TenTK LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= "ss";
        }

        $sql .= " ORDER BY CASE WHEN bc.TrangThai = 'ChoXuLy' THEN 1 ELSE 2 END, bc.NgayTao DESC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    //Lấy thông tin 1 báo cáo cụ thể
    public function getBaoCaoById($maBC)
    {
        $sql = "SELECT * FROM BaoCao WHERE MaBC = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maBC);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    //Cập nhật trạng thái Báo cáo
    public function capNhatTrangThaiBaoCao($maBC, $trangThai)
    {
        $sql = "UPDATE BaoCao SET TrangThai = ? WHERE MaBC = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $trangThai, $maBC);
        return $stmt->execute();
    }

    //Cộng 1 gậy vi phạm cho User
    public function tangGayViPham($idTaiKhoan)
    {
        $sql_up = "UPDATE TaiKhoan SET DiemViPham = DiemViPham + 1 WHERE IdTaiKhoan = ?";
        $stmt_up = $this->conn->prepare($sql_up);
        $stmt_up->bind_param("i", $idTaiKhoan);
        $stmt_up->execute();

        $sql_get = "SELECT DiemViPham FROM TaiKhoan WHERE IdTaiKhoan = ?";
        $stmt_get = $this->conn->prepare($sql_get);
        $stmt_get->bind_param("i", $idTaiKhoan);
        $stmt_get->execute();
        return $stmt_get->get_result()->fetch_assoc()['DiemViPham'];
    }

    //Khóa quyền đăng bài 7 ngày
    public function khoaDangBai($idTaiKhoan)
    {
        $sql = "UPDATE TaiKhoan SET HanKhoaTaiKhoan = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE IdTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTaiKhoan);
        return $stmt->execute();
    }

    //Khóa tài khoản vĩnh viễn
    public function khoaTaiKhoanVinhVien($idTaiKhoan)
    {
        $sql = "UPDATE TaiKhoan SET TrangThaiBanHang = 'BiKhoa' WHERE IdTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTaiKhoan);
        return $stmt->execute();
    }

    //Ẩn sản phẩm vi phạm
    public function anSanPhamViPham($maHH)
    {
        $sql = "UPDATE HangHoa SET HienThi = 0, TrangThaiDuyet = 'TuChoi' WHERE MaHH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHH);
        return $stmt->execute();
    }
    //thu hôi hình phạt
    public function thuHoiHinhPhat($maBC, $idBiBaoCao, $maHH)
    {
        $this->conn->begin_transaction();
        try {
            //Đổi báo cáo thành Không vi phạm
            $sql1 = "UPDATE BaoCao SET TrangThai = 'KhongViPham' WHERE MaBC = ?";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bind_param("i", $maBC);
            $stmt1->execute();

            // Trừ 1 gay vi phạm (không cho âm)
            $sql2 = "UPDATE TaiKhoan SET DiemViPham = GREATEST(0, DiemViPham - 1) WHERE IdTaiKhoan = ?";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bind_param("i", $idBiBaoCao);
            $stmt2->execute();

            // Nếu số gậy < 3 thì mở khóa tài khoản
            $sql3 = "UPDATE TaiKhoan SET TrangThaiBanHang = 'DangHoatDong' WHERE IdTaiKhoan = ? AND DiemViPham < 3 AND TrangThaiBanHang = 'BiKhoa'";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bind_param("i", $idBiBaoCao);
            $stmt3->execute();

            // Nếu là SP, mở lại SP
            if (!empty($maHH)) {
                $sql4 = "UPDATE HangHoa SET HienThi = 1 WHERE MaHH = ?";
                $stmt4 = $this->conn->prepare($sql4);
                $stmt4->bind_param("i", $maHH);
                $stmt4->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
