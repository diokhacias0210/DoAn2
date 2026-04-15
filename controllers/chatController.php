<?php
session_start();
$conn = new mysqli("localhost", "root", "", "doan2");
if ($conn->connect_error) { die("Lỗi kết nối CSDL"); }

require_once '../models/chatModel.php';
$chatModel = new chatModel($conn);

$action = $_REQUEST['action'] ?? 'index';
$idNguoiDung = isset($_SESSION['IdTaiKhoan']) ? (int)$_SESSION['IdTaiKhoan'] : 0;

if ($idNguoiDung <= 0) {
    die("Vui lòng đăng nhập để sử dụng tính năng Chat!");
}

switch ($action) {
    case 'tao_phong':
        $maHH = isset($_GET['MaHH']) ? (int)$_GET['MaHH'] : 0;
        if ($maHH <= 0) die("Mã sản phẩm không hợp lệ!");

        $idNguoiBan = $chatModel->layIdNguoiBanTuSanPham($maHH);
        if ($idNguoiBan === 0) die("Không tìm thấy người bán!");
        
        if ($idNguoiDung === $idNguoiBan) {
            echo "<script>alert('Bạn không thể tự chat với chính mình!'); history.back();</script>";
            exit();
        }

        $maPhong = $chatModel->kiemTraPhongTonTai($idNguoiDung, $idNguoiBan, $maHH);
        if ($maPhong == 0) {
            $maPhong = $chatModel->taoPhongChat($idNguoiDung, $idNguoiBan, $maHH);
        }

        header("Location: chatController.php?action=index&MaPhong=" . $maPhong);
        exit();
        break;

    case 'index':
        $maPhong = isset($_GET['MaPhong']) ? (int)$_GET['MaPhong'] : 0;
        
        $danhSachPhong = $chatModel->layDanhSachPhongCuaNguoiDung($idNguoiDung);
        
        $chiTietPhong = null;
        if ($maPhong > 0) {
            $chiTietPhong = $chatModel->layChiTietPhongChat($maPhong, $idNguoiDung);
            
            // ĐÁNH DẤU ĐÃ ĐỌC (bổ sung cho người mua)
            $sqlUpdate = "UPDATE TinNhan SET DaXem = 1 WHERE MaPhong = ? AND IdNguoiGui != ?";
            $stmtU = $conn->prepare($sqlUpdate);
            $stmtU->bind_param("ii", $maPhong, $idNguoiDung);
            $stmtU->execute();
        }

        require_once '../views/chat.php';
        break;

    case 'load':
        $maPhong = isset($_GET['MaPhong']) ? (int)$_GET['MaPhong'] : 0;
        if ($maPhong > 0) {
            $tinNhanList = $chatModel->layDanhSachTinNhan($maPhong);
            foreach ($tinNhanList as $tn) {
                $class = ($tn['IdNguoiGui'] == $idNguoiDung) ? 'msg sent' : 'msg received';
                $time = date('H:i', strtotime($tn['NgayGui']));
                
                $content = htmlspecialchars($tn['NoiDung']);
                $status = '';
                
                if ($tn['TrangThai'] == 1) {
                    $content = '<em style="color:#888;font-style:italic;">Tin nhắn đã được thu hồi</em>';
                } else if ($tn['DaChinhSua'] == 1) {
                    $status = ' <small style="color:#666;font-size:11px;">(đã sửa)</small>';
                }

                echo "<div class='$class' data-ma-tn='{$tn['MaTN']}'>";
                echo "    <div class='message-bubble'>";
                echo "        $content$status";
                echo "    </div>";
                echo "    <div class='message-time'>$time</div>";

                // Chỉ tin nhắn của mình + chưa thu hồi mới có nút hover
                // Chỉ hiện Icon nếu thời gian <= 10 phút VÀ tin nhắn chưa bị thu hồi
                if ($tn['IdNguoiGui'] == $idNguoiDung && $tn['TrangThai'] != 1) {
                    $phutDaQua = (time() - strtotime($tn['NgayGui'])) / 60;
                    if ($phutDaQua <= 10) {
                        echo "    <div class='message-actions'>";
                        echo "        <i class='fa-solid fa-pen' onclick='editMessage({$tn['MaTN']}); event.stopImmediatePropagation();' title='Sửa tin nhắn'></i>";
                        echo "        <i class='fa-solid fa-rotate-left' onclick='recallMessage({$tn['MaTN']}); event.stopImmediatePropagation();' title='Thu hồi tin nhắn'></i>";
                        echo "    </div>";
                    }
                }
                echo "</div>";
            }
        }
        break;

    case 'send':
        $maPhong = isset($_POST['MaPhong']) ? (int)$_POST['MaPhong'] : 0;
        $noiDung = isset($_POST['NoiDung']) ? trim($_POST['NoiDung']) : '';
        if ($maPhong > 0 && !empty($noiDung)) {
            $chatModel->themTinNhan($maPhong, $idNguoiDung, $noiDung);
        }
        break;

    // ==================== THÊM MỚI: SỬA & THU HỒI (GIỚI HẠN 10 PHÚT) ====================
    case 'edit':
        $maTN = isset($_POST['MaTN']) ? (int)$_POST['MaTN'] : 0;
        $noiDung = isset($_POST['NoiDung']) ? trim($_POST['NoiDung']) : '';
        if ($maTN > 0 && !empty($noiDung)) {
            // Kiểm tra xem tin nhắn đã gửi quá 10 phút chưa
            $checkTime = $conn->query("SELECT TIMESTAMPDIFF(MINUTE, NgayGui, NOW()) as PhutDaQua FROM TinNhan WHERE MaTN = $maTN");
            $phut = $checkTime->fetch_assoc()['PhutDaQua'] ?? 999;
            
            if ($phut > 1) {
                echo "QUATHAIGIAN"; // Trả về cờ báo lỗi
                exit;
            }
            
            $chatModel->suaTinNhan($maTN, $noiDung, $idNguoiDung);
            echo "OK";
        }
        break;

    case 'recall':
        $maTN = isset($_POST['MaTN']) ? (int)$_POST['MaTN'] : 0;
        if ($maTN > 0) {
            // Kiểm tra xem tin nhắn đã gửi quá 10 phút chưa
            $checkTime = $conn->query("SELECT TIMESTAMPDIFF(MINUTE, NgayGui, NOW()) as PhutDaQua FROM TinNhan WHERE MaTN = $maTN");
            $phut = $checkTime->fetch_assoc()['PhutDaQua'] ?? 999;
            
            if ($phut > 1) {
                echo "QUATHAIGIAN"; // Trả về cờ báo lỗi
                exit;
            }
            
            $chatModel->thuHoiTinNhan($maTN, $idNguoiDung);
            echo "OK";
        }
        break;

    default:
        echo "Lỗi: Không tìm thấy chức năng!";
        break;
}
?>