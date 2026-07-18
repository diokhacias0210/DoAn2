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
        body {
            background-color: #f8f9fa;
        }

        /*  ĐỒNG BỘ KHUNG SƯỜN  */
        .seller-wrapper {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .seller-content-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            min-height: 600px;
        }

        /*  MENU BÊN TRÁI (SIDEBAR)  */
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
            position: relative;
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

        /*  BADGE TIN NHẮN  */
        .chat-badge {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background-color: #dc3545;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 50px;
            line-height: 1;
        }

        /* Custom Table & Button */
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

        /* Style Phân trang (Màu hồng) */
        .pagination .page-item.active .page-link {
            background-color: var(--bs-pink-500);
            border-color: var(--bs-pink-500);
            color: white;
        }

        .pagination .page-link {
            color: var(--bs-pink-500);
        }

        .pagination .page-link:hover {
            color: var(--bs-pink-600);
            background-color: var(--bs-pink-50);
        }
    </style>
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <?php
    // ĐẾM SỐ TIN NHẮN KHÁCH HÀNG CHƯA ĐỌC ĐỂ HIỂN THỊ BADGE TRÊN MENU "Tin nhắn" 
    $soTinNhanChuaDoc = 0;
    if (isset($_SESSION['IdTaiKhoan']) && isset($conn)) {
        $idSellerCurrent = $_SESSION['IdTaiKhoan'];

        // Câu lệnh đếm các tin nhắn thuộc phòng chat của Seller này,
        // do người khác gửi (Khách hàng) và có trạng thái DaDoc = 0
        $sqlDemTinNhan = "SELECT COUNT(tn.MaTN) AS SoLuong 
                                FROM TinNhan tn 
                                JOIN PhongChat p ON tn.MaPhong = p.MaPhong 
                                WHERE p.IdNguoiBan = $idSellerCurrent 
                                AND tn.IdNguoiGui != $idSellerCurrent 
                                AND tn.DaXem = 0";

        $rsDem = $conn->query($sqlDemTinNhan);
        if ($rsDem && $rsDem->num_rows > 0) {
            $rowDem = $rsDem->fetch_assoc();
            $soTinNhanChuaDoc = $rowDem['SoLuong'];
        }
    }
    ?>

    <div class="seller-wrapper mt-4 mb-5">
        <h3 class="mb-4 text-secondary text-center"><i class="fa-solid fa-shop"></i> KÊNH NGƯỜI BÁN</h3>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="seller-sidebar">
                    <a href="sellerThongTinController.php"><i class="fa-solid fa-circle-info"></i> Thông tin cửa hàng</a>
                    <a href="sellerSanPhamController.php" class="active"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
                    <a href="sellerDonHangController.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
                    <a href="sellerChatController.php">
                        <i class="fa-solid fa-comments"></i> Tin nhắn

                        <?php if (isset($soTinNhanChuaDoc) && $soTinNhanChuaDoc > 0): ?>
                            <span class="chat-badge"><?php echo $soTinNhanChuaDoc; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="sellerDoanhThuController.php"><i class="fa-solid fa-chart-line"></i> Doanh thu & Rút tiền</a>

                </div>
            </div>

            <div class="col-md-9">
                <div class="seller-content-box">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body rounded">
                            <form method="GET" action="sellerSanPhamController.php" class="row gx-2 gy-2 align-items-center">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm..." value="<?= htmlspecialchars($keyword ?? '') ?>">
                                </div>

                                <div class="col-md-2">
                                    <select name="madm" class="form-select">
                                        <option value="">- Mọi danh mục -</option>
                                        <?php if (isset($danhSachDanhMuc)): ?>
                                            <?php foreach ($danhSachDanhMuc as $dm): ?>
                                                <option value="<?= $dm['MaDM'] ?>" <?= (isset($filter_dm) && $filter_dm == $dm['MaDM']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($dm['TenDM']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="trangthaiduyet" class="form-select">
                                        <option value="">- Trạng thái -</option>
                                        <option value="DaDuyet" <?= (isset($filter_duyet) && $filter_duyet == 'DaDuyet') ? 'selected' : '' ?>>Đã duyệt</option>
                                        <option value="ChoDuyet" <?= (isset($filter_duyet) && $filter_duyet == 'ChoDuyet') ? 'selected' : '' ?>>Chờ duyệt</option>
                                        <option value="TuChoi" <?= (isset($filter_duyet) && $filter_duyet == 'TuChoi') ? 'selected' : '' ?>>Bị từ chối</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="sort" class="form-select">
                                        <option value="new" <?= (isset($sort) && $sort == 'new') ? 'selected' : '' ?>>Mới nhất</option>
                                        <option value="banchay" <?= (isset($sort) && $sort == 'banchay') ? 'selected' : '' ?>>Bán chạy nhất</option>
                                        <option value="gia_desc" <?= (isset($sort) && $sort == 'gia_desc') ? 'selected' : '' ?>>Giá cao đến thấp</option>
                                        <option value="gia_asc" <?= (isset($sort) && $sort == 'gia_asc') ? 'selected' : '' ?>>Giá thấp đến cao</option>
                                        <option value="tonkho_asc" <?= (isset($sort) && $sort == 'tonkho_asc') ? 'selected' : '' ?>>Sắp hết hàng</option>
                                    </select>
                                </div>

                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-pink"><i class="fas fa-filter"></i> Lọc</button>
                                    <a href="sellerSanPhamController.php" class="btn btn-outline-secondary">Xóa lọc</a>
                                    <button type="button" class="btn btn-success ms-auto" onclick="openModal('add')">
                                        <i class="fas fa-plus"></i> Đăng mới
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive bg-white p-3 border-0 shadow-sm rounded">
                        <table class="table table-hover seller-table align-middle">
                            <thead>
                                <tr>
                                    <th>Ảnh</th>
                                    <th style="width: 30%">Tên sản phẩm</th>
                                    <th>Giá bán</th>
                                    <th>Kho</th>
                                    <th>Đã bán</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($danhSachSanPham)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 10px;"></i><br>
                                            Không tìm thấy sản phẩm nào phù hợp.
                                        </td>
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
                                                <small class="text-muted"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($sp['TenDM']) ?></small>
                                            </td>
                                            <td class="text-center text-danger font-weight-bold">
                                                <?= number_format($sp['Gia'], 0, ',', '.') ?>đ
                                            </td>
                                            <td class="text-center"><?= $sp['SoLuongHH'] ?></td>
                                            <td class="text-center fw-bold text-success">
                                                <?= isset($sp['DaBan']) ? $sp['DaBan'] : 0 ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($sp['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                                    <span class="badge bg-warning text-dark">⏳ Chờ duyệt</span>
                                                <?php elseif ($sp['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                                    <span class="badge bg-success">✅ Đã duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">❌ Bị từ chối</span>
                                                    <?php if (!empty($sp['LyDoTuChoi'])): ?>
                                                        <div style="font-size: 11px; margin-top: 3px; color: red;" title="<?= htmlspecialchars($sp['LyDoTuChoi']) ?>">
                                                            (<?= htmlspecialchars(mb_substr($sp['LyDoTuChoi'], 0, 20)) ?>...)
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="sellerSanPhamController.php?edit=<?= $sp['MaHH'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="sellerChiTietSanPhamController.php?MaHH=<?= $sp['MaHH'] ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="fa-solid fa-eye"></i> Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <?php if (isset($total_pages) && $total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword ?? '') ?>&madm=<?= $filter_dm ?? '' ?>&trangthaiduyet=<?= $filter_duyet ?? '' ?>&sort=<?= $sort ?? 'new' ?>">« Trước</a>
                                    </li>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword ?? '') ?>&madm=<?= $filter_dm ?? '' ?>&trangthaiduyet=<?= $filter_duyet ?? '' ?>&sort=<?= $sort ?? 'new' ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword ?? '') ?>&madm=<?= $filter_dm ?? '' ?>&trangthaiduyet=<?= $filter_duyet ?? '' ?>&sort=<?= $sort ?? 'new' ?>">Sau »</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div id="productModal" class="modal" tabindex="-1" style="background: rgba(0,0,0,0.5); <?php echo isset($edit_item) ? 'display:block;' : 'display:none;'; ?>">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form action="sellerSanPhamController.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= isset($edit_item) ? '<i class="fas fa-edit"></i> Cập nhật sản phẩm' : '<i class="fas fa-plus-circle"></i> Đăng bán sản phẩm mới' ?></h5>
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
                                    <small class="text-muted">Giữ Ctrl để chọn nhiều ảnh. Ảnh đầu tiên sẽ làm ảnh đại diện.</small>

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
                    // Nếu đang ở URL có tham số edit, chuyển hướng về trang chủ sản phẩm để mở modal rỗng
                    window.location.href = 'sellerSanPhamController.php';
                } else {
                    document.getElementById('productModal').style.display = 'block';
                }
            }
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
            // Nếu thoát khỏi modal sửa, xóa tham số URL
            if (window.location.search.includes('edit')) {
                window.location.href = 'sellerSanPhamController.php';
            }
        }

        function deleteImage(element, url, mahh) {
            if (confirm('Bạn muốn xóa ảnh này? Lịch sử sẽ không thể hoàn tác nếu bạn nhấn lưu form.')) {
                element.parentElement.style.display = 'none';

                // Thêm input hidden để báo cho controller biết cần ảnh nào khi ấn Lưu
                let hiddenInput = document.createElement("input");
                hiddenInput.setAttribute("type", "hidden");
                hiddenInput.setAttribute("name", "delete_images[]");
                hiddenInput.setAttribute("value", url);
                document.querySelector("form").appendChild(hiddenInput);
            }
        }

        // Tự động ẩn Alert sau 3s
        $(document).ready(function() {
            setTimeout(function() {
                $(".alert").slideUp(500);
            }, 3000);
        });
        CKEDITOR.config.versionCheck = false;
    </script>
    <script src="../../assets/js/js.js"></script>

</body>

</html>