<?php
include '../includes/ketnoi.php';
include '../models/danhMuc.php';

$sanPham = new DanhMuc($conn);
$danhSachDanhMuc = $sanPham->getDanhMuc();

if (!empty($danhSachDanhMuc)) {
    foreach ($danhSachDanhMuc as $dm) {
        // Chuẩn bị dữ liệu
        $maDM = $dm['MaDM'];
        $tenDM = htmlspecialchars($dm['TenDM']);
        echo "
        <a href='danhSachSanPhamController.php?caterory={$maDM}'>
          <button><i class='fa-solid fa-tags'></i>{$tenDM}</button>
        </a>
        ";
    }
} else {
    echo "<p>Chưa có danh mục nào.</p>";
}
$conn->close();
