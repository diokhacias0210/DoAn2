<?php
// File: admin/views/dashboard.php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin - Tổng quan</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%);
        }

        .stat-card.purple {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-card .icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .stat-card .sub-info {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 10px;
        }

        .filter-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .filter-card h3 {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .filter-tab.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .filter-content {
            display: none;
        }

        .filter-content.active {
            display: block;
        }

        .top-products {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .top-products h3 {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .product-item:hover {
            background: #f8f9fa;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .product-stats {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .product-revenue {
            font-weight: bold;
            color: #27ae60;
            font-size: 1.1rem;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .quick-link {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .quick-link:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .quick-link .icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .quick-link h4 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .time-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .time-stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #3498db;
        }

        .time-stat-card h4 {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin: 0 0 10px 0;
            font-weight: 500;
        }

        .time-stat-card .value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .time-stat-card .orders {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="admin-layout">

        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">
                <h1>Dashboard - Tổng quan hệ thống</h1>

                <!-- Thống kê tổng quan -->
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="icon">📦</div>
                        <h3>Tổng sản phẩm</h3>
                        <div class="number"><?= number_format($tongSanPham) ?></div>
                    </div>

                    <div class="stat-card green">
                        <div class="icon">🛒</div>
                        <h3>Tổng đơn hàng</h3>
                        <div class="number"><?= number_format($tongDonHang) ?></div>
                    </div>

                    <div class="stat-card orange">
                        <div class="icon">👥</div>
                        <h3>Người dùng</h3>
                        <div class="number"><?= number_format($tongNguoiDung) ?></div>
                    </div>

                    <div class="stat-card purple">
                        <div class="icon">💰</div>
                        <h3>Tổng doanh thu</h3>
                        <div class="number"><?= number_format($tongDoanhThu / 1000000, 1) ?>M</div>
                        <div class="sub-info">Từ các đơn hoàn tất</div>
                    </div>
                </div>
                <!-- Bộ lọc thống kê -->
                <div class="filter-card">
                    <h3>🔍 Lọc thống kê chi tiết</h3>

                    <div class="filter-tabs">
                        <button class="filter-tab <?= ($loaiLoc ?? 'tat-ca') == 'tat-ca' ? 'active' : '' ?>" onclick="showFilter('tat-ca')">
                            Tất cả
                        </button>
                        <button class="filter-tab <?= ($loaiLoc ?? '') == 'ngay' ? 'active' : '' ?>" onclick="showFilter('ngay')">
                            Theo khoảng ngày
                        </button>
                        <button class="filter-tab <?= ($loaiLoc ?? '') == 'thang' ? 'active' : '' ?>" onclick="showFilter('thang')">
                            Theo tháng
                        </button>
                        <button class="filter-tab <?= ($loaiLoc ?? '') == 'nam' ? 'active' : '' ?>" onclick="showFilter('nam')">
                            Theo năm
                        </button>
                    </div>

                    <!-- Form lọc theo ngày -->
                    <div id="filter-ngay" class="filter-content <?= ($loaiLoc ?? '') == 'ngay' ? 'active' : '' ?>">
                        <form method="GET" action="">
                            <input type="hidden" name="loai_loc" value="ngay">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label">Từ ngày:</label>
                                    <input type="date" name="tu_ngay" class="form-control"
                                        value="<?= $tuNgay ?? date('Y-m-01') ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Đến ngày:</label>
                                    <input type="date" name="den_ngay" class="form-control"
                                        value="<?= $denNgay ?? date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Xem thống kê</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Form lọc theo tháng -->
                    <div id="filter-thang" class="filter-content <?= ($loaiLoc ?? '') == 'thang' ? 'active' : '' ?>">
                        <form method="GET" action="">
                            <input type="hidden" name="loai_loc" value="thang">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label">Tháng:</label>
                                    <select name="thang" class="form-control" required>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= $m ?>" <?= ($thang ?? date('m')) == $m ? 'selected' : '' ?>>
                                                Tháng <?= $m ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Năm:</label>
                                    <select name="nam" class="form-control" required>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($y = $currentYear; $y >= $currentYear - 5; $y--):
                                        ?>
                                            <option value="<?= $y ?>" <?= ($nam ?? date('Y')) == $y ? 'selected' : '' ?>>
                                                <?= $y ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Xem thống kê</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Form lọc theo năm -->
                    <div id="filter-nam" class="filter-content <?= ($loaiLoc ?? '') == 'nam' ? 'active' : '' ?>">
                        <form method="GET" action="">
                            <input type="hidden" name="loai_loc" value="nam">
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="form-label">Năm:</label>
                                    <select name="nam" class="form-control" required>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($y = $currentYear; $y >= $currentYear - 5; $y--):
                                        ?>
                                            <option value="<?= $y ?>" <?= ($nam ?? date('Y')) == $y ? 'selected' : '' ?>>
                                                <?= $y ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Xem thống kê</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Hiển thị thông tin khoảng thời gian đang xem -->
                    <?php if (isset($loaiLoc) && $loaiLoc != 'tat-ca'): ?>
                        <div class="alert alert-info mt-3">
                            <strong>📊 Đang xem thống kê:</strong>
                            <?php if ($loaiLoc == 'ngay'): ?>
                                Từ ngày <?= date('d/m/Y', strtotime($tuNgay)) ?> đến <?= date('d/m/Y', strtotime($denNgay)) ?>
                            <?php elseif ($loaiLoc == 'thang'): ?>
                                Tháng <?= $thang ?>/<?= $nam ?>
                            <?php elseif ($loaiLoc == 'nam'): ?>
                                Năm <?= $nam ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Thống kê doanh thu -->
                <h2 style="margin-top: 40px; color: #2c3e50; font-size: 1.5rem;">📊 Thống kê doanh thu</h2>

                <?php if (isset($loaiLoc) && $loaiLoc != 'tat-ca' && isset($doanhThuLoc)): ?>
                    <!-- Hiển thị thống kê theo bộ lọc -->
                    <div class="time-stats">
                        <div class="time-stat-card">
                            <h4>
                                <?php if ($loaiLoc == 'ngay'): ?>
                                    Từ <?= date('d/m/Y', strtotime($tuNgay)) ?> đến <?= date('d/m/Y', strtotime($denNgay)) ?>
                                <?php elseif ($loaiLoc == 'thang'): ?>
                                    Tháng <?= $thang ?>/<?= $nam ?>
                                <?php elseif ($loaiLoc == 'nam'): ?>
                                    Năm <?= $nam ?>
                                <?php endif; ?>
                            </h4>
                            <div class="value"><?= number_format($doanhThuLoc) ?> ₫</div>
                            <div class="orders"><?= $soDonHangLoc ?> đơn hàng</div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Hiển thị thống kê mặc định -->
                    <div class="time-stats">
                        <div class="time-stat-card">
                            <h4>Hôm nay</h4>
                            <div class="value"><?= number_format($doanhThuHomNay['DoanhThu']) ?> ₫</div>
                            <div class="orders"><?= $doanhThuHomNay['SoDonHang'] ?> đơn hàng</div>
                        </div>

                        <div class="time-stat-card" style="border-left-color: #27ae60;">
                            <h4>Tháng này</h4>
                            <div class="value"><?= number_format($doanhThuThangNay['DoanhThu']) ?> ₫</div>
                            <div class="orders"><?= $doanhThuThangNay['SoDonHang'] ?> đơn hàng</div>
                        </div>

                        <div class="time-stat-card" style="border-left-color: #e74c3c;">
                            <h4>Năm nay</h4>
                            <div class="value"><?= number_format($doanhThuNamNay['DoanhThu']) ?> ₫</div>
                            <div class="orders"><?= $doanhThuNamNay['SoDonHang'] ?> đơn hàng</div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Top sản phẩm bán chạy -->
                <div class="top-products">
                    <h3>🔥 Top 5 sản phẩm bán chạy</h3>
                    <?php if (empty($topSanPham)): ?>
                        <p style="text-align: center; color: #7f8c8d; padding: 20px;">Chưa có dữ liệu bán hàng</p>
                    <?php else: ?>
                        <?php foreach ($topSanPham as $index => $sp): ?>
                            <div class="product-item">
                                <span style="font-size: 1.5rem; font-weight: bold; color: #3498db; margin-right: 15px;">#<?= $index + 1 ?></span>
                                <img src="../../<?= htmlspecialchars($sp['AnhDaiDien'] ?? 'assets/images/default.png') ?>"
                                    alt="<?= htmlspecialchars($sp['TenHH']) ?>">
                                <div class="product-info">
                                    <div class="product-name"><?= htmlspecialchars($sp['TenHH']) ?></div>
                                    <div class="product-stats">
                                        Đã bán: <strong><?= number_format($sp['TongSoLuongBan']) ?></strong> sản phẩm
                                    </div>
                                </div>
                                <div class="product-revenue">
                                    <?= number_format($sp['TongDoanhThu']) ?> ₫
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Truy cập nhanh -->
                <h2 style="margin-top: 40px; color: #2c3e50; font-size: 1.5rem;">⚡ Truy cập nhanh</h2>
                <div class="quick-links">
                    <a href="adminSanPhamController.php" class="quick-link">
                        <div class="icon">📦</div>
                        <h4>Quản lý sản phẩm</h4>
                    </a>
                    <a href="adminDonHangController.php" class="quick-link">
                        <div class="icon">🛒</div>
                        <h4>Quản lý đơn hàng</h4>
                    </a>
                    <a href="adminNguoiDungController.php" class="quick-link">
                        <div class="icon">👥</div>
                        <h4>Quản lý người dùng</h4>
                    </a>
                </div>

            </div>
        </main>
    </div>

    <script>
        // Chuyển đổi tab lọc
        function showFilter(type) {
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.filter-content').forEach(content => {
                content.classList.remove('active');
            });

            if (type !== 'tat-ca') {
                event.target.classList.add('active');
                document.getElementById('filter-' + type).classList.add('active');
            } else {
                event.target.classList.add('active');
                window.location.href = '?';
            }
        }
    </script>

</body>

</html>