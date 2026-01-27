<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kênh người bán - Quản lý sản phẩm</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script>

    <style>
        body {
            background: #f5f6fa;
        }

        .seller-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .badge-pending {
            background: #ffc107;
            color: #000;
        }

        .badge-approved {
            background: #28a745;
            color: #fff;
        }

        .badge-rejected {
            background: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">TWO HAND STORE</a>
            <div class="ml-auto">
                <span class="text-white">Xin chào, <?php echo $_SESSION['TenTK']; ?></span>
                <a href="../../controllers/dangXuatController.php" class="btn btn-sm btn-outline-light ml-2">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="seller-container">

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>📦 Sản phẩm của tôi</h3>
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Đăng bán mới
                </button>
            </div>

            <form method="GET" class="mb-3 d-flex" style="max-width: 400px;">
                <input type="text" name="search" class="form-control mr-2" placeholder="Tìm tên sản phẩm..." value="<?= htmlspecialchars($keyword) ?>">
                <button class="btn btn-secondary">Tìm</button>
            </form>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá bán</th>
                        <th>Kho</th>
                        <th>Trạng thái duyệt</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($danhSachSanPham)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Bạn chưa đăng sản phẩm nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhSachSanPham as $sp): ?>
                            <tr>
                                <td>
                                    <img src="../../<?= !empty($sp['AnhDaiDien']) ? $sp['AnhDaiDien'] : 'assets/images/placeholder.png' ?>">
                                </td>
                                <td><?= htmlspecialchars($sp['TenHH']) ?></td>
                                <td><?= number_format($sp['Gia'], 0, ',', '.') ?>đ</td>
                                <td><?= $sp['SoLuongHH'] ?></td>
                                <td>
                                    <?php if ($sp['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                        <span class="badge badge-pending">Chờ duyệt</span>
                                    <?php elseif ($sp['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                        <span class="badge badge-approved">Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="badge badge-rejected">Bị từ chối</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="sellerSanPhamController.php?edit=<?= $sp['MaHH'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="sellerSanPhamController.php?xoa=<?= $sp['MaHH'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
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

    <div id="productModal" class="modal" tabindex="-1" style="background: rgba(0,0,0,0.5); <?php echo isset($edit_item) ? 'display:block;' : 'display:none;'; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="sellerSanPhamController.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= isset($edit_item) ? 'Sửa sản phẩm' : 'Đăng bán sản phẩm mới' ?></h5>
                        <button type="button" class="btn-close" onclick="closeModal()" style="border:none; background:none; font-size:1.5rem;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= isset($edit_item) ? 'update' : 'add' ?>">
                        <input type="hidden" name="mahh" value="<?= $edit_item['MaHH'] ?? 0 ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tên sản phẩm (*)</label>
                                <input type="text" name="ten" class="form-control" required value="<?= $edit_item['TenHH'] ?? '' ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Danh mục (*)</label>
                                <select name="madm" class="form-control" required>
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
                                <label>Giá bán (VNĐ) (*)</label>
                                <input type="number" name="gia" class="form-control" required value="<?= $edit_item['Gia'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Số lượng (*)</label>
                                <input type="number" name="soluong" class="form-control" required value="<?= $edit_item['SoLuongHH'] ?? 1 ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Tình trạng</label>
                                <select name="chatluong" class="form-control">
                                    <option value="Mới" <?= (isset($edit_item) && $edit_item['ChatLuongHang'] == 'Mới') ? 'selected' : '' ?>>Mới</option>
                                    <option value="Đã qua sử dụng" <?= (isset($edit_item) && $edit_item['ChatLuongHang'] == 'Đã qua sử dụng') ? 'selected' : '' ?>>Đã qua sử dụng</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Hình ảnh (Chọn nhiều ảnh)</label>
                            <input type="file" name="image_file[]" class="form-control" multiple accept="image/*">
                            <?php if (isset($edit_item['DanhSachAnh'])): ?>
                                <div class="mt-2">
                                    <small>Ảnh hiện tại:</small><br>
                                    <?php foreach ($edit_item['DanhSachAnh'] as $img): ?>
                                        <img src="../../<?= $img['URL'] ?>" style="height: 50px; margin-right: 5px; border:1px solid #ddd;">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label>Mô tả chi tiết</label>
                            <textarea name="mota" id="mota" class="form-control" rows="4"><?= $edit_item['MoTa'] ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        CKEDITOR.replace('mota');

        function openModal(mode) {
            // Nếu là add thì xóa sạch form (đơn giản nhất là reload trang hoặc reset form bằng JS)
            if (mode === 'add') {
                // Xóa query param 'edit' trên URL để về mode add
                if (window.location.search.includes('edit')) {
                    window.location.href = 'sellerSanPhamController.php';
                } else {
                    document.getElementById('productModal').style.display = 'block';
                }
            }
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
            // Nếu đang ở mode edit thì quay về trang chủ controller để thoát mode edit
            if (window.location.search.includes('edit')) {
                window.location.href = 'sellerSanPhamController.php';
            }
        }
    </script>
</body>

</html>