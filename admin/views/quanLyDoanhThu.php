<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Doanh Thu & Rút Tiền - Admin</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .admin-container {
            padding: 20px;
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* Chỉnh màu xanh đồng bộ cho bảng */
        .qlt-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .qlt-table th {
            background-color: #4CAF50 !important;
            color: white;
            border: none;
            vertical-align: middle;
            white-space: nowrap;
            text-align: center;
        }

        .qlt-table td {
            vertical-align: middle;
            text-align: center;
        }

        .stat-card {
            border-radius: 12px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .stat-card i {
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 100px;
            opacity: 0.2;
        }

        .bg-pink-grad {
            background: linear-gradient(135deg, #e91e63, #9c27b0);
        }

        .bg-blue-grad {
            background: linear-gradient(135deg, #2196f3, #00bcd4);
        }

        .bg-green-grad {
            background: linear-gradient(135deg, #4caf50, #8bc34a);
        }

        .bg-orange-grad {
            background: linear-gradient(135deg, #ff9800, #ffeb3b);
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm"><i class="fa-solid fa-check-circle"></i> <?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm"><i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="m-0 text-dark fw-bold"><i class="fa-solid fa-vault text-success"></i> Doanh thu & Tài chính</h2>
                        <p class="text-muted mt-1">Giám sát lợi nhuận sàn và xét duyệt các lệnh rút tiền của đối tác bán hàng.</p>
                    </div>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-md-3">
                        <div class="stat-card bg-green-grad">
                            <h6 class="fw-bold mb-2">Lợi nhuận Sàn thu được</h6>
                            <h3 class="fw-bold mb-0">+<?= number_format($thongKe['TongLoiNhuan'], 0, ',', '.') ?>đ</h3>
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-orange-grad">
                            <h6 class="fw-bold mb-2 text-dark">Tiền Đang Chờ Rút</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?= number_format($thongKe['TienChoRut'], 0, ',', '.') ?>đ</h3>
                            <i class="fa-solid fa-hourglass-half text-dark"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-blue-grad">
                            <h6 class="fw-bold mb-2">Đã Thanh Toán (Payout)</h6>
                            <h3 class="fw-bold mb-0"><?= number_format($thongKe['DaThanhToan'], 0, ',', '.') ?>đ</h3>
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-pink-grad d-flex flex-column justify-content-center">
                            <h6 class="fw-bold mb-2">Cấu Hình Phí Sàn (%)</h6>
                            <form method="POST" action="adminDoanhThuController.php" class="d-flex gap-2">
                                <input type="hidden" name="action" value="update_fee">
                                <input type="number" name="phiSanMoi" class="form-control" value="<?= $phiSanHienTai ?>" min="0" max="100" step="0.1" style="width: 100px; font-weight:bold;">
                                <button type="submit" class="btn btn-light fw-bold text-pink" onclick="return confirm('Cập nhật phí sẽ gửi thông báo đến tất cả Người bán. Tiếp tục?')">Lưu</button>
                            </form>
                            <i class="fa-solid fa-percent"></i>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs fw-bold mb-3" id="financeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-success" id="rut-tien-tab" data-bs-toggle="tab" data-bs-target="#rut-tien" type="button" role="tab"><i class="fa-solid fa-money-check-dollar"></i> Lệnh Rút Tiền (Payout)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-success" id="doanh-thu-tab" data-bs-toggle="tab" data-bs-target="#doanh-thu" type="button" role="tab"><i class="fa-solid fa-chart-pie"></i> Doanh Thu Từng Shop</button>
                    </li>
                </ul>

                <div class="tab-content" id="financeTabContent">

                    <div class="tab-pane fade show active" id="rut-tien" role="tabpanel">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body bg-white rounded d-flex justify-content-between align-items-center">
                                <div><i class="fa-solid fa-circle-info text-info"></i> Admin chuyển khoản xong hãy bấm duyệt.</div>
                                <div class="d-flex gap-3 align-items-center">
                                    <a href="adminDoanhThuController.php?action=approve_all" class="btn btn-success fw-bold text-nowrap shadow-sm" onclick="return confirm('CẢNH BÁO: Bạn chắc chắn đã chuyển tiền cho TẤT CẢ các shop đang chờ và muốn Duyệt Hàng Loạt chứ?')">
                                        <i class="fa-solid fa-check-double"></i> Duyệt Tất Cả
                                    </a>
                                    <form method="GET" class="d-flex gap-2 m-0">
                                        <select name="trangthairut" class="form-select" onchange="this.form.submit()">
                                            <option value="">- Tất cả lệnh rút -</option>
                                            <option value="ChoDuyet" <?= $filter_rutTien == 'ChoDuyet' ? 'selected' : '' ?>>⏳ Đang chờ duyệt</option>
                                            <option value="DaChuyen" <?= $filter_rutTien == 'DaChuyen' ? 'selected' : '' ?>>✅ Đã chuyển thành công</option>
                                            <option value="TuChoi" <?= $filter_rutTien == 'TuChoi' ? 'selected' : '' ?>>❌ Bị từ chối</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover qlt-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã Lệnh</th>
                                        <th>Shop Yêu cầu</th>
                                        <th>Ngân hàng & STK</th>
                                        <th>Số tiền rút</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dsRutTien)): ?>
                                        <tr>
                                            <td colspan="6" class="text-muted py-4">Không có lệnh rút tiền nào.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dsRutTien as $yc): ?>
                                            <tr>
                                                <td class="fw-bold">#<?= $yc['MaYC'] ?><br><small class="text-muted fw-normal"><?= date('d/m/Y H:i', strtotime($yc['NgayYeuCau'])) ?></small></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($yc['TenCuaHang'] ?? $yc['TenTK']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($yc['Email']) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border fs-6"><?= htmlspecialchars($yc['NganHang']) ?></span><br>
                                                    <strong><?= htmlspecialchars($yc['SoTaiKhoan']) ?></strong><br>
                                                    <small class="text-primary"><?= htmlspecialchars($yc['TenChuTaiKhoan']) ?></small>
                                                </td>
                                                <td class="fs-5 fw-bold text-danger"><?= number_format($yc['SoTien'], 0, ',', '.') ?>đ</td>
                                                <td>
                                                    <?php if ($yc['TrangThai'] == 'ChoDuyet'): ?>
                                                        <span class="badge bg-warning text-dark px-3 py-2">Chờ chuyển</span>
                                                    <?php elseif ($yc['TrangThai'] == 'DaChuyen'): ?>
                                                        <span class="badge bg-success px-3 py-2">Đã chuyển</span><br>
                                                        <small class="text-muted"><?= date('d/m/y H:i', strtotime($yc['NgayXuLy'])) ?></small>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger px-3 py-2">Bị từ chối</span><br>
                                                        <small class="text-danger" title="<?= htmlspecialchars($yc['LyDoTuChoi']) ?>"><i class="fa-solid fa-circle-exclamation"></i> Lỗi STK</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($yc['TrangThai'] == 'ChoDuyet'): ?>
                                                        <div class="d-flex flex-column gap-1 align-items-center">
                                                            <a href="adminDoanhThuController.php?action=approve&id=<?= $yc['MaYC'] ?>" class="btn btn-sm btn-success w-100 fw-bold" onclick="return confirm('Bạn XÁC NHẬN ĐÃ CHUYỂN KHOẢN cho shop này?')">
                                                                <i class="fa-solid fa-check"></i> Đã chuyển
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-danger w-100" onclick="tuChoiRutTien(<?= $yc['MaYC'] ?>)">
                                                                Từ chối (Hoàn tiền)
                                                            </button>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="doanh-thu" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover qlt-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Top</th>
                                        <th>Shop / Người Bán</th>
                                        <th>Đơn Hoàn Tất</th>
                                        <th>Tổng Doanh Thu Shop</th>
                                        <th>Phí Sàn Admin Thu Được</th>
                                        <th>Số Dư Ví Của Shop Hiện Tại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dsDoanhThu)): ?>
                                        <tr>
                                            <td colspan="6" class="text-muted py-4">Chưa có dữ liệu.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $stt = 1;
                                        foreach ($dsDoanhThu as $dt): ?>
                                            <tr>
                                                <td><span class="badge bg-secondary rounded-circle fs-6"><?= $stt++ ?></span></td>
                                                <td class="text-start ps-4">
                                                    <strong><i class="fa-solid fa-store text-warning"></i> <?= htmlspecialchars($dt['TenCuaHang'] ?? $dt['TenTK']) ?></strong>
                                                </td>
                                                <td class="fw-bold text-primary fs-5"><?= $dt['SoDonHoanTat'] ?></td>
                                                <td class="fw-bold text-success fs-6">+<?= number_format($dt['TongDoanhThu'], 0, ',', '.') ?>đ</td>
                                                <td class="fw-bold text-danger fs-6">+<?= number_format($dt['PhiSanThuDuoc'], 0, ',', '.') ?>đ</td>
                                                <td class="fw-bold text-dark fs-6"><?= number_format($dt['SoDu'], 0, ',', '.') ?>đ</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        setTimeout(() => {
            $(".alert").slideUp(500);
        }, 4000);

        function tuChoiRutTien(id) {
            let lydo = prompt("Vui lòng nhập lý do từ chối (VD: Sai số tài khoản, Ngân hàng bảo trì...). Tiền sẽ được cộng lại vào Số dư của Người bán.");
            if (lydo !== null && lydo.trim() !== "") {
                window.location.href = "adminDoanhThuController.php?action=reject&id=" + id + "&lydo=" + encodeURIComponent(lydo);
            }
        }
    </script>
</body>

</html>