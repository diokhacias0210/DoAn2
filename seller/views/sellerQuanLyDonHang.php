<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kênh người bán - Đơn hàng</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
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

        .order-card {
            border: 1px solid var(--bs-pink-200);
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            background-color: var(--bs-pink-100);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--bs-pink-800);
        }

        .order-body {
            padding: 20px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 15px;
            align-items: center;
        }

        .status-select {
            padding: 5px 10px;
            border-radius: 20px;
            border: 1px solid #ddd;
            font-weight: bold;
            cursor: pointer;
            outline: none;
        }

        /* Màu trạng thái */
        .st-cho-xu-ly {
            color: #fd7e14;
            border-color: #fd7e14;
        }

        .st-da-xac-nhan {
            color: #0d6efd;
            border-color: #0d6efd;
        }

        .st-dang-giao {
            color: #6f42c1;
            border-color: #6f42c1;
        }

        .st-hoan-tat {
            color: #198754;
            border-color: #198754;
            background: #d1e7dd;
        }

        .st-da-huy {
            color: #dc3545;
            border-color: #dc3545;
        }

        .price-info strong {
            font-size: 1.1rem;
            color: var(--bs-pink-600);
        }

        .fee-info {
            font-size: 0.85rem;
            color: #888;
        }

        @media (max-width: 768px) {
            .order-body {
                grid-template-columns: 1fr;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }

        /* --- CSS CHO SỐ THÔNG BÁO TIN NHẮN --- */
        .seller-nav a {
            position: relative; /* Thêm dòng này để làm mốc gắn huy hiệu đỏ */
        }

        .chat-badge {
            position: absolute;
            top: -4px;
            right: -8px;
            background-color: #dc3545; /* Màu đỏ nổi bật */
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 50px;
            line-height: 1;
            border: 2px solid #fff; /* Viền trắng để tách biệt khỏi nền */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="container" style="max-width: 1200px; padding: 0;">

            <div class="seller-nav">
                <a href="sellerSanPhamController.php"><i class="fa-solid fa-box"></i> Quản lý sản phẩm</a>
                <a href="sellerDonHangController.php" class="active"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
                <a href="sellerChatController.php">
                    Tin nhắn khách hàng 
                    <span class="chat-badge">3</span> 
                </a>
            </div>

            <div class="mb-4">
                <form method="GET" class="d-flex" style="max-width: 500px;">
                    <input type="text" name="search" class="form-control" placeholder="Tìm mã đơn hàng hoặc tên khách..." value="<?= htmlspecialchars($keyword) ?>" style="border-radius: 20px 0 0 20px;">
                    <button class="btn btn-pink" style="background: var(--bs-pink-500); color: white; border-radius: 0 20px 20px 0; border:none; padding: 0 20px;">Tìm</button>
                </form>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="order-list">
                <?php if (empty($dsDonHang)): ?>
                    <div class="text-center py-5">
                        <i class="fa-regular fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có đơn hàng nào.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($dsDonHang as $dh): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <i class="fa-solid fa-hashtag"></i> Mã đơn: <strong>#<?= $dh['MaDH'] ?></strong>
                                    <span class="mx-2">|</span>
                                    <i class="fa-regular fa-clock"></i> <?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?>
                                </div>
                                <div>
                                    Khách hàng: <strong><?= htmlspecialchars($dh['NguoiMua']) ?></strong>
                                </div>
                            </div>

                            <div class="order-body">
                                <div>
                                    <p class="mb-1"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($dh['Sdt']) ?></p>
                                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">
                                        <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($dh['DiaChiGiao'] ?? 'Chưa cập nhật') ?>
                                    </p>
                                    <?php if (!empty($dh['GhiChu'])): ?>
                                        <p class="mb-0 text-danger" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-note-sticky"></i> Note: <?= htmlspecialchars($dh['GhiChu']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="price-info">
                                    <div>Khách trả: <?= number_format($dh['TongTien'], 0, ',', '.') ?>đ</div>
                                    <div class="fee-info">Phí sàn (5%): -<?= number_format($dh['PhiSan'], 0, ',', '.') ?>đ</div>
                                    <div class="mt-1" style="border-top: 1px dashed #ccc; padding-top:5px;">
                                        Thực nhận: <strong><?= number_format($dh['TienNguoiBanNhan'], 0, ',', '.') ?>đ</strong>
                                    </div>
                                </div>

                                <div>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="maDH" value="<?= $dh['MaDH'] ?>">

                                        <?php
                                        // Mapping class màu cho select
                                        $classStatus = 'st-cho-xu-ly';
                                        if ($dh['TrangThai'] == 'Đã xác nhận') $classStatus = 'st-da-xac-nhan';
                                        if ($dh['TrangThai'] == 'Đang giao') $classStatus = 'st-dang-giao';
                                        if ($dh['TrangThai'] == 'Hoàn tất') $classStatus = 'st-hoan-tat';
                                        if ($dh['TrangThai'] == 'Đã hủy') $classStatus = 'st-da-huy';

                                        // Nếu đơn đã hoàn tất hoặc hủy thì disable không cho sửa
                                        $disabled = ($dh['TrangThai'] == 'Hoàn tất' || $dh['TrangThai'] == 'Đã hủy') ? 'disabled' : '';
                                        ?>

                                        <select name="trangThai" class="status-select <?= $classStatus ?>" onchange="this.form.submit()" <?= $disabled ?>>
                                            <option value="Chờ xử lý" <?= $dh['TrangThai'] == 'Chờ xử lý' ? 'selected' : '' ?>>⏳ Chờ xử lý</option>
                                            <option value="Đã xác nhận" <?= $dh['TrangThai'] == 'Đã xác nhận' ? 'selected' : '' ?>>✅ Đã xác nhận</option>
                                            <option value="Đang giao" <?= $dh['TrangThai'] == 'Đang giao' ? 'selected' : '' ?>>🚚 Đang giao</option>
                                            <option value="Hoàn tất" <?= $dh['TrangThai'] == 'Hoàn tất' ? 'selected' : '' ?>>🎉 Hoàn tất</option>
                                            <option value="Đã hủy" <?= $dh['TrangThai'] == 'Đã hủy' ? 'selected' : '' ?>>❌ Hủy đơn</option>
                                        </select>
                                    </form>
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="xemChiTiet(<?= $dh['MaDH'] ?>)">
                                        Xem sản phẩm <i class="fa-solid fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="modalChiTiet" class="modal" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn #<span id="spanMaDH"></span></h5>
                    <button type="button" class="btn-close" onclick="closeModal()" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center text-muted">Chức năng xem chi tiết đang cập nhật...</p>
                    <div class="text-center">
                        <small>Bạn có thể tạo thêm controller lấy chi tiết đơn hàng trả về HTML tại đây.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function xemChiTiet(maDH) {
            // Để đơn giản cho đồ án, ta có thể alert hoặc redirect
            // Hoặc hiển thị modal placeholder
            document.getElementById('spanMaDH').innerText = maDH;
            document.getElementById('modalChiTiet').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalChiTiet').style.display = 'none';
        }
    </script>
    <script src="../../assets/js/js.js"></script>

</body>

</html>