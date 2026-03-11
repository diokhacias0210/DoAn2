<?php
session_start();
// Khởi tạo kết nối CSDL (Hoặc require file ketnoi.php của bạn vào đây)
$conn = new mysqli("localhost", "root", "", "doan2");
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

// Require file Model vào để sử dụng
require_once '../models/chatModel.php';
$chatModel = new chatModel($conn);

// Lấy action và thông tin người dùng
$action = $_REQUEST['action'] ?? 'index';
$idNguoiDung = isset($_SESSION['IdTaiKhoan']) ? (int)$_SESSION['IdTaiKhoan'] : 0;

if ($idNguoiDung <= 0) {
    die("Vui lòng đăng nhập để sử dụng tính năng Chat!");
}

switch ($action) {
    // 1. CHỨC NĂNG TẠO PHÒNG (Khi bấm nút "Chat với người bán" ở trang Sản phẩm)
    case 'tao_phong':
        $maHH = isset($_GET['MaHH']) ? (int)$_GET['MaHH'] : 0;
        if ($maHH <= 0) die("Mã sản phẩm không hợp lệ!");

        $idNguoiBan = $chatModel->layIdNguoiBanTuSanPham($maHH);
        if ($idNguoiBan === 0) die("Không tìm thấy người bán!");
        
        // Tránh trường hợp người mua tự chat với chính sản phẩm của mình
        if ($idNguoiDung === $idNguoiBan) {
            echo "<script>alert('Bạn không thể tự chat với chính mình!'); history.back();</script>";
            exit();
        }

        // Kiểm tra xem phòng chat giữa 2 người về sản phẩm này đã tồn tại chưa
        $maPhong = $chatModel->kiemTraPhongTonTai($idNguoiDung, $idNguoiBan, $maHH);
        
        // Nếu chưa có thì tạo phòng mới
        if ($maPhong == 0) {
            $maPhong = $chatModel->taoPhongChat($idNguoiDung, $idNguoiBan, $maHH);
        }

        // QUAN TRỌNG: Chuyển hướng sang giao diện chat và truyền MaPhong lên URL
        header("Location: chatController.php?action=index&MaPhong=" . $maPhong);
        exit();
        break;

    // 2. CHỨC NĂNG HIỂN THỊ GIAO DIỆN CHAT (Mở file View)
    case 'index':
        $maPhong = isset($_GET['MaPhong']) ? (int)$_GET['MaPhong'] : 0;
        
        // Gọi model lấy dữ liệu danh sách phòng bên cột trái
        $danhSachPhong = $chatModel->layDanhSachPhongCuaNguoiDung($idNguoiDung);
        
        // Lấy chi tiết phòng chat hiện tại để hiển thị ở khung bên phải
        $chiTietPhong = null;
        if ($maPhong > 0) {
            $chiTietPhong = $chatModel->layChiTietPhongChat($maPhong, $idNguoiDung);
        }

        // Load file View giao diện (Giả sử file chat.php nằm trong thư mục views)
        require_once '../views/chat.php';
        break;

    // 3. API - LOAD TIN NHẮN (Dùng cho AJAX gọi ngầm)
    case 'load':
        $maPhong = isset($_GET['MaPhong']) ? (int)$_GET['MaPhong'] : 0;
        if ($maPhong > 0) {
            $tinNhanList = $chatModel->layDanhSachTinNhan($maPhong);
            foreach ($tinNhanList as $tn) {
                // View nhỏ gọn được render ngay tại Controller (Dành cho API)
                $class = ($tn['IdNguoiGui'] == $idNguoiDung) ? 'msg sent' : 'msg received';
                echo "<div class='$class'>" . htmlspecialchars($tn['NoiDung']) . "</div>";
            }
        }
        break;

    // 4. API - GỬI TIN NHẮN (Dùng cho AJAX gọi ngầm)
    case 'send':
        $maPhong = isset($_POST['MaPhong']) ? (int)$_POST['MaPhong'] : 0;
        $noiDung = isset($_POST['NoiDung']) ? trim($_POST['NoiDung']) : '';
        
        if ($maPhong > 0 && !empty($noiDung)) {
            $chatModel->themTinNhan($maPhong, $idNguoiDung, $noiDung);
        }
        break;
        
    default:
        echo "Lỗi: Không tìm thấy chức năng!";
        break;
}
?>