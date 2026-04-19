<?php
require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/dangKyModel.php';

// Các biến này sẽ được truyền sang View
$errors = [];
$old = [];
$success = '';

$dangKyModel = new DangKyModel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $tentk = trim($_POST['tentk'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_Password = $_POST['confirm_Password'] ?? '';

    // Lấy dữ liệu địa chỉ và tọa độ
    $diachi = trim($_POST['diachi'] ?? '');
    $vido = !empty($_POST['ViDo']) ? (float)$_POST['ViDo'] : null;
    $kinhdo = !empty($_POST['KinhDo']) ? (float)$_POST['KinhDo'] : null;

    $old['tentk'] = $tentk;
    $old['email'] = $email;
    $old['phone'] = $phone;
    $old['diachi'] = $diachi;
    // $old['ViDo'] = $vido;
    // $old['KinhDo'] = $kinhdo;


    if (empty($tentk)) {
        $errors['username'] = 'Vui lòng nhập họ tên.';
    }
    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ.';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Vui lòng nhập số điện thoại.';
    }
    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu.';
    }
    if ($password !== $confirm_Password) {
        $errors['confirm_Password'] = 'Mật khẩu nhập lại không khớp.';
    }

    // Kiểm tra email đã tồn tại chưa 
    if (!$errors) {
        if ($dangKyModel->emailDaTonTai($email)) {
            $errors['email'] = 'Email đã được sử dụng.';
        }
    }

    // Check lỗi địa chỉ và tọa độ (Bắt buộc phải có cả 2)
    if (empty($diachi)) {
        $errors['diachi'] = 'Vui lòng nhập địa chỉ của bạn.';
    } elseif (empty($vido) || empty($kinhdo)) {
        $errors['diachi'] = 'Vui lòng nhấn nút "Tìm" hoặc kéo ghim trên bản đồ để xác nhận vị trí.';
    }

    if (!$errors) {
        if ($dangKyModel->emailDaTonTai($email)) {
            $errors['email'] = 'Email đã được sử dụng.';
        }
    }

    if (!$errors) {
        // GỌI HÀM VỚI CÁC BIẾN MỚI
        if ($dangKyModel->themTaiKhoan($tentk, $email, $phone, $password, $vido, $kinhdo, $diachi)) {
            $_SESSION['message'] = '<div class="alert alert-success">Đăng ký thành công! Hãy đăng nhập.</div>';
            header("Location: ../controllers/dangNhapController.php");
            exit;
        } else {
            $errors['general'] = 'Đã xảy ra lỗi hệ thống, vui lòng thử lại.';
        }
    }
}

include_once __DIR__ . '/../views/dangKy.php';
