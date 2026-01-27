<?php
include '../includes/ketnoi.php';
include '../models/sanPham.php';

$sanPham = new SanPham($conn);
$danhSachSanPham = $sanPham->getSanPham(12);

if (!empty($danhSachSanPham)) {
  foreach ($danhSachSanPham as $sp) {
    // Chuẩn bị dữ liệu
    $maHH = $sp['MaHH'];
    if (!empty($sp['URL'])) {
      $url = $sp['URL'];
    } else {
      $url = 'assets/images/placeholder.png';
    }

    $tenHH = htmlspecialchars($sp['TenHH']);
    $rating = $sp['Rating'];
    $gia = number_format($sp['Gia'], 0, ',', '.');
    $soLuong = $sp['SoLuongHH'];

    // Kiểm tra hết hàng
    $hetHang = $soLuong == 0;
    $productClass = $hetHang ? "<div class='product-item het-hang' style='background: rgba(0, 0, 0, 0.1);'>" : "<div class='product-item'>";
    $badgeHetHang = $hetHang ? "<div class='badge-het-hang'>Hết hàng</div>" : '';

    // nếu có giảm giá
    if (!empty($sp['GiaTri'])) {
      $giaGiam = $sp['Gia'] - ($sp['Gia'] * ($sp['GiaTri'] / 100));
      $giaGiam = number_format($giaGiam, 0, ',', '.');

      $giaHienThi = "
        <span class='gia-goc'>{$gia} đ</span>
        <span class='gia-giam'>{$giaGiam} đ</span>";
    } else {
      $giaHienThi = "<span class='gia-giam'>{$gia} đ</span>";
    }

    echo "
        <a href='chiTietSanPhamController.php?id={$maHH}' class='product-link'>
          {$productClass}
            <div class='product-item-top'>
              <img src='../{$url}' alt='{$tenHH}' loading='lazy'>
              {$badgeHetHang}
              <div class='tieude-sanpham'>{$tenHH}</div>  
            </div>
            <div class='product-item-bottom'>
              <div class='gia-rating'>
                <div class='rating'>
                  <i class='fa-solid fa-star'></i>
                  <span>{$rating}</span>  
                </div>
                <div class='gia-san-pham'>
                  {$giaHienThi}
                </div>
              </div>
            </div>
          </div>
        </a>";
  }
} else {
  echo "<p>Chưa có sản phẩm nào.</p>";
}
$conn->close();
