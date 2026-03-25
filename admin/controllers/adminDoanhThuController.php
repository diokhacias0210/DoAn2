<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminDoanhThuModel.php';
require_once __DIR__ . '/../../models/thongBaoModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$model = new AdminDoanhThuModel($conn);
$thongBaoModel = new ThongBaoModel($conn);
$idAdmin = $_SESSION['IdTaiKhoan'];

$message = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
$error = isset($_SESSION['err']) ? $_SESSION['err'] : '';
unset($_SESSION['msg'], $_SESSION['err']);

// --- XỬ LÝ ĐỔI PHÍ SÀN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_fee') {
    $newFee = floatval($_POST['phiSanMoi']);
    if ($newFee >= 0 && $newFee <= 100) {
        if ($model->updatePhiSan($newFee)) {
            $_SESSION['msg'] = "Đã cập nhật Phí sàn mới thành $newFee%!";

            // THÔNG BÁO CHO TẤT CẢ NGƯỜI BÁN
            $dsIdBan = $model->getTatCaIdNguoiBanActive();
            if (!empty($dsIdBan)) {
                $thongBaoModel->guiThongBaoNangCao(
                    "Sàn cập nhật Phí Hoa Hồng",
                    "Kể từ bây giờ, phí sàn áp dụng cho mỗi đơn hàng hoàn tất sẽ là <b>$newFee%</b>. Vui lòng cập nhật giá bán nếu cần thiết.",
                    "HeThong",
                    $idAdmin,
                    "custom",
                    $dsIdBan
                );
            }
        } else {
            $_SESSION['err'] = "Lỗi khi cập nhật phí sàn.";
        }
    }
    header("Location: adminDoanhThuController.php");
    exit;
}

// --- XỬ LÝ DUYỆT RÚT TIỀN ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $maYC = intval($_GET['id']);
    // Duyệt tất cả lệnh rút tiền đang chờ
    if ($_GET['action'] == 'approve_all') {
        $dsChoDuyet = $model->layDanhSachIdChoDuyet();
        if (!empty($dsChoDuyet)) {
            if ($model->duyetTatCaRutTien()) {
                $_SESSION['msg'] = "Đã duyệt Thành công TẤT CẢ các lệnh rút tiền đang chờ!";
                $thongBaoModel->guiThongBaoNangCao("Rút tiền thành công", "Lệnh rút tiền của bạn đã được Admin chuyển khoản thành công. Vui lòng kiểm tra ứng dụng ngân hàng.", "HeThong", $idAdmin, "custom", $dsChoDuyet);
            }
        } else {
            $_SESSION['err'] = "Không có lệnh rút tiền nào đang chờ duyệt.";
        }
        header("Location: adminDoanhThuController.php");
        exit;
    }

    if ($_GET['action'] == 'approve') {
        if ($model->duyetRutTien($maYC)) {
            $_SESSION['msg'] = "Đã chuyển trạng thái lệnh rút tiền #$maYC thành Thành công!";
            // Báo cho user biết tiền đã chuyển
            $idTaiKhoanYeuCau = $conn->query("SELECT IdTaiKhoan FROM YeuCauRutTien WHERE MaYC = $maYC")->fetch_assoc()['IdTaiKhoan'];
            $thongBaoModel->guiThongBaoNangCao("Rút tiền thành công", "Lệnh rút tiền của bạn đã được Admin chuyển khoản thành công. Vui lòng kiểm tra ứng dụng ngân hàng.", "HeThong", $idAdmin, "custom", [$idTaiKhoanYeuCau]);
        }
    } elseif ($_GET['action'] == 'reject' && isset($_GET['lydo'])) {
        $lyDo = trim(urldecode($_GET['lydo']));
        $kq = $model->tuChoiRutTien($maYC, $lyDo);
        if ($kq['status']) {
            $_SESSION['msg'] = "Đã từ chối lệnh rút #$maYC và hoàn lại tiền vào Số dư của Shop!";
            // Báo cho user biết bị từ chối
            $thongBaoModel->guiThongBaoNangCao("Từ chối lệnh rút tiền", "Lệnh rút số tiền <b>" . number_format($kq['SoTien'], 0, ',', '.') . "đ</b> của bạn đã bị từ chối với lý do: <i>$lyDo</i>. Tiền đã được hoàn lại vào Số dư ví của bạn.", "HeThong", $idAdmin, "custom", [$kq['IdTaiKhoan']]);
        } else {
            $_SESSION['err'] = "Lỗi khi hoàn tiền cho giao dịch này.";
        }
    }
    header("Location: adminDoanhThuController.php");
    exit;
}

// LẤY DỮ LIỆU HIỂN THỊ
$thongKe = $model->getThongKeTongQuan();
$phiSanHienTai = $model->getPhiSan();
$filter_rutTien = $_GET['trangthairut'] ?? '';
$dsRutTien = $model->getDanhSachRutTien($filter_rutTien);
$dsDoanhThu = $model->getDoanhThuNguoiBan();

include_once __DIR__ . '/../views/quanLyDoanhThu.php';
