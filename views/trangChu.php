<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
  <link href="../assets/css/trangChu.css" rel="stylesheet">
  <link href="../assets/css/header.css" rel="stylesheet">
  <link href="../assets/css/sanPham.css" rel="stylesheet">
  <link href="../assets/css/color.css" rel="stylesheet">
  <title>Trang chủ - TwoHand</title>

  <style>
    /* CSS TẠO THANH CUỘN NGANG CHO SẢN PHẨM GỢI Ý */
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
      background-color: #f1f1f1;
      border-radius: 10px;
    }

    /* Chiều rộng thẻ khi cuộn ngang ở trang chủ */
    .horizontal-scroll-wrapper .product-link {
      flex: 0 0 auto;
      width: 250px;
      scroll-snap-align: start;
    }

    .horizontal-scroll-wrapper .product-item {
      height: 100%;
      min-height: 320px;
    }
  </style>
</head>

<body>

  <?php include '../includes/header.php'; ?>

  <div class="giua-trang">
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="true" data-bs-touch="true">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="3" aria-label="Slide 4"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="../assets/images/source/anh-qc-1.webp" class="d-block w-100" alt="qc1">
        </div>
        <div class="carousel-item">
          <img src="../assets/images/source/anh-qc-2.webp" class="d-block w-100" alt="qc2">
        </div>
        <div class="carousel-item">
          <img src="../assets/images/source/anh-qc-3.webp" class="d-block w-100" alt="qc3">
        </div>
        <div class="carousel-item">
          <img src="../assets/images/source/anh-qc-4.webp" class="d-block w-100" alt="qc4">
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>
    </div>

    <div class="danh-muc">
      <div class="tieu-de-danh-muc">
        <h2><i class="fa-solid fa-minus"></i> DANH MỤC</h2>
      </div>
      <div class="nut-danh-muc">
      </div>
    </div>

    <div class="san-pham-giam-gia">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> SẢN PHẨM ĐANG GIẢM GIÁ</h2>
      </div>
      <div class="loai-san-pham-giam-gia horizontal-scroll-wrapper mt-3">
      </div>
    </div>

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> GẦN BẠN NHẤT (GỢI Ý)</h2>
      </div>

      <div class="horizontal-scroll-wrapper mt-3">
        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top">
              <img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Bàn học gỗ cao su lắp ráp <br><small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn 2.5km</small></div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>4.2</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham">
                  <span class="gia-giam">150.000đ</span>
                </div>
              </div>
            </div>
          </div>
        </a>

        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top">
              <img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Màn hình PC Dell 24 inch cũ <br><small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn 3.1km</small></div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>5.0</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham">
                  <span class="gia-giam">1.200.000đ</span>
                </div>
              </div>
            </div>
          </div>
        </a>

        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Bàn phím cơ cũ <br><small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn 4.0km</small></div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>4.5</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham"><span class="gia-giam">300.000đ</span></div>
              </div>
            </div>
          </div>
        </a>
        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Chuột Logitech <br><small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn 5.0km</small></div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>4.8</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham"><span class="gia-giam">200.000đ</span></div>
              </div>
            </div>
          </div>
        </a>
        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Loa Bluetooth Sony <br><small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn 6.5km</small></div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>4.9</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham"><span class="gia-giam">450.000đ</span></div>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> CÓ THỂ BẠN SẼ THÍCH</h2>
      </div>

      <div class="horizontal-scroll-wrapper mt-3">
        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top">
              <img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Sách Đắc Nhân Tâm (Bìa cứng)</div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>4.8</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham">
                  <span class="gia-giam">50.000đ</span>
                </div>
              </div>
            </div>
          </div>
        </a>
        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Tiểu thuyết Mắt Biếc</div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>4.9</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham"><span class="gia-giam">80.000đ</span></div>
              </div>
            </div>
          </div>
        </a>
        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Sách Nhà Giả Kim</div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>5.0</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham"><span class="gia-giam">60.000đ</span></div>
              </div>
            </div>
          </div>
        </a>
        <a href="#" class="product-link">
          <div class="product-item">
            <div class="product-item-top"><img src="../assets/images/placeholder.png" alt="sp">
              <div class="tieude-sanpham">Truyện Harry Potter</div>
            </div>
            <div class="product-item-bottom">
              <div class="gia-rating">
                <div class="rating"><span>4.7</span><i class="fa-solid fa-star"></i></div>
                <div class="gia-san-pham"><span class="gia-giam">150.000đ</span></div>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> TẤT CẢ SẢN PHẨM</h2>
      </div>
      <div class="loai-san-pham-moi" id="main-product-list">
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