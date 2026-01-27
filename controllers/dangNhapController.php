<?php

require_once __DIR__ . '/../includes/ketnoi.php';
require_once __DIR__ . '/../models/TaiKhoanModel.php';

$errors = [];
$old = [];

$taiKhoanModel = new TaiKhoanModel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old['email'] = $email;

    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email.';
    }
    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu.';
    }

    if (!$errors) {
        // SỬ DỤNG MODEL thay vì query trực tiếp
        $user = $taiKhoanModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['MatKhau'])) {
            // Đăng nhập thành công, lưu session
            $_SESSION['IdTaiKhoan'] = $user['IdTaiKhoan'];
            $_SESSION['TenTK'] = $user['TenTK'];
            $_SESSION['VaiTro'] = $user['VaiTro'];

            if ($user['VaiTro'] == 1) {
                $_SESSION['vaitro'] = 'admin';
                header("Location: ../admin/index.php");
                exit;
            } else {
                $_SESSION['vaitro'] = 'user';
                header("Location: trangChuController.php");
                exit;
            }
        } else {
            // Đăng nhập thất bại
            $errors['password'] = 'Email hoặc mật khẩu không đúng.';
        }
    }
}

include_once __DIR__ . '/../views/dangNhap.php';
