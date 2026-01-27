<?php

class AdminMaGiamGiaModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }


    //Tự động cập nhật mã hết hạn

    public function autoUpdateTrangThai()
    {
        $this->conn->query("
            UPDATE MaGiamGia
            SET TrangThai='Hết hạn'
            WHERE NgayKetThuc < NOW()
            AND TrangThai='Hoạt động'
        ");
    }

    public function timKiemMaGiamGia($keyword)
    {
        $keyword = '%' . $keyword . '%';
        $stmt = $this->conn->prepare("
        SELECT gg.*, GROUP_CONCAT(dm.TenDM SEPARATOR ', ') AS DanhMucApDung
        FROM MaGiamGia gg
        LEFT JOIN MaGiamGiaDanhMuc ggdm ON gg.MaGG = ggdm.MaGG
        LEFT JOIN DanhMuc dm ON ggdm.MaDM = dm.MaDM
        WHERE gg.Code LIKE ?
        GROUP BY gg.MaGG
        ORDER BY gg.MaGG DESC
    ");
        $stmt->bind_param("s", $keyword);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    //Lấy tất cả mã giảm giá

    public function getTatCaMaGiamGia()
    {
        $sql = "
            SELECT gg.*, GROUP_CONCAT(dm.TenDM SEPARATOR ', ') AS DanhMucApDung
            FROM MaGiamGia gg
            LEFT JOIN MaGiamGiaDanhMuc ggdm ON gg.MaGG = ggdm.MaGG
            LEFT JOIN DanhMuc dm ON ggdm.MaDM = dm.MaDM
            GROUP BY gg.MaGG
            ORDER BY gg.MaGG DESC
        ";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }


    //Lấy một mã giảm giá bằng ID

    public function getMaGiamGiaTheoId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM MaGiamGia WHERE MaGG=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    // Lấy danh sách ID danh mục đã được áp dụng cho một mã

    public function getDanhMucCuaMa($id)
    {
        $ids = [];
        $stmt = $this->conn->prepare("SELECT MaDM FROM MaGiamGiaDanhMuc WHERE MaGG=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res_dm = $stmt->get_result();
        while ($r = $res_dm->fetch_assoc()) {
            $ids[] = $r['MaDM'];
        }
        return $ids;
    }


    //Xóa một mã giảm giá

    public function xoaMaGiamGia($id)
    {
        // Xóa trong bảng trung gian MaGiamGiaDanhMuc trước
        $stmt = $this->conn->prepare("DELETE FROM MaGiamGiaDanhMuc WHERE MaGG = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Xóa trong bảng chính
        $stmt = $this->conn->prepare("DELETE FROM MaGiamGia WHERE MaGG = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }


    // Thêm mã giảm giá mới (bao gồm danh mục)

    public function themMaGiamGia($data, $danhMucIds)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO MaGiamGia (Code, MoTa, GiaTri, SoLuong, LoaiApDung, TrangThai, NgayBatDau, NgayKetThuc)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssdissss", $data['Code'], $data['MoTa'], $data['GiaTri'], $data['SoLuong'], $data['LoaiApDung'], $data['TrangThai'], $data['NgayBatDau'], $data['NgayKetThuc']);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $this->capNhatDanhMucChoMa($id, $danhMucIds);
            return true;
        }
        return false;
    }

    public function suaMaGiamGia($id, $data, $danhMucIds)
    {
        $stmt = $this->conn->prepare("
            UPDATE MaGiamGia
            SET Code=?, MoTa=?, GiaTri=?, SoLuong=?, LoaiApDung=?, TrangThai=?, NgayBatDau=?, NgayKetThuc=?
            WHERE MaGG=?
        ");
        $stmt->bind_param("ssdissssi", $data['Code'], $data['MoTa'], $data['GiaTri'], $data['SoLuong'], $data['LoaiApDung'], $data['TrangThai'], $data['NgayBatDau'], $data['NgayKetThuc'], $id);

        if ($stmt->execute()) {
            $this->capNhatDanhMucChoMa($id, $danhMucIds);
            return true;
        }
        return false;
    }

    private function capNhatDanhMucChoMa($idMa, $danhMucIds)
    {
        // Xóa tất cả danh mục cũ
        $stmt_del = $this->conn->prepare("DELETE FROM MaGiamGiaDanhMuc WHERE MaGG=?");
        $stmt_del->bind_param("i", $idMa);
        $stmt_del->execute();
        $stmt_del->close();

        // Thêm danh mục mới
        if (!empty($danhMucIds)) {
            $stmt_add = $this->conn->prepare("INSERT INTO MaGiamGiaDanhMuc (MaGG, MaDM) VALUES (?, ?)");
            foreach ($danhMucIds as $madm) {
                $madm_int = intval($madm);
                $stmt_add->bind_param("ii", $idMa, $madm_int);
                $stmt_add->execute();
            }
            $stmt_add->close();
        }
    }
}
