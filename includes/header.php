<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="dau-trang">
    <div class="header-container">
        <!-- Logo -->
        <a href="../controllers/trangChuController.php">
            <div class="logo-container">
                <img src="../assets/images/source/logo.png" alt="logo" id="logo">
            </div>
        </a>
        <!-- Thanh tìm kiếm -->
        <div class="head-mid">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon" style="color: pink;"></i>
                <input type="search" id="search" placeholder="Tìm kiếm sản phẩm" autocomplete="off">
                <div id="live-search-result"></div>
            </div>
        </div>
        <!-- Menu điều hướng -->
        <nav class="header-right">
            <ul class="nav-menu">
                <li class="trangchu"><a href="/DoAn2/controllers/trangChuController.php"><i class="fa-solid fa-house"></i>Trang chủ</a></li>
                <li class="danhsachsanpham"><a href="/DoAn2/controllers/danhSachSanPhamController.php"><i class="fa-solid fa-cart-shopping"></i> Sản phẩm</a></li>
                <li class="giohang"><a href="/DoAn2/controllers/gioHangController.php"><i class="fa-solid fa-bag-shopping"></i> Giỏ hàng</a></li>
                <li class="lichsu"><a href="/DoAn2/controllers/lichSuDonHangController.php"><i class="fa-solid fa-box"></i> Lịch sử giao dịch</a></li>
                <li class="dangnhap" style="display:none;"><a href="/DoAn2/controllers/dangNhapController.php"><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</a></li>
                <li class="dangky" style="display:none;"><a href="/DoAn2/controllers/dangKyController.php"><i class="fa-solid fa-user-plus"></i> Đăng ký</a></li>
                <li class="dangxuat" style="display:none;"><a href="/DoAn2/controllers/dangXuatController.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                <?php if (isset($_SESSION['IdTaiKhoan'])): ?>
                    <li class="taikhoan">
                        <a href="/DoAn2/controllers/thongTinTaiKhoanController.php">
                            <i class="fa-solid fa-user"></i>
                            <?php echo htmlspecialchars($_SESSION['TenTK']); ?>
                        </a>
                    </li>
                    <li class="dangxuat">
                        <a href="/DoAn2/controllers/dangXuatController.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                    </li>
                <?php else: ?>
                    <li class="taikhoan"><a href="/DoAn2/controllers/thongTinTaiKhoanController.php"><i class="fa-solid fa-user"></i> Tài khoản</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
<script src="../assets/js/liveSearch.js"></script>