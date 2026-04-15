<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kênh người bán - Doanh Thu & Ví Tiền</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
        }

        .seller-wrapper {
            max-width: 1300px;
            margin: 100px auto 50px auto !important;
            padding: 0 15px;
        }

        .seller-content-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            min-height: 600px;
        }

        .seller-sidebar {
            background: #ffffff;
            border-radius: 10px;
            padding: 15px 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .seller-sidebar a {
            text-decoration: none;
            color: #555;
            font-weight: 600;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .seller-sidebar a:hover {
            background-color: #f8f9fa;
            color: var(--bs-pink-500);
        }

        .seller-sidebar a.active {
            background-color: var(--bs-pink-100);
            color: var(--bs-pink-600);
            border-left: 4px solid var(--bs-pink-600);
        }

        .stat-card {
            border-radius: 10px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-card i {
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 100px;
            opacity: 0.2;
        }

        .bg-pink-grad {
            background: linear-gradient(135deg, var(--bs-pink-400), var(--bs-pink-600));
        }

        .bg-blue-grad {
            background: linear-gradient(135deg, #36D1DC, #5B86E5);
        }

        .bg-orange-grad {
            background: linear-gradient(135deg, #f12711, #f5af19);
        }

        .dt-table th {
            background-color: var(--bs-pink-500) !important;
            color: white;
            text-align: center;
        }

        .toast-container {
            z-index: 1055;
            margin-top: 80px;
        }
    </style>
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <?php if (!empty($message)): ?>
            <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold"><i class="fa-solid fa-circle-check"></i> <?= $message ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="seller-wrapper">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="seller-sidebar">
                    <a href="sellerThongTinController.php"><i class="fa-solid fa-circle-info"></i> Thông tin cửa hàng</a>
                    <a href="sellerSanPhamController.php"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
                    <a href="sellerDonHangController.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
                    <a href="sellerChatController.php">
                        <i class="fa-solid fa-comments"></i> Tin nhắn

                        <?php if (isset($soTinNhanChuaDoc) && $soTinNhanChuaDoc > 0): ?>
                            <span class="chat-badge"><?php echo $soTinNhanChuaDoc; ?></span>
                        <?php endif; ?>
                    </a>      
                    <a href="sellerDoanhThuController.php" class="active"><i class="fa-solid fa-wallet"></i> Doanh thu & Ví tiền</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="seller-content-box">
                    <h4 class="mb-4 fw-bold" style="color: var(--bs-pink-600);"><i class="fa-solid fa-wallet"></i> DOANH THU & VÍ TIỀN</h4>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card bg-orange-grad">
                                <h6 class="fw-bold mb-2">Số Dư Hiện Tại</h6>
                                <h3 class="fw-bold mb-3"><?= number_format($soDuHienTai, 0, ',', '.') ?> đ</h3>
                                <button class="btn btn-light btn-sm fw-bold text-dark w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRutTien">
                                    <i class="fa-solid"></i> Yêu cầu Rút tiền
                                </button>
                                <i class="fa-solid fa-piggy-bank"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card bg-pink-grad">
                                <h6 class="fw-bold mb-2">Doanh Thu (Trong kỳ lọc)</h6>
                                <h3 class="fw-bold mb-3"><?= number_format($thongKe['TongDoanhThu'] ?? 0, 0, ',', '.') ?> đ</h3>
                                <small class="opacity-75">Tiền thực nhận từ đơn hoàn tất</small>
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card bg-blue-grad">
                                <h6 class="fw-bold mb-2">Đơn Hàng (Trong kỳ lọc)</h6>
                                <h3 class="fw-bold mb-3"><?= $thongKe['SoDon'] ?? 0 ?> đơn</h3>
                                <small class="opacity-75">Đơn hàng đã giao thành công</small>
                                <i class="fa-solid fa-box-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" class="row gx-2 gy-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="fw-bold text-muted small">Từ ngày</label>
                                    <input type="date" name="tungay" class="form-control" value="<?= $tuNgay ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold text-muted small">Đến ngày</label>
                                    <input type="date" name="denngay" class="form-control" value="<?= $denNgay ?>" required>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn text-white w-100 fw-bold" style="background: var(--bs-pink-500);"><i class="fa-solid fa-filter"></i> Lọc Doanh Thu</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-fire text-danger"></i> Sản phẩm bán chạy nhất kỳ này</h5>
                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0 dt-table">
                                    <thead>
                                        <tr>
                                            <th>Top</th>
                                            <th class="text-start">Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th>Đã bán</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($topSanPham)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Chưa có sản phẩm nào được bán trong khoảng thời gian này.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php $top = 1;
                                            foreach ($topSanPham as $sp): ?>
                                                <tr>
                                                    <td class="text-center fw-bold text-danger">#<?= $top++ ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="../../<?= $sp['AnhDaiDien'] ?? 'assets/images/placeholder.png' ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                                            <span class="fw-bold"><?= htmlspecialchars($sp['TenHH']) ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center text-danger"><?= number_format($sp['Gia'], 0, ',', '.') ?>đ</td>
                                                    <td class="text-center fw-bold text-success"><?= $sp['TongDaBan'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử rút tiền gần đây</h5>
                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0 dt-table">
                                    <thead>
                                        <tr>
                                            <th>Ngày tạo lệnh</th>
                                            <th>Ngân hàng</th>
                                            <th>Số tiền rút</th>
                                            <th>Trạng thái</th>
                                            <th>Ghi chú từ Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($lichSuRut)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Bạn chưa có lịch sử rút tiền nào.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($lichSuRut as $ls): ?>
                                                <tr>
                                                    <td class="text-center"><?= date('d/m/Y H:i', strtotime($ls['NgayYeuCau'])) ?></td>
                                                    <td class="text-center">
                                                        <strong><?= htmlspecialchars($ls['NganHang']) ?></strong><br>
                                                        <small><?= htmlspecialchars($ls['SoTaiKhoan']) ?></small>
                                                    </td>
                                                    <td class="text-center fw-bold text-danger"><?= number_format($ls['SoTien'], 0, ',', '.') ?>đ</td>
                                                    <td class="text-center">
                                                        <?php if ($ls['TrangThai'] == 'ChoDuyet'): ?>
                                                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                                                        <?php elseif ($ls['TrangThai'] == 'DaChuyen'): ?>
                                                            <span class="badge bg-success">Thành công</span><br>
                                                            <small class="text-muted"><?= date('d/m/Y', strtotime($ls['NgayXuLy'])) ?></small>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Bị từ chối</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center text-muted small"><?= $ls['LyDoTuChoi'] ?? '-' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRutTien" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="sellerDoanhThuController.php" method="POST">
                    <div class="modal-header text-white" style="background: var(--bs-pink-500);">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-money-bill-transfer"></i> Rút tiền về Ngân hàng</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="rut_tien">

                        <div class="alert alert-info text-center">
                            Số dư có thể rút: <br><strong class="fs-4 text-danger"><?= number_format($soDuHienTai, 0, ',', '.') ?> đ</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số tiền muốn rút (*)</label>
                            <input type="number" name="soTien" class="form-control" required min="50000" max="<?= $soDuHienTai ?>" placeholder="Tối thiểu 50.000đ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngân hàng (*)</label>
                            <input type="text" name="nganHang" class="form-control" required placeholder="VD: Vietcombank, MB Bank...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số tài khoản (*)</label>
                            <input type="text" name="stk" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên chủ tài khoản (*)</label>
                            <input type="text" name="chuTk" class="form-control" required placeholder="VIẾT HOA KHÔNG DẤU">
                        </div>
                        <p class="text-muted small m-0"><i class="fa-solid fa-triangle-exclamation text-warning"></i> Lưu ý: Tiền sẽ được xử lý vào tài khoản của bạn trong 1-2 ngày làm việc sau khi Admin duyệt.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn text-white fw-bold" style="background: var(--bs-pink-500);">Tạo lệnh rút tiền</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            $('.toast').toast('hide');
        }, 3500);
    </script>
    <script src="../../assets/js/js.js"></script>
</body>

</html>