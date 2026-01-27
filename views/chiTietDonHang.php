    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chi tiết đơn hàng #<?= $maDH ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="../assets/css/header.css" rel="stylesheet">
        <link href="../assets/css/color.css" rel="stylesheet">
        <link href="../assets/css/chiTietDonHang.css" rel="stylesheet">
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <div class="container">
            <a href="lichSuDonHangController.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>

            <div class="order-detail-card">
                <div class="order-header">
                    <div class="order-title">
                        <i class="fa-solid fa-file-invoice"></i> Chi tiết đơn hàng #<?= $maDH ?>
                    </div>
                    <div class="order-status status-<?= strtolower(str_replace(' ', '-', $order['TrangThai'])) ?>">
                        <?= htmlspecialchars($order['TrangThai']) ?>
                    </div>
                </div>

                <!-- Thông tin đơn hàng và khách hàng -->
                <div class="info-section">
                    <div class="info-box">
                        <h3><i class="fa-solid fa-user"></i> Thông tin người nhận</h3>
                        <div class="info-item">
                            <strong>Họ tên:</strong> <?= htmlspecialchars($order['TenTK']) ?>
                        </div>
                        <div class="info-item">
                            <strong>Địa chỉ:</strong> <?= htmlspecialchars($order['DiaChiGiao']) ?>
                        </div>
                    </div>

                    <div class="info-box">
                        <h3><i class="fa-solid fa-info-circle"></i> Thông tin đơn hàng</h3>
                        <div class="info-item">
                            <strong>Thanh toán:</strong>
                            <?= htmlspecialchars($payment['PhuongThuc'] ?? 'Chưa rõ') ?>
                        </div>
                        <?php if ($payment): ?>
                            <div class="info-item">
                                <?php if ($payment['PhuongThuc'] == 'Tiền mặt'): ?>
                                    <strong>Mã thanh toán:</strong>
                                    <span style="font-weight: bold; color: #555;">
                                        <?= htmlspecialchars($payment['MaThanhToan']) ?>
                                    </span>
                                <?php else: ?>
                                    <strong>Mã thanh toán:</strong>
                                    <span style="font-weight: bold; color: #555;">
                                        <?= htmlspecialchars($payment['MaThanhToan']) ?>
                                    </span>
                                    <strong>Trạng thái TT:</strong>
                                    <span style="color: green; font-weight: bold;">
                                        Thanh toán hoàn tất
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
                <h3 style="margin-bottom: 15px;">
                    <i class="fa-solid fa-box"></i> Sản phẩm trong đơn hàng
                </h3>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tongTienSanPham = 0;
                        foreach ($items as $item):
                            $thanhTien = $item['DonGia'] * $item['SoLuongSanPham'];
                            $tongTienSanPham += $thanhTien;
                            $imgSrc = !empty($item['AnhDaiDien']) ? '../' . $item['AnhDaiDien'] : '../assets/images/no-image.png';
                        ?>
                            <tr>
                                <td>
                                    <div class="product-name-cell">
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="" class="product-img">
                                        <strong><?= htmlspecialchars($item['TenHH']) ?></strong>
                                    </div>
                                </td>
                                <td><?= number_format($item['DonGia'], 0, ',', '.') ?>đ</td>
                                <td>x<?= $item['SoLuongSanPham'] ?></td>
                                <td><strong><?= number_format($thanhTien, 0, ',', '.') ?>đ</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Tổng cộng -->
                <div class="summary-box">
                    <div class="summary-item">
                        <span>Tổng tiền hàng:</span>
                        <strong><?= number_format($tongTienSanPham, 0, ',', '.') ?>đ</strong>
                    </div>
                    <div class="summary-item">
                        <span>Phí vận chuyển:</span>
                        <strong><?= number_format($order['TongTien'] - $tongTienSanPham, 0, ',', '.') ?>đ</strong>
                    </div>
                    <div class="summary-item summary-total">
                        <span>Tổng thanh toán:</span>
                        <span><?= number_format($order['TongTien'], 0, ',', '.') ?>đ</span>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../includes/footer.php'; ?>
        <script src="../assets/js/js.js"></script>

    </body>

    </html>