<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
  <link href="../assets/css/trangChu.css" rel="stylesheet">
  <link href="../assets/css/header.css" rel="stylesheet">
  <link href="../assets/css/sanPham.css" rel="stylesheet">
  <link href="../assets/css/color.css" rel="stylesheet">
  <title>Trang chủ</title>
</head>

<body>

  <?php include '../includes/header.php'; ?>


  <div class="giua-trang">
    <!-- Slide quảng cáo -->
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="true" data-bs-touch="true">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="3" aria-label="Slide 4"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="../assets/images/source/anh-qc-1.webp" class="d-block w-100" alt="anh-qc-1.webp" loading="lazy">
        </div>
        <div class="carousel-item">
          <img src="../assets/images/source/anh-qc-2.webp" class="d-block w-100" alt="anh-qc-2.webp" loading="lazy">
        </div>
        <div class="carousel-item">
          <img src="../assets/images/source/anh-qc-3.webp" class="d-block w-100" alt="anh-qc-3.webp" loading="lazy">
        </div>
        <div class="carousel-item">
          <img src="../assets/images/source/anh-qc-4.webp" class="d-block w-100" alt="anh-qc-4.webp" loading="lazy">
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
    <!--  sản phẩm giảm giá-->
    <div class="san-pham-giam-gia">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> SẢN PHẨM ĐANG GIẢM GIÁ</h2>
      </div>

      <div class="loai-san-pham-giam-gia">
      </div>
    </div>

    <!-- -----------------------phần danh mục ------------------------- -->
    <div class="danh-muc">
      <div class="tieu-de-danh-muc">
        <h2><i class="fa-solid fa-minus"></i> DANH MỤC</h2>
      </div>
      <div class="nut-danh-muc">

      </div>
    </div>
    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> SẢN PHẨM</h2>
      </div>
      <div class="loai-san-pham-moi">
      </div>
    </div>
    <div class="nut-xem-them-san-pham">
      <a href="danhSachSanPhamController.php">
        <button>xem thêm</button>
      </a>
    </div>
  </div>

  <?php include '../includes/footer.php'; ?>

  <script src="../assets/js/bootstrap/bootstrap.bundle.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../assets/js/loadSanPham.js"></script>
  <script src="../assets/js/js.js"></script>
  <script src="../assets/js/yeuThich.js"></script>
</body>

</html>