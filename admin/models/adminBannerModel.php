<?php
class BannerModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllBanners() {
        $sql = "SELECT * FROM Banner ORDER BY NgayTao DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getVisibleBanners() {
        $sql = "SELECT * FROM Banner WHERE TrangThai = 'HienThi' ORDER BY NgayTao DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getBannerById($id) {
        $sql = "SELECT * FROM Banner WHERE MaBanner = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Đã bỏ $lienKet, chỉ thêm TieuDe, HinhAnh, TrangThai
    public function addBanner($tieuDe, $hinhAnh, $trangThai) {
        $sql = "INSERT INTO Banner (TieuDe, HinhAnh, TrangThai) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $tieuDe, $hinhAnh, $trangThai);
        return $stmt->execute();
    }

    // Đã bỏ $lienKet
    public function updateBanner($id, $tieuDe, $trangThai, $hinhAnh = null) {
        if ($hinhAnh) {
            $sql = "UPDATE Banner SET TieuDe=?, HinhAnh=?, TrangThai=? WHERE MaBanner=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $tieuDe, $hinhAnh, $trangThai, $id);
        } else {
            // Nếu không upload ảnh mới thì giữ nguyên ảnh cũ
            $sql = "UPDATE Banner SET TieuDe=?, TrangThai=? WHERE MaBanner=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $tieuDe, $trangThai, $id);
        }
        return $stmt->execute();
    }

    public function deleteBanner($id) {
        $sql = "DELETE FROM Banner WHERE MaBanner = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}