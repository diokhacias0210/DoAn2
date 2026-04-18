<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/danhSachSanPham.css" rel="stylesheet">
    <link href="../assets/css/sanPham.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">

    <style>
        .horizontal-scroll-wrapper {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 10px 5px 20px 5px;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
        }

        .horizontal-scroll-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .horizontal-scroll-wrapper::-webkit-scrollbar-thumb {
            background-color: var(--bs-pink-200);
            border-radius: 10px;
        }

        .horizontal-scroll-wrapper::-webkit-scrollbar-track {
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .horizontal-scroll-wrapper .product-link {
            flex: 0 0 auto;
            width: 280px;
            scroll-snap-align: start;
            text-decoration: none;
        }

        .badge-match {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 20;
            background-color: #e91e63;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .layout-2-cot {
            display: flex;
            gap: 30px;
            margin-top: 10px;
            align-items: flex-start;
        }

        .cot-trai-filter {
            width: 280px;
            flex-shrink: 0;
            background-color: var(--bs-pink-50);
            border: 1px solid var(--bs-pink-200);
            border-radius: 12px;
            padding: 20px;
            position: sticky;
            top: 100px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            z-index: 10;
        }

        .cot-phai-products {
            flex-grow: 1;
            min-width: 0;
        }

        .cot-phai-products .loai-san-pham {
            display: grid;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 20px;
        }

        .btn-pink {
            background-color: var(--bs-pink-500);
            color: white;
            border: none;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .layout-2-cot {
                flex-direction: column;
            }

            .cot-trai-filter {
                width: 100%;
                position: relative;
                top: 0;
            }

            .cot-phai-products .loai-san-pham {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="san-pham-chinh">
            <div class="goi-y-section pt-3">
                <div class="tieu-de-san-pham">
                    <h2><i class="fa-solid fa-minus"></i> CÓ THỂ BẠN SẼ THÍCH</h2>
                </div>

                <div class="horizontal-scroll-wrapper mt-3">
                    <?php
                    $hasRecommendations = false;
                    $idCheck = isset($_SESSION['IdTaiKhoan']) ? $_SESSION['IdTaiKhoan'] : 0;

                    if ($idCheck > 0) {
                        $api_url = "http://127.0.0.1:5000/recommend?user_id=$idCheck&top_n=8";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $api_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                        $response = curl_exec($ch);
                        curl_close($ch);

                        if ($response) {
                            $goi_y_list = json_decode($response, true);
                            if (!empty($goi_y_list) && !isset($goi_y_list['error'])) {
                                $hasRecommendations = true;
                                $ids = array_column($goi_y_list, 'id');
                                $ids_string = implode(',', $ids);

                                $sql_ai = "SELECT hh.*, (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) as Anh 
                                           FROM HangHoa hh 
                                           WHERE MaHH IN ($ids_string) 
                                           AND IdNguoiBan != $idCheck 
                                           ORDER BY FIELD(MaHH, $ids_string)";
                                $result_ai = $conn->query($sql_ai);

                                $sanphams = [];
                                while ($row = $result_ai->fetch_assoc()) {
                                    $sanphams[$row['MaHH']] = $row;
                                }

                                foreach ($goi_y_list as $item) {
                                    $sp = $sanphams[$item['id']] ?? null;
                                    if (!$sp) continue;

                                    if (isset($item['reason']) && $item['reason'] == 'Trending') {
                                        $badgeHtml = '<div class="badge-match" style="background:#fd7e14;"><i class="fa-solid fa-fire"></i> Đang thịnh hành</div>';
                                    } else {
                                        $badgeHtml = '<div class="badge-match">Phù hợp ' . $item['match'] . '%</div>';
                                    }

                                    $maHH = $sp['MaHH'];
                                    $tenHH = htmlspecialchars($sp['TenHH']);
                                    $anh = $sp['Anh'] ?? 'assets/images/placeholder.png';
                                    $imgSrc = (strpos($anh, 'http') === 0) ? $anh : '../' . $anh;
                                    $rating = isset($sp['Rating']) ? number_format((float)$sp['Rating'], 1) : "0.0";
                                    $gia = number_format($sp['Gia'], 0, ',', '.');

                                    if (!empty($sp['GiaTri']) && $sp['GiaTri'] > 0) {
                                        $giaGiamVal = $sp['Gia'] - ($sp['Gia'] * ($sp['GiaTri'] / 100));
                                        $giaGiam = number_format($giaGiamVal, 0, ',', '.');
                                        $giaHienThi = "<span class='gia-goc'>{$gia} đ</span><span class='gia-giam'>{$giaGiam} đ</span>";
                                    } else {
                                        $giaHienThi = "<span class='gia-giam'>{$gia} đ</span>";
                                    }
                    ?>
                                    <a href="chiTietSanPhamController.php?id=<?= $maHH ?>" class="product-link">
                                        <div class="product-item">
                                            <?= $badgeHtml ?>
                                            <div class="product-item-top">
                                                <img src="<?= $imgSrc ?>" style="height: 180px; width: 100%; object-fit:cover; border-radius: 8px 8px 0 0;">
                                                <div class="tieude-sanpham"><?= $tenHH ?></div>
                                            </div>
                                            <div class="product-item-bottom">
                                                <div class="gia-rating">
                                                    <div class="rating"><i class="fa-solid fa-star"></i><span><?= $rating ?></span></div>
                                                    <div class="gia-san-pham"><?= $giaHienThi ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                            <?php
                                }
                            }
                        }
                    }

                    if (!$hasRecommendations) {
                        $sql_new = "SELECT hh.*, (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) as Anh 
                                    FROM HangHoa hh 
                                    WHERE TrangThaiDuyet = 'DaDuyet' AND SoLuongHH > 0 AND IdNguoiBan != $idCheck 
                                    ORDER BY NgayThem DESC LIMIT 8";
                        $result_new = $conn->query($sql_new);
                        while ($sp = $result_new->fetch_assoc()) {
                            $maHH = $sp['MaHH'];
                            $tenHH = htmlspecialchars($sp['TenHH']);
                            $anh = $sp['Anh'] ?? 'assets/images/placeholder.png';
                            $imgSrc = (strpos($anh, 'http') === 0) ? $anh : '../' . $anh;
                            $gia = number_format($sp['Gia'], 0, ',', '.');
                            ?>
                            <a href="chiTietSanPhamController.php?id=<?= $maHH ?>" class="product-link">
                                <div class="product-item">
                                    <div class="badge-match" style="background:#28a745;">Mới nhất</div>
                                    <div class="product-item-top">
                                        <img src="<?= $imgSrc ?>" style="height: 180px; width: 100%; object-fit:cover; border-radius: 8px 8px 0 0;">
                                        <div class="tieude-sanpham"><?= $tenHH ?></div>
                                    </div>
                                    <div class="product-item-bottom">
                                        <div class="gia-rating">
                                            <div class="rating"><i class="fa-solid fa-star"></i><span>0.0</span></div>
                                            <div class="gia-san-pham"><span class="gia-giam"><?= $gia ?> đ</span></div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="tieu-de-san-pham mt-4">
                <h2><i class="fa-solid fa-minus"></i> TẤT CẢ SẢN PHẨM</h2>
            </div>

            <div class="layout-2-cot">
                <div class="cot-trai-filter shadow-sm">
                    <h5 class="fw-bold mb-4 border-bottom pb-2" style="color: var(--bs-pink-600);">
                        <i class="fa-solid fa-filter"></i> Bộ Lọc Tìm Kiếm
                    </h5>
                    <form id="search-form">
                        <div class="mb-3">
                            <label>Từ khóa</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Nhập tên sản phẩm...">
                        </div>
                        <div class="mb-3">
                            <label>Danh mục</label>
                            <select id="category" class="form-select">
                                <option value="">- Tất cả -</option>
                                <?php if (!empty($danhSachDanhMuc)): ?>
                                    <?php foreach ($danhSachDanhMuc as $dm): ?>
                                        <option value="<?php echo $dm['MaDM']; ?>"><?php echo htmlspecialchars($dm['TenDM']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Sắp xếp theo</label>
                            <select id="sort" class="form-select">
                                <option value="new">Mới nhất</option>
                                <option value="old">Cũ nhất</option>
                                <option value="az">Từ A đến Z</option>
                                <option value="za">Từ Z đến A</option>
                                <option value="giacao">Giá cao xuống thấp</option>
                                <option value="giathap">Giá thấp đến cao</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label>Khoảng giá (VNĐ)</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="number" class="form-control px-2" id="minPrice" placeholder="Từ..." style="font-size: 13px;">
                                <span class="text-muted">-</span>
                                <input type="number" class="form-control px-2" id="maxPrice" placeholder="Đến..." style="font-size: 13px;">
                            </div>
                        </div>
                        <button class="btn btn-pink w-100 py-2 rounded-pill" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i> Áp Dụng
                        </button>
                    </form>
                </div>

                <div class="cot-phai-products">
                    <div class="loai-san-pham" id="product-list"></div>
                    <div class="trang-thai-san-pham mt-4">
                        <div id="no-products" style="display:none; text-align: center; margin: 40px 0;">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted fs-5">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
                        </div>
                        <div id="load-more-container" class="nut-xem-them-san-pham">
                            <button type="button" id="nut-xem-them">Tải thêm sản phẩm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/loadLocSanPham.js"></script>
    <script src="../assets/js/js.js"></script>
    <script src="../assets/js/liveSearch.js"></script>
</body>

</html>