<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo của tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css/thongTinTaiKhoan.css" rel="stylesheet">
    <link href="../assets/css/thongBao.css" rel="stylesheet">
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="container">

            <?php
            $soThongBaoMoi_Sidebar = 0;
            if (isset($_SESSION['IdTaiKhoan'])) {
                if (!class_exists('ThongBaoModel')) {
                    require_once __DIR__ . '/../models/thongBaoModel.php';
                }
                global $conn;
                if ($conn) {
                    $modelTB_Sidebar = new ThongBaoModel($conn);
                    $soThongBaoMoi_Sidebar = $modelTB_Sidebar->demThongBaoChuaDoc($_SESSION['IdTaiKhoan']);
                }
            }

            // Hàm hỗ trợ kiểm tra file hiện tại để gán class "active" tự động
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>

            <div class="side">
                <div class="avatar-ten" style="text-align: center; margin-bottom: 20px;">
                    <div class="avatar" style="width: 100px; height: 100px; margin: 0 auto 10px; border-radius: 50%; border: 2px solid var(--bs-pink-200); padding: 3px; display: flex; justify-content: center; align-items: center;">

                        <img src="../<?php echo isset($_SESSION['Avatar']) && !empty($_SESSION['Avatar']) ? $_SESSION['Avatar'] : 'assets/images/placeholder.png'; ?>"
                            alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">

                    </div>
                </div>

                <nav class="menu">
                    <ul>
                        <li>
                            <a href="../controllers/thongTinTaiKhoanController.php" class="<?= ($current_page == 'thongTinTaiKhoanController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-id-badge"></i> Quản lý tài khoản
                            </a>
                        </li>

                        <li>
                            <a href="../controllers/thongBaoController.php" class="<?= ($current_page == 'thongBaoController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-bell"></i> Thông báo hệ thống
                                <?php if ($soThongBaoMoi_Sidebar > 0): ?>
                                    <span class="menu-badge-count"><?= $soThongBaoMoi_Sidebar ?></span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <li>
                            <a href="../controllers/gioHangController.php" class="<?= ($current_page == 'gioHangController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của tôi
                            </a>
                        </li>

                        <li>
                            <a href="../controllers/danhSachYeuThichController.php" class="<?= ($current_page == 'danhSachYeuThichController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-heart"></i> Sản phẩm yêu thích
                            </a>
                        </li>

                        <li>
                            <a href="../controllers/lichSuDonHangController.php" class="<?= ($current_page == 'lichSuDonHangController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-clipboard-list"></i> Lịch sử giao dịch
                            </a>
                        </li>

                        <li>
                            <a href="../seller/controllers/sellerSanPhamController.php" class="seller-link">
                                <i class="fa-solid fa-store"></i> Kênh Người Bán
                            </a>
                        </li>

                        <li>
                            <a href="../controllers/dangXuatController.php" class="logout-link">
                                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="content" style="flex: 1; padding-left: 20px; background: transparent; border: none;">
                <div class="thong-bao-container">
                    <div class="tb-header">
                        <h2><i class="fa-solid fa-bell"></i> Thông báo hệ thống</h2>
                    </div>

                    <div class="tb-list">
                        <?php if (empty($dsThongBao)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa-regular fa-bell-slash fa-3x mb-3"></i>
                                <p>Bạn chưa có thông báo nào.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($dsThongBao as $tb):
                                // Xác định icon dựa trên loại thông báo
                                $iconClass = 'fa-info-circle';
                                $bgClass = 'icon-hethong';

                                if ($tb['LoaiTB'] == 'KhuyenMai') {
                                    $iconClass = 'fa-tag';
                                    $bgClass = 'icon-khuyenmai';
                                } elseif ($tb['LoaiTB'] == 'ViPham') {
                                    $iconClass = 'fa-triangle-exclamation';
                                    $bgClass = 'icon-vipham';
                                } elseif ($tb['LoaiTB'] == 'BaoCao') {
                                    $iconClass = 'fa-gavel';
                                    $bgClass = 'icon-baocao';
                                }

                                // Kiểm tra xem đã đọc chưa
                                $isUnread = ($tb['DaXem'] == 0) ? 'unread' : '';
                            ?>
                                <div class="tb-item <?= $isUnread ?>"
                                    id="item-tb-<?= $tb['MaTB'] ?>"
                                    onclick="moThongBao(<?= $tb['MaTB'] ?>, '<?= htmlspecialchars($tb['TieuDe'], ENT_QUOTES) ?>')">

                                    <div class="tb-icon <?= $bgClass ?>">
                                        <i class="fa-solid <?= $iconClass ?>"></i>
                                    </div>
                                    <div class="tb-content">
                                        <h4 class="tb-title"><?= htmlspecialchars($tb['TieuDe']) ?></h4>
                                        <div class="tb-date"><?= date('d/m/Y H:i', strtotime($tb['NgayTao'])) ?></div>
                                        <div class="tb-snippet"><?= strip_tags($tb['NoiDung']) ?></div>

                                        <div id="content-tb-<?= $tb['MaTB'] ?>" style="display:none;">
                                            <?= $tb['NoiDung'] ?>
                                            <hr>
                                            <small class="text-muted">Nhận lúc: <?= date('d/m/Y H:i:s', strtotime($tb['NgayTao'])) ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDocThongBao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header text-white" style="background: var(--bs-pink-500);">
                    <h5 class="modal-title" id="modal-tb-title">Chi tiết thông báo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="modal-tb-content" style="font-size: 15px; line-height: 1.6;">
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function moThongBao(maTB, tieuDe) {
            // 1. Load data vào Modal
            document.getElementById('modal-tb-title').innerText = tieuDe;
            document.getElementById('modal-tb-content').innerHTML = document.getElementById('content-tb-' + maTB).innerHTML;

            // 2. Hiện Modal
            new bootstrap.Modal(document.getElementById('modalDocThongBao')).show();

            // 3. Nếu là thông báo chưa đọc, gửi AJAX để đổi trạng thái
            let item = document.getElementById('item-tb-' + maTB);
            if (item.classList.contains('unread')) {
                $.ajax({
                    url: 'thongBaoController.php',
                    type: 'POST',
                    data: {
                        action: 'mark_read',
                        maTB: maTB
                    },
                    success: function(res) {
                        // Bỏ class unread (mất màu nền hồng, chữ hết in đậm)
                        item.classList.remove('unread');

                        // Cập nhật số lượng trên chuông (nếu bạn có ID chuông là bell-count)
                        let bellCount = document.getElementById('bell-count');
                        if (bellCount) {
                            let count = parseInt(bellCount.innerText) - 1;
                            if (count > 0) {
                                bellCount.innerText = count;
                            } else {
                                bellCount.style.display = 'none'; // Xóa số đỏ nếu hết
                            }
                        }
                    }
                });
            }
        }
    </script>
</body>

</html>