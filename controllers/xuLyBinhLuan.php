<?php

session_start();
include_once __DIR__ . '/../includes/ketnoi.php';
include_once __DIR__ . '/../models/danhGiaModel.php';
include_once __DIR__ . '/../models/TaiKhoanModel.php';

header('Content-Type: application/json');

function json_error($message)
{
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

//  KIỂM TRA CHUNG
if (!isset($_SESSION['IdTaiKhoan'])) {
    json_error('Vui lòng đăng nhập để đánh giá');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Phương thức không hợp lệ');
}

//  VALIDATE INPUT
$idTaiKhoan = $_SESSION['IdTaiKhoan'];
$maHH = isset($_POST['maHH']) ? intval($_POST['maHH']) : 0;
$noiDung = isset($_POST['noiDung']) ? trim($_POST['noiDung']) : '';
$soSao = isset($_POST['soSao']) ? intval($_POST['soSao']) : 0;

if ($maHH <= 0) json_error('Sản phẩm không hợp lệ');
if (empty($noiDung)) json_error('Vui lòng nhập nội dung bình luận');
if ($soSao < 1 || $soSao > 5) json_error('Số sao phải từ 1 đến 5');

try {
    //  GỌI MODEL ĐỂ XỬ LÝ LOGIC
    $model = new DanhGiaModel($conn);
    $result = $model->themDanhGiaVaBinhLuan($idTaiKhoan, $maHH, $soSao, $noiDung);

    if (!$result['success']) {
        json_error($result['message']);
    }

    //  THÀNH CÔNG -> LẤY TÊN USER ĐỂ TRẢ VỀ
    $tkModel = new TaiKhoanModel($conn);
    $user = $tkModel->getUserById($idTaiKhoan);
    echo json_encode([
        'success' => true,
        'message' => $result['message'],
        'data' => [
            'tenTK' => $user['TenTK'] ?? 'Người dùng',
            'noiDung' => $noiDung,
            'soSao' => $soSao,
            'ngayBL' => date('d/m/Y H:i')
        ]
    ]);
} catch (Exception $e) {
    error_log('Lỗi xử lý bình luận: ' . $e->getMessage());
    json_error('Có lỗi hệ thống xảy ra: ' . $e->getMessage());
}

if (isset($conn)) {
    $conn->close();
}
