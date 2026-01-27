<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sản phẩm yêu thích</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/sanPham.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css//danhSachYeuThich.css" rel="stylesheet">
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="wishlist-container">
            <h2 class="wishlist-title"><i class="fa-solid fa-heart"></i> SẢN PHẨM YÊU THÍCH</h2>
            <div class="nut-tro-lai">
                <button onclick="window.location.href='ThongTinTaiKhoanController.php'" class="back-btn">⬅ Quay lại</button>
            </div>
            <?php if (empty($danhSachYeuThich)): ?>
                <div class="empty-wishlist">
                    <i class="fa-regular fa-heart" style="font-size: 48px; margin-bottom: 15px; color: #ccc;"></i>
                    <p>Bạn chưa có sản phẩm yêu thích nào.</p>
                    <a href="trangChuController.php" style="color: var(--bs-pink-500); text-decoration: underline; font-weight: bold;">Dạo một vòng xem sao?</a>
                </div>
            <?php else: ?>
                <div class="loai-san-pham-moi">
                    <?php foreach ($danhSachYeuThich as $sp):
                        $maHH = $sp['MaHH'];
                        $tenHH = htmlspecialchars($sp['TenHH']);
                        $anhDaiDien = !empty($sp['AnhDaiDien']) ? '../' . $sp['AnhDaiDien'] : '../assets/images/placeholder.png';
                        $rating = $sp['Rating'] ?? 0; // Lấy rating từ model

                        // Xử lý giá
                        $giaGoc = $sp['Gia'];
                        $giaHienThi = '';
                        if ($sp['GiaTri'] && $sp['Gia'] > 0) {
                            $giaGiam = $giaGoc - ($giaGoc * ($sp['GiaTri'] / 100));
                            $giaHienThi = "<span class='gia-goc'>" . number_format($giaGoc, 0, ',', '.') . " đ</span>" .
                                "<span class='gia-giam'>" . number_format($giaGiam, 0, ',', '.') . " đ</span>";
                        } else {
                            $giaHienThi = "<span class='gia-giam'>" . number_format($giaGoc, 0, ',', '.') . " đ</span>";
                        }
                    ?>
                        <a href="chiTietSanPhamController.php?id=<?= $maHH ?>" class="product-link">
                            <div class="product-item">
                                <div class="product-item-top">
                                    <img src="<?= $anhDaiDien ?>" alt="<?= $tenHH ?>" loading="lazy">

                                    <button class="nut-bo-yeu-thich"
                                        onclick="event.preventDefault(); removeFavorite(this, <?= $maHH ?>);"
                                        title="Bỏ yêu thích">
                                        <i class="fa-solid fa-heart" style="color: red;"></i>
                                    </button>

                                    <div class="tieude-sanpham"><?= $tenHH ?></div>
                                </div>
                                <div class="product-item-bottom">
                                    <div class="gia-rating">
                                        <div class="rating">
                                            <i class='fa-solid fa-star'></i>
                                            <span><?= $rating ?></span>
                                        </div>
                                        <div class="gia-san-pham">
                                            <?= $giaHienThi ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php include '../includes/footer.php'; ?>

        <script src="../assets/js/js.js"></script>
        <script>
            function removeFavorite(btn, maHH) {
                if (!confirm('Bạn muốn xóa sản phẩm này khỏi danh sách yêu thích?')) return;

                const formData = new FormData();
                formData.append('MaHH', maHH);

                // Gọi API Controller mới
                fetch('yeuThichApiController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.favorited === false) {
                            // Xóa phần tử khỏi giao diện
                            const productCard = btn.closest('.product-link'); // Tìm thẻ a bao ngoài
                            productCard.style.transition = 'opacity 0.5s';
                            productCard.style.opacity = '0';
                            setTimeout(() => productCard.remove(), 500);

                            // Nếu xóa hết thì reload để hiện thông báo trống
                            const container = document.querySelector('.loai-san-pham-moi');
                            if (container.querySelectorAll('.product-link').length <= 1) {
                                setTimeout(() => location.reload(), 500);
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra');
                        }
                    })
                    .catch(err => console.error(err));
            }
        </script>
</body>

</html>