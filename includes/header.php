<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// KHAI BÁO ĐƯỜNG DẪN GỐC CỦA PROJECT
// Nếu thư mục htdocs của bạn tên khác, hãy đổi '/DoAn2' thành tên tương ứng
$baseURL = '/DoAn2';

$soThongBaoMoi = 0;
if (isset($_SESSION['IdTaiKhoan'])) {
    require_once __DIR__ . '/../models/thongBaoModel.php'; // __DIR__ của PHP luôn lấy đúng thư mục chứa file, nên giữ nguyên
    global $conn;
    if ($conn) {
        $modelTB_Header = new ThongBaoModel($conn);
        $soThongBaoMoi = $modelTB_Header->demThongBaoChuaDoc($_SESSION['IdTaiKhoan']);
    }
}
?>

<div class="dau-trang">
    <div class="header-container">
        <a href="<?= $baseURL ?>/controllers/trangChuController.php">
            <div class="logo-container">
                <img src="<?= $baseURL ?>/assets/images/source/logo.png" alt="logo" id="logo">
            </div>
        </a>

        <div class="head-mid">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon" style="color: pink;"></i>
                <input type="search" id="search" placeholder="Tìm kiếm sản phẩm" autocomplete="off">
                <div id="live-search-result"></div>
            </div>
        </div>

        <div class="header-right" style="display: flex; align-items: center; gap: 20px;">

            <a href="<?= $baseURL ?>/controllers/trangChuController.php" class="trangchu"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <a href="<?= $baseURL ?>/controllers/danhSachSanPhamController.php" class="sanpham"><i class="fa-solid fa-box"></i> Sản phẩm</a>

            <?php if (isset($_SESSION['IdTaiKhoan'])): ?>
                <div class="account-dropdown">
                    <a href="<?= $baseURL ?>/controllers/thongTinTaiKhoanController.php" class="account-btn">
                        <i class="fa-solid fa-user-circle" style="font-size: 22px;"></i>
                        <span><?php echo isset($_SESSION['TenTK']) ? htmlspecialchars($_SESSION['TenTK']) : 'Tài khoản'; ?></span>
                        <?php if ($soThongBaoMoi > 0): ?>
                            <span class="dot-badge"></span>
                        <?php endif; ?>
                    </a>

                    <div class="dropdown-content">
                        <a href="<?= $baseURL ?>/controllers/thongTinTaiKhoanController.php">
                            <i class="fa-solid fa-id-badge"></i> Quản lý tài khoản
                        </a>

                        <a href="<?= $baseURL ?>/controllers/thongBaoController.php">
                            <i class="fa-solid fa-bell"></i> Thông báo hệ thống
                            <?php if ($soThongBaoMoi > 0): ?>
                                <span class="badge-count" id="bell-count"><?= $soThongBaoMoi ?></span>
                            <?php endif; ?>
                        </a>

                        <a href="<?= $baseURL ?>/controllers/gioHangController.php">
                            <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của tôi
                        </a>

                        <a href="<?= $baseURL ?>/controllers/danhSachYeuThichController.php">
                            <i class="fa-solid fa-heart"></i> Sản phẩm yêu thích
                        </a>

                        <a href="<?= $baseURL ?>/controllers/lichSuDonHangController.php">
                            <i class="fa-solid fa-clipboard-list"></i> Lịch sử giao dịch
                        </a>

                        <a href="<?= $baseURL ?>/seller/controllers/sellerSanPhamController.php" style="color: #fd7e14;">
                            <i class="fa-solid fa-store" style="color: #fd7e14;"></i> Kênh Người Bán
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="<?= $baseURL ?>/controllers/dangXuatController.php" class="logout-link">
                            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <a href="<?= $baseURL ?>/controllers/dangNhapController.php" style="text-decoration: none; color: var(--bs-pink-600); font-weight: bold; font-size: 16px;">
                    <i class="fa-solid fa-user"></i> Đăng nhập
                </a>
            <?php endif; ?>

        </div>
    </div>
</div>
<script src="<?= $baseURL ?>/assets/js/liveSearch.js"></script>