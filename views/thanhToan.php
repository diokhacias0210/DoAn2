<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/thanhToan.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <style>
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 600;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .required {
            color: red;
        }

        select#address-select,
        input#input-location {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .pd-cell img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            vertical-align: middle;
            margin-right: 8px;
        }

        input:invalid+.validation-message {
            display: block;
            color: red;
            font-size: 0.8em;
            margin-top: -5px;
            margin-bottom: 10px;
        }

        .validation-message {
            display: none;
        }

        .modal-body div input:focus {
            box-shadow: 0 0 0 .25rem var(--bs-pink-200);
            border-color: var(--bs-pink-200);
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="top-banner">
            <span>│ Thanh toán</span>
        </div>

        <div id="alert-message" style="display:none;"></div>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div id="session-alert">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        ?>

        <form id="checkout-form" method="POST" action="xuLyThanhToan.php">
            <div class="section">
                <div class="section-title">
                    <div><i class="fa-solid fa-location-dot" style="color: var(--bs-pink);"></i> Địa chỉ nhận hàng</div>
                </div>
                <div id="thongtin">
                    <div class="hoten">
                        <label for="input-name">Họ và tên: <span class="required">*</span></label>
                        <input type="text" id="input-name" name="TenNguoiNhan"
                            value="<?= htmlspecialchars($user['TenTK'] ?? '') ?>" required>
                    </div>
                    <div class="sodienthoai">
                        <label for="input-phone">Số điện thoại: <span class="required">*</span></label>
                        <input type="tel" id="input-phone" name="SdtNguoiNhan"
                            value="<?= htmlspecialchars($user['Sdt'] ?? '') ?>"
                            required>
                    </div>
                    <div class="diachi">
                        <label for="address-select">Địa chỉ: <span class="required">*</span></label>
                        <div style="display: flex; gap: 10px; flex: 1;">
                            <select id="address-select" name="DiaChiGiao" required style="flex: 1;">
                                <?php if (count($all_addresses) > 0): ?>
                                    <?php foreach ($all_addresses as $addr): ?>
                                        <option value="<?= htmlspecialchars($addr['DiaChiChiTiet']) ?>" <?= $addr['MacDinh'] == 1 ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($addr['DiaChiChiTiet']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>

                            <button type="button" class="nut-them-dia-chi" data-bs-toggle="modal" data-bs-target="#addAddressModal" style="white-space: nowrap;">
                                <i class="fa-solid fa-plus"></i> Thêm địa chỉ mới
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sanpham">
                <div class="section-title">
                    <div><i class="fa-solid fa-box-open" style="color: var(--bs-pink);"></i> Sản phẩm đã chọn (<?= count($cart_items) ?>)</div>
                </div>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Ảnh sản phẩm</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item):
                            $thanhTien = $item['Gia'] * $item['SoLuong'];
                            $imgSrc = !empty($item['AnhDaiDien']) ? '../' . $item['AnhDaiDien'] : '../assets/images/placeholder.png';

                        ?>

                            <tr>
                                <td class="img-cell">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($item['TenHH']) ?>">
                                </td>
                                <td>
                                    <div class="pd-cell">
                                        <span><?= htmlspecialchars($item['TenHH']) ?></span>
                                    </div>
                                </td>
                                <td><?= number_format($item['Gia'], 0, ',', '.') ?>đ</td>
                                <td><?= $item['SoLuong'] ?></td>
                                <td style="text-align: right;"><strong><?= number_format($thanhTien, 0, ',', '.') ?>đ</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row-flex">
                <div class="half-box">
                    <div class="half-box-left">
                        <h3><i class="fa-solid fa-pen" style="color: var(--bs-pink);"></i> Lưu ý cho người bán</h3>
                        <textarea class="luuy" name="GhiChu" placeholder="Ghi chú đơn hàng (không bắt buộc)"></textarea>
                    </div>
                    <div class="half-box-right">
                        <div class="thanhtoan">
                            <div class="tieude-thanhtoan">
                                <h3><i class="fa-solid fa-lock" style="color: var(--bs-pink);"></i> Phương thức thanh toán</h3>
                            </div>
                            <div class="phuongthucthanhtoan">
                                <label>
                                    <input type="radio" name="PhuongThucTT" value="Tiền mặt" checked>
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                                <label>
                                    <input type="radio" name="PhuongThucTT" value="Chuyển khoản">
                                    Chuyển khoản ngân hàng
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="total-box">
                <div>
                    <i class="fa-solid fa-dolly" style="color: var(--bs-pink);"></i>
                    Tổng tiền hàng:
                    <span><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
                </div>
                <div>
                    <i class="fa-solid fa-truck" style="color: var(--bs-pink);"></i>
                    Phí vận chuyển:
                    <span><?= number_format($phiVanChuyen, 0, ',', '.') ?>đ</span>
                </div>
                <div style="font-size: 1.2rem; font-weight: bold;">
                    <i class="fa-solid fa-sack-dollar" style="color: var(--bs-pink);"></i>
                    Tổng thanh toán:
                    <span style="color: #e74c3c;"><?= number_format($tongThanhToan, 0, ',', '.') ?>đ</span>
                </div>
                <div class="action-box">
                    <button type="button" onclick="window.location.href='gioHang.php'" class="back-btn">
                        ⬅️ Quay lại giỏ hàng
                    </button>
                    <button type="submit" class="order-btn" id="btn-dat-hang">
                        Đặt hàng
                    </button>
                </div>
            </div>
        </form>
    </div>
    <!-- form thêm địa chỉ -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="../controllers/thongTinTaiKhoanController.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm địa chỉ mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="them_diachi">
                        <input type="hidden" name="redirect" value="thanhtoan">

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ chi tiết:</label>
                            <input type="text" name="diachi_moi" class="form-control" autocomplete="off" placeholder="Số nhà, tên đường, phường/xã..." required>
                            <div class="form-text text-muted">Bạn có thể lưu tối đa 5 địa chỉ.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" style="background-color: var(--bs-pink-500); border:none;">Lưu địa chỉ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



    <?php include '../includes/footer.php'; ?>

    <script>
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!this.checkValidity()) {
                showAlert('Vui lòng kiểm tra lại các thông tin bắt buộc.', 'error');
                const firstInvalidField = this.querySelector(':invalid');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
                return;
            }

            const btn = document.getElementById('btn-dat-hang');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            const formData = new FormData(this);

            fetch('xuLyThanhToan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            throw new Error("Phản hồi không phải JSON: " + text);
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        showAlert('Đặt hàng thành công! Đang chuyển hướng về giỏ hàng...', 'success'); // Cập nhật thông báo
                        setTimeout(() => {
                            window.location.href = 'lichSuDonHangController.php'; // <<< SỬA LẠI ĐÍCH ĐẾN
                        }, 1500);
                    } else {
                        showAlert(data.message || 'Có lỗi xảy ra khi đặt hàng.', 'error');
                        btn.disabled = false;
                        btn.innerHTML = 'Đặt hàng';
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    showAlert('Có lỗi xảy ra trong quá trình xử lý. Vui lòng thử lại!', 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Đặt hàng';
                });
        });

        function showAlert(message, type = 'warning') {
            const alertDiv = document.getElementById('alert-message');
            alertDiv.className = '';
            alertDiv.classList.add('alert');
            if (type === 'success') {
                alertDiv.classList.add('alert-success');
            } else if (type === 'error') {
                alertDiv.classList.add('alert-danger');
            } else {
                alertDiv.classList.add('alert-warning');
            }
            alertDiv.textContent = message;
            alertDiv.style.display = 'block';
            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 5000);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        const sessionAlert = document.getElementById('session-alert');
        if (sessionAlert) {
            setTimeout(() => {
                sessionAlert.style.transition = 'opacity 0.5s ease';
                sessionAlert.style.opacity = '0';
                setTimeout(() => sessionAlert.remove(), 500);
            }, 5000);
        }
    </script>
</body>

</html>