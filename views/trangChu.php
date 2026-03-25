
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
        <?php if (!empty($danhSachBanner)): ?>
            <?php foreach ($danhSachBanner as $index => $banner): ?>
                <button type="button" 
                        data-bs-target="#carouselExampleAutoplaying" 
                        data-bs-slide-to="<?= $index ?>" 
                        class="<?= $index === 0 ? 'active' : '' ?>" 
                        aria-current="<?= $index === 0 ? 'true' : 'false' ?>">
                </button>
            <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="carousel-inner">
        <?php if (!empty($danhSachBanner)): ?>
            <?php foreach ($danhSachBanner as $index => $banner): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <img src="../assets/images/banners/<?= htmlspecialchars($banner['HinhAnh']) ?>" 
                       class="d-block w-100" 
                       alt="<?= htmlspecialchars($banner['TieuDe']) ?>">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="carousel-item active">
              <img src="../assets/images/placeholder.png" class="d-block w-100" alt="Mặc định">
            </div>
        <?php endif; ?>
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>
    </div>

    <div class="container text-center my-4">
        <button onclick="batDauTimViTri()" class="btn btn-danger btn-lg" style="border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fa-solid fa-map-location-dot"></i> Tìm Cửa Hàng Gần Tôi Ngay!
        </button>
    </div>

    <div class="container mb-5">
        <div id="status-message" class="alert alert-info text-center shadow-sm" style="display: none;"></div>

        <div class="row" id="danh-sach-cua-hang"></div>
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
  <script>
    // Hàm này chạy khi người dùng bấm nút
    function batDauTimViTri() {
        let statusDiv = document.getElementById('status-message');
        let listDiv = document.getElementById('danh-sach-cua-hang');
        
        // Hiện thông báo đang quét, xóa kết quả cũ
        statusDiv.style.display = 'block';
        statusDiv.className = "alert alert-info text-center shadow-sm";
        statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-info me-2" role="status"></div> Đang xin quyền vị trí... Vui lòng ấn "Cho phép" (Allow) trên trình duyệt.';
        listDiv.innerHTML = ''; // Làm sạch danh sách cũ

        // Gọi API HTML5 Geolocation để xin Vĩ độ/Kinh độ
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    let latNguoiMua = position.coords.latitude;
                    let lngNguoiMua = position.coords.longitude;
                    
                    statusDiv.innerHTML = '<i class="fa-solid fa-satellite-dish"></i> Đã lấy được vị trí! Đang tìm cửa hàng quanh bạn...';
                    
                    // Lấy được tọa độ rồi thì gửi ngầm xuống Backend
                    goiApiTimCuaHang(latNguoiMua, lngNguoiMua);
                },
                function(error) {
                    statusDiv.className = "alert alert-warning text-center shadow-sm";
                    statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Bạn đã từ chối cung cấp vị trí hoặc thiết bị không bật GPS.';
                }
            );
        } else {
            statusDiv.className = "alert alert-danger text-center shadow-sm";
            statusDiv.innerText = "Trình duyệt của bạn quá cũ, không hỗ trợ định vị GPS.";
        }
    }

    // Hàm nhận tọa độ và đẩy xuống file PHP bằng Fetch API
    function goiApiTimCuaHang(lat, lng) {
        let formData = new FormData();
        formData.append('lat', lat);
        formData.append('lng', lng);

        // Gọi đến file Backend chúng ta vừa tạo ở Bước 3
        fetch('ajaxTimCuaHang.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            let html = '';
            let statusDiv = document.getElementById('status-message');
            
            if (data.status === 'success') {
                if (data.data.length > 0) {
                    // Nếu có cửa hàng, ẩn thông báo đi và vẽ giao diện
                    statusDiv.style.display = 'none'; 
                    
                    data.data.forEach(shop => {
                        html += `
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm" style="border-radius: 12px; border: none; transition: transform 0.2s;">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary" style="font-weight:bold;">
                                            <i class="fa-solid fa-store" style="color: #ff4d4f;"></i> ${shop.TenCuaHang}
                                        </h5>
                                        <p class="card-text text-muted" style="font-size: 14px; margin-bottom: 10px;">
                                            <i class="fa-solid fa-location-dot"></i> ${shop.DiaChi}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="badge" style="background-color: #ffe5e5; color: #ff4d4f; padding: 8px 12px; font-size: 14px;">
                                                <i class="fa-solid fa-route"></i> Cách đây <b>${shop.KhoangCachKm} km</b>
                                            </span>
                                            <a href="cuaHangController.php?id=${shop.IdTaiKhoan}" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">Xem Shop</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    // Trả về mảng rỗng (Không có shop nào < 10km)
                    statusDiv.className = "alert alert-secondary text-center shadow-sm";
                    statusDiv.innerHTML = '<i class="fa-regular fa-face-frown"></i> Tiếc quá, không có cửa hàng nào trong bán kính 10km quanh bạn.';
                }
            } else {
                // Lỗi SQL từ Backend
                statusDiv.className = "alert alert-danger text-center shadow-sm";
                statusDiv.innerHTML = '<i class="fa-solid fa-bug"></i> Lỗi hệ thống: ' + data.message;
            }
            
            // Đổ HTML vào danh sách
            document.getElementById('danh-sach-cua-hang').innerHTML = html;
        })
        .catch(err => {
            // Lỗi sập đường truyền hoặc sai đường dẫn fetch
            console.error('Lỗi khi fetch dữ liệu:', err);
            let statusDiv = document.getElementById('status-message');
            statusDiv.className = "alert alert-danger text-center shadow-sm";
            statusDiv.innerHTML = '<i class="fa-solid fa-link-slash"></i> Đã xảy ra lỗi kết nối với máy chủ (Mở F12 -> Console để xem chi tiết).';
        });
    }
</script>
</body>

</html>