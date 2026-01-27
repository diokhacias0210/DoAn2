<?php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/chiTietDonHangModel.php';

if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];
$maDH = intval($_GET['MaDH'] ?? 0);

if ($maDH <= 0) {
    header("Location: lichSuDonHangController.php");
    exit;
}

try {
    // KHỞI TẠO MODEL VÀ LẤY DỮ LIỆU
    $model = new chiTietDonHangModel($conn);

    $order = $model->layThongTinDonHang($maDH, $idUser);

    // Nếu không tìm thấy đơn hàng, quay về lịch sử
    if (!$order) {
        header("Location: lichSuDonHangController.php");
        exit;
    }

    $items = $model->layChiTietSanPham($maDH);
    $payment = $model->layThongTinThanhToan($maDH);

    function mapTrangThaiToCssClass($trangThai)
    {
        switch ($trangThai) {
            case 'Chờ xử lý':
                return 'pending';
            case 'Đã xác nhận':
                return 'pending';
            case 'Đang giao':
                return 'shipping';
            case 'Hoàn tất':
                return 'completed';
            case 'Đã hủy':
                return 'cancelled';
            default:
                return 'pending';
        }
    }

    $trangThaiClass = mapTrangThaiToCssClass($order['TrangThai']);
} catch (Exception $e) {
    die("Đã có lỗi xảy ra: " . $e->getMessage());
}

include_once __DIR__ . '/../views/chiTietDonHang.php';
