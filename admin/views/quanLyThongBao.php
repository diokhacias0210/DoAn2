<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Thông báo</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* CSS MÀU XANH GIỐNG TRANG QUẢN LÝ ĐÁNH GIÁ */
        .qldh-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-collapse: collapse;
        }

        .qldh-table th {
            background: #4CAF50;
            color: white;
            padding: 15px;
            text-align: left;
            border: none;
        }

        .qldh-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .qldh-table tr:hover {
            background: #f8f9fa;
        }

        /* Fix Select2 Bootstrap 5 UI */
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 2px 8px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="qldh-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 style="color: #2c3e50; font-size: 24px; margin: 0;">Quản lý Thông báo hệ thống</h1>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalSendNotif">
                        <i class="fa-solid fa-paper-plane"></i> Gửi thông báo mới
                    </button>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="card mb-4" style="border-radius: 10px; border: 1px solid #eee;">
                    <div class="card-body bg-light">
                        <form method="GET" action="adminThongBaoController.php" class="row gx-2 gy-2 align-items-center">
                            <div class="col-md-auto">
                                <strong><i class="fas fa-filter"></i> Lọc:</strong>
                            </div>
                            <div class="col-md-3">
                                <select name="doituong_filter" class="form-select">
                                    <option value="">-- Mọi đối tượng --</option>
                                    <option value="all" <?= ($doiTuongFilter == 'all') ? 'selected' : '' ?>>Gửi số đông</option>
                                    <option value="single" <?= ($doiTuongFilter == 'single') ? 'selected' : '' ?>>Gửi 1 người (Cá nhân)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="loai_filter" class="form-select">
                                    <option value="">-- Mọi loại TB --</option>
                                    <option value="HeThong" <?= ($loaiFilter == 'HeThong') ? 'selected' : '' ?>>Hệ thống</option>
                                    <option value="KhuyenMai" <?= ($loaiFilter == 'KhuyenMai') ? 'selected' : '' ?>>Khuyến mãi</option>
                                    <option value="ViPham" <?= ($loaiFilter == 'ViPham') ? 'selected' : '' ?>>Vi phạm</option>
                                    <option value="BaoCao" <?= ($loaiFilter == 'BaoCao') ? 'selected' : '' ?>>Báo cáo</option>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                                <a href="adminThongBaoController.php" class="btn btn-outline-secondary">Xóa</a>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="qldh-table">
                    <thead>
                        <tr>
                            <th>Mã TB</th>
                            <th>Ngày gửi</th>
                            <th>Loại</th>
                            <th>Người nhận</th>
                            <th style="width: 35%;">Tiêu đề</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($danhSachTB)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Không tìm thấy thông báo nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($danhSachTB as $tb): ?>
                                <tr>
                                    <td>#<?= $tb['MaTB'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($tb['NgayTao'])) ?></td>
                                    <td>
                                        <?php
                                        $badge = 'bg-secondary';
                                        if ($tb['LoaiTB'] == 'KhuyenMai') $badge = 'bg-danger';
                                        if ($tb['LoaiTB'] == 'ViPham' || $tb['LoaiTB'] == 'BaoCao') $badge = 'bg-warning text-dark';
                                        if ($tb['LoaiTB'] == 'HeThong') $badge = 'bg-info text-dark';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= $tb['LoaiTB'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($tb['SoNguoiNhan'] > 1): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-users"></i> Tất cả (<?= $tb['SoNguoiNhan'] ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($tb['TenNguoiNhan']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($tb['TieuDe']) ?></strong></td>

                                    <td class="text-center">
                                        <div id="noidung_<?= $tb['MaTB'] ?>" style="display:none;"><?= $tb['NoiDung'] ?></div>
                                        <button class="btn btn-sm btn-info text-white me-1"
                                            onclick="xemChiTiet(<?= $tb['MaTB'] ?>, '<?= htmlspecialchars($tb['TieuDe'], ENT_QUOTES) ?>')">
                                            <i class="fa-solid fa-eye"></i> Xem
                                        </button>
                                        <a href="adminThongBaoController.php?action=delete&id=<?= $tb['MaTB'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalSendNotif" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="adminThongBaoController.php" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin\'></i> Đang gửi...';">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fa-solid fa-envelope"></i> Soạn thông báo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="send">

                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Chọn đối tượng nhận</label>
                            <div class="border p-3 rounded bg-light">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="cheDoGui" value="all_system" id="rAll" checked onchange="toggleCustomSelect()">
                                    <label class="form-check-label text-success fw-bold" for="rAll">Tất cả thành viên (Người mua & Người bán)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="cheDoGui" value="all_users" id="rUsers" onchange="toggleCustomSelect()">
                                    <label class="form-check-label" for="rUsers">Chỉ Người Mua Hàng</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="cheDoGui" value="all_sellers" id="rSellers" onchange="toggleCustomSelect()">
                                    <label class="form-check-label " for="rSellers">Chỉ Kênh Người Bán (Shop)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cheDoGui" value="custom" id="rCustom" onchange="toggleCustomSelect()">
                                    <label class="form-check-label " for="rCustom">Gửi cho người cụ thể (Tìm kiếm & Chọn nhiều người)</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="customUserContainer" style="display: none;">
                            <label class="form-label fw-bold">Tìm & Chọn người nhận (Có thể chọn nhiều)</label>
                            <select name="nguoiNhanCustom[]" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
                                <?php foreach ($danhSachUser as $u): ?>
                                    <?php $isSeller = ($u['TrangThaiBanHang'] == 'DangHoatDong') ? ' [SHOP]' : ''; ?>
                                    <option value="<?= $u['IdTaiKhoan'] ?>">
                                        <?= htmlspecialchars($u['TenTK']) ?> (ID: <?= $u['IdTaiKhoan'] ?>) <?= $isSeller ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">2. Phân loại thông báo</label>
                                <select name="loaiTB" class="form-select">
                                    <option value="HeThong">Hệ thống (Chính sách, Cập nhật...)</option>
                                    <option value="KhuyenMai">Khuyến mãi (Mã giảm giá, Event...)</option>
                                    <option value="ViPham">Cảnh cáo Vi phạm</option>
                                    <option value="BaoCao">Phản hồi Báo cáo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">3. Tiêu đề</label>
                            <input type="text" name="tieuDe" class="form-control" placeholder="Nhập tiêu đề thông báo..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">4. Nội dung</label>
                            <textarea name="noiDung" id="noiDungTB" class="form-control" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i> Gửi Thông Báo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalViewDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-eye"></i> Nội dung thông báo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h4 id="view-title" class="text-primary mb-3"></h4>
                    <div class="p-3 bg-light border rounded" id="view-content" style="min-height: 150px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Khởi tạo Select2 (Thư viện tạo ô chọn tìm kiếm)
            $('.select2-multiple').select2({
                placeholder: " Gõ tên hoặc ID để tìm kiếm...",
                allowClear: true,
                dropdownParent: $('#modalSendNotif') // Quan trọng: fix lỗi select2 nằm dưới Modal
            });

            // Ẩn thông báo alert sau 3s
            setTimeout(function() {
                $('.alert').fadeOut('fast');
            }, 3000);

            // CKEditor
            CKEDITOR.replace('noiDungTB', {
                height: 150,
                toolbar: [
                    ['Bold', 'Italic', 'Underline', 'Strike'],
                    ['TextColor'],
                    ['NumberedList', 'BulletedList']
                ]
            });
        });

        // Hàm hiện/ẩn ô chọn danh sách người dùng tùy biến
        function toggleCustomSelect() {
            if (document.getElementById('rCustom').checked) {
                document.getElementById('customUserContainer').style.display = 'block';
                // Yêu cầu ô select phải có dữ liệu khi form submit
                document.querySelector('.select2-multiple').setAttribute('required', 'required');
            } else {
                document.getElementById('customUserContainer').style.display = 'none';
                document.querySelector('.select2-multiple').removeAttribute('required');
            }
        }

        // Hàm xem chi tiết
        function xemChiTiet(id, tieuDe) {
            document.getElementById('view-title').innerText = tieuDe;
            document.getElementById('view-content').innerHTML = document.getElementById('noidung_' + id).innerHTML;
            new bootstrap.Modal(document.getElementById('modalViewDetail')).show();
        }
    </script>
</body>

</html>