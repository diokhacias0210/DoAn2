<?php
session_start();
require_once '../includes/ketnoi.php'; 

if (isset($_POST['mahh']) && isset($_SESSION['IdTaiKhoan'])) {
    $mahh = intval($_POST['mahh']);
    $idUser = intval($_SESSION['IdTaiKhoan']);

    // Ghi nhận 1 sao (tương tác Xem) và để TrangThai='Ẩn'
    // Điều này giúp Python hiểu là user đã tương tác, từ đó loại món đồ này ra khỏi gợi ý lần sau
    $sql = "INSERT INTO HanhVi_AI (IdTaiKhoan, MaHH, Diem) 
            VALUES ($idUser, $mahh, 1) 
            ON DUPLICATE KEY UPDATE Diem = 'Diem'";

    $conn->query($sql);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi xác thực']);
}
