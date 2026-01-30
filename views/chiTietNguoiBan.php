<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng: <?php echo htmlspecialchars($shopInfo['TenCuaHang']); ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css/sanPham.css" rel="stylesheet">
    <link href="../assets/css/danhSachSanPham.css" rel="stylesheet">
    <link href="../assets/css/chiTietNguoiBan.css" rel="stylesheet">
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="san-pham-chinh">

            <input type="hidden" id="seller-id" value="<?php echo $shopInfo['IdTaiKhoan']; ?>">

            <div class="shop-header-container">
                <div class="shop-info-wrapper">
                    <div class="shop-left">
                        <img src="../<?php echo !empty($shopInfo['Avatar']) ? $shopInfo['Avatar'] : 'assets/images/placeholder.png'; ?>"
                            alt="Avatar" class="shop-avatar">
                        <a href="chatController.php?IdTaiKhoan=<?php echo $shopInfo['IdTaiKhoan']; ?>" class="btn-chat-shop">
                            <i class="fa-solid fa-comments"></i> Chat ngay
                        </a>
                    </div>
                    <div class="shop-right">
                        <h1 class="shop-name">
                            <?php echo htmlspecialchars($shopInfo['TenCuaHang']); ?>
                        </h1>
                        <div class="shop-stats">
                            <div class="stat-item">
                                <i class="fa-solid fa-box-open"></i>
                                <div>Sản phẩm: <span id="total-products">...</span></div>
                            </div>
                            <div class="stat-item">
                                <i class="fa-solid fa-calendar-days"></i>
                                <div>Tham gia: <span><?php echo date('d/m/Y', strtotime($shopInfo['NgayDuyet'] ?? 'now')); ?></span></div>
                            </div>
                            <div class="stat-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <div>Địa chỉ: <span><?php echo htmlspecialchars($shopInfo['DiaChiKhoHang'] ?? 'Chưa cập nhật'); ?></span></div>
                            </div>
                            <div class="stat-item">
                                <i class="fa-solid fa-phone"></i>
                                <div>SĐT: <span><?php echo htmlspecialchars($shopInfo['Sdt']); ?></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shop-toolbar-container">
                <div class="toolbar-top">
                    <button onclick="history.back()" class="btn-back-text">
                        <i class="fa-solid fa-angle-left"></i> Trở lại
                    </button>
                    <h2>SẢN PHẨM CỦA SHOP</h2>
                </div>

                <div class="toolbar-bottom">
                    <div class="filter-group">
                        <div class="custom-select-wrapper">
                            <i class="fa-solid fa-filter"></i>
                            <select id="category-filter">
                                <option value="">Tất cả danh mục</option>
                                <?php if (isset($danhSachDanhMuc)): foreach ($danhSachDanhMuc as $dm): ?>
                                        <option value="<?= $dm['MaDM'] ?>"><?= htmlspecialchars($dm['TenDM']) ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>

                        <div class="custom-select-wrapper">
                            <i class="fa-solid fa-arrow-up-wide-short"></i>
                            <select id="sort-filter">
                                <option value="new">Mới nhất</option>
                                <option value="old">Cũ nhất</option>
                                <option value="giathap">Giá tăng dần</option>
                                <option value="giacao">Giá giảm dần</option>
                                <option value="az">Tên A-Z</option>
                            </select>
                        </div>
                    </div>

                    <div class="search-group-modern">
                        <input type="text" id="search-input" placeholder="Tìm sản phẩm tại shop...">
                        <button id="btn-search"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>

            <div class="loai-san-pham" id="shop-product-list">
                <div class="text-center w-100 py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
            </div>

            <div class="trang-thai-san-pham">
                <div id="no-products-msg" style="display:none; text-align:center;">
                    <i class="fas fa-box-open fa-3x text-muted"></i>
                    <p class="mt-2 text-muted">Không tìm thấy sản phẩm nào.</p>
                </div>
                <div class="nut-xem-them-san-pham" id="load-more-btn-container">
                    <button type="button" id="btn-load-more">Xem thêm</button>
                </div>
            </div>

        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/js.js"></script>
    <script src="../assets/js/yeuThich.js"></script>
    <script src="../assets/js/sellerShop.js"></script>
</body>

</html>