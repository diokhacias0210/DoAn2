<?php
require_once '../../includes/ketnoi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$idUser = $_SESSION['IdTaiKhoan'];

// Lấy thông tin cửa hàng của user hiện tại
$sql = "SELECT TenCuaHang, SoCCCD, DiaChiKhoHang, TenNganHang, SoTaiKhoanNganHang, TenChuTaiKhoan 
        FROM HoSoNguoiBan 
        WHERE IdTaiKhoan = $idUser";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $thongTin = $result->fetch_assoc();
} else {
    $thongTin = null;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kênh người bán - Thông tin cửa hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        /* --- ĐỒNG BỘ KHUNG SƯỜN --- */
        .seller-wrapper {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .seller-content-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            min-height: 600px;
        }

        /* --- MENU BÊN TRÁI (SIDEBAR) --- */
        .seller-sidebar {
            background: #ffffff;
            border-radius: 10px;
            padding: 15px 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .seller-sidebar a {
            text-decoration: none;
            color: #555;
            font-weight: 600;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .seller-sidebar a:hover {
            background-color: #f8f9fa;
            color: var(--bs-pink-500);
        }

        .seller-sidebar a.active {
            background-color: var(--bs-pink-100);
            color: var(--bs-pink-600);
            border-left: 4px solid var(--bs-pink-600);
        }

        /* --- BADGE TIN NHẮN --- */
        .chat-badge {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background-color: #dc3545;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 50px;
            line-height: 1;
        }

        /* Đồng bộ thông tin */
        .info-row {
            border-bottom: 1px dashed #eee;
            padding: 15px 0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #666;
        }

        .info-value {
            color: #222;
            font-size: 1.1em;
        }
    </style>
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <?php
    // --- ĐẾM SỐ TIN NHẮN KHÁCH HÀNG CHƯA ĐỌC ---
    $soTinNhanChuaDoc = 0;
    if (isset($_SESSION['IdTaiKhoan']) && isset($conn)) {
        $idSellerCurrent = $_SESSION['IdTaiKhoan'];

        // Câu lệnh đếm các tin nhắn thuộc phòng chat của Seller này, 
        // do người khác gửi (Khách hàng) và có trạng thái DaDoc = 0
        $sqlDemTinNhan = "SELECT COUNT(tn.MaTN) AS SoLuong 
                        FROM TinNhan tn 
                        JOIN PhongChat p ON tn.MaPhong = p.MaPhong 
                        WHERE p.IdNguoiBan = $idSellerCurrent 
                        AND tn.IdNguoiGui != $idSellerCurrent 
                        AND tn.DaXem = 0";

        $rsDem = $conn->query($sqlDemTinNhan);
        if ($rsDem && $rsDem->num_rows > 0) {
            $rowDem = $rsDem->fetch_assoc();
            $soTinNhanChuaDoc = $rowDem['SoLuong'];
        }
    }
    ?>

    <div class="seller-wrapper mt-4 mb-5">
        <h3 class="mb-4 text-secondary text-center"><i class="fa-solid fa-shop"></i> KÊNH NGƯỜI BÁN</h3>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="seller-sidebar">
                    <a href="sellerThongTinController.php" class="active"><i class="fa-solid fa-circle-info"></i> Thông tin cửa hàng</a>
                    <a href="sellerSanPhamController.php"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
                    <a href="sellerDonHangController.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
                    <a href="sellerChatController.php">
                        <i class="fa-solid fa-comments"></i> Tin nhắn

                        <?php if (isset($soTinNhanChuaDoc) && $soTinNhanChuaDoc > 0): ?>
                            <span class="chat-badge"><?php echo $soTinNhanChuaDoc; ?></span>
                        <?php endif; ?>
                    </a>                    
                    <a href="sellerDoanhThuController.php"><i class="fa-solid fa-chart-line"></i> Doanh thu & Rút tiền</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="seller-content-box">
                    <h4 class="mb-4 text-center" style="color: var(--bs-pink-600);">Hồ Sơ Cửa Hàng</h4>

                    <?php if (isset($thongTin) && $thongTin): ?>
                        <div class="row info-row">
                            <div class="col-sm-4 info-label">Tên cửa hàng:</div>
                            <div class="col-sm-8 info-value text-uppercase fw-bold text-success"><?= htmlspecialchars($thongTin['TenCuaHang']) ?></div>
                        </div>
                        <div class="row info-row">
                            <div class="col-sm-4 info-label">Căn cước công dân:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($thongTin['SoCCCD']) ?></div>
                        </div>
                        <div class="row info-row">
                            <div class="col-sm-4 info-label">Địa chỉ lấy/trả hàng:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($thongTin['DiaChiKhoHang']) ?></div>
                        </div>
                        <div class="row info-row">
                            <div class="col-sm-4 info-label">Ngân hàng:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($thongTin['TenNganHang']) ?></div>
                        </div>
                        <div class="row info-row">
                            <div class="col-sm-4 info-label">Số tài khoản:</div>
                            <div class="col-sm-8 info-value fw-bold"><?= htmlspecialchars($thongTin['SoTaiKhoanNganHang']) ?></div>
                        </div>
                        <div class="row info-row">
                            <div class="col-sm-4 info-label">Chủ tài khoản:</div>
                            <div class="col-sm-8 info-value text-uppercase"><?= htmlspecialchars($thongTin['TenChuTaiKhoan']) ?></div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Không tìm thấy thông tin cửa hàng. Vui lòng thử lại.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="../../assets/js/js.js"></script>

</body>

</html>