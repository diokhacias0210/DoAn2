<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminSanPhamModel.php';
require_once __DIR__ . '/../models/adminDanhMucModel.php';
require_once __DIR__ . '/../../models/thongBaoModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$sanPhamModel = new AdminSanPhamModel($conn);
$danhMucModel = new AdminDanhMucModel($conn);
$thongBaoModel = new ThongBaoModel($conn);
$idAdmin = $_SESSION['IdTaiKhoan'];
$message = '';

try {
    // ---- XÓA SẢN PHẨM ----
    if (isset($_GET['xoa'])) {
        $id = (int)$_GET['xoa'];
        $conn->begin_transaction();
        if ($sanPhamModel->xoaSanPham($id)) {
            $conn->commit();
            $_SESSION['msg'] = "Đã xóa sản phẩm!";
        } else {
            $conn->rollback();
            $_SESSION['err'] = "Lỗi khi xóa sản phẩm.";
        }
        header("Location: adminSanPhamController.php");
        exit;
    }

    // ---- DUYỆT TẤT CẢ (HÀNG LOẠT) ----
    if (isset($_GET['action']) && $_GET['action'] == 'duyet_tat_ca') {
        $pendingProducts = $sanPhamModel->getTatCaSanPhamChoDuyet();
        if (count($pendingProducts) > 0) {
            $sanPhamModel->duyetTatCaSanPham();

            // Gửi thông báo cho từng người bán
            foreach ($pendingProducts as $sp) {
                $thongBaoModel->guiThongBaoNangCao("🎉 Sản phẩm được duyệt", "Sản phẩm <b>{$sp['TenHH']}</b> của bạn đã được admin duyệt và đang hiển thị trên sàn.", 'HeThong', $idAdmin, 'custom', [$sp['IdNguoiBan']]);
            }
            $_SESSION['msg'] = 'Đã duyệt thành công ' . count($pendingProducts) . ' sản phẩm!';
        } else {
            $_SESSION['err'] = 'Không có sản phẩm nào đang chờ duyệt.';
        }
        header("Location: adminSanPhamController.php");
        exit;
    }

    // ---- DUYỆT, TỪ CHỐI, ẨN, HIỆN (ĐƠN LẺ) ----
    if (isset($_GET['action']) && in_array($_GET['action'], ['duyet', 'tuchoi', 'an', 'hien']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $id = (int)$_GET['id'];

        $spInfo = $sanPhamModel->getSanPhamTheoId($id);
        if ($spInfo) {
            $idSeller = $spInfo['IdNguoiBan'];
            $tenSP = $spInfo['TenHH'];

            if ($action === 'duyet') {
                $sanPhamModel->duyetSanPham($id, 'DaDuyet');
                $thongBaoModel->guiThongBaoNangCao("🎉 Sản phẩm được duyệt", "Sản phẩm <b>$tenSP</b> của bạn đã được duyệt và đang hiển thị.", 'HeThong', $idAdmin, 'custom', [$idSeller]);
                $_SESSION['msg'] = 'Đã duyệt sản phẩm!';
            } elseif ($action === 'tuchoi') {
                // Lấy lý do từ chối từ URL (nếu có), mặc định là 'Không phù hợp'
                $lyDo = isset($_GET['lydo']) ? trim($_GET['lydo']) : 'Vi phạm tiêu chuẩn cộng đồng hoặc thông tin không rõ ràng.';
                $sanPhamModel->duyetSanPham($id, 'TuChoi', $lyDo);
                $thongBaoModel->guiThongBaoNangCao("❌ Sản phẩm bị từ chối", "Sản phẩm <b>$tenSP</b> không được phê duyệt. <br><b>Lý do:</b> $lyDo", 'ViPham', $idAdmin, 'custom', [$idSeller]);
                $_SESSION['msg'] = 'Đã từ chối sản phẩm!';
            } elseif ($action === 'an') {
                $sanPhamModel->capNhatHienThi($id, 0);
                $thongBaoModel->guiThongBaoNangCao("⚠️ Sản phẩm bị ẩn", "Admin đã tạm ẩn sản phẩm <b>$tenSP</b> khỏi trang chủ để kiểm tra.", 'HeThong', $idAdmin, 'custom', [$idSeller]);
                $_SESSION['msg'] = 'Đã ẩn sản phẩm!';
            } elseif ($action === 'hien') {
                $sanPhamModel->capNhatHienThi($id, 1);
                $thongBaoModel->guiThongBaoNangCao("✅ Khôi phục hiển thị", "Sản phẩm <b>$tenSP</b> đã được mở lại và hiển thị bình thường.", 'HeThong', $idAdmin, 'custom', [$idSeller]);
                $_SESSION['msg'] = 'Đã cho phép hiển thị lại!';
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'adminSanPhamController.php';
        header("Location: $referer");
        exit;
    }
} catch (Exception $e) {
    if (isset($conn) && $conn->connect_error == null && !$conn->autocommit(true)) $conn->rollback();
    $_SESSION['err'] = "❌ " . $e->getMessage();
    header("Location: adminSanPhamController.php");
    exit;
}

// Xử lý Alert
if (isset($_SESSION['msg'])) {
    $message = '<div class="alert alert-success alert-dismissible fade show shadow-sm" style="position:fixed; top:20px; right:20px; z-index:9999;">' . $_SESSION['msg'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['msg']);
}
if (isset($_SESSION['err'])) {
    $message = '<div class="alert alert-danger alert-dismissible fade show shadow-sm" style="position:fixed; top:20px; right:20px; z-index:9999;">' . $_SESSION['err'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['err']);
}

// ---- LẤY DỮ LIỆU HIỂN THỊ ----
$keyword = $_GET['search'] ?? '';
$filter_dm = $_GET['madm'] ?? '';
$filter_nguoiban = $_GET['idnguoiban'] ?? '';
$filter_tinhtrang = $_GET['tinhtrang'] ?? '';
$filter_duyet = $_GET['trangthaiduyet'] ?? '';
$sort = $_GET['sort'] ?? 'new';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_records = $sanPhamModel->countTatCaSanPham($keyword, $filter_dm, $filter_nguoiban, $filter_tinhtrang, $filter_duyet);
$total_pages = ceil($total_records / $limit);

$danhSachSanPham = $sanPhamModel->getTatCaSanPham($keyword, $filter_dm, $filter_nguoiban, $filter_tinhtrang, $filter_duyet, $sort, $limit, $offset);
$danhSachDanhMuc = $danhMucModel->getTatCaDanhMuc();
$danhSachNguoiBan = $sanPhamModel->layDanhSachNguoiBan();

include_once __DIR__ . '/../views/quanLySanPham.php';
