<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/chiTietSanPham.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <title>Chi tiết sản phẩm</title>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="chi-tiet-san-pham">
            <!-- tiêu đề -->
            <div class="tieude-chitietsanpham">
                <h2>CHI TIẾT SẢN PHẨM</h2>
                <button onclick="history.back()"><i class="fa-solid fa-angle-left"></i> Trở lại</button>
            </div>

            <!-- phần trên: ảnh + thông tin -->
            <div class="container-top">
                <div class="container-top-left">
                    <!-- gallery -->
                    <div class="image-gallery">
                        <div class="main-image">
                            <?php if (!empty($hinhAnhs)): ?>
                                <img id="product-image" src="../<?php echo $hinhAnhs[0]['URL']; ?>" alt="<?php echo htmlspecialchars($chiTiet['TenHH']); ?>">
                            <?php else: ?>
                                <img id="product-image" src="../assets/images/placeholder.png" alt="Không có ảnh">
                            <?php endif; ?>
                        </div>
                        <div class="thumbnail-images">
                            <?php if (!empty($hinhAnhs)): ?>
                                <?php foreach ($hinhAnhs as $index => $img): ?>
                                    <img src="../<?php echo $img['URL']; ?>"
                                        alt="ảnh <?php echo $index + 1; ?>"
                                        class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="phan-thong-tin">
                    <div class="phan-thong-tin-tren">
                        <h2 id="product-name"><?php echo htmlspecialchars($chiTiet['TenHH']); ?></h2>

                        <!-- rating -->
                        <div class="rating">
                            <span><?php echo number_format($chiTiet['Rating'] ?? 0, 1); ?></span>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>

                    <!-- giá gốc + giá giảm -->
                    <div class="product-price">
                        <span class="new-price"><?php echo number_format($giaSauGiam, 0, ',', '.'); ?> VNĐ</span>
                        <?php if ($giamGia > 0): ?>
                            <span class="old-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VNĐ</span>
                            <span class="discount-badge">Giảm <?php echo number_format($giamGia, 0); ?>%</span>
                        <?php endif; ?>
                    </div>

                    <!-- thông tin -->
                    <div class="thong-tin-san-pham">
                        <!-- giá thị trường -->
                        <?php if (!empty($chiTiet['GiaThiTruong']) && $chiTiet['GiaThiTruong'] > $chiTiet['Gia']): ?>
                            <p id="product-market-price">
                                <i class="fa-solid fa-tag"></i> Giá thị trường:
                                <span>
                                    ~ <?php echo number_format($chiTiet['GiaThiTruong'], 0, ',', '.'); ?> VNĐ
                                </span>
                            </p>
                        <?php endif; ?>
                        <p id="product-trademark"><i class="fa-solid fa-star"></i> Danh Mục: <span><?php echo htmlspecialchars($chiTiet['TenDM']); ?></span></p>
                        <p id="product-quantity"><i class="fa-solid fa-hourglass-half"></i> Số lượng còn: <span id="quantity-value"><?php echo $chiTiet['SoLuongHH']; ?></span></p>
                        <p id="product-condition"><i class="fa-solid fa-arrows-rotate"></i> Chất lượng hàng: <span><?php echo $chiTiet['ChatLuongHang']; ?></span></p>
                        <p id="product-contact"><i class="fa-solid fa-phone-flip"></i> Thời gian đăng: <span><?php echo date('d/m/Y', strtotime($chiTiet['NgayThem'])); ?></span></p>
                    </div>

                    <div class="phan-nut">
                        <div class="phan-nut-tren">
                            <div class="nut-them-vao-gio-hang">
                                <button <?php echo $nutThemGioHangClass; ?> data-mahh="<?php echo $chiTiet['MaHH']; ?>">Thêm vào giỏ hàng</button>
                            </div>
                            <div class="nut-mua-ngay">
                                <button <?php echo $nutMuaNgayClass; ?> data-mahh="<?php echo $chiTiet['MaHH']; ?>">Mua ngay</button>
                            </div>
                        </div>
                        <div class="phan-nut-duoi">
                            <!-- Phải có data-mahh và data-favorited -->
                            <!-- chỉnh sửa -->
                            <div class="nut-yeu-thich">
                                <button id="save-favorite" onclick="toggleFavorite(this, <?php echo $chiTiet['MaHH']; ?>)">
                                    <i class="<?php echo $daYeuThich ? 'fa-solid' : 'fa-regular'; ?> fa-heart"
                                        style="<?php echo $daYeuThich ? 'color: red;' : ''; ?>"></i>
                                    <?php echo $daYeuThich ? 'Đã yêu thích' : 'Yêu thích'; ?>
                                </button>
                                <!-- chỉnh sửa  -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-mid">
                <div class="nguoi-ban">
                    <div class="avatar-nguoi-ban">
                        <img src="../<?php echo $hoSoNguoiBan['Avatar'] ?? '../assets/images/placeholder.png'; ?>" alt="avatar người bán">
                    </div>
                    <div class="thong-tin-nguoi-ban">
                        <h3><?php echo htmlspecialchars($hoSoNguoiBan['TenCuaHang'] ?? 'Người bán ẩn danh'); ?></h3>
                        <p><i class="fa-solid fa-location-dot"></i> Địa chỉ: <span><?php echo htmlspecialchars($hoSoNguoiBan['DiaChiKhoHang'] ?? 'Chưa cập nhật'); ?></span></p>
                    </div>
                    <div class="nut-xem-nguoi-ban">
                        <a href="chiTietNguoiBanController.php?IdTaiKhoan=<?php echo $chiTiet['IdNguoiBan']; ?>">
                            <button>Xem cửa hàng</button>
                        </a>
                    </div>

                    <div class="chat-voi-nguoi-ban">
                        <?php if (isset($_SESSION['IdTaiKhoan']) && $_SESSION['IdTaiKhoan'] == $chiTiet['IdNguoiBan']): ?>
                            <button disabled style="background:#ccc; cursor:not-allowed;">Bạn là người bán</button>
                        <?php else: ?>
                            <a href="chatController.php?IdTaiKhoan=<?php echo $chiTiet['IdNguoiBan']; ?>">
                                <button><i class="fa-solid fa-comments"></i> Chat với người bán</button>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- mô tả -->
            <div class="container-bot">
                <div class="thong-tin-them">
                    <h2>Thông tin thêm</h2>
                    <div class="product-description">
                        <?php echo nl2br($chiTiet['MoTa']); ?>
                    </div>
                </div>

                <div class="container-comment">
                    <h2>Đánh giá & Bình luận</h2>

                    <!-- form đánh giá -->
                    <form class="comment-form" id="comment-form">
                        <div class="rating-select">
                            <label>Đánh giá:</label>
                            <i class="fa-regular fa-star" data-rating="1"></i>
                            <i class="fa-regular fa-star" data-rating="2"></i>
                            <i class="fa-regular fa-star" data-rating="3"></i>
                            <i class="fa-regular fa-star" data-rating="4"></i>
                            <i class="fa-regular fa-star" data-rating="5"></i>
                        </div>
                        <textarea placeholder="Viết bình luận của bạn..." name="noidung"></textarea>
                        <button type="submit">Gửi</button>
                    </form>

                    <!-- danh sách bình luận -->
                    <div class="comment-list">
                        <?php if (!empty($binhLuans)): ?>
                            <?php foreach ($binhLuans as $bl): ?>
                                <div class="comment-item">
                                    <strong><?php echo htmlspecialchars($bl['TenTK']); ?></strong>
                                    <div class="rating">
                                        <?php
                                        $soSao = $bl['SoSao'] ?? 0;
                                        for ($i = 1; $i <= 5; $i++):
                                        ?>
                                            <i class="fa-<?php echo $i <= $soSao ? 'solid' : 'regular'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p><?php echo htmlspecialchars($bl['NoiDung']); ?></p>
                                    <small><?php echo date('d/m/Y H:i', strtotime($bl['NgayBL'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Chưa có bình luận nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../assets/js/js.js"></script>
    <script src="../assets/js/yeuThich.js"></script>
    <script src="../assets/js/nutThemGioHang_muaNgay.js"></script>
    <script src="../assets/js/danhGia.js"></script>



</body>

</html>