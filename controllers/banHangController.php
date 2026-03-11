<?php
require_once '../models/banHangModel.php';
require_once '../includes/ketnoi.php';


$idUser = $_SESSION['IdTaiKhoan'];
$model = new banHangModel($conn);

// xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'kich_hoat') {
        $model->kichHoat(
            $idUser,
            $_POST['cccd'],
            $_POST['sdt'],
            $_POST['diachi']
        );
        header("Location: thongTinTaiKhoanController.php");
        exit;
    }

    if ($_POST['action'] === 'huy') {
        $model->huy($idUser);
        header("Location: thongTinTaiKhoanController.php");
        exit;
    }
}

// GET
$trangThaiBanHang = $model->getTrangThai($idUser);
