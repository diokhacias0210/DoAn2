<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kênh người bán - Đơn hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
        }

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

        .filter-box {
            background: var(--bs-pink-50);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid var(--bs-pink-200);
        }

        .order-card {
            border: 1px solid var(--bs-pink-200);
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.2s;
            background: #fff;
        }

        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .order-header {
            background-color: var(--bs-pink-100);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--bs-pink-800);
        }

        .order-body {
            padding: 20px;
            display: grid;
            grid-template-columns: 2fr 1.5fr 1.5fr 1fr;
            gap: 15px;
            align-items: center;
        }

        .status-select {
            padding: 6px 12px;
            border-radius: 20px;
            border: 2px solid #ddd;
            font-weight: bold;
            cursor: pointer;
            outline: none;
            width: 100%;
            text-align: center;
        }

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
            cursor: not-allowed;
        }

        .st-da-huy {
            color: #dc3545;
            border-color: #dc3545;
            background: #f8d7da;
            cursor: not-allowed;
        }

        .price-info strong {
            font-size: 1.2rem;
            color: var(--bs-pink-600);
        }

        /* Style cho Toast Thông báo */
        .toast-container {
            z-index: 1055;
            margin-top: 80px;
        }

        /* Đẩy xuống tránh header */
    </style>
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <?php if (!empty($message)): ?>
            <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold"><i class="fa-solid fa-circle-check"></i> <?= $message ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // --- ĐẾM SỐ TIN NHẮN KHÁCH HÀNG CHƯA ĐỌC ---
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
                    <a href="sellerSanPhamController.php"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
                    <a href="sellerDonHangController.php" class="active"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
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

                    <div class="filter-box shadow-sm">
                        <form method="GET" class="row gx-2 gy-2 align-items-end">
                            <div class="col-md-3">
                                <label class="fw-bold text-muted small">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã đơn, Tên khách..." value="<?= htmlspecialchars($keyword) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold text-muted small">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">- Tất cả -</option>
                                    <option value="Chờ xử lý" <?= ($filter_status == 'Chờ xử lý') ? 'selected' : '' ?>>Chờ xử lý</option>
                                    <option value="Đã xác nhận" <?= ($filter_status == 'Đã xác nhận') ? 'selected' : '' ?>>Đã xác nhận</option>
                                    <option value="Đang giao" <?= ($filter_status == 'Đang giao') ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="Hoàn tất" <?= ($filter_status == 'Hoàn tất') ? 'selected' : '' ?>>Hoàn tất</option>
                                    <option value="Đã hủy" <?= ($filter_status == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fw-bold text-muted small">Từ ngày</label>
                                <input type="date" name="tungay" class="form-control" value="<?= $tuNgay ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="fw-bold text-muted small">Đến ngày</label>
                                <input type="date" name="denngay" class="form-control" value="<?= $denNgay ?>">
                            </div>
                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn text-white w-100" style="background: var(--bs-pink-500);"><i class="fa-solid fa-filter"></i> Lọc</button>
                                <a href="sellerDonHangController.php" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-right"></i></a>
                            </div>
                        </form>
                    </div>

                    <div class="order-list">
                        <?php if (empty($dsDonHang)): ?>
                            <div class="text-center py-5">
                                <i class="fa-regular fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Không tìm thấy đơn hàng nào phù hợp.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($dsDonHang as $dh): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <div><i class="fa-solid fa-hashtag"></i> <strong><?= $dh['MaDH'] ?></strong> <span class="mx-2 text-muted">|</span> <i class="fa-regular fa-clock"></i> <?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?></div>
                                        <div>Khách hàng: <strong><i class="fa-solid fa-user"></i> <?= htmlspecialchars($dh['NguoiMua']) ?></strong></div>
                                    </div>

                                    <div class="order-body">
                                        <div>
                                            <p class="mb-1"><i class="fa-solid fa-phone text-muted"></i> <?= htmlspecialchars($dh['Sdt']) ?></p>
                                            <p class="mb-1 small text-muted"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($dh['DiaChiGiao']) ?></p>
                                        </div>

                                        <div class="price-info text-end border-end pe-3">
                                            <div class="small text-muted">Tổng đơn: <?= number_format($dh['TongTien'], 0, ',', '.') ?>đ</div>
                                            <div class="small text-danger mb-1 border-bottom pb-1">Phí sàn: -<?= number_format($dh['PhiSan'], 0, ',', '.') ?>đ</div>
                                            <div>Thực nhận: <br><strong><?= number_format($dh['TienNguoiBanNhan'], 0, ',', '.') ?>đ</strong></div>
                                        </div>

                                        <div>
                                            <form method="POST" action="sellerDonHangController.php">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="maDH" value="<?= $dh['MaDH'] ?>">

                                                <?php
                                                $tt = $dh['TrangThai'];
                                                $classStatus = 'st-cho-xu-ly';
                                                if ($tt == 'Đã xác nhận') $classStatus = 'st-da-xac-nhan';
                                                if ($tt == 'Đang giao') $classStatus = 'st-dang-giao';
                                                if ($tt == 'Hoàn tất') $classStatus = 'st-hoan-tat';
                                                if ($tt == 'Đã hủy') $classStatus = 'st-da-huy';

                                                $disabled = in_array($tt, ['Đang giao', 'Hoàn tất', 'Đã hủy']) ? 'disabled' : '';
                                                ?>

                                                <select name="trangThai" class="status-select <?= $classStatus ?>" onchange="if(confirm('Xác nhận đổi trạng thái đơn hàng?')) this.form.submit(); else this.value='<?= $tt ?>';" <?= $disabled ?>>
                                                    <option value="<?= $tt ?>" selected hidden><?= $tt ?></option>
                                                    <?php if ($tt == 'Chờ xử lý'): ?>
                                                        <option value="Đã xác nhận">✅ Xác nhận đơn</option>
                                                        <option value="Đã hủy">❌ Hủy đơn này</option>
                                                    <?php elseif ($tt == 'Đã xác nhận'): ?>
                                                        <option value="Đang giao">🚚 Giao cho Vận chuyển</option>
                                                    <?php endif; ?>
                                                </select>
                                            </form>
                                        </div>

                                        <div class="text-center">
                                            <a href="sellerChiTietDonHangController.php?id=<?= $dh['MaDH'] ?>" class="btn btn-outline-info btn-sm rounded-pill">
                                                <i class="fa-solid fa-circle-info"></i> Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <ul class="pagination justify-content-center mt-4">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>&status=<?= urlencode($filter_status) ?>&tungay=<?= $tuNgay ?>&denngay=<?= $denNgay ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="../../assets/js/js.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tự động ẩn Toast sau 3.5 giây
        setTimeout(() => {
            $('.toast').toast('hide');
        }, 3500);
    </script>
</body>

</html>