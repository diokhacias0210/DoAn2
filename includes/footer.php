<?php
// Kiểm tra nếu biến $baseURL chưa được khai báo (phòng trường hợp trang nào đó không gọi header)
if (!isset($baseURL)) {
    $baseURL = '/DoAn2';
}
?>

<link href="<?= $baseURL ?>/assets/css/footer.css" rel="stylesheet">

<footer>
    <div class="footer-section" style="text-align: center;">
        <div class="footer-info" style="margin-bottom: 10px;">
            <a href="mailto:contact@websecondhand.com" style="text-decoration: none; color: inherit;">
                <i class="fa-solid fa-envelope"></i> contact@websecondhand.com
            </a>
            <span style="margin: 0 10px;"><i class="fa-solid fa-phone"></i> 0123 456 789</span>
            <span><i class="fa-solid fa-location-dot"></i> 123 Đường ABC, Quận 1, TP. Cần Thơ</span>
        </div>
        <p style="margin: 0;">Bản quyền © 2023 WEB SECONDHAND. Đã đăng ký bản quyền.</p>
    </div>
</footer>

<button id="btnUp" type="button"><i class="fa-solid fa-circle-up fa-bounce"></i></button>