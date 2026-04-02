
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

  < class="giua-trang">
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

    <!-- <div class="container text-center my-4">
        <button onclick="batDauTimViTri()" class="btn btn-danger btn-lg" style="border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fa-solid fa-map-location-dot"></i>Cửa Hàng Gần Bạn
        </button>
    </div> -->

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

      <div class="horizontal-scroll-wrapper mt-3" id="danh-sach-sp-gan-nhat">
          <div class="w-100 text-center text-muted py-4">
              <div class="spinner-border spinner-border-sm text-danger" role="status"></div> Đang tìm sản phẩm quanh bạn...
          </div>
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
  <!-- <script>
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
                // ĐOẠN GỢI Ý (Sửa lại dựa trên code của bạn):
                function(error) {
                    console.warn("GPS bị từ chối, đang chuyển sang địa chỉ đã đăng ký...");
                    
                    // Thay vì báo lỗi, hãy gọi hàm lấy vị trí từ Database (PHP truyền xuống)
                    let latDuPhong = <?php echo $userLat; ?>; 
                    let lngDuPhong = <?php echo $userLng; ?>;

                    if (latDuPhong != 0) {
                        goiApiTimCuaHang(latDuPhong, lngDuPhong); 
                        statusDiv.innerHTML = "Đang hiện cửa hàng dựa trên địa chỉ đăng ký của bạn.";
                    } else {
                        statusDiv.innerHTML = "Vui lòng bật định vị hoặc đăng nhập để xem cửa hàng gần nhất.";
                    }
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
</script> -->
  <?php if (isset($_SESSION['IdTaiKhoan'])): ?>
  <script>
    // Tọa độ dự phòng lấy từ địa chỉ mặc định trong Database (PHP truyền sang)
    var latDuPhong = <?php echo $userLat ? $userLat : 0; ?>;
    var lngDuPhong = <?php echo $userLng ? $userLng : 0; ?>;

    document.addEventListener("DOMContentLoaded", function() {
        // Chỉ chạy khi có session
        requestLocation();
    });

    function requestLocation() {
        let statusDiv = document.getElementById('status-message');
        if (!statusDiv) return;

        statusDiv.style.display = 'block';
        statusDiv.className = 'alert alert-info text-center small';
        statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-info"></div> Đang xác định vị trí...';

        // NẾU TRÌNH DUYỆT HỖ TRỢ GPS
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // TH 1: LẤY ĐƯỢC GPS
                    statusDiv.className = 'alert alert-success text-center small shadow-sm';
                    statusDiv.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Đang gợi ý cửa hàng theo vị trí hiện tại của bạn.';
                    
                    goiApiTimCuaHang(position.coords.latitude, position.coords.longitude);
                    goiApiTimSanPhamGanNhat(position.coords.latitude, position.coords.longitude);
                },
                function(error) {
                    // TH 2: KHÔNG CÓ GPS -> CHUYỂN SANG ĐỊA CHỈ MẶC ĐỊNH
                    if (latDuPhong != 0 && lngDuPhong != 0) {
                        statusDiv.className = 'alert alert-info text-center small shadow-sm';
                        // Đã sửa lại câu thông báo ở đây cho rõ ràng:
                        statusDiv.innerHTML = '<i class="fa-solid fa-house-user"></i> Đang hiển thị cửa hàng quanh <b>địa chỉ mặc định</b> của bạn.';
                        
                        goiApiTimCuaHang(latDuPhong, lngDuPhong);
                        goiApiTimSanPhamGanNhat(latDuPhong, lngDuPhong);
                    } else {
                        // TH 3: KHÔNG CÓ GPS, CŨNG CHƯA CÓ ĐỊA CHỈ MẶC ĐỊNH
                        statusDiv.className = 'alert alert-warning text-center small shadow-sm';
                        statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Vui lòng bật GPS hoặc thiết lập địa chỉ mặc định để tìm cửa hàng.';
                    }
                }
            );
        } else {
            // NẾU TRÌNH DUYỆT QUÁ CŨ KHÔNG HỖ TRỢ GPS
            if (latDuPhong != 0 && lngDuPhong != 0) {
                statusDiv.className = 'alert alert-info text-center small shadow-sm';
                statusDiv.innerHTML = '<i class="fa-solid fa-house-user"></i> Đang hiển thị cửa hàng quanh <b>địa chỉ mặc định</b> của bạn.';
                goiApiTimCuaHang(latDuPhong, lngDuPhong);
                goiApiTimSanPhamGanNhat(latDuPhong, lngDuPhong);
            } else {
                statusDiv.className = 'alert alert-warning text-center small shadow-sm';
                statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Trình duyệt không hỗ trợ GPS. Vui lòng thiết lập địa chỉ mặc định.';
            }
        }
    }

    function goiApiTimCuaHang(lat, lng) {
        let formData = new FormData();
        formData.append('lat', lat);
        formData.append('lng', lng);

        fetch('ajaxTimCuaHang.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            let html = '';
            let statusDiv = document.getElementById('status-message');
            let listDiv = document.getElementById('danh-sach-cua-hang');
            
            if (data.status === 'success') {
                if (data.data.length > 0) {
                    // QUAN TRỌNG: Đã xóa dòng statusDiv.style.display = 'none'; 
                    // Để thông báo (GPS hay Địa chỉ nhà) luôn hiển thị trên màn hình
                    
                    data.data.forEach(shop => {
                        html += `
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary"><b>${shop.TenCuaHang}</b></h5>
                                        <p class="card-text text-muted small"><i class="fa-solid fa-location-dot"></i> ${shop.DiaChi}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-danger">Cách: ${shop.KhoangCachKm} km</span>
                                            <a href="cuaHangController.php?id=${shop.IdTaiKhoan}" class="btn btn-sm btn-outline-danger">Xem</a>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
                } else {
                    // Nếu không có shop nào, đổi thông báo thành màu xám
                    statusDiv.className = "alert alert-secondary text-center shadow-sm";
                    statusDiv.innerHTML = '<i class="fa-regular fa-face-frown"></i> Không có cửa hàng nào trong bán kính 10km quanh vị trí này.';
                }
            } else {
                // Nếu lỗi code PHP
                statusDiv.className = "alert alert-danger text-center shadow-sm";
                statusDiv.innerHTML = '<i class="fa-solid fa-bug"></i> Lỗi hệ thống: ' + data.message;
            }
            
            // Đổ HTML hiển thị danh sách
            listDiv.innerHTML = html;
        });
    }

    function goiApiTimSanPhamGanNhat(lat, lng) {
        let formData = new FormData();
        formData.append('lat', lat);
        formData.append('lng', lng);

        fetch('ajaxTimSanPhamGanNhat.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            let container = document.getElementById('danh-sach-sp-gan-nhat');
            if (!container) return;

            let html = '';
            
            if (data.status === 'success' && data.data.length > 0) {
                // 1. Vòng lặp in ra tối đa 20 sản phẩm như bình thường
                data.data.forEach(sp => {
                    html += `
                        <a href="chiTietSanPhamController.php?id=${sp.MaHH}" class="product-link">
                          <div class="product-item">
                            <div class="product-item-top">
                              <img src="../${sp.HinhAnh}" alt="${sp.TenHH}" style="height: 180px; width: 100%; object-fit: cover; border-radius: 8px 8px 0 0;">
                              <div class="tieude-sanpham">${sp.TenHH} <br>
                                  <small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn ${sp.KhoangCachKm}km</small>
                              </div>
                              <p class="text-muted small mb-2"><i class="fa-solid fa-store"></i> ${sp.TenCuaHang}</p>
                              <p class="text-muted small mb-2"><i class="fa-solid fa-location-dot"></i> ${sp.DiaChi}</p>
                            </div>
                            <div class="product-item-bottom">
                              <div class="gia-rating">
                                <div class="rating"><span>5.0</span><i class="fa-solid fa-star"></i></div>
                                <div class="gia-san-pham">
                                  <span class="gia-giam">${sp.GiaFormat}</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                    `;
                });

                // 2. SAU KHI IN XONG, THÊM THẺ "XEM TẤT CẢ" VÀO CUỐI CÙNG
                // Mình set link tạm là sanPhamGanBanController.php nhé
                html += `
                    <a href="sanPhamGanBanController.php?lat=${lat}&lng=${lng}" class="product-link" style="display: flex; align-items: center; justify-content: center; min-width: 160px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-decoration: none; border: 1px dashed #dc3545; margin-right: 15px;">
                        <div class="text-center text-danger p-3">
                            <i class="fa-solid fa-circle-arrow-right fa-3x mb-3"></i>
                            <h6 class="mb-0 fw-bold">Xem tất cả<br>sản phẩm gần đây</h6>
                        </div>
                    </a>
                `;

            } else {
                html = `<div class="w-100 text-center text-muted py-4"><i class="fa-regular fa-face-frown"></i> Chưa có sản phẩm nào được bán gần bạn.</div>`;
            }
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Lỗi khi lấy sản phẩm:', error);
            let container = document.getElementById('danh-sach-sp-gan-nhat');
            if(container) container.innerHTML = `<div class="w-100 text-center text-danger py-4">Lỗi kết nối máy chủ!</div>`;
        });
    }
  </script>
  <?php else: ?>
  <script>
      document.addEventListener("DOMContentLoaded", function() {
          let statusDiv = document.getElementById('status-message');
          if (statusDiv) {
              statusDiv.className = "alert alert-light text-center border";
              statusDiv.innerHTML = '<i class="fa-solid fa-user-lock"></i> <a href="dangNhapController.php">Đăng nhập</a> để xem các cửa hàng gần bạn nhất.';
          }
      });
  </script>
  <?php endif; ?>
</body>

</html>