<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $maDH ?></title>
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
            margin: 80px auto 50px auto !important;
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

        .toast-container {
            z-index: 1055;
            margin-top: 80px;
        }
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

    <div class="seller-wrapper mt-4 mb-5">
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

                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h4 class="m-0 fw-bold" style="color: var(--bs-pink-600);"><i class="fa-solid fa-file-invoice"></i> CHI TIẾT ĐƠN HÀNG #<?= $maDH ?></h4>
                        <a href="sellerDonHangController.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
                    </div>

                    <?php
                    $ngayDat = date('d/m/Y H:i', strtotime($thongTin['NgayDat']));
                    $ghiChu = !empty($thongTin['GhiChu']) ? htmlspecialchars($thongTin['GhiChu']) : 'Không có';
                    $tongTien = number_format($thongTin['TongTien'], 0, ',', '.');
                    $phiSan = number_format($thongTin['PhiSan'], 0, ',', '.');
                    $thucNhan = number_format($thongTin['TienNguoiBanNhan'], 0, ',', '.');
                    ?>

                    <div class='row mb-4'>
                        <div class='col-md-6 border-end'>
                            <h6 class='fw-bold mb-3' style='color: var(--bs-pink-600);'><i class='fa-solid fa-location-dot'></i> Thông tin nhận hàng</h6>
                            <p class='mb-1'><strong>Khách hàng:</strong> <?= htmlspecialchars($thongTin['NguoiMua']) ?></p>
                            <p class='mb-1'><strong>Số điện thoại:</strong> <?= htmlspecialchars($thongTin['Sdt']) ?></p>
                            <p class='mb-1'><strong>Địa chỉ:</strong> <?= htmlspecialchars($thongTin['DiaChiGiao']) ?></p>
                            <p class='mb-1 text-danger'><strong>Ghi chú:</strong> <?= $ghiChu ?></p>
                        </div>
                        <div class='col-md-6 ps-4'>
                            <h6 class='fw-bold mb-3' style='color: var(--bs-pink-600);'><i class='fa-solid fa-file-invoice-dollar'></i> Thông tin thanh toán</h6>

                            <form method="POST" action="sellerDonHangController.php" class="d-flex align-items-center gap-2 mb-2">
                                <strong>Trạng thái:</strong>
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="maDH" value="<?= $maDH ?>">
                                <input type="hidden" name="from_detail" value="1">
                                <?php
                                $tt = $thongTin['TrangThai'];
                                $classStatus = 'st-cho-xu-ly';
                                if ($tt == 'Đã xác nhận') $classStatus = 'st-da-xac-nhan';
                                if ($tt == 'Đang giao') $classStatus = 'st-dang-giao';
                                if ($tt == 'Hoàn tất') $classStatus = 'st-hoan-tat';
                                if ($tt == 'Đã hủy') $classStatus = 'st-da-huy';
                                $disabled = in_array($tt, ['Đang giao', 'Hoàn tất', 'Đã hủy']) ? 'disabled' : '';
                                ?>
                                <select name="trangThai" class="status-select <?= $classStatus ?>" style="width: auto; padding: 2px 10px;" onchange="if(confirm('Xác nhận đổi trạng thái?')) this.form.submit(); else this.value='<?= $tt ?>';" <?= $disabled ?>>
                                    <option value="<?= $tt ?>" selected hidden><?= $tt ?></option>
                                    <?php if ($tt == 'Chờ xử lý'): ?>
                                        <option value="Đã xác nhận">✅ Xác nhận đơn</option>
                                        <option value="Đã hủy">❌ Hủy đơn này</option>
                                    <?php elseif ($tt == 'Đã xác nhận'): ?>
                                        <option value="Đang giao">🚚 Giao cho Vận chuyển</option>
                                    <?php endif; ?>
                                </select>
                            </form>

                            <p class='mb-1'><strong>Ngày đặt:</strong> <?= $ngayDat ?></p>
                            <p class='mb-1'><strong>Tổng tiền khách trả:</strong> <?= $tongTien ?>đ</p>
                            <p class='mb-1 text-danger'><strong>Phí sàn (5%):</strong> -<?= $phiSan ?>đ</p>
                            <div class='mt-2 pt-2 border-top'>
                                <span class='fs-6'><strong>Thực nhận:</strong></span> <span class='fs-5 text-success fw-bold'><?= $thucNhan ?>đ</span>
                            </div>
                        </div>
                    </div>

                    <h6 class='fw-bold mb-3 mt-4' style='color: var(--bs-pink-600);'><i class='fa-solid fa-box-open'></i> Sản phẩm đã đặt</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>SL</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chiTiet as $sp): ?>
                                    <?php
                                    $anh = !empty($sp['AnhDaiDien']) ? '../../' . $sp['AnhDaiDien'] : '../../assets/images/placeholder.png';
                                    $thanhtien = ($sp['DonGia'] * $sp['SoLuongSanPham']) - $sp['GiamGia'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class='d-flex align-items-center gap-3'>
                                                <img src='<?= $anh ?>' style='width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #eee;'>
                                                <span class='fw-bold text-dark'><?= htmlspecialchars($sp['TenHH']) ?></span>
                                            </div>
                                        </td>
                                        <td class='text-danger text-center'><?= number_format($sp['DonGia'], 0, ',', '.') ?>đ</td>
                                        <td class='text-center'><?= $sp['SoLuongSanPham'] ?></td>
                                        <td class='fw-bold text-danger text-end pe-3'><?= number_format($thanhtien, 0, ',', '.') ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="../../assets/js/js.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            $('.toast').toast('hide');
        }, 3500);
    </script>
</body>

</html>