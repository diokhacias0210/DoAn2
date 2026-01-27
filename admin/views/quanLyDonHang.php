<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .qldh-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            table-layout: fixed;
        }

        .qldh-table th {
            background: #4CAF50;
            color: white;
            padding: 15px;
            text-align: left;
        }

        .qldh-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .qldh-table tr:hover {
            background: #f8f9fa;
        }

        .order-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .shipping {
            background: #cce5ff;
            color: #004085;
        }

        .completed {
            background: #d4edda;
            color: #155724;
        }

        .cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-action {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }

        .btn-view {
            background: #17a2b8;
            color: white;
        }

        .btn-view:hover {
            background: #138496;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background: #218838;
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background: #c82333;
        }

        .alert-container {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include '../../includes/navbar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="qldh-container">
                <h1><i class="fas fa-box"></i> Quản lý đơn hàng</h1>

                <form method="GET" action="adminDonHangController.php" class="search-bar" style="display:flex; gap:8px; margin-bottom: 20px; ">
                    <input type="text"
                        class="form-control"
                        name="search"
                        placeholder="Tìm kiếm theo mã giảm giá"
                        value="<?= htmlspecialchars($keyword ?? '') ?>"
                        autocomplete="off">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <a href="adminDonHangController.php?action=duyet_tat_ca" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn muốn duyệt TOÀN BỘ các đơn hàng đang chờ xử lý không?');"> Duyệt tất cả đơn chờ
                </a>

                <div class="alert-container">
                    <?php if (!empty($message)) echo $message; ?>
                </div>

                <table class="qldh-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Mã ĐH</th>
                            <th style="width: 12%;">Khách hàng</th>
                            <th style="width: 10%;">Tổng tiền</th>
                            <th style="width: 15%;">Ngày đặt</th>
                            <th style="width: 12%;">Tình trạng đơn hàng</th>
                            <th style="width: 12%;">Trạng thái duyệt</th>
                            <th style="width: 150px;">Ghi chú</th>
                            <th style="width: 20%;">Mã TT</th>
                            <th style="width: 16%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($danhSachDonHang)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Chưa có đơn hàng nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($danhSachDonHang as $dh): ?>
                                <tr>
                                    <td><strong>#<?= $dh['MaDH'] ?></strong></td>
                                    <td><?= htmlspecialchars($dh['TenTK']) ?></td>
                                    <td><strong><?= number_format($dh['TongTien'], 0, ',', '.') ?>đ</strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'pending';
                                        switch ($dh['TrangThai']) {
                                            case 'Đã xác nhận':
                                                $statusClass = 'confirmed';
                                                break;
                                            case 'Đang giao':
                                                $statusClass = 'shipping';
                                                break;
                                            case 'Hoàn tất':
                                                $statusClass = 'completed';
                                                break;
                                            case 'Đã hủy':
                                                $statusClass = 'cancelled';
                                                break;
                                        }
                                        ?>
                                        <span class="order-status <?= $statusClass ?>">
                                            <?= htmlspecialchars($dh['TrangThai']) ?>
                                        </span>

                                    </td>
                                    <td>
                                        <?php
                                        if ($dh['TrangThai'] == 'Chờ xử lý') {
                                            echo "<span class='order-status pending'>Chưa duyệt</span>";
                                        } elseif ($dh['TrangThai'] == 'Đã hủy') {
                                            echo "<span class='order-status cancelled'>Đã hủy</span>";
                                        } else {
                                            echo "<span class='order-status confirmed'>Đã duyệt</span>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($dh['TrangThai'] == 'Đã hủy' && !empty($dh['GhiChu'])): ?>
                                            <div class="text-danger" style="font-size: 0.9em; line-height: 1.4;">
                                                <?= htmlspecialchars($dh['GhiChu']) ?>
                                            </div>
                                        <?php elseif (!empty($dh['GhiChu'])): ?>
                                            <span class="text-muted" style="font-size: 0.9em;">
                                                <?= htmlspecialchars($dh['GhiChu']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($dh['MaThanhToan'])): ?>
                                            <small><?= htmlspecialchars($dh['MaThanhToan']) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">Chưa có</small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="xemChiTiet(<?= $dh['MaDH'] ?>)">
                                                <i class="fas fa-eye"></i> Xem
                                            </button>

                                            <?php if ($dh['TrangThai'] == 'Chờ xử lý'): ?>
                                                <button class="btn-action btn-approve"
                                                    onclick="duyetDonHang(<?= $dh['MaDH'] ?>, 'Đã xác nhận')">
                                                    <i class="fas fa-check"></i> Duyệt
                                                </button>
                                            <?php elseif ($dh['TrangThai'] == 'Đã xác nhận'): ?>
                                                <button class="btn-action btn-approve"
                                                    onclick="duyetDonHang(<?= $dh['MaDH'] ?>, 'Đang giao')">
                                                    <i class="fas fa-truck"></i> Giao
                                                </button>
                                            <?php elseif ($dh['TrangThai'] == 'Đang giao'): ?>
                                                <button class="btn-action btn-approve"
                                                    onclick="duyetDonHang(<?= $dh['MaDH'] ?>, 'Hoàn tất')">
                                                    <i class="fas fa-check-circle"></i> Hoàn tất
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../assets/js/bootstrap/bootstrap.bundle.js"></script>
    <script>
        // Toggle sidebar trên mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                const toggleBtn = document.createElement('button');
                toggleBtn.className = 'sidebar-toggle';
                toggleBtn.innerHTML = '☰';
                toggleBtn.onclick = function() {
                    sidebar.classList.toggle('active');
                };
                document.body.appendChild(toggleBtn);
            }

            // Tự động ẩn alert sau 5 giây
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
        });

        function xemChiTiet(maDH) {
            window.location.href = 'adminChiTietDonHangController.php?action=chitiet&MaDH=' + maDH;
        }

        function duyetDonHang(maDH, trangThai) {
            const statusText = {
                'Đã xác nhận': 'xác nhận',
                'Đang giao': 'chuyển sang đang giao',
                'Hoàn tất': 'hoàn tất'
            };

            if (confirm(`Bạn có chắc chắn muốn ${statusText[trangThai]} đơn hàng #${maDH}?`)) {
                window.location.href = `adminDonHangController.php?action=duyet&MaDH=${maDH}&status=${trangThai}`;
            }
        }
    </script>
</body>

</html>