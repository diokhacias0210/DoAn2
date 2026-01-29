<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kênh người bán - Sản phẩm</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/chiTietSanPham.css" rel="stylesheet">

    <script src="https://cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Tùy chỉnh riêng cho trang Seller để đồng bộ màu */
        body {
            background-color: #fff;
        }

        .seller-nav {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--bs-pink-200);
            padding-bottom: 10px;
        }

        .seller-nav a {
            text-decoration: none;
            font-weight: 700;
            font-size: 18px;
            color: #666;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .seller-nav a.active,
        .seller-nav a:hover {
            background-color: var(--bs-pink-100);
            color: var(--bs-pink-600);
        }

        .seller-table th {
            background-color: var(--bs-pink-500) !important;
            color: white;
            text-align: center;
            vertical-align: middle;
        }

        .seller-table td {
            vertical-align: middle;
        }

        .btn-pink {
            background-color: var(--bs-pink-500);
            color: white;
            border: none;
        }

        .btn-pink:hover {
            background-color: var(--bs-pink-600);
            color: white;
        }

        /* Modal Style theo theme */
        .modal-header {
            background-color: var(--bs-pink-500);
            color: white;
        }

        .modal-content {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .img-preview-box {
            position: relative;
            display: inline-block;
            margin: 5px;
            border: 1px solid #ddd;
            padding: 2px;
            border-radius: 4px;
        }

        .img-preview-box img {
            height: 60px;
            width: 60px;
            object-fit: cover;
        }

        .img-preview-box .btn-remove {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            line-height: 18px;
            text-align: center;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="container" style="max-width: 1200px; padding: 0;">

            <div class="seller-nav">
                <a href="sellerSanPhamController.php" class="active"><i class="fa-solid fa-box"></i> Quản lý sản phẩm</a>
                <a href="sellerDonHangController.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <form method="GET" class="d-flex" style="gap: 10px;">
                    <input type="text" name="search" class="form-control" placeholder="Tìm tên sản phẩm..." value="<?= htmlspecialchars($keyword) ?>" style="width: 300px; border-radius: 20px;">
                    <button class="btn btn-pink" style="border-radius: 20px;"><i class="fas fa-search"></i> Tìm</button>
                </form>
                <button class="btn btn-pink" onclick="openModal('add')" style="border-radius: 20px; padding: 8px 20px; font-weight: bold;">
                    <i class="fas fa-plus"></i> Đăng bán mới
                </button>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <table class="table table-hover seller-table">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá bán</th>
                            <th>Kho</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($danhSachSanPham)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Bạn chưa đăng sản phẩm nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($danhSachSanPham as $sp): ?>
                                <tr>
                                    <td class="text-center">
                                        <img src="../../<?= !empty($sp['AnhDaiDien']) ? $sp['AnhDaiDien'] : 'assets/images/placeholder.png' ?>"
                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #eee;">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($sp['TenHH']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($sp['TenDM']) ?></small>
                                    </td>
                                    <td class="text-center text-danger font-weight-bold">
                                        <?= number_format($sp['Gia'], 0, ',', '.') ?>đ
                                    </td>
                                    <td class="text-center"><?= $sp['SoLuongHH'] ?></td>
                                    <td class="text-center">
                                        <?php if ($sp['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                            <span class="badge bg-warning text-dark">⏳ Chờ duyệt</span>
                                        <?php elseif ($sp['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                            <span class="badge bg-success">✅ Đã duyệt</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">❌ Bị từ chối</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="sellerSanPhamController.php?edit=<?= $sp['MaHH'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="sellerSanPhamController.php?xoa=<?= $sp['MaHH'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
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

    <div id="productModal" class="modal" tabindex="-1" style="background: rgba(0,0,0,0.5); <?php echo isset($edit_item) ? 'display:block;' : 'display:none;'; ?>">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="sellerSanPhamController.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= isset($edit_item) ? 'Cập nhật sản phẩm' : 'Đăng bán sản phẩm mới' ?></h5>
                        <button type="button" class="btn-close" onclick="closeModal()" style="filter: invert(1);"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <input type="hidden" name="action" value="<?= isset($edit_item) ? 'update' : 'add' ?>">
                        <input type="hidden" name="mahh" value="<?= $edit_item['MaHH'] ?? 0 ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tên sản phẩm (*)</label>
                                <input type="text" name="ten" class="form-control" required value="<?= htmlspecialchars($edit_item['TenHH'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục (*)</label>
                                <select name="madm" class="form-select" required>
                                    <?php foreach ($danhSachDanhMuc as $dm): ?>
                                        <option value="<?= $dm['MaDM'] ?>" <?= (isset($edit_item) && $edit_item['MaDM'] == $dm['MaDM']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dm['TenDM']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ) (*)</label>
                                <input type="number" name="gia" class="form-control" required min="1000" value="<?= $edit_item['Gia'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá thị trường (VNĐ)</label>
                                <input type="number" name="giathitruong" class="form-control" min="0" value="<?= $edit_item['GiaThiTruong'] ?? '' ?>">
                                <small class="text-muted" style="font-size: 11px;">(Để so sánh giảm giá)</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số lượng trong kho (*)</label>
                                <input type="number" name="soluong" class="form-control" required min="1" value="<?= $edit_item['SoLuongHH'] ?? 1 ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Chất lượng hàng</label>
                                <select name="chatluong" class="form-select">
                                    <option value="Mới" <?= (isset($edit_item) && $edit_item['ChatLuongHang'] == 'Mới') ? 'selected' : '' ?>>Mới 100%</option>
                                    <option value="Gần như mới" <?= (isset($edit_item) && $edit_item['ChatLuongHang'] == 'Gần như mới') ? 'selected' : '' ?>>Gần như mới (99%)</option>
                                    <option value="Đã qua sử dụng" <?= (isset($edit_item) && $edit_item['ChatLuongHang'] == 'Đã qua sử dụng') ? 'selected' : '' ?>>Đã qua sử dụng</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Trạng thái kinh doanh</label>
                                <select name="tinhtranghang" class="form-select">
                                    <option value="Còn hàng" <?= (isset($edit_item) && $edit_item['TinhTrangHang'] == 'Còn hàng') ? 'selected' : '' ?>>Đang bán</option>
                                    <option value="Ngưng kinh doanh" <?= (isset($edit_item) && $edit_item['TinhTrangHang'] == 'Ngưng kinh doanh') ? 'selected' : '' ?>>Tạm ngưng bán</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình ảnh sản phẩm</label>
                            <input type="file" name="image_file[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Giữ Ctrl để chọn nhiều ảnh.</small>

                            <?php if (isset($edit_item['DanhSachAnh']) && !empty($edit_item['DanhSachAnh'])): ?>
                                <div class="mt-2 p-2 border rounded bg-light">
                                    <label style="font-size: 12px; font-weight: bold;">Ảnh hiện tại (Click X để xóa):</label><br>
                                    <?php foreach ($edit_item['DanhSachAnh'] as $img): ?>
                                        <div class="img-preview-box">
                                            <img src="../../<?= $img['URL'] ?>">
                                            <span class="btn-remove" onclick="deleteImage(this, '<?= $img['URL'] ?>', <?= $edit_item['MaHH'] ?>)">x</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea name="mota" id="mota" class="form-control" rows="4"><?= $edit_item['MoTa'] ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy bỏ</button>
                        <button type="submit" class="btn btn-pink">Lưu sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Khởi tạo CKEditor cho mô tả
        if (document.getElementById('mota')) {
            CKEDITOR.replace('mota');
        }

        function openModal(mode) {
            if (mode === 'add') {
                if (window.location.search.includes('edit')) {
                    window.location.href = 'sellerSanPhamController.php';
                } else {
                    document.getElementById('productModal').style.display = 'block';
                }
            }
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
            if (window.location.search.includes('edit')) {
                window.location.href = 'sellerSanPhamController.php';
            }
        }

        function deleteImage(element, url, mahh) {
            if (confirm('Bạn muốn xóa ảnh này?')) {
                // Gửi AJAX để xóa ảnh (cần thêm case delete_image trong controller)
                // Trong phạm vi bài này, ta tạm ẩn nó đi
                element.parentElement.style.display = 'none';

                // Nếu muốn xử lý backend, cần thêm code AJAX gọi controller
                /*
                $.post('sellerSanPhamController.php', { action: 'delete_image', url: url, mahh: mahh }, function(res){ ... });
                */
            }
        }
    </script>
</body>

</html>