<?php

session_start();
require_once __DIR__ . '/../includes/ketnoi.php';
header('Content-Type: application/json; charset=utf-8'); // Luôn trả về JSON

function send_json_error($message)
{
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// KIỂM TRA ĐĂNG NHẬP VÀ GIỎ HÀNG THANH TOÁN
if (!isset($_SESSION['IdTaiKhoan'])) {
    send_json_error('Bạn cần đăng nhập để đặt hàng.');
}
if (!isset($_SESSION['checkout_cart']) || empty($_SESSION['checkout_cart']['items'])) {
    send_json_error('Giỏ hàng thanh toán của bạn đang trống hoặc đã hết hạn.');
}

$idUser = $_SESSION['IdTaiKhoan'];
$checkout_cart = $_SESSION['checkout_cart'];
$cart_items = $checkout_cart['items']; // Lấy danh sách item từ session

//  LẤY DỮ LIỆU TỪ FORM POST
$payment_method = $_POST['PhuongThucTT'] ?? 'Tiền mặt';
$address = trim($_POST['DiaChiGiao'] ?? '');
$note = trim($_POST['GhiChu'] ?? '');

if (empty($address)) {
    send_json_error('Vui lòng chọn hoặc nhập địa chỉ giao hàng.');
}

// Danh sách MaHH cần xóa khỏi giỏ hàng thực tế sau khi đặt hàng
$maHH_to_remove_from_cart = array_column($cart_items, 'MaHH');

$conn->begin_transaction();
try {
    // 1. NHÓM SẢN PHẨM THEO NGƯỜI BÁN (IdNguoiBan)
    $orders_by_seller = [];
    foreach ($cart_items as $item) {
        $seller_id = $item['IdNguoiBan'];
        if (!isset($orders_by_seller[$seller_id])) {
            $orders_by_seller[$seller_id] = [
                'total' => 0,
                'items' => []
            ];
        }
        $orders_by_seller[$seller_id]['items'][] = $item;
        $orders_by_seller[$seller_id]['total'] += $item['ThanhTien'];
    }

    $ds_ma_don_hang = []; // Lưu các mã đơn hàng vừa tạo

    // 2. TẠO ĐƠN HÀNG CHO TỪNG NGƯỜI BÁN
    foreach ($orders_by_seller as $seller_id => $order_data) {
        $tongTienDonnay = $order_data['total'];

        // Tính phí sàn (Ví dụ 5% - Bạn có thể cấu hình số này)
        $phiSan = $tongTienDonnay * 0.05;
        $tienNguoiBanNhan = $tongTienDonnay - $phiSan;

        // Insert vào bảng DonHang (Lưu ý có thêm IdNguoiBan, PhiSan, TienNguoiBanNhan)
        $stmt = $conn->prepare("INSERT INTO DonHang (IdTaiKhoan, IdNguoiBan, DiaChiGiao, TongTien, PhiSan, TienNguoiBanNhan, GhiChu, TrangThai, NgayDat) VALUES (?, ?, ?, ?, ?, ?, ?, 'Chờ xử lý', NOW())");
        $stmt->bind_param("iisddds", $idUser, $seller_id, $address, $tongTienDonnay, $phiSan, $tienNguoiBanNhan, $note);

        if (!$stmt->execute()) {
            throw new Exception("Lỗi tạo đơn hàng cho Shop ID: " . $seller_id);
        }
        $maDH = $conn->insert_id;
        $ds_ma_don_hang[] = $maDH;
        $stmt->close();

        // Insert chi tiết đơn hàng
        $stmt_detail = $conn->prepare("INSERT INTO ChiTietDonHang (MaDH, MaHH, SoLuongSanPham, DonGia) VALUES (?, ?, ?, ?)");
        $stmt_update_stock = $conn->prepare("UPDATE HangHoa SET SoLuongHH = SoLuongHH - ? WHERE MaHH = ?");

        foreach ($order_data['items'] as $item) {
            // Lưu chi tiết
            $stmt_detail->bind_param("iiid", $maDH, $item['MaHH'], $item['SoLuong'], $item['Gia']);
            if (!$stmt_detail->execute()) {
                throw new Exception("Lỗi thêm chi tiết đơn hàng.");
            }

            // Trừ tồn kho
            $stmt_update_stock->bind_param("ii", $item['SoLuong'], $item['MaHH']);
            $stmt_update_stock->execute();
        }
        $stmt_detail->close();
        $stmt_update_stock->close();

        // Lưu thông tin thanh toán (Mỗi đơn hàng có 1 record thanh toán riêng)
        $stmt_payment = $conn->prepare("INSERT INTO ThanhToan (MaDH, SoTien, PhuongThuc, TrangThai) VALUES (?, ?, ?, 'Đang xử lý')");
        $stmt_payment->bind_param("ids", $maDH, $tongTienDonnay, $payment_method);
        $stmt_payment->execute();
        $stmt_payment->close();
    }

    // 3. XÓA GIỎ HÀNG (Logic cũ giữ nguyên)
    // ... (Copy đoạn code xóa giỏ hàng cũ của bạn vào đây) ...
    $maHH_to_remove_from_cart = array_column($cart_items, 'MaHH');
    $stmt_gh = $conn->prepare("SELECT MaGH FROM GioHang WHERE IdTaiKhoan = ?");
    $stmt_gh->bind_param("i", $idUser);
    $stmt_gh->execute();
    $res_gh = $stmt_gh->get_result();
    if ($row_gh = $res_gh->fetch_assoc()) {
        $maGH = $row_gh['MaGH'];
        if (!empty($maHH_to_remove_from_cart)) {
            $placeholders = implode(',', array_fill(0, count($maHH_to_remove_from_cart), '?'));
            $types = str_repeat('i', count($maHH_to_remove_from_cart));
            $stmt_clear = $conn->prepare("DELETE FROM ChiTietGioHang WHERE MaGH = ? AND MaHH IN ($placeholders)");
            // Bind params...
            $params = array_merge([$maGH], $maHH_to_remove_from_cart);
            $stmt_clear->bind_param("i" . $types, ...$params);
            $stmt_clear->execute();
            $stmt_clear->close();
        }
    }
    $stmt_gh->close();

    unset($_SESSION['checkout_cart']);
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Đặt hàng thành công! (' . count($ds_ma_don_hang) . ' đơn hàng được tạo)',
        'MaDH' => $ds_ma_don_hang[0] // Redirect về đơn đầu tiên hoặc trang lịch sử
    ]);
    exit;
} catch (Exception $exception) {
    $conn->rollback();
    send_json_error("Lỗi hệ thống: " . $exception->getMessage());
}
