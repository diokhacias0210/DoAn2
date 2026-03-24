<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Báo cáo & Vi phạm</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Đồng bộ Layout chuẩn */
        .admin-container {
            padding: 20px;
            background: #f4f6f9;
            min-height: 100vh;
        }

        .qlbc-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .qlbc-table th {
            background-color: #4CAF50 !important;
            color: white;
            border: none;
            vertical-align: middle;
            white-space: nowrap;
        }

        .qlbc-table td {
            vertical-align: middle;
        }

        .strike-badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            text-align: center;
            line-height: 22px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <div class="mb-4">
                    <h2 class="m-0 text-dark fw-bold"><i class="fa-solid fa-shield-halved text-success"></i> Quản lý Báo cáo</h2>
                    <p class="text-muted mt-1">Kiểm duyệt và xử lý các báo cáo vi phạm, giải quyết kháng cáo.</p>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body bg-white rounded">
                        <form method="GET" action="adminBaoCaoController.php" class="row gx-2 gy-2 align-items-center">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Tên người báo cáo/bị báo cáo..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="loai" class="form-select">
                                    <option value="">- Mọi loại báo cáo -</option>
                                    <option value="SanPham" <?= $loaiFilter == 'SanPham' ? 'selected' : '' ?>>Báo cáo Sản phẩm</option>
                                    <option value="NguoiBan" <?= $loaiFilter == 'NguoiBan' ? 'selected' : '' ?>>Báo cáo Người bán</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="trangThai" class="form-select">
                                    <option value="">- Mọi trạng thái -</option>
                                    <option value="ChoXuLy" <?= $trangThaiFilter == 'ChoXuLy' ? 'selected' : '' ?>>Chờ xử lý</option>
                                    <option value="ViPham" <?= $trangThaiFilter == 'ViPham' ? 'selected' : '' ?>>Có vi phạm</option>
                                    <option value="KhongViPham" <?= $trangThaiFilter == 'KhongViPham' ? 'selected' : '' ?>>Không vi phạm</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-success w-100"><i class="fas fa-filter"></i> Lọc</button>
                                <a href="adminBaoCaoController.php" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-right"></i></a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover qlbc-table align-middle">
                        <thead>
                            <tr>
                                <th>Ngày tạo</th>
                                <th>Loại</th>
                                <th>Người Báo cáo</th>
                                <th>Đối tượng Bị Báo Cáo</th>
                                <th style="width: 25%;">Lý do vi phạm</th>
                                <th>Trạng thái</th>
                                <th class="text-center" style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dsBaoCao)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy báo cáo nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($dsBaoCao as $bc): ?>
                                    <tr>
                                        <td><span style="font-size: 13px; color: #666;"><?= date('d/m/Y H:i', strtotime($bc['NgayTao'])) ?></span></td>
                                        <td>
                                            <?php if ($bc['LoaiBaoCao'] == 'SanPham'): ?>
                                                <span class="badge bg-primary"><i class="fa-solid fa-box"></i> Sản phẩm (#<?= $bc['MaHH'] ?>)</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-shop"></i> Người bán</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($bc['TenNguoiBaoCao']) ?></strong></td>
                                        <td>
                                            <strong class="text-primary"><?= htmlspecialchars($bc['TenBiBaoCao']) ?></strong>
                                            <?php if ($bc['DiemViPham'] > 0): ?>
                                                <span class="strike-badge" title="<?= $bc['DiemViPham'] ?> gậy vi phạm"><?= $bc['DiemViPham'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="text-danger"><?= htmlspecialchars($bc['LyDoChinh']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($bc['ChiTiet']) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($bc['TrangThai'] == 'ChoXuLy'): ?>
                                                <span class="badge bg-warning text-dark">⏳ Chờ xử lý</span>
                                            <?php elseif ($bc['TrangThai'] == 'ViPham'): ?>
                                                <span class="badge bg-danger">✅ Có vi phạm</span>
                                                <?php if (!empty($bc['NoiDungKhangCao'])): ?>
                                                    <br><span class="badge bg-info text-dark mt-1"><i class="fa-solid fa-envelope"></i> Có kháng cáo</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">❌ Bỏ qua</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                <?php if ($bc['TrangThai'] == 'ChoXuLy'): ?>
                                                    <button class="btn btn-sm btn-success w-100" onclick="moModalXuLy(<?= $bc['MaBC'] ?>, '<?= $bc['LoaiBaoCao'] ?>')">Xử lý</button>

                                                <?php elseif ($bc['TrangThai'] == 'ViPham'): ?>
                                                    <?php if (!empty($bc['NoiDungKhangCao'])): ?>
                                                        <button class="btn btn-sm btn-info text-white w-100 fw-bold" onclick="moModalKhangCao(`<?= htmlspecialchars($bc['NoiDungKhangCao']) ?>`)">
                                                            <i class="fa-solid fa-envelope-open-text"></i> Xem Kháng cáo
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="adminBaoCaoController.php?action=revoke&id=<?= $bc['MaBC'] ?>" class="btn btn-sm btn-outline-warning text-dark w-100" onclick="return confirm('Bạn muốn thu hồi hình phạt này? (Người bán sẽ được mở khóa nếu đủ điều kiện)')"><i class="fa-solid fa-rotate-left"></i> Thu hồi</a>

                                                <?php else: ?>
                                                    <span class="text-muted"><i class="fa-solid fa-check"></i> Đã đóng</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalXuLyBaoCao" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="adminBaoCaoController.php" method="POST">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa-solid fa-gavel"></i> Đưa ra phán quyết</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="process">
                        <input type="hidden" name="maBC" id="input_maBC">

                        <div class="alert alert-warning" id="warning_sp" style="display:none; font-size:13px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Báo cáo Sản Phẩm: Nếu "CÓ VI PHẠM", sản phẩm này sẽ bị ẩn.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Quyết định của Admin:</label>
                            <select name="quyetDinh" class="form-select border-danger" required>
                                <option value="">-- Chọn phán quyết --</option>
                                <option value="ViPham">CÓ VI PHẠM (Đánh 1 gậy cảnh cáo)</option>
                                <option value="KhongViPham">KHÔNG VI PHẠM (Từ chối báo cáo)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ghi chú gửi kèm cho người bị báo cáo (Tùy chọn):</label>
                            <textarea name="ghiChuUser" class="form-control" rows="2" placeholder="Ví dụ: Lần sau bạn nhớ cập nhật đúng mô tả sản phẩm nhé..."></textarea>
                            <small class="text-muted">Nội dung này sẽ được đính kèm vào chuông thông báo của họ.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalXemKhangCao" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-envelope-open-text"></i> Nội dung Kháng Cáo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-2">Lời giải trình từ người bị báo cáo/khóa tài khoản:</p>
                    <div id="noidung_khangcao_text" class="p-3 bg-light border border-info rounded" style="min-height: 120px; white-space: pre-wrap; font-size: 15px;"></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        setTimeout(() => {
            $(".alert").slideUp(500);
        }, 3000);

        function moModalXuLy(maBC, loaiBaoCao) {
            document.getElementById('input_maBC').value = maBC;
            document.getElementById('warning_sp').style.display = (loaiBaoCao === 'SanPham') ? 'block' : 'none';
            new bootstrap.Modal(document.getElementById('modalXuLyBaoCao')).show();
        }

        function moModalKhangCao(noiDung) {
            document.getElementById('noidung_khangcao_text').innerText = noiDung;
            new bootstrap.Modal(document.getElementById('modalXemKhangCao')).show();
        }
    </script>
</body>

</html>