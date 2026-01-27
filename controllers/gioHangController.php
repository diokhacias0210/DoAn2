<?php

require_once __DIR__ . '/../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    $_SESSION['redirect_url'] = 'gioHangController.php';
    header('Location: dangNhapController.php');
    exit;
}


include_once __DIR__ . '/../views/gioHang.php';
