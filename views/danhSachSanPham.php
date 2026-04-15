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
        /* TÙY CHỈNH CUỘN NGANG CHO GỢI Ý */
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
            /* Cố định chiều rộng thẻ trong cuộn ngang */
            scroll-snap-align: start;
        }

        /* Nút X bỏ qua (Custom để đè lên ảnh) */
        .btn-bo-qua {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 20;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.8);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            transition: 0.2s;
        }

        .btn-bo-qua:hover {
            background-color: #dc3545;
            color: #fff;
        }

        /* Nhãn % phù hợp */
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

        /* TÙY CHỈNH LAYOUT 2 CỘT */
        .layout-2-cot {
            display: flex;
            gap: 30px;
            margin-top: 10px;

            align-items: flex-start;
        }

        /* SIDEBAR BỘ LỌC DÍNH (STICKY) */
        .cot-trai-filter {
            width: 280px;
            flex-shrink: 0;
            background-color: var(--bs-pink-50);
            border: 1px solid var(--bs-pink-200);
            border-radius: 12px;
            padding: 20px;

            position: -webkit-sticky;
            position: sticky;
            top: 100px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            z-index: 10;
        }

        .cot-trai-filter label {
            font-weight: bold;
            color: var(--bs-pink-800);
            font-size: 14px;
            margin-bottom: 6px;
        }

        /* LƯỚI SẢN PHẨM CHÍNH (3 SẢN PHẨM 1 HÀNG) */
        .cot-phai-products {
            flex-grow: 1;
            min-width: 0;
        }

        .cot-phai-products .loai-san-pham {
            display: grid;
            /* Ghi đè để ép buộc luôn hiển thị 3 cột (3 sản phẩm/hàng) */
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 20px;
            padding-top: 0;
        }

        .btn-pink {
            background-color: var(--bs-pink-500);
            color: white;
            border: none;
            font-weight: bold;
        }

        .btn-pink:hover {
            background-color: var(--bs-pink-600);
            color: white;
        }

        .goi-y-section {
            margin-bottom: 30px;
        }

        /* Responsive Mobile */
        @media (max-width: 768px) {
            .layout-2-cot {
                flex-direction: column;
            }

            .cot-trai-filter {
                width: 100%;
                position: relative;
                /* Bỏ sticky trên mobile */
                top: 0;
            }

            .cot-phai-products .loai-san-pham {
                grid-template-columns: repeat(2, 1fr) !important;
                /* 2 cột trên điện thoại */
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
                    if (isset($_SESSION['IdTaiKhoan'])) {
                        $idKhachHang = $_SESSION['IdTaiKhoan'];

                        // Dùng 127.0.0.1 thay cho localhost để không bị lỗi trên XAMPP
                        // 1. Xin AI 15 sản phẩm (xin dư ra để bù trừ cho những món bị lọc bỏ)
                        $api_url = "http://127.0.0.1:5000/recommend?user_id=$idKhachHang&top_n=15";

                        // SỬ DỤNG CURL ĐỂ KẾT NỐI SIÊU ỔN ĐỊNH
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $api_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Đợi tối đa 3 giây
                        $response = curl_exec($ch);
                        curl_close($ch);

                        if ($response) {
                            $goi_y_list = json_decode($response, true);

                            if (!empty($goi_y_list) && !isset($goi_y_list['error'])) {
                                $hasRecommendations = true;
                                $ids = array_column($goi_y_list, 'id');
                                $ids_string = implode(',', $ids);

                                // 2. Truy vấn DB lấy thông tin và CHẶN KHÔNG CHO HIỂN THỊ ĐỒ CỦA CHÍNH MÌNH BÁN
                                $sql_ai = "SELECT hh.*, (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) as Anh 
                               FROM HangHoa hh 
                               WHERE MaHH IN ($ids_string) 
                               AND IdNguoiBan != $idKhachHang 
                               ORDER BY FIELD(MaHH, $ids_string)";
                                $result_ai = $conn->query($sql_ai);

                                $sanphams = [];
                                while ($row = $result_ai->fetch_assoc()) {
                                    $sanphams[$row['MaHH']] = $row;
                                }

                                // 3. Vẽ các thẻ sản phẩm (Chỉ lấy đủ 8 cái)
                                $demSP = 0;
                                foreach ($goi_y_list as $item) {
                                    $sp = $sanphams[$item['id']] ?? null;
                                    if (!$sp) continue; // Bỏ qua nếu món đồ này bị lọc ở bước SQL trên

                                    // Nếu đã in đủ 8 cái thì dừng vòng lặp
                                    if ($demSP >= 8) break;
                                    $demSP++;

                                    // XỬ LÝ NHÃN (HIỆN TRENDING CHO COLD START, % CHO AI MATCH)
                                    if (isset($item['reason']) && $item['reason'] == 'Trending') {
                                        $badgeHtml = '<div class="badge-match" style="background:#fd7e14;"><i class="fa-solid fa-fire"></i> Đang thịnh hành</div>';
                                    } else {
                                        $badgeHtml = '<div class="badge-match">Phù hợp ' . $item['match'] . '%</div>';
                                    }

                                    // CHUẨN BỊ DỮ LIỆU ĐỂ VẼ THẺ
                                    $maHH = $sp['MaHH'];
                                    $tenHH = htmlspecialchars($sp['TenHH']);

                                    // Xử lý ảnh
                                    $anh = $sp['Anh'] ?? (isset($sp['URL']) ? $sp['URL'] : 'assets/images/placeholder.png');
                                    $imgSrc = (strpos($anh, 'http') === 0) ? $anh : '../' . $anh;

                                    // Xử lý số sao, giá, số lượng
                                    $rating = isset($sp['Rating']) ? number_format((float)$sp['Rating'], 1) : "0.0";
                                    $gia = number_format($sp['Gia'], 0, ',', '.');
                                    $soLuong = isset($sp['SoLuongHH']) ? (int)$sp['SoLuongHH'] : 1;

                                    // Xử lý hết hàng
                                    $hetHang = $soLuong == 0;
                                    $productClass = $hetHang ? "<div class='product-item het-hang' style='background: rgba(0, 0, 0, 0.1);'>" : "<div class='product-item'>";
                                    $badgeHetHang = $hetHang ? "<div class='badge-het-hang'>Hết hàng</div>" : '';

                                    // Xử lý giảm giá
                                    if (!empty($sp['GiaTri']) && $sp['GiaTri'] > 0) {
                                        $giaGiamVal = $sp['Gia'] - ($sp['Gia'] * ($sp['GiaTri'] / 100));
                                        $giaGiam = number_format($giaGiamVal, 0, ',', '.');
                                        $giaHienThi = "<span class='gia-goc'>{$gia} đ</span><span class='gia-giam'>{$giaGiam} đ</span>";
                                    } else {
                                        $giaHienThi = "<span class='gia-giam'>{$gia} đ</span>";
                                    }
                    ?>
                                    <a href="chiTietSanPhamController.php?id=<?= $maHH ?>" class="product-link">
                                        <?= $productClass ?>
                                        <button class="btn-bo-qua shadow" onclick="boQuaSanPham(<?= $maHH ?>, this, event)" title="Bỏ qua / Không thích">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <?= $badgeHtml ?>

                                        <div class='product-item-top'>
                                            <img src='<?= $imgSrc ?>' alt='<?= $tenHH ?>' loading='lazy' style='height: 180px; width: 100%; object-fit: cover; border-radius: 8px 8px 0 0;'>
                                            <?= $badgeHetHang ?>
                                            <div class='tieude-sanpham'><?= $tenHH ?></div>
                                        </div>
                                        <div class='product-item-bottom'>
                                            <div class='gia-rating'>
                                                <div class='rating'>
                                                    <i class='fa-solid fa-star'></i>
                                                    <span><?= $rating ?></span>
                                                </div>
                                                <div class='gia-san-pham'>
                                                    <?= $giaHienThi ?>
                                                </div>
                                            </div>
                                        </div>
                </div> </a>
        <?php
                                }
                            }
                        }
                    }

                    // Nếu khách chưa đăng nhập HOẶC AI không có dữ liệu để gợi ý -> Hiện các sản phẩm mới nhất làm mặc định
                    if (!$hasRecommendations) {
                        $sql_new = "SELECT hh.*, (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) as Anh 
                        FROM HangHoa hh WHERE TrangThaiDuyet = 'DaDuyet' ORDER BY NgayThem DESC LIMIT 8";
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
                        <div class="rating">
                            <i class="fa-solid fa-star"></i>
                            <span>0.0</span>
                        </div>
                        <div class="gia-san-pham">
                            <span class="gia-giam"><?= $gia ?> đ</span>
                        </div>
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

                <div class="loai-san-pham" id="product-list">
                </div>

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
    <script>
        // --- HÀM XỬ LÝ NÚT X (BỎ QUA) Ở PHẦN GỢI Ý ---
        function boQuaSanPham(mahh, btnElement, event) {
            event.preventDefault(); // Ngăn thẻ <a> chuyển hướng trang

            // Ẩn mượt mà thẻ sản phẩm này
            $(btnElement).closest('.product-link').fadeOut(300);

            // Gửi AJAX ngầm về Server để ghi nhận 1 sao (đã tương tác) -> Lần sau Python sẽ lọc bỏ
            $.post('boQuaGoiY.php', {
                mahh: mahh
            }, function(response) {
                console.log("Đã loại bỏ sản phẩm ID: " + mahh + " khỏi gợi ý.");
            });
        }
    </script>
</body>

</html>