<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kênh người bán - Thông tin cửa hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">


    <style>
        /* CSS duy nhất được thêm vào để làm tròn Avatar */
        .avatar-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }

        body {
            background-color: #f8f9fa;
        }

        /* --- ĐỒNG BỘ KHUNG SƯỜN --- */
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

        /* --- MENU BÊN TRÁI (SIDEBAR) --- */
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

        /* --- BADGE TIN NHẮN --- */
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

<body style="background-color: #f4f6f9;">
    <?php include '../../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="seller-wrapper" style="max-width: 1300px; margin: 40px auto 50px auto; padding: 0 15px;">
            <div class="row">

                <div class="col-md-3 mb-4">

                    <div class="seller-sidebar">

                        <a href="sellerThongTinController.php" class="active"><i class="fa-solid fa-circle-info"></i> Thông tin cửa hàng</a>
                        <a href="sellerSanPhamController.php"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
                        <a href="sellerDonHangController.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
                        <a href="sellerChatController.php">
                            <i class="fa-solid fa-comments"></i> Tin nhắn

<<<<<<< HEAD
                            <?php if (isset($soTinNhanChuaDoc) && $soTinNhanChuaDoc > 0): ?>
                                <span class="chat-badge"><?php echo $soTinNhanChuaDoc; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="sellerDoanhThuController.php"><i class="fa-solid fa-chart-line"></i> Doanh thu & Rút tiền</a>

                    </div>
=======
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="seller-sidebar">
                    <a href="sellerThongTinController.php" class="active"><i class="fa-solid fa-circle-info"></i> Thông tin cửa hàng</a>
                    <a href="sellerSanPhamController.php"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
                    <a href="sellerDonHangController.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
                    <a href="sellerChatController.php">
                        <i class="fa-solid fa-comments"></i> Tin nhắn

                        <?php if (isset($soTinNhanChuaDoc) && $soTinNhanChuaDoc > 0): ?>
                            <span class="chat-badge"><?php echo $soTinNhanChuaDoc; ?></span>
                        <?php endif; ?>
                    </a>                    
                    <a href="sellerDoanhThuController.php"><i class="fa-solid fa-chart-line"></i> Doanh thu & Rút tiền</a>
>>>>>>> 8645671ea5812c198eece56f72f3510d81c1ceb0
                </div>

                <div class="col-md-9">
                    <div class="seller-content-box" style="background: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); min-height: 600px;">

                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h4 class="fw-bold mb-0">Thông tin Shop</h4>
                            <?php if (isset($_SESSION['msg'])): ?>
                                <div class="text-success fw-bold" id="flash-msg">
                                    <i class="fa-solid fa-circle-check"></i> <?= $_SESSION['msg'];
                                                                                unset($_SESSION['msg']); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($thongTin): ?>
                            <form action="sellerThongTinController.php" method="POST" enctype="multipart/form-data">

                                <div class="text-center mb-5">
                                    <?php
                                    $avatarPath = !empty($thongTin['Avatar']) ? '../../' . $thongTin['Avatar'] : '../../assets/images/user.png';
                                    ?>
                                    <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar Shop" class="avatar-img mb-3" id="previewAvatar"><br>
                                    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="form-control form-control-sm w-auto d-inline-block">
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3 fw-bold text-secondary">Tên cửa hàng:</div>
                                    <div class="col-sm-9"><input type="text" class="form-control" name="TenCuaHang" value="<?= htmlspecialchars($thongTin['TenCuaHang']) ?>" required></div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3 fw-bold text-secondary">Số CCCD/CMND:</div>
                                    <div class="col-sm-9"><input type="text" class="form-control" name="SoCCCD" value="<?= htmlspecialchars($thongTin['SoCCCD']) ?>" required></div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3 fw-bold text-secondary">Địa chỉ lấy/trả hàng:</div>
                                    <div class="col-sm-9"><input type="text" class="form-control" name="DiaChiKhoHang" value="<?= htmlspecialchars($thongTin['DiaChiKhoHang']) ?>" required></div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3 fw-bold text-secondary">Ngân hàng:</div>
                                    <div class="col-sm-9"><input type="text" class="form-control" name="TenNganHang" value="<?= htmlspecialchars($thongTin['TenNganHang']) ?>" required></div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3 fw-bold text-secondary">Số tài khoản:</div>
                                    <div class="col-sm-9"><input type="text" class="form-control fw-bold" name="SoTaiKhoanNganHang" value="<?= htmlspecialchars($thongTin['SoTaiKhoanNganHang']) ?>" required></div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-sm-3 fw-bold text-secondary">Chủ tài khoản:</div>
                                    <div class="col-sm-9"><input type="text" class="form-control text-uppercase" name="TenChuTaiKhoan" value="<?= htmlspecialchars($thongTin['TenChuTaiKhoan']) ?>" required></div>
                                </div>

                                <div class="text-end mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">Lưu Thay Đổi</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">Không tìm thấy thông tin cửa hàng.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/js.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic xem trước ảnh Avatar
            const avatarInput = document.getElementById('avatarInput');
            const preview = document.getElementById('previewAvatar');

            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            preview.src = event.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Tự động mờ dòng thông báo bên phải sau 3.5s
            setTimeout(() => {
                const msg = document.getElementById('flash-msg');
                if (msg) msg.style.display = 'none';
            }, 3500);
        });
    </script>
</body>

</html>