<?php
session_start();
require_once '../includes/ketnoi.php';

if (!isset($_SESSION['IdTaiKhoan']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực']);
    exit;
}

$idKhachHang = (int)$_SESSION['IdTaiKhoan'];
$data = json_decode(file_get_contents('php://input'), true);
$categories = $data['categories'] ?? [];

// Nếu mảng rỗng (khách bấm Bỏ qua), gán mặc định là 9 (Khác)
if (empty($categories)) {
    $categories = [9];
}

$conn->begin_transaction();
try {
    foreach ($categories as $madm) {
        $madm = (int)$madm;
        // Tìm 1 sản phẩm nổi bật nhất của Danh mục này để làm đối tượng khảo sát
        $sql = "SELECT hh.MaHH FROM HangHoa hh 
                LEFT JOIN HanhVi_AI hv ON hh.MaHH = hv.MaHH
                WHERE hh.MaDM = $madm AND hh.TrangThaiDuyet = 'DaDuyet' AND hh.SoLuongHH > 0
                GROUP BY hh.MaHH ORDER BY SUM(hv.Diem) DESC, hh.NgayThem DESC LIMIT 1";
        $res = $conn->query($sql);

        if ($res && $res->num_rows > 0) {
            $mahh = (int)$res->fetch_assoc()['MaHH'];
            // Bơm 2 điểm (tương đương mức độ quan tâm ban đầu)
            $sql_insert = "INSERT INTO HanhVi_AI (IdTaiKhoan, MaHH, Diem) VALUES ($idKhachHang, $mahh, 2)
                           ON DUPLICATE KEY UPDATE Diem = GREATEST(Diem, 2)";
            $conn->query($sql_insert);
        }
    }
    $conn->commit();

    // Gọi Python train lại ngay lập tức (Timeout 1s để web không bị treo)
    $ch = curl_init("http://127.0.0.1:5000/retrain");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    @curl_exec($ch);
    curl_close($ch);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
