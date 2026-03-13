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
                    <a href="#" class="product-link">
                        <div class="product-item">
                            <div class="product-item-top">
                                <img src="../assets/images/placeholder.png" alt="sp">
                                <div class="tieude-sanpham">Áo khoác Blazer nữ phong cách Hàn Quốc</div>
                            </div>
                            <div class="product-item-bottom">
                                <div class="gia-rating">
                                    <div class="rating"><span>4.9</span><i class="fa-solid fa-star"></i></div>
                                    <div class="gia-san-pham">
                                        <span class="gia-giam">180.000đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="product-link">
                        <div class="product-item">
                            <div class="product-item-top">
                                <img src="../assets/images/placeholder.png" alt="sp">
                                <div class="tieude-sanpham">Quần Jean nam ống rộng Vintage</div>
                            </div>
                            <div class="product-item-bottom">
                                <div class="gia-rating">
                                    <div class="rating"><span>4.5</span><i class="fa-solid fa-star"></i></div>
                                    <div class="gia-san-pham">
                                        <span class="gia-goc">300.000đ</span>
                                        <span class="gia-giam">220.000đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="product-link">
                        <div class="product-item">
                            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
                                <div class="tieude-sanpham">Giày Sneaker độn đế hack dáng</div>
                            </div>
                            <div class="product-item-bottom">
                                <div class="gia-rating">
                                    <div class="rating"><span>4.8</span><i class="fa-solid fa-star"></i></div>
                                    <div class="gia-san-pham"><span class="gia-giam">350.000đ</span></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="product-link">
                        <div class="product-item">
                            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
                                <div class="tieude-sanpham">Túi xách da mini đính nơ</div>
                            </div>
                            <div class="product-item-bottom">
                                <div class="gia-rating">
                                    <div class="rating"><span>5.0</span><i class="fa-solid fa-star"></i></div>
                                    <div class="gia-san-pham"><span class="gia-giam">120.000đ</span></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="product-link">
                        <div class="product-item">
                            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
                                <div class="tieude-sanpham">Đồng hồ Casio G-Shock cũ</div>
                            </div>
                            <div class="product-item-bottom">
                                <div class="gia-rating">
                                    <div class="rating"><span>4.7</span><i class="fa-solid fa-star"></i></div>
                                    <div class="gia-san-pham"><span class="gia-giam">500.000đ</span></div>
                                </div>
                            </div>
                        </div>
                    </a>
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
</body>

</html>