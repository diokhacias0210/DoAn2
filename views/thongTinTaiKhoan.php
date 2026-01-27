<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/thongTinTaiKhoan.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="container">
            <div class="side">
                <nav class="menu">
                    <ul>
                        <li class="active"><a href="thongTinTaiKhoanController.php" style="text-decoration:none; color:inherit;"><i class='bx bx-user'></i> Thông tin tài khoản</a></li>
                        <li><a href="lichSuDonHangController.php"><i class='bx bx-package'></i> Lịch sử giao dịch</a></li>
                        <li><a href="gioHangController.php"><i class='bx bx-cart'></i> Giỏ Hàng</a></li>
                        <li><a href="danhSachYeuThichController.php"><i class='bx bx-heart'></i> Sản phẩm yêu thích</a></li>
                        <li class="dangxuat"><a href="dangXuatController.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                    </ul>
                </nav>
            </div>

            <main class="content">
                <h1><i class='bx bx-user'></i> TÀI KHOẢN CỦA TÔI</h1>

                <div id="alert-container">
                    <?php if (!empty($message)) echo $message; ?>
                </div>

                <div class="form-thongtintaikhoan">
                    <div class="form-group">
                        <label><i class='bx bx-id-card'></i> Họ và Tên</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['TenTK'] ?? ''); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label><i class='bx bxs-phone'></i> Số điện thoại</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" value="<?php echo htmlspecialchars($user['Sdt'] ?? 'Chưa cập nhật'); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class='bx bxs-envelope'></i> Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label><i class='bx bx-home'></i> Địa chỉ</label>
                        <select id="diachi-select" class="form-select">
                            <?php if (empty($addresses)): ?>
                                <option>Bạn chưa có địa chỉ nào.</option>
                            <?php else: ?>
                                <?php foreach ($addresses as $addr): ?>
                                    <option value="<?php echo $addr['MaDC']; ?>" <?php echo $addr['MacDinh'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($addr['DiaChiChiTiet']); ?>
                                        <?php echo $addr['MacDinh'] ? ' (Mặc định)' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="address-buttons">
                        <?php if ($address_count < 5): ?>
                            <button type="button" id="btn-them-diachi" class="add-address">+ Thêm địa chỉ</button>
                        <?php else: ?>
                            <p class="text-muted">Bạn đã đạt số lượng địa chỉ tối đa.</p>
                        <?php endif; ?>

                        <?php if ($address_count > 0): ?>
                            <button type="button" id="btn-xoa-diachi" class="delete-address">Xóa địa chỉ</button>
                        <?php endif; ?>
                    </div>

                    <form id="form-them-diachi" method="POST" action="thongTinTaiKhoanController.php" style="display:none; margin-top:15px;">
                        <input type="hidden" name="action" value="them_diachi">
                        <div class="form-group">
                            <input type="text" name="diachi_moi" id="diachi-moi" class="form-control" placeholder="Nhập địa chỉ mới của bạn" required>
                        </div>
                        <button type="submit" id="luu-diachi" class="luudiachi">Lưu địa chỉ</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // thêm xoá địa chỉ
            const btnThem = document.getElementById('btn-them-diachi');
            const formThem = document.getElementById('form-them-diachi');
            const btnXoa = document.getElementById('btn-xoa-diachi');
            const diachiSelect = document.getElementById('diachi-select');

            if (btnThem) {
                btnThem.addEventListener('click', function() {
                    // Toggle hiển thị form
                    if (formThem.style.display === 'none' || formThem.style.display === '') {
                        formThem.style.display = 'block';
                    } else {
                        formThem.style.display = 'none';
                    }
                });
            }

            if (btnXoa) {
                btnXoa.addEventListener('click', function() {
                    const confirmation = window.confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');

                    if (confirmation) {
                        const deleteForm = document.createElement('form');
                        deleteForm.method = 'POST';
                        deleteForm.action = 'thongTinTaiKhoanController.php';

                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'xoa_diachi';
                        deleteForm.appendChild(actionInput);

                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'MaDC_xoa';
                        idInput.value = diachiSelect.value;
                        deleteForm.appendChild(idInput);

                        document.body.appendChild(deleteForm);
                        deleteForm.submit();
                    }
                });
            }

            //thông báo
            const alertContainer = document.getElementById('alert-container');

            if (alertContainer && alertContainer.children.length > 0) {
                const alerts = alertContainer.querySelectorAll('.alert');

                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.classList.add('hide');

                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    }, 3000);
                });
            }
        });
    </script>
</body>

</html>