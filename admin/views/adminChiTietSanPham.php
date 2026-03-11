<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - Admin</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .admin-container {
            padding: 30px;
            background: #f4f6f9;
            min-height: 100vh;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .img-main {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .img-thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .img-thumb:hover {
            border-color: #2ecc71;
        }

        .seller-box {
            background: #f8f9fa;
            border-left: 4px solid #0dcaf0;
            padding: 15px;
            border-radius: 5px;
        }

        .seller-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="m-0 text-success fw-bold"><i class="fa-solid fa-circle-info"></i> Thông tin chi tiết</h2>
                    <a href="adminSanPhamController.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="product-card">
                            <img src="../../<?= !empty($danhSachAnh) ? $danhSachAnh[0]['URL'] : 'assets/images/placeholder.png' ?>" id="mainImage" class="img-main mb-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach ($danhSachAnh as $anh): ?>
                                    <img src="../../<?= $anh['URL'] ?>" class="img-thumb" onclick="document.getElementById('mainImage').src=this.src">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="product-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-success mb-2"><?= htmlspecialchars($chiTiet['TenDM']) ?></span>
                                    <h3 class="fw-bold text-dark"><?= htmlspecialchars($chiTiet['TenHH']) ?></h3>
                                </div>
                                <div class="text-end">
                                    <?php if ($chiTiet['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                        <span class="badge bg-warning text-dark fs-6">⏳ Chờ duyệt</span>
                                    <?php elseif ($chiTiet['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                        <span class="badge bg-success fs-6">✅ Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger fs-6">❌ Bị từ chối</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <h2 class="text-danger fw-bold my-3"><?= number_format($chiTiet['Gia'], 0, ',', '.') ?>đ</h2>

                            <table class="table table-borderless mt-3">
                                <tr>
                                    <th style="width:150px;">Giá thị trường:</th>
                                    <td><?= $chiTiet['GiaThiTruong'] > 0 ? number_format($chiTiet['GiaThiTruong'], 0, ',', '.') . 'đ' : 'Không có' ?></td>
                                </tr>
                                <tr>
                                    <th>Kho hàng:</th>
                                    <td><?= $chiTiet['SoLuongHH'] ?> sản phẩm</td>
                                </tr>
                                <tr>
                                    <th>Chất lượng:</th>
                                    <td><?= $chiTiet['ChatLuongHang'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tình trạng bán:</th>
                                    <td><span class="badge bg-secondary"><?= $chiTiet['TinhTrangHang'] ?></span></td>
                                </tr>
                                <tr>
                                    <th>Ngày đăng:</th>
                                    <td><?= date('d/m/Y H:i', strtotime($chiTiet['NgayThem'])) ?></td>
                                </tr>
                            </table>

                            <hr>
                            <h5 class="fw-bold mt-3">Thông tin người bán</h5>
                            <div class="seller-box d-flex align-items-center gap-3">
                                <img src="../../<?= $chiTiet['Avatar'] ?? 'assets/images/placeholder.png' ?>" class="seller-avatar">
                                <div>
                                    <h5 class="m-0 fw-bold"><?= htmlspecialchars($chiTiet['NguoiBan']) ?></h5>
                                    <small class="text-muted"><i class="fa-solid fa-envelope"></i> <?= $chiTiet['Email'] ?> &nbsp;|&nbsp; <i class="fa-solid fa-phone"></i> <?= $chiTiet['Sdt'] ?></small>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex gap-2">
                                <?php if ($chiTiet['TrangThaiDuyet'] == 'ChoDuyet'): ?>
                                    <a href="adminSanPhamController.php?action=duyet&id=<?= $chiTiet['MaHH'] ?>" class="btn btn-success fw-bold"><i class="fa-solid fa-check"></i> Phê duyệt đăng bài</a>
                                    <a href="adminSanPhamController.php?action=tuchoi&id=<?= $chiTiet['MaHH'] ?>" class="btn btn-danger fw-bold" onclick="return confirm('Xác nhận từ chối?')"><i class="fa-solid fa-xmark"></i> Từ chối</a>
                                <?php elseif ($chiTiet['TrangThaiDuyet'] == 'DaDuyet'): ?>
                                    <?php if ($chiTiet['HienThi'] == 1): ?>
                                        <a href="adminSanPhamController.php?action=an&id=<?= $chiTiet['MaHH'] ?>" class="btn btn-secondary fw-bold"><i class="fa-solid fa-eye-slash"></i> Tạm ẩn khỏi trang chủ</a>
                                    <?php else: ?>
                                        <a href="adminSanPhamController.php?action=hien&id=<?= $chiTiet['MaHH'] ?>" class="btn btn-info text-white fw-bold"><i class="fa-solid fa-eye"></i> Mở lại hiển thị</a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <a href="adminSanPhamController.php?xoa=<?= $chiTiet['MaHH'] ?>" class="btn btn-outline-danger ms-auto" onclick="return confirm('Xóa vĩnh viễn?')"><i class="fas fa-trash"></i> Xóa SP</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-card mt-2">
                    <h5 class="fw-bold border-bottom pb-2">Mô tả chi tiết sản phẩm</h5>
                    <div class="mt-3" style="line-height: 1.8;">
                        <?= $chiTiet['MoTa'] ?>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>