<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Kênh Người Bán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css/kichHoatBanHang.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
</head>
<body>

    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid giua-trang">
        <div class="form-dang-ky">
            <div class="text-center mb-4">
                <i class="fa-solid fa-store icon-header"></i>
                <h3 class="title-header">ĐĂNG KÝ NGƯỜI BÁN</h3>
                <p class="text-muted">Cung cấp thông tin để bắt đầu bán hàng ngay hôm nay</p>
            </div>
            
            <form action="../controllers/banHangController.php" method="POST">
                <input type="hidden" name="action" value="kich_hoat">
                
                <div class="section-title">1. Thông tin cửa hàng</div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên cửa hàng <span class="text-danger">*</span></label>
                    <input type="text" name="TenCuaHang" class="form-control" required placeholder="Nhập tên cửa hàng của bạn">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Số CCCD / CMND <span class="text-danger">*</span></label>
                    <input type="text" name="SoCCCD" class="form-control" placeholder="Nhập đúng số CCCD/CMND của bạn">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ kho lấy/trả hàng <span class="text-danger">*</span></label>
                    <input type="text" name="DiaChiKhoHang" class="form-control" required placeholder="Nhập địa chỉ nhà hoặc kho hàng của bạn">
                </div>

                <div class="section-title">2. Thông tin thanh toán (Nhận tiền)</div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên ngân hàng <span class="text-danger">*</span></label>
                    <input type="text" name="TenNganHang" class="form-control" required placeholder="Ví dụ: Vietcombank, MB Bank, Agribank...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Số tài khoản <span class="text-danger">*</span></label>
                    <input type="text" name="SoTaiKhoanNganHang" class="form-control" required placeholder="Nhập số tài khoản ngân hàng">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Tên chủ tài khoản <span class="text-danger">*</span></label>
                    <input type="text" name="TenChuTaiKhoan" class="form-control" required placeholder="VIẾT HOA KHÔNG DẤU (VD: NGUYEN VAN A)">
                </div>

                <div class="d-grid gap-3 mt-5">
                    <button type="submit" class="btn btn-xac-nhan btn-lg">
                        Hoàn tất đăng ký
                    </button>
                    <a href="../controllers/thongTinTaiKhoanController.php" class="btn btn-huy btn-lg">Hủy và quay lại</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>