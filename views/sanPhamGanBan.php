<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/sanPham.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <title>Sản phẩm gần bạn - TwoHand</title>
</head>

<body style="background-color: #f5f5f5;">
   

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-danger"><i class="fa-solid fa-location-dot"></i> TẤT CẢ SẢN PHẨM GẦN BẠN</h3>
            <a href="trangChuController.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
        </div>

        <?php if (empty($danhSachSanPham)): ?>
            <div class="alert alert-warning text-center shadow-sm py-5">
                <i class="fa-regular fa-face-frown fa-3x mb-3 text-muted"></i>
                <h5>Rất tiếc!</h5>
                <p>Không tìm thấy sản phẩm nào trong bán kính 10km quanh bạn.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($danhSachSanPham as $sp): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="chiTietSanPhamController.php?id=<?= $sp['MaHH'] ?>" class="product-link text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm border-0 product-item" style="border-radius: 8px; overflow: hidden; transition: transform 0.2s;">
                                <img src="../<?= htmlspecialchars($sp['HinhAnh']) ?>" class="card-img-top" alt="<?= htmlspecialchars($sp['TenHH']) ?>" style="height: 180px; width: 100%; object-fit: cover;">
                                
                                <div class="card-body p-2 p-md-3 d-flex flex-column">
                                    <h6 class="card-title text-truncate mb-1" title="<?= htmlspecialchars($sp['TenHH']) ?>">
                                        <?= htmlspecialchars($sp['TenHH']) ?>
                                    </h6>
                                    
                                    <small class="text-success mb-2">
                                        <i class="fa-solid fa-location-arrow"></i> Cách <?= $sp['KhoangCachKm'] ?> km
                                    </small>
                                    
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="text-danger fw-bold"><?= $sp['GiaFormat'] ?></span>
                                        <div class="text-warning small"><i class="fa-solid fa-star"></i> 5.0</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.querySelectorAll('.product-item').forEach(item => {
            item.addEventListener('mouseenter', () => item.style.transform = 'translateY(-5px)');
            item.addEventListener('mouseleave', () => item.style.transform = 'translateY(0)');
        });
    </script>
</body>
</html>