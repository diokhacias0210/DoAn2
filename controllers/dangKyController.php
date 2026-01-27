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

    // Giữ lại giá trị cũ để hiển thị lại trên form nếu lỗi
    $old['tentk'] = $tentk;
    $old['email'] = $email;
    $old['phone'] = $phone;


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

    // Thêm tài khoản m
    if (!$errors) {
        // Model sẽ tự động hash mật khẩu
        if ($dangKyModel->themTaiKhoan($tentk, $email, $phone, $password)) {
            $success = "Đăng ký thành công! Bạn có thể đăng nhập.";
            $old = [];
            header("Location: dangNhapController.php");
        } else {
            $errors['general'] = "Có lỗi xảy ra. Vui lòng thử lại.";
        }
    }
}

include_once __DIR__ . '/../views/dangKy.php';
