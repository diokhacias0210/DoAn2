<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng - Kênh người bán</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: #f5f6fa;
        }

        .seller-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .status-select {
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .status-select.pending {
            color: orange;
            border-color: orange;
        }

        .status-select.confirmed {
            color: blue;
            border-color: blue;
        }

        .status-select.shipping {
            color: purple;
            border-color: purple;
        }

        .status-select.completed {
            color: green;
            border-color: green;
        }

        .status-select.cancelled {
            color: red;
            border-color: red;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">TWO HAND STORE (Seller)</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="sellerSanPhamController.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="sellerDonHangController.php">Đơn hàng</a>
                    </li>
                </ul>
            </div>
            <div class="ml-auto text-white">
                Hello, <?php echo $_SESSION['TenTK']; ?>
            </div>
        </div>
    </nav>

    <div class="seller-container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <div class="card p-4">
            <h3 class="mb-4">📋 Đơn hàng của bạn</h3>

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Ngày đặt</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền khách trả</th>
                        <th>Phí sàn (5%)</th>
                        <th>Thực nhận</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dsDonHang)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Chưa có đơn hàng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dsDonHang as $dh): ?>
                            <tr>
                                <td>#<?= $dh['MaDH'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($dh['NguoiMua']) ?></strong><br>
                                    <small><?= htmlspecialchars($dh['Sdt']) ?></small>
                                </td>
                                <td><?= number_format($dh['TongTien'], 0, ',', '.') ?>đ</td>
                                <td class="text-danger">-<?= number_format($dh['PhiSan'], 0, ',', '.') ?>đ</td>
                                <td class="text-success font-weight-bold"><?= number_format($dh['TienNguoiBanNhan'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="maDH" value="<?= $dh['MaDH'] ?>">
                                        <select name="trangThai" class="status-select" onchange="this.form.submit()">
                                            <option value="Chờ xử lý" <?= $dh['TrangThai'] == 'Chờ xử lý' ? 'selected' : '' ?>>⏳ Chờ xử lý</option>
                                            <option value="Đã xác nhận" <?= $dh['TrangThai'] == 'Đã xác nhận' ? 'selected' : '' ?>>✅ Đã xác nhận</option>
                                            <option value="Đang giao" <?= $dh['TrangThai'] == 'Đang giao' ? 'selected' : '' ?>>🚚 Đang giao</option>
                                            <option value="Hoàn tất" <?= $dh['TrangThai'] == 'Hoàn tất' ? 'selected' : '' ?>>🎉 Hoàn tất</option>
                                            <option value="Đã hủy" <?= $dh['TrangThai'] == 'Đã hủy' ? 'selected' : '' ?>>❌ Hủy đơn</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="xemChiTiet(<?= $dh['MaDH'] ?>)">
                                        Xem hàng
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalChiTiet" class="modal" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết sản phẩm đơn hàng #<span id="spanMaDH"></span></h5>
                    <button type="button" class="btn-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body" id="modalContent">
                    Đang tải...
                </div>
            </div>
        </div>
    </div>

    <!-- <script>
        function xemChiTiet(maDH) {
            document.getElementById('modalChiTiet').style.display = 'block';
            document.getElementById('spanMaDH').innerText = maDH;

            // Gọi AJAX lấy chi tiết (hoặc reload trang có kèm param view_id - cách đơn giản nhất cho đồ án)
            // Ở đây dùng cách đơn giản là fetch API tự chế
            // Hoặc bạn có thể dùng cách reload: location.href = '?view_id=' + maDH;

            // Cách dùng AJAX gọi về Controller hiện tại
            // Để đơn giản, tôi sẽ giả lập HTML render từ PHP luôn nếu bạn dùng reload trang
            // Nhưng để UX tốt hơn, ta dùng fetch nhẹ:

            // (Bạn cần viết thêm 1 case trong Controller để trả về JSON hoặc HTML cho AJAX này)
            // Tạm thời tôi sẽ hiển thị thông báo.

            // Để code chạy ngay không cần sửa controller nhiều, ta dùng logic JS render từ mảng PHP (nếu load hết từ đầu)
            // Hoặc đơn giản nhất: redirect sang trang chi tiết đơn hàng (tận dụng trang chi tiết đơn hàng cũ nhưng sửa lại quyền)

            alert("Bạn có thể tạo thêm file sellerChiTietDonHang.php để xem kỹ hơn!");
        }

        function closeModal() {
            document.getElementById('modalChiTiet').style.display = 'none';
        }
    </script> -->
</body>

</html>