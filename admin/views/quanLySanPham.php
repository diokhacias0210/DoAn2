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
    <script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .form-message {
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .error {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .info {
            color: #0c5460;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
        }

        .qlsp-table th,
        .qlsp-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
            vertical-align: middle;
        }

        .qlsp-table img {
            height: 50px;
            width: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .add-dm-box,
        .manage-dm-box {
            margin-top: 8px;
            display: none;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            border-radius: 4px;
        }

        .manage-dm-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 150px;
            overflow-y: auto;
        }

        .manage-dm-box li {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        .manage-dm-box li:last-child {
            border-bottom: none;
        }

        .manage-dm-box input {
            flex-grow: 1;
            padding: 2px 6px;
            font-size: 0.9em;
        }

        .dm-btn {
            cursor: pointer;
            border: none;
            background: none;
            font-size: 1rem;
            padding: 0 4px;
        }

        .qlsp-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }

        .qlsp-table th,
        .qlsp-table td {
            padding: 8px 6px;
            border: 1px solid #e0e0e0;
            vertical-align: top;
            /* Căn trên cùng để khi dòng giãn cao, các cột khác vẫn đẹp */

            /* Xử lý văn bản dài */
            word-wrap: break-word;
            /* Ngắt từ nếu quá dài */
            overflow-wrap: break-word;
            /* Chuẩn mới hơn của word-wrap */
            word-break: break-word;
            /* Đảm bảo không có từ nào tràn ra ngoài */
            white-space: normal;
            /* Cho phép xuống dòng */
        }

        .col-ten {
            width: 15%;
            white-space: normal;
        }

        .col-danhmuc {
            width: 8%;
            white-space: normal;
        }

        .col-sl {
            width: 5%;
            text-align: center;
        }

        .col-gia {
            width: 8%;
        }

        .col-giaTT {
            width: 8%;
            text-align: right;
        }

        .col-chatluong {
            width: 10%;
        }

        .col-tinhtrang {
            width: 10%;
        }

        .col-mota {
            width: 10%;
            white-space: normal;
        }

        .col-anh {
            width: 10%;
            text-align: center;
        }

        .col-thaotac {
            width: 10%;
            text-align: center;
        }

        .col-mota {
            width: 320px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .ten-rutgon,
        .mota-rutgon {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            /* Chỉ hiện tối đa 2 dòng */
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 3em;
            /* Chiều cao an toàn cho 2 dòng */
        }

        .ten-full,
        .mota-full {
            display: none;
            /* Mặc định ẩn */
            /* Khi hiện ra sẽ tuân theo width của cột cha (.col-ten hoặc .col-mota) */
        }

        .xem-them {
            color: #0d6efd;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: underline;
            display: inline-block;
            margin-top: 4px;
            user-select: none;
            /* Tránh bôi đen khi click nhanh */
        }

        .qlsp-table .status-con-hang {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .qlsp-table .status-het-hang {
            background: #fbe7e7;
            color: #c0392b;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .qlsp-table .status-ngung-kd {
            background: #f3f4f6;
            color: #6b7280;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .modals-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.25s ease-in;
        }

        .modals {
            background: #fff;
            border-radius: 12px;
            width: 700px;
            max-width: 95%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modals.show {
            transform: scale(1);
            opacity: 1;
        }

        .modals-header {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
            padding: 12px 16px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modals-close {
            font-size: 1.5rem;
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .modals-close:hover {
            transform: scale(1.2);
        }

        .modals-body {
            padding: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modals-body label {
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .modals-body input,
        .modals-body select,
        .modals-body textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .modals-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 12px 20px;
            border-top: 1px solid #eee;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .alert,
        .form-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Đổ bóng cho đẹp */

        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>
        <main class="main-content">
            <div class="admin-container">
                <h1>📦 Quản lý sản phẩm</h1>

                <form method="GET" action="adminSanPhamController.php" class="filter-bar search-bar" style="display:flex; gap:8px; margin-bottom: 20px;">
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." autocomplete="off"
                        value="<?= htmlspecialchars($keyword ?? '') ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <?= $message ?> <button class="btn btn-success mb-3" onclick="openModals()">+ Thêm sản phẩm mới</button>

                <h2 class="mt-5 mb-3">Danh sách sản phẩm</h2>
                <div class="table-responsive">
                    <table class="qlsp-table qldh-table table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="col-ten">Tên</th>
                                <th>Người bán</th>
                                <th class="col-danhmuc">Danh mục</th>
                                <th class="col-sl">SL</th>
                                <th class="col-gia">Giá</th>
                                <th class="col-giaTT">Giá TT</th>
                                <th>Trạng thái duyệt</th>
                                <th class="col-chatluong">Chất lượng</th>
                                <th class="col-tinhtrang">Tình trạng</th>
                                <th class="col-mota">Mô tả</th>
                                <th class="col-anh">Ảnh</th>
                                <th class="col-thaotac">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($danhSachSanPham)): ?>
                                <?php foreach ($danhSachSanPham as $r):
                                    $mota_raw = $r['MoTa'] ?? '';
                                    $mota_text_plain = strip_tags($mota_raw);
                                    $is_long_desc = mb_strlen($mota_text_plain) > 100;
                                    $rutgon = $is_long_desc ? mb_substr($mota_text_plain, 0, 100) . '...' : $mota_text_plain;
                                    $ten_raw = $r['TenHH'] ?? '';
                                    $ten_limit = 50;
                                    $is_long_ten = mb_strlen($ten_raw) > $ten_limit;
                                    $ten_full = htmlspecialchars($ten_raw);
                                    $ten_rutgon = $is_long_ten ? htmlspecialchars(mb_substr($ten_raw, 0, $ten_limit)) . '...' : $ten_full;

                                    $img_url_relative = $r['AnhDaiDien'] ?? '';
                                    $full_image_url = !empty($img_url_relative) ? '../../' . $img_url_relative : '';
                                    $physical_image_path = realpath(__DIR__ . "/../../" . $img_url_relative);
                                    $image_is_valid = !empty($full_image_url) && $physical_image_path !== false && file_exists($physical_image_path);
                                ?>
                                    <tr>

                                        <td style="white-space: normal;">
                                            <div class="ten-rutgon"><strong><?= $ten_rutgon ?></strong></div>
                                            <div class="ten-full" style="display:none;"><strong><?= $ten_full ?></strong></div>
                                            <?php if ($is_long_ten): ?>
                                                <span class="xem-them" onclick="toggleTen(this)">Xem thêm</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <i class="fas fa-user"></i> <?= htmlspecialchars($r['NguoiBan'] ?? 'Admin') ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($r['TenDM']) ?></td>
                                        <td class="text-center"><?= $r['SoLuongHH'] ?></td>
                                        <td><?= number_format($r['Gia'], 0, ',', '.') ?>đ</td>
                                        <td class="text-center">
                                            <?php if ($r['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                                <span class="badge bg-warning text-dark">⏳ Chờ duyệt</span>
                                            <?php elseif ($r['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                                <span class="badge bg-success">✅ Đã duyệt</span>
                                            <?php elseif ($r['TrangThaiDuyet'] == 'TuChoi'): ?>
                                                <span class="badge bg-danger">❌ Bị từ chối</span>
                                                <?php if (!empty($r['LyDoTuChoi'])): ?>
                                                    <div style="font-size: 0.75rem; color: red; margin-top: 2px;">
                                                        (<?= htmlspecialchars($r['LyDoTuChoi']) ?>)
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <div class="mt-1">
                                                <?php if ($r['HienThi'] == 0): ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-eye-slash"></i> Đang ẩn</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <?php
                                            if ($r['GiaThiTruong'] > 0) {
                                                echo number_format($r['GiaThiTruong'], 0, ',', '.') . 'đ';
                                            } else {
                                                echo "<div style='display:flex; '>-</div>";
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($r['ChatLuongHang']) ?></td>

                                        <td>
                                            <?php
                                            $status_class = '';
                                            switch ($r['TinhTrangHang']) {
                                                case 'Còn hàng':
                                                    $status_class = 'status-con-hang';
                                                    break;
                                                case 'Hết hàng':
                                                    $status_class = 'status-het-hang';
                                                    break;
                                                case 'Ngưng kinh doanh':
                                                    $status_class = 'status-ngung-kd';
                                                    break;
                                            }
                                            ?>
                                            <span class="<?= $status_class ?>"><?= htmlspecialchars($r['TinhTrangHang']) ?></span>
                                        </td>

                                        <td style="white-space: normal;">
                                            <div class="mota-rutgon"><?= htmlspecialchars($rutgon) ?></div>
                                            <div class="mota-full"><?= $mota_raw ?></div>
                                            <?php if ($is_long_desc): ?>
                                                <span class="xem-them" onclick="toggleMoTa(this)">Xem thêm</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if (!empty($r['DanhSachAnh'])): ?>
                                                <div class="anh-nhieu">
                                                    <?php foreach ($r['DanhSachAnh'] as $anh):
                                                        $url = '../../' . $anh['URL'];
                                                        $path = realpath(__DIR__ . "/../../" . $anh['URL']);
                                                        if ($path && file_exists($path)):
                                                    ?>
                                                            <img src="<?= htmlspecialchars('../../' . $anh['URL']) ?>" alt="Ảnh sản phẩm" style="max-width: 60px; margin: 2px;">
                                                    <?php endif;
                                                    endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span>Không ảnh</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <div style="display: flex; gap: 5px; justify-content: center; flex-wrap: wrap;">

                                                <?php if ($r['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                                    <a href="adminSanPhamController.php?action=duyet&id=<?= $r['MaHH'] ?>"
                                                        class="btn btn-success btn-sm"
                                                        title="Duyệt bài này">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="adminSanPhamController.php?action=tuchoi&id=<?= $r['MaHH'] ?>"
                                                        class="btn btn-warning btn-sm"
                                                        onclick="return confirm('Bạn chắc chắn muốn từ chối bài đăng này?')"
                                                        title="Từ chối">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($r['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                                    <?php if ($r['HienThi'] == 1): ?>
                                                        <a href="adminSanPhamController.php?action=an&id=<?= $r['MaHH'] ?>"
                                                            class="btn btn-secondary btn-sm"
                                                            title="Ẩn bài đăng">
                                                            <i class="fas fa-eye-slash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="adminSanPhamController.php?action=hien&id=<?= $r['MaHH'] ?>"
                                                            class="btn btn-info btn-sm"
                                                            title="Hiện lại bài đăng">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <a href="adminSanPhamController.php?xoa=<?= $r['MaHH'] ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bạn chắc chắn muốn xóa vĩnh viễn?')"
                                                    title="Xóa vĩnh viễn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Chưa có sản phẩm nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modals-overlay" id="modals">
        <div class="modals">
            <form method="POST" action="adminSanPhamController.php" enctype="multipart/form-data" class="modals-form">
                <input type="hidden" name="mahh" value="<?= $edit_item['MaHH'] ?? 0 ?>">

                <div class="modals-header">
                    <h3 id="modals-title"><?= $edit_item ? 'Sửa' : 'Thêm' ?> sản phẩm</h3>
                    <button type="button" class="modals-close" onclick="closeModals()">&times;</button>
                </div>

                <div class="modals-body">
                    <div>
                        <label>Tên sản phẩm *</label>
                        <input type="text" name="ten" required value="<?= htmlspecialchars($edit_item['TenHH'] ?? '') ?>">
                    </div>

                    <div>
                        <label>Mô tả sản phẩm</label>
                        <textarea name="mota" id="mota-modal" rows="4"><?= htmlspecialchars($edit_item['MoTa'] ?? '') ?></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-grid" style="grid-template-columns: repeat(3, 1fr);">
                            <div>
                                <label>Giá bán *</label>
                                <input type="number" name="gia" min="0" step="any" required value="<?= htmlspecialchars($edit_item['Gia'] ?? '') ?>">
                            </div>

                            <div>
                                <label>Giá thị trường</label>
                                <input type="number" name="giathitruong" min="0" step="any" value="<?= htmlspecialchars($edit_item['GiaThiTruong'] ?? '') ?>">
                            </div>

                            <div>
                                <label>Số lượng *</label>
                                <input type="number" name="soluong" min="0" required value="<?= htmlspecialchars($edit_item['SoLuongHH'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label>Danh mục *</label>
                        <div class="input-group mb-2">
                            <select name="madm" id="madm-modal" required class="form-select">
                                <option value="">-- Chọn danh mục --</option>
                                <?php if (!empty($danhSachDanhMuc)): ?>
                                    <?php foreach ($danhSachDanhMuc as $dm): ?>
                                        <option value="<?= $dm['MaDM'] ?>" <?= (isset($edit_item) && $edit_item['MaDM'] == $dm['MaDM']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dm['TenDM']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <button type="button" id="btnAddDM-modal" class="btn btn-outline-primary" title="Thêm danh mục mới"> Thêm</button>
                            <button type="button" id="btnManageDM-modal" class="btn btn-outline-secondary" title="Quản lý danh mục"> Quản lý</button>
                        </div>

                        <div class="add-dm-box" id="add-dm-box-modal">
                            <input type="text" id="newDM-modal" placeholder="Nhập tên danh mục mới..." class="form-control mb-2" style="width:auto; display:inline-block; max-width: 70%;">
                            <button type="button" id="saveDM-modal" class="btn btn-success btn-sm">Lưu</button>
                            <button type="button" id="cancelDM-modal" class="btn btn-secondary btn-sm">Hủy</button>
                            <div id="add-dm-msg-modal" class="mt-2" style="font-size:0.9rem;"></div>
                        </div>

                        <div class="manage-dm-box" id="manage-dm-box-modal">
                            <h6>Quản lý danh mục hiện có:</h6>
                            <ul id="dmList-modal"></ul>
                            <div id="manage-dm-msg-modal" class="mt-2" style="font-size:0.9rem;"></div>
                            <button type="button" id="closeManage-modal" class="btn btn-secondary btn-sm mt-2">Đóng</button>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div>
                            <label>Chất lượng</label>
                            <select name="chatluong" class="form-select">
                                <option value="Mới" <?= ($edit_item['ChatLuongHang'] ?? '') == 'Mới' ? 'selected' : '' ?>>Mới</option>
                                <option value="Đã qua sử dụng" <?= ($edit_item['ChatLuongHang'] ?? '') == 'Đã qua sử dụng' ? 'selected' : '' ?>>Đã qua sử dụng</option>
                                <option value="Gần như mới" <?= ($edit_item['ChatLuongHang'] ?? '') == 'Gần như mới' ? 'selected' : '' ?>>Gần như mới</option>
                            </select>
                        </div>
                        <div>
                            <label>Tình trạng hàng</label>
                            <select name="tinhtranghang" class="form-select">
                                <option value="Còn hàng" <?= ($edit_item['TinhTrangHang'] ?? '') == 'Còn hàng' ? 'selected' : '' ?>>Còn hàng</option>
                                <option value="Hết hàng" <?= ($edit_item['TinhTrangHang'] ?? '') == 'Hết hàng' ? 'selected' : '' ?>>Hết hàng</option>
                                <option value="Ngưng kinh doanh" <?= ($edit_item['TinhTrangHang'] ?? '') == 'Ngưng kinh doanh' ? 'selected' : '' ?>>Ngưng kinh doanh</foption>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label>Ảnh sản phẩm</label>
                        <input type="file" name="image_file[]" accept="image/*" class="form-control" multiple>

                        <div id="preview-images" class="mt-2 d-flex flex-wrap gap-2"></div>

                        <?php if (!empty($edit_item['MaHH'])):
                            $anh_cu = $sanPhamModel->getAnhSanPham($edit_item['MaHH']);
                            if (!empty($anh_cu)):
                        ?>
                                <div class="mt-2">
                                    <label>Ảnh hiện tại:</label>

                                    <div class="d-flex flex-wrap gap-3" id="old-images-area">
                                        <?php foreach ($anh_cu as $anh):
                                            $url = '../../' . $anh['URL'];
                                            $path = realpath(__DIR__ . "/../../" . $anh['URL']);
                                            if ($path && file_exists($path)):
                                        ?>
                                                <div class="position-relative border p-1 rounded old-image-box"
                                                    data-url="<?= htmlspecialchars($anh['URL']) ?>"
                                                    style="display:inline-block;">

                                                    <img src="<?= htmlspecialchars($url) ?>"
                                                        style="max-width: 80px; height:80px; object-fit:cover;">

                                                    <button type="button" class="btn btn-danger btn-sm delete-old-image"
                                                        style="position:absolute; top:-6px; right:-6px; padding:2px 6px;">
                                                        <i class="fa-solid fa-x"></i>
                                                    </button>

                                                </div>
                                        <?php endif;
                                        endforeach; ?>
                                    </div>
                                </div>

                        <?php endif;
                        endif; ?>

                    </div>
                </div>

                <div class="modals-footer">
                    <?php if ($edit_item): ?>
                        <button type="submit" name="action" value="update" class="btn btn-primary">💾 Lưu thay đổi</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="add" class="btn btn-primary">💾 Thêm sản phẩm</button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-danger" onclick="closeModals()">Đóng</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- MODAL FUNCTIONS ---
        const modals = document.getElementById('modals');
        const modalsForm = document.querySelector('.modals-form');
        const modalsTitle = document.getElementById('modals-title');
        const modalsBox = modals.querySelector('.modals');

        function openModals() {
            if (!window.location.search.includes('edit=')) {
                modalsForm.reset();
                modalsTitle.innerText = 'Thêm sản phẩm';
                document.querySelector('input[name="mahh"]').value = 0;
                if (CKEDITOR.instances['mota-modal']) {
                    CKEDITOR.instances['mota-modal'].setData('');
                }
            } else {
                modalsTitle.innerText = 'Sửa sản phẩm';
            }
            modals.style.display = 'flex';
            setTimeout(() => {
                modalsBox.classList.add('show');
            }, 10);
        }

        function closeModals() {
            modalsBox.classList.remove('show');
            setTimeout(() => {
                modals.style.display = 'none';
                const url = new URL(window.location);
                url.searchParams.delete('edit');
                window.history.pushState({}, '', url);
            }, 200);
        }

        <?php if (isset($edit_item)): ?>
            window.addEventListener('load', () => {
                openModals();
            });
        <?php endif; ?>

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modals.style.display === 'flex') {
                closeModals();
            }
        });

        // --- DANH MỤC AJAX (TRONG MODAL) ---
        function showModalMessage(selector, message, isError = false) {
            const el = $(selector);
            let className = isError ? 'alert alert-danger' : 'alert alert-success';
            el.removeClass('alert-danger alert-success').addClass(className).text(message).fadeIn();
            setTimeout(() => el.fadeOut().text(''), 3000);
        }

        function updateSelectOptions(newId, newTen) {
            $('#madm-modal').append(`<option value="${newId}" selected>${htmlspecialchars(newTen)}</option>`);
        }

        // Nút "+ Thêm"
        $('#btnAddDM-modal').click(() => {
            $('#manage-dm-box-modal').slideUp();
            $('#add-dm-box-modal').slideToggle();
            $('#newDM-modal').focus();
        });

        $('#cancelDM-modal').click(() => {
            $('#add-dm-box-modal').slideUp();
            $('#newDM-modal').val('');
            $('#add-dm-msg-modal').hide().text('');
        });

        // Nút "Lưu" (khi thêm mới)
        $('#saveDM-modal').click(() => {
            const ten = $('#newDM-modal').val().trim();
            if (!ten) return showModalMessage('#add-dm-msg-modal', 'Vui lòng nhập tên danh mục.', true);

            $.post('adminDanhMucController.php', {
                action: 'add',
                ten_danhmuc: ten
            }, (response) => {
                // Tải lại trang để cập nhật danh sách
                location.reload();
            }).fail(() => showModalMessage('#add-dm-msg-modal', 'Lỗi kết nối.', true));
        });

        // Nút "Quản lý" (NÚT BỊ LỖI CỦA BẠN)
        $('#btnManageDM-modal').click(() => {
            $('#add-dm-box-modal').slideUp();
            $('#manage-dm-box-modal').slideToggle();
            $('#dmList-modal').html(''); // Xóa list cũ
            $('#manage-dm-msg-modal').hide().text('');

            // Lấy danh sách từ <select>
            $('#madm-modal option').each(function() {
                const id = $(this).val();
                const ten = $(this).text();
                if (!id) return; // Bỏ qua option "-- Chọn danh mục --"
                $('#dmList-modal').append(`<li data-id="${id}">
                <input value="${htmlspecialchars(ten)}" class="form-control form-control-sm d-inline-block flex-grow-1" />
                <button class="dm-btn text-success editDM-modal" title="Lưu">Lưu</button>
                <button class="dm-btn text-danger delDM-modal" title="Xóa">Xóa</button></li>`);
            });
        });

        // Nút "Đóng" (trong box quản lý)
        $('#closeManage-modal').click(() => {
            $('#manage-dm-box-modal').slideUp();
            $('#manage-dm-msg-modal').hide().text('');
        });

        // Nút lưu
        $(document).on('click', '.editDM-modal', function() {
            const li = $(this).closest('li');
            const id = li.data('id');
            const tenMoi = li.find('input').val().trim();
            if (!tenMoi) return showModalMessage('#manage-dm-msg-modal', 'Tên không được trống.', true);

            $.post('adminDanhMucController.php', {
                action: 'update',
                capnhat_id: id,
                capnhat_ten: tenMoi
            }, (response) => {
                location.reload(); // Tải lại trang
            }).fail(() => showModalMessage('#manage-dm-msg-modal', 'Lỗi kết nối.', true));
        });

        // Nút "Xóa" (trong box quản lý)
        $(document).on('click', '.delDM-modal', function() {
            if (!confirm('Bạn chắc chắn muốn xóa danh mục này?')) return;
            const li = $(this).closest('li');
            const id = li.data('id');

            $.get('adminDanhMucController.php', {
                xoa: id
            }, (response) => {
                location.reload(); // Tải lại trang
            }).fail(() => showModalMessage('#manage-dm-msg-modal', 'Lỗi kết nối.', true));
        });

        // --- MÔ TẢ TOGGLE ---
        function toggleMoTa(element) {
            const td = element.closest('td');
            if (!td) return;
            const divRutgon = td.querySelector('.mota-rutgon');
            const divFull = td.querySelector('.mota-full');
            if (divRutgon && divFull) {
                if (element.textContent === "Xem thêm") {
                    divRutgon.style.display = 'none';
                    divFull.style.display = 'block';
                    element.textContent = "Thu gọn";
                } else {
                    divRutgon.style.display = '-webkit-box';
                    divFull.style.display = 'none';
                    element.textContent = "Xem thêm";
                }
            }
        }

        function toggleTen(element) {
            const td = element.closest('td');
            if (!td) return;
            const divRutgon = td.querySelector('.ten-rutgon');
            const divFull = td.querySelector('.ten-full');
            if (divRutgon && divFull) {
                if (element.textContent === "Xem thêm") {
                    divRutgon.style.display = 'none';
                    divFull.style.display = 'block';
                    element.textContent = "Thu gọn";
                } else {
                    divRutgon.style.display = '-webkit-box';
                    divFull.style.display = 'none';
                    element.textContent = "Xem thêm";
                }
            }
        }
        // ckeditor
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('mota-modal')) {
                CKEDITOR.replace('mota-modal', {
                    height: 200,
                    filebrowserUploadUrl: '../../controllers/upload_image.php',
                    filebrowserUploadMethod: 'form',

                    versionCheck: false
                });
            }

            // chỉnh sửa chỗ hình ảnh trong CKEDITOR để không bắt buộc nhập URL
            CKEDITOR.on('dialogDefinition', function(ev) {
                const dialogName = ev.data.name;
                const dialogDefinition = ev.data.definition;

                // Chỉ can thiệp vào hộp thoại "image"
                if (dialogName === 'image') {
                    const infoTab = dialogDefinition.getContents('info');
                    const urlField = infoTab.get('txtUrl');

                    if (urlField) {
                        // 1. Loại bỏ dấu '*' khỏi nhãn (label)
                        urlField.label = urlField.label.replace('*', '').trim();

                        // 2. Thiết lập hàm validate là null để không bắt buộc phải điền
                        urlField.validate = null;
                    }
                }
            });

            // Hàm JS thay thế htmlspecialchars của PHP
            window.htmlspecialchars = function(str) {
                if (typeof str !== 'string') return str;
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return str.replace(/[&<>"']/g, function(m) {
                    return map[m];
                });
            }

            const imageInput = document.querySelector('input[name="image_file[]"]');
            const previewContainer = document.getElementById('preview-images');

            if (imageInput && previewContainer) {
                imageInput.addEventListener('change', function() {
                    previewContainer.innerHTML = '';
                    Array.from(this.files).forEach(file => {
                        if (!file.type.startsWith('image/')) return;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '80px';
                            img.style.margin = '4px';
                            img.style.border = '1px solid #ccc';
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            // xóa ảnh trong chỗ ảnh sản phẩm

            $(document).on('click', '.delete-old-image', function() {
                if (!confirm("Xóa ảnh này?")) return;

                const box = $(this).closest('.old-image-box');
                const imageUrl = box.data('url');
                const productId = $("input[name='mahh']").val();

                $.post("adminSanPhamController.php", {
                    action: "delete_image",
                    image_url: imageUrl,
                    mahh: productId
                }, function(res) {
                    box.remove();
                }).fail(function() {
                    alert("Lỗi xóa ảnh.");
                });
            });

        });
        //hàm ẩn thông báo
        $(document).ready(function() {
            // Hoặc bạn có thể thay '.alert' bằng class riêng của bạn như '.form-message'
            var $alert = $(".alert, .form-message");

            // Kiểm tra xem có thông báo nào đang hiện không
            if ($alert.length > 0 && $alert.text().trim() !== "") {

                setTimeout(function() {

                    $alert.slideUp(500, function() {
                        $(this).remove();
                    });

                }, 3000);
            }
        });
    </script>
</body>

</html>