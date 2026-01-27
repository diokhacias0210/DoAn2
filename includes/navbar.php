<?php
// Lấy tên file controller hiện tại
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="dashboardController.php" style="color:white; text-decoration:none;">
            <h2>Admin Panel</h2>
        </a>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboardController.php" class="sidebar-link <?= ($current_page == 'dashboardController.php') ? 'active' : '' ?>">
            📊 <span>Tổng quan</span>
        </a>

        <a href="adminSanPhamController.php" class="sidebar-link <?= ($current_page == 'adminSanPhamController.php') ? 'active' : '' ?>">📦 <span>Sản phẩm</span></a>

        <a href="adminDanhMucController.php" class="sidebar-link <?= ($current_page == 'adminDanhMucController.php') ? 'active' : '' ?>">📁 <span>Danh mục</span></a>
        <a href="adminMaGiamGiaController.php" class="sidebar-link <?= ($current_page == 'adminMaGiamGiaController.php') ? 'active' : '' ?>">🎟️ <span>Mã giảm giá</span></a>
        <a href="adminDonHangController.php" class="sidebar-link <?= ($current_page == 'adminDonHangController.php') ? 'active' : '' ?>">🛒 <span>Đơn hàng</span></a>

        <a href="adminNguoiDungController.php" class="sidebar-link <?= ($current_page == 'adminNguoiDungController.php') ? 'active' : '' ?>">👥 <span>Người dùng</span></a>

        <a href="adminDanhGiaController.php" class="sidebar-link <?= ($current_page == 'adminDanhGiaController.php') ? 'active' : '' ?>">✏️ <span>Đánh giá</span></a>



        <a href="../../controllers/dangXuatController.php" class="sidebar-link">🚪 <span>Đăng xuất</span></a>
    </nav>
</aside>