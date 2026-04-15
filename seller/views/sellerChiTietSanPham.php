<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm - Kênh Người Bán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/chiTietSanPham.css" rel="stylesheet">

    <style>
        .seller-detail-box { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .product-image-main { max-height: 420px; object-fit: contain; border: 1px solid #eee; border-radius: 8px; }
        .thumbnail { cursor: pointer; border: 2px solid transparent; transition: 0.3s; }
        .thumbnail.active { border-color: var(--bs-pink-500); }
        .star { color: #ffc107; }
        .review-card { border-left: 4px solid var(--bs-pink-500); }
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
        /* Phần mô tả sản phẩm */
        .product-description {
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #e0e0e0;
            background: #fafafa;
        }

        .product-description img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 12px 0;
        }

        .product-description p {
            margin-bottom: 1.2em;
        }

        .product-description ul, .product-description ol {
            padding-left: 1.5em;
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
        <h3 class="mb-4 text-secondary text-center">
            <i class="fa-solid fa-box-open"></i> CHI TIẾT SẢN PHẨM
        </h3>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="seller-sidebar">
                    <a href="sellerThongTinController.php"><i class="fa-solid fa-circle-info"></i> Thông tin cửa hàng</a>
                    <a href="sellerSanPhamController.php" class="active"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
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
                <div class="seller-detail-box">
                    <div class="row">
                        <!-- Hình ảnh -->
                        <div class="col-md-5">
                            <img id="mainImage" src="../../<?= $dsAnh[0]['URL'] ?? 'assets/images/placeholder.png' ?>" 
                                 class="product-image-main w-100 mb-3" alt="<?= htmlspecialchars($sanPham['TenHH']) ?>">

                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach ($dsAnh as $index => $anh): ?>
                                    <img src="../../<?= $anh['URL'] ?>" class="thumbnail rounded <?= $index === 0 ? 'active' : '' ?>" 
                                         style="width: 70px; height: 70px; object-fit: cover;" 
                                         onclick="changeImage(this, '../../<?= $anh['URL'] ?>')">
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div class="col-md-7">
                            <h4 class="fw-bold"><?= htmlspecialchars($sanPham['TenHH']) ?></h4>
                            <div class="mb-3">
                                <span class="badge bg-success fs-6"><?= number_format($sanPham['Gia'], 0, ',', '.') ?> đ</span>
                                <?php if ($sanPham['GiaThiTruong'] > 0): ?>
                                    <span class="text-muted text-decoration-line-through ms-2"><?= number_format($sanPham['GiaThiTruong'], 0, ',', '.') ?> đ</span>
                                <?php endif; ?>
                            </div>

                            <p><strong>Kho:</strong> <?= $sanPham['SoLuongHH'] ?> sản phẩm</p>
                            <p><strong>Đã bán:</strong> <?= $sanPham['DaBan'] ?? 0 ?> sản phẩm</p>

                            <!-- Đánh giá trung bình -->
                            <div class="mb-4">
                                <h6>Đánh giá trung bình</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="mb-0 fw-bold text-warning"><?= $trungBinhSao ?></h3>
                                    <div>
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="fa-solid fa-star <?= $i <= $trungBinhSao ? 'star' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-muted">(<?= count($danhGiaList) ?> đánh giá)</span>
                                </div>
                            </div>

                            <p>
                                <strong>Danh mục:</strong> 
                                <small class="text-muted">
                                    <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($sanPham['TenDM'] ?? 'Chưa phân loại') ?>
                                </small>
                            </p>
                            <p>
                                <strong>Chất lượng:</strong> 
                                <small class="text-muted">
                                    <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($sanPham['ChatLuongHang']) ?>
                                </small>
                            </p>
                            <p>
                                <strong></strong>Tình trạng:</strong> 
                                <small class="text-muted">
                                    <i class="fa-solid fa-info-circle"></i> <?= htmlspecialchars($sanPham['TinhTrangHang']) ?>
                                </small>
                            </p>
                            
                            <h5 class="fw-bold mb-3"><i class="fa-solid fa-file-lines"></i> Mô tả chi tiết sản phẩm</h5>
                            <div class="product-description border rounded p-4 bg-light" style="line-height: 1.8; font-size: 15.5px;">
                                <?= $sanPham['MoTa'] ?? '<em class="text-muted">Chưa có mô tả</em>' ?>
                            </div>

                            <a href="sellerSanPhamController.php" class="btn btn-outline-secondary mt-3">← Quay lại danh sách</a>
                        </div>
                    </div>

                    <!-- ==================== PHẦN ĐÁNH GIÁ & BÌNH LUẬN ==================== -->
                    <hr class="my-5">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-star"></i> ĐÁNH GIÁ TỪ KHÁCH HÀNG</h5>

                    <?php if (empty($danhGiaList)): ?>
                        <p class="text-muted text-center py-4">Chưa có đánh giá nào cho sản phẩm này.</p>
                    <?php else: ?>
                        <?php foreach ($danhGiaList as $dg): ?>
                            <div class="review-card p-3 mb-3 bg-light rounded">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?= htmlspecialchars($dg['TenTK']) ?></strong>
                                        <div>
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="fa-solid fa-star <?= $i <= $dg['SoSao'] ? 'star' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($dg['NgayDG'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Bình luận -->
                    <h6 class="mt-5 mb-3">Bình luận từ khách hàng</h6>
                    <?php if (empty($binhLuanList)): ?>
                        <p class="text-muted">Chưa có bình luận nào.</p>
                    <?php else: ?>
                        <?php foreach ($binhLuanList as $bl): ?>
                            <div class="border-start border-pink ps-3 mb-3">
                                <strong><?= htmlspecialchars($bl['TenTK']) ?></strong> 
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($bl['NgayBL'])) ?></small>
                                <p class="mb-0"><?= htmlspecialchars($bl['NoiDung']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
        function changeImage(thumb, src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail').forEach(el => el.classList.remove('active'));
            thumb.classList.add('active');
        }
    </script>
</body>
</html>