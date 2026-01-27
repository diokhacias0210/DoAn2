<?php


session_start();
require_once __DIR__ . '/../includes/ketnoi.php';
header('Content-Type: application/json; charset=utf-8');

// Phản hồi lỗi và thoát
function error_response($message)
{
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    error_response('Bạn cần đăng nhập để thực hiện chức năng này.');
}

$idUser = $_SESSION['IdTaiKhoan'];
$action = $_POST['action'] ?? '';

if ($action === 'prepare_checkout') {
    $items_json = $_POST['items'] ?? '[]';
    $items = json_decode($items_json, true);

    if (empty($items) || !is_array($items)) {
        error_response('Không có sản phẩm nào được chọn.');
    }

    // Xử lý và xác thực dữ liệu
    $validated_items = [];
    $total_amount = 0;

    foreach ($items as $item) {
        $maHH = intval($item['MaHH'] ?? 0);
        $soLuong = intval($item['SoLuong'] ?? 0);

        if ($maHH > 0 && $soLuong > 0) {

            $sql = "SELECT TenHH, Gia, SoLuongHH, IdNguoiBan,
                    (SELECT URL FROM HinhAnh WHERE MaHH = HangHoa.MaHH LIMIT 1) AS AnhDaiDien 
                    FROM HangHoa WHERE MaHH = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $maHH);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($product = $result->fetch_assoc()) {
                // Kiểm tra lại tồn kho
                if ($soLuong > $product['SoLuongHH']) {
                    error_response("Sản phẩm '{$product['TenHH']}' không đủ số lượng tồn kho.");
                }

                // Thêm vào danh sách hợp lệ
                $validated_items[] = [
                    'MaHH' => $maHH,
                    'IdNguoiBan' => $product['IdNguoiBan'],
                    'TenHH' => $product['TenHH'],
                    'SoLuong' => $soLuong,
                    'Gia' => $product['Gia'],
                    'ThanhTien' => $soLuong * $product['Gia'],

                    'AnhDaiDien' => $product['AnhDaiDien']
                ];
                $total_amount += $soLuong * $product['Gia'];
            }
            $stmt->close();
        }
    }

    if (empty($validated_items)) {
        error_response('Không có sản phẩm hợp lệ nào được chọn.');
    }

    // Lưu thông tin giỏ hàng thanh toán vào session
    $_SESSION['checkout_cart'] = [
        'items' => $validated_items,
        'total_amount' => $total_amount
    ];

    echo json_encode(['success' => true, 'message' => 'Đã sẵn sàng để thanh toán.']);
    exit;
}

// Các action khác cho việc đặt hàng có thể được thêm ở đây
error_response('Hành động không hợp lệ.');
