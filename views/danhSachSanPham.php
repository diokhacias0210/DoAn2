<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/danhSachSanPham.css" rel="stylesheet">
    <link href="../assets/css/sanPham.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <title>Danh sách sản phẩm</title>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="san-pham-chinh">

            <div class="tieu-de-san-pham">
                <h2>DANH SÁCH SẢN PHẨM</h2>
            </div>

            <div class="nut-san-pham">
                <div class="filter-bar">
                    <label for="category">Danh mục</label>
                    <select id="category" class="form-select">
                        <option value="">-- Tất cả --</option>

                        <?php foreach ($danhSachDanhMuc as $dm): ?>
                            <option value="<?php echo $dm['MaDM']; ?>"><?php echo htmlspecialchars($dm['TenDM']); ?></option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="filter-bar">
                    <label for="sort">Sắp xếp</label>
                    <select id="sort" class="form-select">
                        <option value="new">Mới nhất</option>
                        <option value="old">Cũ nhất</option>
                        <option value="az">Từ a đến z</option>
                        <option value="za">Từ z đến a</option>
                        <option value="giacao">Giá cao nhất</option>
                        <option value="giathap">Giá thấp nhất</option>
                    </select>
                </div>

                <form class="filter-bar search-bar" id="search-form">
                    <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm...">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>


            <div class="loai-san-pham" id="product-list">
            </div>

            <div class="trang-thai-san-pham">
                <div id="no-products" style="display:none">
                    <i class="fas fa-box-open fa-3x"></i>
                    <p>Không tìm thấy sản phẩm nào</p>
                </div>
                <div id="load-more-container" class="nut-xem-them-san-pham">
                    <button type="button" id="nut-xem-them">Xem thêm</button>
                </div>
            </div>

        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/loadLocSanPham.js"></script>
    <script src="../assets/js/js.js"></script>
    <script src="../assets/js/liveSearch.js"></script>
</body>

</html>