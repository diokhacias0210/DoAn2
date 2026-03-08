<?php
session_start();
require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminBaoCaoModel.php';
require_once __DIR__ . '/../../models/thongBaoModel.php';

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] != 1) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$adminBaoCaoModel = new AdminBaoCaoModel($conn);
$thongBaoModel = new ThongBaoModel($conn);
$idAdmin = $_SESSION['IdTaiKhoan'];

$message = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';
$error = isset($_SESSION['err']) ? $_SESSION['err'] : '';
unset($_SESSION['msg'], $_SESSION['err']);

// XỬ LÝ QUYẾT ĐỊNH (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'process') {
    $maBC = intval($_POST['maBC']);
    $quyetDinh = $_POST['quyetDinh'];
    $ghiChuUser = trim($_POST['ghiChuUser']); // Lấy ghi chú của Admin để gửi cho user

    $baoCao = $adminBaoCaoModel->getBaoCaoById($maBC);

    if ($baoCao) {
        $adminBaoCaoModel->capNhatTrangThaiBaoCao($maBC, $quyetDinh);

        if ($quyetDinh == 'ViPham') {
            $idViPham = $baoCao['IdDoiTuongBiBaoCao'];
            $soGay = $adminBaoCaoModel->tangGayViPham($idViPham);

            $thongBaoViPham = "Tài khoản của bạn đã vi phạm chính sách. Lý do: <b>" . $baoCao['LyDoChinh'] . "</b>";
            if (!empty($ghiChuUser)) {
                $thongBaoViPham .= "<br><b>Ghi chú từ Admin:</b> <i>$ghiChuUser</i>";
            }

            if ($soGay == 1) {
                $thongBaoViPham .= "<br><br><b>CẢNH CÁO LẦN 1:</b> Hệ thống ghi nhận 1 gậy vi phạm. Vui lòng tuân thủ quy định.";
            } elseif ($soGay == 2) {
                $adminBaoCaoModel->khoaDangBai($idViPham);
                $thongBaoViPham .= "<br><br><b>CẢNH CÁO LẦN 2:</b> Bạn bị cấm đăng bán sản phẩm mới trong 7 ngày.";
            } elseif ($soGay >= 3) {
                $adminBaoCaoModel->khoaTaiKhoanVinhVien($idViPham);
                $thongBaoViPham .= "<br><br><b>CẢNH CÁO LẦN 3:</b> Tài khoản bán hàng của bạn đã <b>BỊ KHÓA VĨNH VIỄN</b>.";
            }

            if ($baoCao['LoaiBaoCao'] == 'SanPham' && $baoCao['MaHH'] != null) {
                $adminBaoCaoModel->anSanPhamViPham($baoCao['MaHH']);
                $thongBaoViPham .= "<br>Sản phẩm vi phạm đã bị gỡ bỏ khỏi hệ thống.";
            }

            $thongBaoModel->guiThongBaoNangCao("⚠️ Cảnh báo Vi Phạm (#$maBC)", $thongBaoViPham, 'ViPham', $idAdmin, 'custom', [$idViPham]);
            $thongBaoModel->guiThongBaoNangCao("Cập nhật Báo cáo (#$maBC)", "Cảm ơn bạn đã gửi báo cáo. Admin đã xác minh có vi phạm và xử lý.", 'BaoCao', $idAdmin, 'custom', [$baoCao['IdNguoiBaoCao']]);

            $_SESSION['msg'] = "Đã phạt vi phạm, tăng lên $soGay gậy!";
        } else {
            $thongBaoModel->guiThongBaoNangCao("Cập nhật Báo cáo (#$maBC)", "Báo cáo của bạn chưa đủ cơ sở để xử lý. Cảm ơn bạn.", 'BaoCao', $idAdmin, 'custom', [$baoCao['IdNguoiBaoCao']]);
            $_SESSION['msg'] = "Đã từ chối báo cáo (Không vi phạm).";
        }
    }
    header("Location: adminBaoCaoController.php");
    exit;
}

// XỬ LÝ THU HỒI HÌNH PHẠT (GET)
if (isset($_GET['action']) && $_GET['action'] == 'revoke' && isset($_GET['id'])) {
    $maBC = intval($_GET['id']);
    $baoCao = $adminBaoCaoModel->getBaoCaoById($maBC);

    if ($baoCao && $baoCao['TrangThai'] == 'ViPham') {
        if ($adminBaoCaoModel->thuHoiHinhPhat($maBC, $baoCao['IdDoiTuongBiBaoCao'], $baoCao['MaHH'])) {
            $thongBaoModel->guiThongBaoNangCao("Khôi phục tài khoản/sản phẩm", "Admin đã thu hồi quyết định phạt đối với báo cáo #$maBC. Số gậy vi phạm của bạn đã được giảm.", 'HeThong', $idAdmin, 'custom', [$baoCao['IdDoiTuongBiBaoCao']]);
            $_SESSION['msg'] = "Đã thu hồi hình phạt thành công! (Giảm 1 gậy, mở khóa nếu cần).";
        }
    }
    header("Location: adminBaoCaoController.php");
    exit;
}

//LẤY DỮ LIỆU ĐỂ HIỂN THỊ (CÓ LỌC)
$loaiFilter = $_GET['loai'] ?? '';
$trangThaiFilter = $_GET['trangThai'] ?? '';
$search = $_GET['search'] ?? '';

$dsBaoCao = $adminBaoCaoModel->getDanhSachBaoCao($loaiFilter, $trangThaiFilter, $search);
include_once __DIR__ . '/../views/quanLyBaoCao.php';
