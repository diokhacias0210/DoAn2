<?php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/TaiKhoanModel.php';

$model = new TaiKhoanModel($conn);

//  KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: dangNhapController.php");
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];
$message = '';


// --- XỬ LÝ UPLOAD AVATAR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cap_nhat_avatar') {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['avatar']['name']);
        $uploadDir = __DIR__ . '/../assets/images/avatars/';

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $destPath = $uploadDir . $fileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $duongDanDB = 'assets/images/avatars/' . $fileName;
            $model->updateAvatar($idUser, $duongDanDB);
            $_SESSION['message'] = '<div class="alert alert-success">Cập nhật ảnh đại diện thành công!</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Lỗi khi tải ảnh lên.</div>';
        }
        header("Location: thongTinTaiKhoanController.php");
        exit;
    }
}
// XỬ LÝ LOGIC POST (Thêm / Xóa / Đặt mặc định địa chỉ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'them_diachi') {
        $diaChiMoi = trim($_POST['diachi_moi']);
        $viDoMoi = !empty($_POST['ViDo_moi']) ? (float)$_POST['ViDo_moi'] : null;
        $kinhDoMoi = !empty($_POST['KinhDo_moi']) ? (float)$_POST['KinhDo_moi'] : null;

        $result = $model->themDiaChi($idUser, $diaChiMoi, $viDoMoi, $kinhDoMoi);

        $redirect = $_POST['redirect'] ?? '';
        if ($result === "max") {
            $_SESSION['message'] = '<div class="alert alert-danger">Bạn chỉ có thể thêm tối đa 5 địa chỉ.</div>';
        } elseif ($result === "ok") {
            $_SESSION['message'] = '<div class="alert alert-success">Đã thêm địa chỉ mới thành công!</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-warning">Thêm địa chỉ thất bại.</div>';
        }

        if ($redirect === 'thanhtoan') {
            header("Location: thanhToanController.php");
        } else {
            header("Location: thongTinTaiKhoanController.php");
        }
        exit;
    } elseif ($action === 'xoa_diachi') {
        $result = $model->xoaDiaChi($idUser, (int)$_POST['MaDC_xoa']);
        if ($result === "default") {
            $message = '<div class="alert alert-warning">Không thể xóa địa chỉ mặc định.</div>';
        } elseif ($result === "ok") {
            header("Location: thongTinTaiKhoanController.php?status=delete_success");
            exit;
        } else {
            $message = '<div class="alert alert-danger">Lỗi khi xóa hoặc địa chỉ không tồn tại.</div>';
        }
    } elseif ($action === 'dat_mac_dinh') {
        $maDC = (int)$_POST['MaDC'];

        // Gọi hàm bên model
        if ($model->setDiaChiMacDinh($idUser, $maDC)) {
            header("Location: thongTinTaiKhoanController.php?status=set_default_success");
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Có lỗi xảy ra khi đặt địa chỉ mặc định.</div>';
            header("Location: thongTinTaiKhoanController.php");
        }
        exit;
    }
}

// XỬ LÝ THÔNG BÁO TỪ URL 
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'add_success') {
        $message = '<div class="alert alert-success">Đã thêm địa chỉ mới thành công!</div>';
    } elseif ($_GET['status'] === 'delete_success') {
        $message = '<div class="alert alert-success">Đã xóa địa chỉ thành công!</div>';
    } elseif ($_GET['status'] === 'set_default_success') {
        // Thông báo khi đặt mặc định thành công
        $message = '<div class="alert alert-success">Đã thay đổi địa chỉ mặc định thành công!</div>';
    }
}

// LẤY DỮ LIỆU ĐỂ HIỂN THỊ
$user = $model->getThongTin($idUser);
$addresses = $model->getDanhSachDiaChi($idUser);
$address_count = count($addresses);

include_once __DIR__ . '/../views/thongTinTaiKhoan.php';
