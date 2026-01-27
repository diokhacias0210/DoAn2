<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/dangNhap.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <title>Đăng Ký</title>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="than-trai">
            <div class="cauchao">
                <h1 class="animated-title">
                    <span style="animation-delay: 0s">Welcome</span>
                    <span style="animation-delay: 0.2s">to</span>
                    <span style="animation-delay: 0.4s">TWO</span>
                    <span style="animation-delay: 0.6s">HAND</span>
                    <span style="animation-delay: 0.8s">STORE</span>
                    <span style="animation-delay: 1s">!!!</span>
                </h1>
                <p><i class="fa-solid fa-check"></i> Nơi bạn có thể tìm thấy những món đồ cũ như mới.</p>
                <p><i class="fa-solid fa-check"></i> Đảm bảo giao dịch an toàn.</p>
                <p><i class="fa-solid fa-check"></i> Cam kết hoàn tiền nếu sản phẩm không giống như mô tả.</p>
                <p><i class="fa-solid fa-check"></i> Đồ cũ, giá tốt, chất như đồ mới.</p>
            </div>
            <div class="quote">
                <blockquote>"Kho báu đồ cũ - Giá trị không cũ.”</blockquote>
                <blockquote>“Biến điều cũ kỹ thành điều có giá trị.”</blockquote>
            </div>
        </div>

        <div class="than-phai">
            <form method="POST" action="dangKyController.php">
                <div class="bieu-mau-tren">
                    <div class="bieu-mau" id="registerForm">
                        <h2>| Đăng Ký</h2>

                        <?php if (!empty($success)): ?>
                            <div class="alert-box success show">
                                <?= htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <div class="bieu-mau-noi">
                            <input type="text" id="regName" name="tentk" placeholder=" " required value="<?= htmlspecialchars($old['tentk'] ?? '') ?>">
                            <label for="regName">Họ và tên:</label>
                        </div>

                        <div class="bieu-mau-noi">
                            <input type="email" id="regEmail" name="email" placeholder=" " required value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                            <label for="regEmail">Email:</label>

                        </div>

                        <div class="bieu-mau-noi">
                            <input type="text" id="regPhone" name="phone" placeholder=" " required value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                            <label for="regPhone">Điện thoại:</label>

                        </div>

                        <div class="bieu-mau-noi">
                            <input type="password" id="regPassword" name="password" placeholder=" " autocomplete="off" required>
                            <label for="regPassword">Mật khẩu:</label>

                        </div>

                        <div class="bieu-mau-noi">
                            <input type="password" id="regConfirm" name="confirm_Password" placeholder=" " required>
                            <label for="regConfirm">Nhập lại mật khẩu:</label>

                        </div>
                        <?php if (!empty($errors)): ?>
                            <div class="global-error-container show">
                                <?php foreach ($errors as $error): ?>
                                    <p><i class="fa-solid fa-circle-exclamation"></i> <?= $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bieu-mau-duoi">
                    <button type="submit" class="submit">Đăng ký</button>
                    <div class="chuyen-toi-dang-nhap">
                        Đã có tài khoản?<a href="dangNhapController.php"> <u>Đăng nhập</u></a>
                    </div>
                    <div style="display:flex; justify-content: center; margin: 24px 0">Hoặc tiếp tục với Google</div>
                    <hr>
                    <div class="dang-nhap-google">
                        <a href="../project/public/google_login.php" style="text-decoration: none;">
                            <button type="button" class="gsi-material-button">
                                <div class="gsi-material-button-state"></div>
                                <div class="gsi-material-button-content-wrapper">
                                    <div class="gsi-material-button-icon">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                                            <path fill="none" d="M0 0h48v48H0z"></path>
                                        </svg>
                                    </div>
                                    <span class="gsi-material-button-contents">Đăng nhập bằng Google</span>
                                    <span style="display: none;">Đăng nhập bằng Google</span>
                                </div>
                            </button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chọn container lỗi tổng hợp và alert box (nếu có)
            const messages = document.querySelectorAll('.global-error-container.show, .alert-box.show');

            if (messages.length > 0) {
                setTimeout(() => {
                    messages.forEach(msg => {
                        msg.classList.remove('show');

                        // Xóa khỏi DOM hoặc ẩn display sau khi hiệu ứng mờ kết thúc
                        setTimeout(() => {
                            msg.style.display = 'none';
                        }, 500);
                    });
                }, 2000);
            }
        });
    </script>
    <script src="../assets/js/js.js"></script>
</body>

</html>