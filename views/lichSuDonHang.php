<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/lichSuGiaoDich.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <style>
        .order-timeline {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .order-timeline .timeline-item {
            margin-bottom: 5px;
        }

        .order-timeline .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-status {
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container my-5">
        <h1 class="mb-4"><i class="fas fa-history"></i> Lịch sử Giao Dịch</h1>

        <div id="alert-container">
            <?php if (!empty($message)) echo $message; ?>
        </div>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info text-center">Bạn chưa có đơn hàng nào.</div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card order-card shadow-sm">
                    <div class="card-header order-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <strong>Mã đơn hàng: #<?= $order['MaDH'] ?></strong>
                            <span class="ms-md-3 text-muted d-block d-md-inline">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['NgayDat'])) ?></span>
                            <?php if (!empty($order['MaThanhToan'])): ?>
                                <small class="payment-code">Mã TT: <?= htmlspecialchars($order['MaThanhToan']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="chiTietDonHangController.php?MaDH=<?= $order['MaDH'] ?>"
                                class="btn btn-sm btn-outline-primary action-btn">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['DiaChiGiao']) ?>
                        </div>

                        <!-- TRẠNG THÁI GIAO HÀNG -->
                        <div class="mb-3">
                            <strong><i class="fas fa-truck"></i> Tình trạng giao hàng:</strong>
                            <?php
                            $status_class = 'bg-secondary';
                            $status_text = htmlspecialchars($order['TrangThai']);
                            if ($order['TrangThai'] == 'Đã xác nhận') $status_class = 'bg-info text-dark';
                            elseif ($order['TrangThai'] == 'Đang giao') $status_class = 'bg-primary';
                            elseif ($order['TrangThai'] == 'Hoàn tất') $status_class = 'bg-success';
                            elseif ($order['TrangThai'] == 'Đã hủy') $status_class = 'bg-danger';
                            ?>
                            <span class="badge status-badge <?= $status_class ?> ms-2"><?= $status_text ?></span>
                        </div>

                        <!-- TRẠNG THÁI ĐƠN HÀNG (Lịch sử) -->
                        <?php if (!empty($order['lichsu'])): ?>
                            <div class="mb-3">
                                <strong><i class="fas fa-history"></i> Trạng thái đơn hàng:</strong>
                                <?php
                                $ls = $order['lichsu'][0]; // Lấy trạng thái mới nhất
                                $trangThaiClass = '';
                                if ($ls['TrangThai'] == 'Đã xác nhận') $trangThaiClass = 'text-info';
                                elseif ($ls['TrangThai'] == 'Đang giao') $trangThaiClass = 'text-primary';
                                elseif ($ls['TrangThai'] == 'Hoàn tất') $trangThaiClass = 'text-success';
                                elseif ($ls['TrangThai'] == 'Đã hủy') $trangThaiClass = 'text-danger';
                                ?>
                                <span class="badge bg-light text-dark ms-2">
                                    <strong class="<?= $trangThaiClass ?>"><?= htmlspecialchars($ls['TrangThai']) ?></strong>
                                </span>
                                <?php if (!empty($ls['GhiChu'])): ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- NÚT HỦY / XÓA -->
                        <div class="mb-3">
                            <?php
                            // Kiểm tra xem đã có lịch sử "Đã xác nhận" chưa
                            $daXacNhan = false;
                            if (!empty($order['lichsu'])) {
                                foreach ($order['lichsu'] as $ls) {
                                    if (
                                        $ls['TrangThai'] == 'Đã xác nhận' ||
                                        $ls['TrangThai'] == 'Đang giao' ||
                                        $ls['TrangThai'] == 'Hoàn tất'
                                    ) {
                                        $daXacNhan = true;
                                        break;
                                    }
                                }
                            }
                            ?>

                            <?php if ($order['TrangThai'] == 'Chờ xử lý' && !$daXacNhan): ?>
                                <button type="button"
                                    class="btn btn-sm btn-outline-warning"
                                    onclick="openCancelModal(<?= $order['MaDH'] ?>)">
                                    <i class="fas fa-times"></i> Hủy đơn
                                </button>
                            <?php elseif ($daXacNhan && ($order['TrangThai'] == 'Chờ xử lý' || $order['TrangThai'] == 'Đã xác nhận' || $order['TrangThai'] == 'Đang giao')): ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Đơn hàng đã được xác nhận, không thể hủy">
                                    <i class="fas fa-lock"></i> Không thể hủy
                                </button>
                            <?php endif; ?>

                            <?php if ($order['TrangThai'] == 'Đã hủy'): ?>
                                <a href="lichSuDonHangController.php?action=delete&MaDH=<?= $order['MaDH'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hành động này sẽ XÓA VĨNH VIỄN đơn hàng #<?= $order['MaDH'] ?> khỏi lịch sử. Bạn có chắc chắn?')">
                                    <i class="fas fa-trash-alt"></i> Xóa hẳn
                                </a>
                            <?php endif; ?>
                        </div>


                    </div>
                    <div class="card-footer text-end">
                        <strong>Tổng cộng: <span class="text-danger h5"><?= number_format($order['TongTien']) ?>đ</span></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="mt-4">
            <a href="thongTinTaiKhoanController.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại tài khoản</a>
        </div>
    </div>

    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="lichSuDonHangController.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác nhận hủy đơn hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="cancel">
                        <input type="hidden" name="MaDH" id="modalMaDH">

                        <p>Bạn đang yêu cầu hủy đơn hàng <strong id="displayMaDH"></strong>.</p>

                        <div class="mb-3">
                            <label class="form-label">Vui lòng chọn lý do hủy:</label>
                            <select class="form-select" name="lyDoSelect" id="lyDoSelect" required onchange="toggleLyDoKhac(this)">
                                <option value="">-- Chọn lý do --</option>
                                <option value="Thay đổi ý định">Thay đổi ý định</option>
                                <option value="Tìm thấy giá rẻ hơn">Tìm thấy giá rẻ hơn ở nơi khác</option>
                                <option value="Thời gian giao hàng quá lâu">Thời gian giao hàng quá lâu</option>
                                <option value="Đặt nhầm sản phẩm">Đặt nhầm sản phẩm</option>
                                <option value="Khác">Lý do khác...</option>
                            </select>
                        </div>

                        <div class="mb-3" id="lyDoKhacContainer" style="display: none;">
                            <label class="form-label">Nhập lý do của bạn:</label>
                            <textarea class="form-control" name="lyDoKhac" rows="3" placeholder="Chi tiết lý do..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/js.js"></script>
    <script>
        function openCancelModal(maDH) {
            document.getElementById('modalMaDH').value = maDH;
            document.getElementById('displayMaDH').innerText = '#' + maDH;
            var myModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            myModal.show();
        }

        function toggleLyDoKhac(selectObject) {
            var container = document.getElementById('lyDoKhacContainer');
            var textArea = container.querySelector('textarea');

            if (selectObject.value === 'Khác') {
                container.style.display = 'block';
                textArea.required = true;
            } else {
                container.style.display = 'none';
                textArea.required = false;
            }
        }

        // xử lý thông báo
        document.addEventListener('DOMContentLoaded', function() {
            // --- XỬ LÝ ẨN THÔNG BÁO TỰ ĐỘNG ---
            const alertContainer = document.getElementById('alert-container');
            if (alertContainer && alertContainer.children.length > 0) {
                // Sau 4 giây thì bắt đầu mờ dần
                setTimeout(() => {
                    const alerts = alertContainer.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-20px)'; // Bay lên nhẹ

                        // Sau khi mờ xong thì xóa khỏi DOM
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    });
                }, 4000);
            }
        });

        // ... (Giữ nguyên các hàm openCancelModal cũ của bạn) ...
        function openCancelModal(maDH) {
            document.getElementById('modalMaDH').value = maDH;
            document.getElementById('displayMaDH').innerText = '#' + maDH;
            var myModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            myModal.show();
        }

        function toggleLyDoKhac(selectObject) {
            var container = document.getElementById('lyDoKhacContainer');
            var textArea = container.querySelector('textarea');

            if (selectObject.value === 'Khác') {
                container.style.display = 'block';
                textArea.required = true;
            } else {
                container.style.display = 'none';
                textArea.required = false;
            }
        }
    </script>
</body>

</html>