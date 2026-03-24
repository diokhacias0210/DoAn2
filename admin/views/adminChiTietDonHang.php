<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $maDH ?> - Admin</title>
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

        /* ĐÃ XÓA VIỀN MÀU BORDER-TOP Ở ĐÂY */
        .info-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
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
                    <h2 class="m-0 text-dark fw-bold"><i class="fa-solid fa-file-invoice"></i> Chi tiết đơn hàng #<?= $maDH ?></h2>
                    <a href="adminDonHangController.php" class="btn btn-secondary shadow-sm"><i class="fa-solid fa-arrow-left"></i> Trở lại</a>
                </div>

                <div class="info-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 border-end text-center">
                            <p class="text-muted mb-1">Trạng thái</p>
                            <h4 class="fw-bold mb-0">
                                <?php
                                $tt = $thongTin['TrangThai'];
                                if ($tt == 'Chờ xử lý') echo '<span class="text-warning">Chờ xử lý</span>';
                                elseif ($tt == 'Đã xác nhận') echo '<span class="text-primary">Đã xác nhận</span>';
                                elseif ($tt == 'Đang giao') echo '<span style="color:#6f42c1;">Đang giao</span>';
                                elseif ($tt == 'Hoàn tất') echo '<span class="text-success">Hoàn tất</span>';
                                elseif ($tt == 'Đã hủy') echo '<span class="text-danger">Đã hủy</span>';
                                ?>
                            </h4>
                        </div>
                        <div class="col-md-3 border-end text-center">
                            <p class="text-muted mb-1">Ngày đặt</p>
                            <h5 class="fw-bold mb-0"><?= date('d/m/Y H:i', strtotime($thongTin['NgayDat'])) ?></h5>
                        </div>
                        <div class="col-md-3 border-end text-center">
                            <p class="text-muted mb-1">Tổng tiền thanh toán</p>
                            <h4 class="fw-bold text-danger mb-0"><?= number_format($thongTin['TongTien'], 0, ',', '.') ?>đ</h4>
                        </div>
                        <div class="col-md-3 text-center">
                            <p class="text-muted mb-1">Lợi nhuận Sàn (Phí 5%)</p>
                            <h4 class="fw-bold text-success mb-0">+<?= number_format($thongTin['PhiSan'], 0, ',', '.') ?>đ</h4>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fa-solid fa-user-tag text-info"></i> Thông tin Người Mua</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 120px;">Khách hàng:</th>
                                    <td><strong><?= htmlspecialchars($thongTin['NguoiMua']) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>SĐT liên hệ:</th>
                                    <td><?= htmlspecialchars($thongTin['SdtMua']) ?></td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ giao:</th>
                                    <td><?= htmlspecialchars($thongTin['DiaChiGiao']) ?></td>
                                </tr>
                                <tr>
                                    <th>Ghi chú đơn:</th>
                                    <td class="text-danger"><?= !empty($thongTin['GhiChu']) ? htmlspecialchars($thongTin['GhiChu']) : 'Không có' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fa-solid fa-store text-warning"></i> Thông tin Người Bán</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 120px;">Tên Shop:</th>
                                    <td><strong><?= htmlspecialchars($thongTin['TenCuaHang'] ?? $thongTin['NguoiBan']) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Chủ tài khoản:</th>
                                    <td><?= htmlspecialchars($thongTin['NguoiBan']) ?></td>
                                </tr>
                                <tr>
                                    <th>SĐT Shop:</th>
                                    <td><?= htmlspecialchars($thongTin['SdtBan']) ?></td>
                                </tr>
                                <tr>
                                    <th>Thực nhận:</th>
                                    <td class="text-success fw-bold"><?= number_format($thongTin['TienNguoiBanNhan'], 0, ',', '.') ?>đ <small class="text-muted fw-normal">(Đã trừ phí sàn)</small></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-boxes-stacked text-secondary"></i> Chi tiết Sản phẩm</h5>
                    <div class="table-responsive">
                        <table class="table table-hover sp-table align-middle">
                            <thead class="text-center">
                                <tr>
                                    <th class="text-start">Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-end pe-3">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chiTiet as $sp): ?>
                                    <?php
                                    $anh = !empty($sp['AnhDaiDien']) ? '../../' . $sp['AnhDaiDien'] : '../../assets/images/placeholder.png';
                                    $thanhtien = ($sp['DonGia'] * $sp['SoLuongSanPham']) - $sp['GiamGia'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class='d-flex align-items-center gap-3'>
                                                <img src='<?= $anh ?>' style='width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #eee;'>
                                                <span class='fw-bold text-dark'><?= htmlspecialchars($sp['TenHH']) ?></span>
                                            </div>
                                        </td>
                                        <td class='text-danger text-center'><?= number_format($sp['DonGia'], 0, ',', '.') ?>đ</td>
                                        <td class='text-center'><?= $sp['SoLuongSanPham'] ?></td>
                                        <td class='fw-bold text-danger text-end pe-3'><?= number_format($thanhtien, 0, ',', '.') ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
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