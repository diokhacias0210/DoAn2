<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết Người dùng - Admin</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .admin-container {
            padding: 30px;
            background: #f4f6f9;
            min-height: 100vh;
        }

        .info-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            border-top: 4px solid #34495e;
        }

        .shop-card {
            border-top: 4px solid #0dcaf0;
        }

        .avatar-lg {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #eee;
        }

        .sp-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="m-0 text-dark fw-bold"><i class="fa-solid fa-address-card"></i> Hồ sơ Người dùng</h2>
                    <div>
                        <?php if ($chiTietUser['VaiTro'] != 1): ?>
                            <?php if ($chiTietUser['TrangThaiBanHang'] != 'BiKhoa'): ?>
                                <a href="adminNguoiDungController.php?action=lock&id=<?= $chiTietUser['IdTaiKhoan'] ?>" class="btn btn-danger fw-bold shadow-sm" onclick="return confirm('Khóa tài khoản này?')"><i class="fa-solid fa-lock"></i> Khóa tài khoản</a>
                            <?php else: ?>
                                <a href="adminNguoiDungController.php?action=unlock&id=<?= $chiTietUser['IdTaiKhoan'] ?>" class="btn btn-success fw-bold shadow-sm" onclick="return confirm('Mở khóa tài khoản này?')"><i class="fa-solid fa-unlock"></i> Mở khóa</a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <a href="adminNguoiDungController.php" class="btn btn-secondary shadow-sm ms-2"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
                    </div>
                </div>

                <div class="row g-4 mb-4">

                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fa-solid fa-user text-primary"></i> Thông tin cơ bản</h5>
                            <div class="d-flex gap-4 align-items-center mb-4">
                                <img src="../../<?= !empty($chiTietUser['Avatar']) ? $chiTietUser['Avatar'] : 'assets/images/placeholder.png' ?>" class="avatar-lg shadow-sm">
                                <div>
                                    <h3 class="fw-bold text-primary m-0"><?= htmlspecialchars($chiTietUser['TenTK']) ?></h3>
                                    <p class="text-muted m-0 mt-1">Tham gia: <?= date('d/m/Y', strtotime($chiTietUser['ThoiGianTao'])) ?></p>
                                    <div class="mt-2">
                                        <?php if ($chiTietUser['VaiTro'] == 1) echo '<span class="badge bg-danger"><i class="fa-solid fa-crown"></i> Admin</span>';
                                        else echo '<span class="badge bg-info text-dark"><i class="fa-solid fa-user"></i> Khách hàng</span>'; ?>
                                        <?php if ($chiTietUser['TrangThaiBanHang'] == 'BiKhoa') echo '<span class="badge bg-danger ms-1">Bị khóa</span>'; ?>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 120px;"><i class="fa-solid fa-envelope text-muted"></i> Email:</th>
                                    <td><?= htmlspecialchars($chiTietUser['Email']) ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fa-solid fa-phone text-muted"></i> SĐT:</th>
                                    <td><?= htmlspecialchars($chiTietUser['Sdt']) ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fa-solid fa-triangle-exclamation text-danger"></i> Vi phạm:</th>
                                    <td><strong class="text-danger"><?= $chiTietUser['DiemViPham'] ?> lần</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card shop-card">
                            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fa-solid fa-store text-info"></i> Thông tin Cửa hàng</h5>

                            <?php if (!empty($chiTietUser['TenCuaHang'])): ?>
                                <h4 class="fw-bold text-dark mb-3 text-uppercase"><?= htmlspecialchars($chiTietUser['TenCuaHang']) ?></h4>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th style="width: 140px;"><i class="fa-solid fa-location-dot text-muted"></i> Kho hàng:</th>
                                        <td><?= htmlspecialchars($chiTietUser['DiaChiKhoHang']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fa-solid fa-id-card text-muted"></i> CCCD:</th>
                                        <td><?= htmlspecialchars($chiTietUser['SoCCCD']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fa-solid fa-building-columns text-muted"></i> Ngân hàng:</th>
                                        <td><?= htmlspecialchars($chiTietUser['TenNganHang']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fa-solid fa-money-check text-muted"></i> Số TK:</th>
                                        <td><?= htmlspecialchars($chiTietUser['SoTaiKhoanNganHang']) ?></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fa-solid fa-signature text-muted"></i> Chủ TK:</th>
                                        <td><?= htmlspecialchars($chiTietUser['TenChuTaiKhoan']) ?></td>
                                    </tr>
                                </table>
                            <?php else: ?>
                                <div class="text-center text-muted py-5">
                                    <i class="fa-solid fa-shop-slash fs-1 mb-2"></i><br>
                                    Người dùng này chưa thiết lập thông tin cửa hàng.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="info-card" style="border-top: 4px solid #2ecc71;">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-boxes-stacked text-success"></i> Sản phẩm đã đăng bán</h5>

                    <div class="table-responsive">
                        <table class="table table-hover sp-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Ảnh</th>
                                    <th>Tên Sản phẩm / Danh mục</th>
                                    <th>Giá bán</th>
                                    <th class="text-center">Kho hàng</th>
                                    <th>Trạng thái duyệt</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($danhSachSanPham)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Chưa đăng bán sản phẩm nào.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($danhSachSanPham as $sp): ?>
                                        <tr>
                                            <td><img src="../../<?= $sp['AnhDaiDien'] ?? 'assets/images/placeholder.png' ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 5px; border: 1px solid #eee;"></td>
                                            <td>
                                                <strong class="text-dark"><?= htmlspecialchars($sp['TenHH']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($sp['TenDM']) ?></small>
                                            </td>
                                            <td class="text-danger fw-bold"><?= number_format($sp['Gia'], 0, ',', '.') ?>đ</td>
                                            <td class="text-center"><?= $sp['SoLuongHH'] ?></td>
                                            <td>
                                                <?php if ($sp['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                                <?php elseif ($sp['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                                    <span class="badge bg-success">Đã duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Bị từ chối</span>
                                                <?php endif; ?>

                                                <?php if ($sp['HienThi'] == 0): ?>
                                                    <span class="badge bg-secondary ms-1">Đang ẩn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="adminChiTietSanPhamController.php?id=<?= $sp['MaHH'] ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết sản phẩm này">
                                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem SP
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>