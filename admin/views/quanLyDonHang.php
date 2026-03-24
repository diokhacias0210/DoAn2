<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng toàn hệ thống</title>
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

        .qldh-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* ĐỔI MÀU TIÊU ĐỀ BẢNG GIỐNG TRANG THÔNG BÁO/BÁO CÁO */
        .qldh-table th {
            background-color: #4CAF50 !important;
            color: white;
            border: none;
            vertical-align: middle;
            white-space: nowrap;
        }

        .qldh-table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>
        <main class="main-content">
            <div class="admin-container">

                <div class="mb-4">
                    <h2 class="m-0 text-dark fw-bold"><i class="fa-solid fa-truck-fast text-success"></i> Quản lý Đơn hàng</h2>
                    <p class="text-muted mt-1">Giám sát các giao dịch giữa người mua và người bán trên toàn hệ thống.</p>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body bg-white rounded">
                        <form method="GET" action="adminDonHangController.php" class="row gx-2 gy-2 align-items-end">
                            <div class="col-md-2">
                                <label class="fw-bold text-muted small">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã đơn, Tên mua/bán..." value="<?= htmlspecialchars($keyword) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold text-muted small">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">- Tất cả -</option>
                                    <option value="Chờ xử lý" <?= ($filter_status == 'Chờ xử lý') ? 'selected' : '' ?>>Chờ xử lý</option>
                                    <option value="Đã xác nhận" <?= ($filter_status == 'Đã xác nhận') ? 'selected' : '' ?>>Đã xác nhận</option>
                                    <option value="Đang giao" <?= ($filter_status == 'Đang giao') ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="Hoàn tất" <?= ($filter_status == 'Hoàn tất') ? 'selected' : '' ?>>Hoàn tất</option>
                                    <option value="Đã hủy" <?= ($filter_status == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold text-muted small">Từ ngày</label>
                                <input type="date" name="tungay" class="form-control" value="<?= $tuNgay ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold text-muted small">Đến ngày</label>
                                <input type="date" name="denngay" class="form-control" value="<?= $denNgay ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold text-muted small">Sắp xếp</label>
                                <select name="sort" class="form-select">
                                    <option value="new" <?= ($sort == 'new') ? 'selected' : '' ?>>Mới nhất</option>
                                    <option value="cu" <?= ($sort == 'cu') ? 'selected' : '' ?>>Cũ nhất</option>
                                    <option value="giacao" <?= ($sort == 'giacao') ? 'selected' : '' ?>>Giá trị cao</option>
                                    <option value="giathap" <?= ($sort == 'giathap') ? 'selected' : '' ?>>Giá trị thấp</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-success w-100"><i class="fas fa-filter"></i> Lọc</button>
                                <a href="adminDonHangController.php" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-right"></i></a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover qldh-table align-middle">
                        <thead>
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Ngày đặt</th>
                                <th>Người Mua</th>
                                <th>Người Bán (Shop)</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-end">Phí sàn thu</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dsDonHang)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($dsDonHang as $dh): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#<?= $dh['MaDH'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?></td>

                                        <td><strong><?= htmlspecialchars($dh['NguoiMua']) ?></strong></td>

                                        <td><span class="badge bg-light text-dark border"><i class="fa-solid fa-store text-info"></i> <?= htmlspecialchars($dh['NguoiBan']) ?></span></td>

                                        <td class="text-end fw-bold text-danger"><?= number_format($dh['TongTien'], 0, ',', '.') ?>đ</td>
                                        <td class="text-end fw-bold text-success">+<?= number_format($dh['PhiSan'], 0, ',', '.') ?>đ</td>

                                        <td class="text-center">
                                            <?php
                                            $tt = $dh['TrangThai'];
                                            if ($tt == 'Chờ xử lý') echo '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
                                            elseif ($tt == 'Đã xác nhận') echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                            elseif ($tt == 'Đang giao') echo '<span class="badge" style="background:#6f42c1;">Đang giao</span>';
                                            elseif ($tt == 'Hoàn tất') echo '<span class="badge bg-success">Hoàn tất</span>';
                                            elseif ($tt == 'Đã hủy') echo '<span class="badge bg-danger">Đã hủy</span>';
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="adminChiTietDonHangController.php?id=<?= $dh['MaDH'] ?>" class="btn btn-sm btn-info text-white shadow-sm" title="Xem chi tiết">
                                                <i class="fa-solid fa-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>&status=<?= urlencode($filter_status) ?>&tungay=<?= $tuNgay ?>&denngay=<?= $denNgay ?>&sort=<?= $sort ?>">«</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>&status=<?= urlencode($filter_status) ?>&tungay=<?= $tuNgay ?>&denngay=<?= $denNgay ?>&sort=<?= $sort ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>&status=<?= urlencode($filter_status) ?>&tungay=<?= $tuNgay ?>&denngay=<?= $denNgay ?>&sort=<?= $sort ?>">»</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>