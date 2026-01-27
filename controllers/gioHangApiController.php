<?php

require_once __DIR__ . '/../includes/ketnoi.php';
header('Content-Type: application/json; charset=utf-8');

// Hàm phản hồi lỗi
function json_error($message)
{
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Hàm phản hồi thành công
function json_success($data, $message = '')
{
    $response = ['success' => true];
    if ($message) $response['message'] = $message;
    if (is_array($data)) $response = array_merge($response, $data);
    echo json_encode($response);
    exit;
}

if (!isset($_SESSION['IdTaiKhoan'])) {
    json_error('Bạn cần đăng nhập để sử dụng giỏ hàng.');
}

$idUser = $_SESSION['IdTaiKhoan'];

// Lấy MaGH (Mã Giỏ Hàng) của user, nếu chưa có thì tạo mới
$maGH = 0;
$stmt_get_gh = $conn->prepare("SELECT MaGH FROM GioHang WHERE IdTaiKhoan = ?");
$stmt_get_gh->bind_param("i", $idUser);
$stmt_get_gh->execute();
$result_gh = $stmt_get_gh->get_result();
if ($row_gh = $result_gh->fetch_assoc()) {
    $maGH = $row_gh['MaGH'];
} else {
    // Nếu user chưa có giỏ hàng, tạo mới
    $stmt_create_gh = $conn->prepare("INSERT INTO GioHang (IdTaiKhoan) VALUES (?)");
    $stmt_create_gh->bind_param("i", $idUser);
    if ($stmt_create_gh->execute()) {
        $maGH = $conn->insert_id;
    } else {
        json_error('Không thể tạo giỏ hàng mới.');
    }
    $stmt_create_gh->close();
}
$stmt_get_gh->close();
if ($maGH == 0) json_error('Lỗi không xác định được giỏ hàng.');

// Xác định hành động (action)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    // LẤY GIỎ HÀNG (GET)
    case 'get':
        $sql = "
            SELECT 
                ct.MaHH, 
                ct.SoLuong,
                h.TenHH,
                h.Gia,
                h.SoLuongHH as TonKho,
                (SELECT URL FROM HinhAnh WHERE MaHH = h.MaHH LIMIT 1) as AnhDaiDien
            FROM ChiTietGioHang ct
            JOIN HangHoa h ON ct.MaHH = h.MaHH
            WHERE ct.MaGH = ?
            ORDER BY ct.MaCTGH DESC
        ";
        $stmt_get = $conn->prepare($sql);
        $stmt_get->bind_param("i", $maGH);
        $stmt_get->execute();
        $items = $stmt_get->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_get->close();
        json_success(['items' => $items]);
        break;

    // THÊM VÀO GIỎ HÀNG

    case 'add':
        $maHH = intval($_POST['MaHH'] ?? 0);
        $soLuongThem = intval($_POST['SoLuong'] ?? 1); // Số lượng khách muốn thêm

        if ($maHH <= 0 || $soLuongThem <= 0) {
            json_error('Dữ liệu không hợp lệ.');
        }

        // Lấy thông tin Tồn kho hiện tại
        $stmt_check_stock = $conn->prepare("SELECT SoLuongHH FROM HangHoa WHERE MaHH = ?");
        $stmt_check_stock->bind_param("i", $maHH);
        $stmt_check_stock->execute();
        $res_stock = $stmt_check_stock->get_result();
        $product = $res_stock->fetch_assoc();
        $stmt_check_stock->close();

        if (!$product) {
            json_error('Sản phẩm không tồn tại.');
        }

        $tonKho = intval($product['SoLuongHH']);

        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        $stmt_check_cart = $conn->prepare("SELECT SoLuong FROM ChiTietGioHang WHERE MaGH = ? AND MaHH = ?");
        $stmt_check_cart->bind_param("ii", $maGH, $maHH);
        $stmt_check_cart->execute();
        $res_cart = $stmt_check_cart->get_result();
        $cartItem = $res_cart->fetch_assoc();
        $stmt_check_cart->close();

        // Tính toán và Validate
        if ($cartItem) {
            // Đã có trong giỏ -> Cộng dồn
            $soLuongTrongGio = intval($cartItem['SoLuong']);
            $tongSoLuong = $soLuongTrongGio + $soLuongThem;

            //Kiểm tra tổng 
            if ($tongSoLuong > $tonKho) {
                // Tính số lượng tối đa khách còn có thể thêm
                $coTheThem = $tonKho - $soLuongTrongGio;
                if ($coTheThem > 0) {
                    json_error("Trong giỏ bạn đã có $soLuongTrongGio. Kho chỉ còn $tonKho. Bạn chỉ có thể thêm tối đa $coTheThem sản phẩm nữa.");
                } else {
                    json_error("Bạn đã có $soLuongTrongGio sản phẩm trong giỏ hàng.");
                }
            }

            // Nếu OK -> Update
            $stmt_update = $conn->prepare("UPDATE ChiTietGioHang SET SoLuong = ? WHERE MaGH = ? AND MaHH = ?");
            $stmt_update->bind_param("iii", $tongSoLuong, $maGH, $maHH);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            // Chưa có trong giỏ thi  Thêm mới
            // Kiểm tra tồn kho với số lượng thêm
            if ($soLuongThem > $tonKho) {
                json_error("Số lượng yêu cầu vượt quá tồn kho (Chỉ còn $tonKho).");
            }

            $stmt_insert = $conn->prepare("INSERT INTO ChiTietGioHang (MaGH, MaHH, SoLuong) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("iii", $maGH, $maHH, $soLuongThem);
            $stmt_insert->execute();
            $stmt_insert->close();
        }

        json_success(null, 'Đã thêm sản phẩm vào giỏ hàng!');
        break;

    // CẬP NHẬT SỐ LƯỢNG (UPDATE)
    case 'update':
        $maHH = intval($_POST['MaHH'] ?? 0);
        $soLuong = intval($_POST['SoLuong'] ?? 1);
        if ($maHH <= 0 || $soLuong <= 0) json_error('Dữ liệu không hợp lệ.');

        // Kiểm tra tồn kho
        $stmt_check_stock = $conn->prepare("SELECT SoLuongHH FROM HangHoa WHERE MaHH = ?");
        $stmt_check_stock->bind_param("i", $maHH);
        $stmt_check_stock->execute();
        $tonKho = $stmt_check_stock->get_result()->fetch_assoc()['SoLuongHH'] ?? 0;
        $stmt_check_stock->close();
        if ($tonKho < $soLuong) $soLuong = $tonKho; // Tự động giảm nếu vượt tồn kho
        if ($soLuong <= 0) json_error('Sản phẩm đã hết hàng.');

        $stmt_update = $conn->prepare("UPDATE ChiTietGioHang SET SoLuong = ? WHERE MaGH = ? AND MaHH = ?");
        $stmt_update->bind_param("iii", $soLuong, $maGH, $maHH);
        $stmt_update->execute();
        $stmt_update->close();
        json_success(null, 'Cập nhật số lượng thành công.');
        break;

    // XÓA KHỎI GIỎ HÀNG (REMOVE)
    case 'remove':
        $maHH = intval($_POST['MaHH'] ?? 0);
        if ($maHH <= 0) json_error('Dữ liệu không hợp lệ.');

        $stmt_delete = $conn->prepare("DELETE FROM ChiTietGioHang WHERE MaGH = ? AND MaHH = ?");
        $stmt_delete->bind_param("ii", $maGH, $maHH);
        $stmt_delete->execute();
        $stmt_delete->close();
        json_success(null, 'Đã xóa sản phẩm khỏi giỏ hàng.');
        break;

    default:
        json_error('Hành động không hợp lệ.');
}
