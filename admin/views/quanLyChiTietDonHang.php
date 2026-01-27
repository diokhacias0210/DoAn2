<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $thongTinDH['MaDH'] ?></title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .detail-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-label {
            font-weight: bold;
            color: #666;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4CAF50;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #4CAF50;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -17px;
            top: 17px;
            width: 2px;
            height: calc(100% - 17px);
            background: #ddd;
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include '../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="container-fluid">
                <a href="adminDonHangController.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>

                <h2><i class="fas fa-file-invoice"></i> Chi tiết đơn hàng #<?= $thongTinDH['MaDH'] ?></h2>

                <!-- Thông tin khách hàng -->
                <div class="detail-card">
                    <h4><i class="fas fa-user"></i> Thông tin khách hàng</h4>
                    <div class="info-row">
                        <span class="info-label">Họ tên:</span>
                        <span><?= htmlspecialchars($thongTinDH['TenTK']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span><?= htmlspecialchars($thongTinDH['Email']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">SĐT:</span>
                        <span><?= htmlspecialchars($thongTinDH['Sdt']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Địa chỉ giao:</span>
                        <span><?= htmlspecialchars($thongTinDH['DiaChiGiao']) ?></span>
                    </div>
                </div>
                <!-- Danh sách sản phẩm -->
                <div class="detail-card">
                    <h4><i class="fas fa-list"></i> Danh sách sản phẩm</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Giảm giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($chiTietSP as $sp): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../<?= htmlspecialchars($sp['AnhDaiDien'] ?? 'assets/images/placeholder.png') ?>"
                                                class="product-img me-2">
                                            <span><?= htmlspecialchars($sp['TenHH']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($sp['DonGia'], 0, ',', '.') ?>đ</td>
                                    <td><?= $sp['SoLuongSanPham'] ?></td>
                                    <td><?= number_format($sp['GiamGia'], 0, ',', '.') ?>đ</td>
                                    <td><strong><?= number_format(($sp['DonGia'] * $sp['SoLuongSanPham']) - $sp['GiamGia'], 0, ',', '.') ?>đ</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/js/bootstrap/bootstrap.bundle.js"></script>
</body>

</html>