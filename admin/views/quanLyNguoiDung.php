<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Người dùng</title>
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

        .qlnd-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .qlnd-table th {
            background-color: #34495e !important;
            color: white;
            border: none;
            vertical-align: middle;
        }

        .qlnd-table td {
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

                <div class="mb-4">
                    <h2 class="m-0 text-dark fw-bold"><i class="fa-solid fa-users"></i> Quản lý Người dùng</h2>
                    <p class="text-muted mt-1">Tìm kiếm, lọc và quản lý trạng thái hoạt động của thành viên.</p>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body bg-white rounded">
                        <form method="GET" action="adminNguoiDungController.php" class="row gx-2 gy-2 align-items-center">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Tên, Email hoặc SĐT..." value="<?= htmlspecialchars($keyword) ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="vaitro" class="form-select">
                                    <option value="">- Tất cả vai trò -</option>
                                    <option value="0" <?= $filter_vaitro === '0' ? 'selected' : '' ?>>Khách hàng / Người bán</option>
                                    <option value="1" <?= $filter_vaitro === '1' ? 'selected' : '' ?>>Quản trị viên (Admin)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="trangthai" class="form-select">
                                    <option value="">- Trạng thái hoạt động -</option>
                                    <option value="DangHoatDong" <?= $filter_trangthai == 'DangHoatDong' ? 'selected' : '' ?>>Đang hoạt động</option>
                                    <option value="BiKhoa" <?= $filter_trangthai == 'BiKhoa' ? 'selected' : '' ?>>Bị khóa vĩnh viễn</option>
                                    <option value="ChuaKichHoat" <?= $filter_trangthai == 'ChuaKichHoat' ? 'selected' : '' ?>>Chưa có Cửa hàng</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-dark"><i class="fas fa-filter"></i> Lọc</button>
                                <a href="adminNguoiDungController.php" class="btn btn-outline-secondary">Xóa</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover qlnd-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 70px;" class="text-center">Ảnh</th>
                                <th>Thông tin người dùng</th>
                                <th>Vai trò</th>
                                <th class="text-center">Gậy vi phạm</th>
                                <th>Trạng thái</th>
                                <th class="text-center" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($danhSachNguoiDung)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Không tìm thấy người dùng nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($danhSachNguoiDung as $nd): ?>
                                    <tr>
                                        <td class="text-center">
                                            <img src="../../<?= !empty($nd['Avatar']) ? $nd['Avatar'] : 'assets/images/placeholder.png' ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 1px solid #ddd;">
                                        </td>
                                        <td>
                                            <strong class="text-primary fs-6"><?= htmlspecialchars($nd['TenTK']) ?></strong><br>
                                            <small class="text-muted"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($nd['Email']) ?> &nbsp;|&nbsp; <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($nd['Sdt']) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($nd['VaiTro'] == 1): ?>
                                                <span class="badge bg-danger"><i class="fa-solid fa-crown"></i> Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark"><i class="fa-solid fa-user"></i> Khách hàng</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($nd['DiemViPham'] > 0): ?>
                                                <span class="badge bg-warning text-dark fs-6 rounded-circle" title="<?= $nd['DiemViPham'] ?> lần vi phạm"><?= $nd['DiemViPham'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($nd['TrangThaiBanHang'] == 'DangHoatDong'): ?>
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            <?php elseif ($nd['TrangThaiBanHang'] == 'BiKhoa'): ?>
                                                <span class="badge bg-danger">Bị khóa vĩnh viễn</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Chưa kích hoạt</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group shadow-sm">
                                                <a href="adminChiTietNguoiDungController.php?id=<?= $nd['IdTaiKhoan'] ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                                    <i class="fa-solid fa-bars"></i>
                                                </a>

                                                <?php if ($nd['VaiTro'] != 1): // Không khóa Admin 
                                                ?>
                                                    <?php if ($nd['TrangThaiBanHang'] != 'BiKhoa'): ?>
                                                        <a href="adminNguoiDungController.php?action=lock&id=<?= $nd['IdTaiKhoan'] ?>" class="btn btn-sm btn-danger" title="Khóa tài khoản" onclick="return confirm('Xác nhận KHÓA tài khoản bán hàng này?')">
                                                            <i class="fa-solid fa-lock"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="adminNguoiDungController.php?action=unlock&id=<?= $nd['IdTaiKhoan'] ?>" class="btn btn-sm btn-success" title="Mở khóa tài khoản" onclick="return confirm('MỞ KHÓA cho tài khoản này?')">
                                                            <i class="fa-solid fa-unlock"></i>
                                                        </a>
                                                    <?php endif; ?>
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
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>&vaitro=<?= $filter_vaitro ?>&trangthai=<?= $filter_trangthai ?>">« Trước</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>&vaitro=<?= $filter_vaitro ?>&trangthai=<?= $filter_trangthai ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>&vaitro=<?= $filter_vaitro ?>&trangthai=<?= $filter_trangthai ?>">Sau »</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        setTimeout(() => {
            $(".alert").slideUp(500);
        }, 3000);
    </script>
</body>

</html>