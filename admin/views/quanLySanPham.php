<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .admin-container {
            padding: 20px;
            background: #f4f6f9;
            min-height: 100vh;
        }

        .qlsp-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .qlsp-table th {
            background-color: #4CAF50 !important;
            color: white;
            border: none;
            vertical-align: middle;
            white-space: nowrap;
        }

        .qlsp-table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">
                <?= $message ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="m-0 text-dark fw-bold"><i class="fa-solid fa-box-open text-success"></i> Quản lý Sản Phẩm</h2>
                    </div>

                    <a href="adminSanPhamController.php?action=duyet_tat_ca" class="btn btn-primary fw-bold" onclick="return confirm('Bạn chắc chắn muốn duyệt TẤT CẢ các sản phẩm đang chờ chứ?')">
                        <i class="fa-solid fa-check-double"></i> Duyệt Tất Cả
                    </a>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body bg-white rounded">
                        <form method="GET" action="adminSanPhamController.php" class="row gx-2 gy-2 align-items-center">
                            <div class="col-md-2">
                                <input type="text" name="search" class="form-control" placeholder="Tên SP / Người bán..." value="<?= htmlspecialchars($keyword) ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="madm" class="form-select">
                                    <option value="">- Danh mục -</option>
                                    <?php foreach ($danhSachDanhMuc as $dm): ?>
                                        <option value="<?= $dm['MaDM'] ?>" <?= $filter_dm == $dm['MaDM'] ? 'selected' : '' ?>><?= $dm['TenDM'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="idnguoiban" class="form-select">
                                    <option value="">- Người bán -</option>
                                    <?php foreach ($danhSachNguoiBan as $nb): ?>
                                        <option value="<?= $nb['IdTaiKhoan'] ?>" <?= $filter_nguoiban == $nb['IdTaiKhoan'] ? 'selected' : '' ?>><?= $nb['TenTK'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="trangthaiduyet" class="form-select">
                                    <option value="">- Trạng thái -</option>
                                    <option value="ChoDuyet" <?= $filter_duyet == 'ChoDuyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                                    <option value="DaDuyet" <?= $filter_duyet == 'DaDuyet' ? 'selected' : '' ?>>Đã duyệt</option>
                                    <option value="TuChoi" <?= $filter_duyet == 'TuChoi' ? 'selected' : '' ?>>Từ chối</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="sort" class="form-select">
                                    <option value="new" <?= $sort == 'new' ? 'selected' : '' ?>>Mới nhất</option>
                                    <option value="gia_desc" <?= $sort == 'gia_desc' ? 'selected' : '' ?>>Giá cao xuống thấp</option>
                                    <option value="tonkho_desc" <?= $sort == 'tonkho_desc' ? 'selected' : '' ?>>Tồn kho nhiều</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-success w-100"><i class="fas fa-filter"></i> Lọc</button>
                                <a href="adminSanPhamController.php" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-right"></i></a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover qlsp-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Ảnh</th>
                                <th style="width: 25%;">Tên SP / Danh mục</th>
                                <th>Người bán</th>
                                <th>Giá bán</th>
                                <th>Kho</th>
                                <th>Trạng thái</th>
                                <th class="text-center" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($danhSachSanPham)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy sản phẩm nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($danhSachSanPham as $r): ?>
                                    <tr>
                                        <td><img src="../../<?= $r['AnhDaiDien'] ?? 'assets/images/placeholder.png' ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;"></td>
                                        <td>
                                            <strong class="text-dark"><?= htmlspecialchars($r['TenHH']) ?></strong><br>
                                            <small class="text-muted"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($r['TenDM']) ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><i class="fas fa-user text-success"></i> <?= htmlspecialchars($r['NguoiBan']) ?></span></td>
                                        <td class="text-danger fw-bold"><?= number_format($r['Gia'], 0, ',', '.') ?>đ</td>
                                        <td><?= $r['SoLuongHH'] ?></td>
                                        <td>
                                            <?php if ($r['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                                <span class="badge bg-warning text-dark">⏳ Chờ duyệt</span>
                                            <?php elseif ($r['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                                <span class="badge bg-success">✅ Đã duyệt</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">❌ Bị từ chối</span>
                                                <div style="font-size: 11px; margin-top: 3px; color: red;" title="<?= htmlspecialchars($r['LyDoTuChoi']) ?>">
                                                    (<?= htmlspecialchars(mb_substr($r['LyDoTuChoi'], 0, 20)) ?>...)
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($r['HienThi'] == 0): ?><br><span class="badge bg-secondary mt-1"><i class="fas fa-eye-slash"></i> Đang ẩn</span><?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group shadow-sm">
                                                <a href="adminChiTietSanPhamController.php?id=<?= $r['MaHH'] ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                                    <i class="fa-solid fa-bars"></i>
                                                </a>

                                                <?php if ($r['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                                    <a href="adminSanPhamController.php?action=duyet&id=<?= $r['MaHH'] ?>" class="btn btn-sm btn-success" title="Duyệt bài"><i class="fa-solid fa-check"></i></a>

                                                    <button type="button" class="btn btn-sm btn-warning" onclick="tuChoiSanPham(<?= $r['MaHH'] ?>)" title="Từ chối"><i class="fa-solid fa-xmark"></i></button>

                                                <?php elseif ($r['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                                    <a href="adminSanPhamController.php?action=<?= $r['HienThi'] == 1 ? 'an' : 'hien' ?>&id=<?= $r['MaHH'] ?>" class="btn btn-sm <?= $r['HienThi'] == 1 ? 'btn-secondary' : 'btn-primary' ?>" title="<?= $r['HienThi'] == 1 ? 'Ẩn bài' : 'Hiện bài' ?>">
                                                        <i class="fa-solid <?= $r['HienThi'] == 1 ? 'fa-eye-slash' : 'fa-eye-action' ?>"></i>
                                                    </a>
                                                <?php endif; ?>

                                            </div>
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
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>&madm=<?= $filter_dm ?>&idnguoiban=<?= $filter_nguoiban ?>&trangthaiduyet=<?= $filter_duyet ?>&sort=<?= $sort ?>">« Trước</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>&madm=<?= $filter_dm ?>&idnguoiban=<?= $filter_nguoiban ?>&trangthaiduyet=<?= $filter_duyet ?>&sort=<?= $sort ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>&madm=<?= $filter_dm ?>&idnguoiban=<?= $filter_nguoiban ?>&trangthaiduyet=<?= $filter_duyet ?>&sort=<?= $sort ?>">Sau »</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            $(".alert").slideUp(500);
        }, 3000);

        function tuChoiSanPham(id) {
            let lydo = prompt("Vui lòng nhập lý do từ chối sản phẩm này:", "Hình ảnh hoặc nội dung vi phạm tiêu chuẩn.");
            if (lydo !== null && lydo.trim() !== "") {
                window.location.href = "adminSanPhamController.php?action=tuchoi&id=" + id + "&lydo=" + encodeURIComponent(lydo);
            }
        }
    </script>
</body>

</html>