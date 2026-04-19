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

    .horizontal-scroll-wrapper .product-link {
      flex: 0 0 auto;
      width: 280px;
      scroll-snap-align: start;
      text-decoration: none;
      position: relative;
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

    .survey-checkbox:checked+.btn-survey {
      background-color: var(--bs-pink-500) !important;
      color: white !important;
      border-color: var(--bs-pink-500) !important;
    }

    .btn-survey {
      border-radius: 20px;
      font-weight: 500;
      border: 1px solid var(--bs-pink-300);
      color: var(--bs-pink-600);
    }
  </style>
</head>

<body>
  <?php include '../includes/header.php'; ?>

  <?php
  // Logic Modal khảo sát gợi ý AI: Chỉ hiển thị nếu user đã đăng nhập và chưa có tương tác nào được ghi nhận trong HanhVi_AI
  $showSurvey = false;
  if (isset($_SESSION['IdTaiKhoan'])) {
    $idKhach = (int)$_SESSION['IdTaiKhoan'];
    $checkAI = $conn->query("SELECT COUNT(*) as cnt FROM HanhVi_AI WHERE IdTaiKhoan = $idKhach");
    if ($checkAI && $checkAI->fetch_assoc()['cnt'] == 0) {
      $showSurvey = true;
    }
  }
  ?>

  <?php if ($showSurvey): ?>
    <div class="modal fade" id="modalSurveyAI" data-bs-backdrop="static" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
          <div class="modal-header text-white" style="border-radius: 16px 16px 0 0; background: var(--bs-pink-500);">
            <h5 class="modal-title fw-bold"><i class="fa-solid fa-wand-magic-sparkles"></i> Bạn đang quan tâm đến gì?</h5>
          </div>
          <div class="modal-body text-center p-4">
            <div class="d-flex flex-wrap justify-content-center gap-2">
              <input type="checkbox" class="btn-check survey-checkbox" id="cat1" value="1"><label class="btn btn-survey" for="cat1">Đồ gia dụng</label>
              <input type="checkbox" class="btn-check survey-checkbox" id="cat2" value="2"><label class="btn btn-survey" for="cat2">Linh kiện PC</label>
              <input type="checkbox" class="btn-check survey-checkbox" id="cat3" value="3"><label class="btn btn-survey" for="cat3">Máy tính</label>
              <input type="checkbox" class="btn-check survey-checkbox" id="cat4" value="4"><label class="btn btn-survey" for="cat4">Nội thất</label>
              <input type="checkbox" class="btn-check survey-checkbox" id="cat5" value="5"><label class="btn btn-survey" for="cat5">Quần áo</label>
              <input type="checkbox" class="btn-check survey-checkbox" id="cat6" value="6"><label class="btn btn-survey" for="cat6">Thiết bị chơi game</label>
              <input type="checkbox" class="btn-check survey-checkbox" id="cat7" value="7"><label class="btn btn-survey" for="cat7">Thiết bị điện tử</label>
            </div>
          </div>
          <div class="modal-footer justify-content-center border-0 pb-4">
            <button type="button" class="btn btn-light rounded-pill px-4" onclick="submitSurvey(true)">Khác (Bỏ qua)</button>
            <button type="button" class="btn rounded-pill px-5 text-white" style="background: var(--bs-pink-500);" onclick="submitSurvey(false)">Hoàn tất</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

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

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> CỬA HÀNG GẦN BẠN</h2>
      </div>
      <div class="container-fluid mt-3">
        <div id="status-message" class="alert alert-info text-center shadow-sm" style="display: none;"></div>
      </div>
      <div class="horizontal-scroll-wrapper mt-2" id="danh-sach-cua-hang"></div>
    </div>

    <div class="danh-muc">
      <div class="tieu-de-danh-muc">
        <h2><i class="fa-solid fa-minus"></i> DANH MỤC</h2>
      </div>
      <div class="nut-danh-muc"></div>
    </div>

    <div class="san-pham-giam-gia">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> SẢN PHẨM ĐANG GIẢM GIÁ</h2>
      </div>
      <div class="loai-san-pham-giam-gia horizontal-scroll-wrapper mt-3"></div>
    </div>

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> GẦN BẠN NHẤT</h2>
      </div>
      <div class="horizontal-scroll-wrapper mt-3" id="danh-sach-sp-gan-nhat">
        <div class="w-100 text-center text-muted py-4">
          <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
        </div>
      </div>
    </div>

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> CÓ THỂ BẠN SẼ THÍCH</h2>
      </div>
      <div class="horizontal-scroll-wrapper mt-3" id="danh-sach-goi-y-ai">
        <div class="w-100 text-center py-4 text-muted">
          <div class="spinner-border spinner-border-sm text-danger"></div> Đang tải gợi ý dành cho bạn...
        </div>
      </div>
    </div>

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> TẤT CẢ SẢN PHẨM</h2>
      </div>
      <div class="loai-san-pham-moi" id="main-product-list"></div>
    </div>
    <div class="nut-xem-them-san-pham"><a href="danhSachSanPhamController.php"><button>xem thêm</button></a></div>
  </div>

  <?php include '../includes/footer.php'; ?>
  <script src="../assets/js/bootstrap/bootstrap.bundle.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../assets/js/loadSanPham.js"></script>
  <script src="../assets/js/js.js"></script>

  <script>
    //  GỌI GỢI Ý AI BẰNG AJAX
    document.addEventListener("DOMContentLoaded", function() {
      fetch('ajaxLayGoiYAI.php')
        .then(res => res.text())
        .then(html => {
          document.getElementById('danh-sach-goi-y-ai').innerHTML = html;
        })
        .catch(err => {
          document.getElementById('danh-sach-goi-y-ai').innerHTML = "<div class='w-100 text-center py-4 text-muted'>Không thể tải gợi ý lúc này.</div>";
        });
    });

    // Xử lý khảo sát AI
    <?php if ($showSurvey): ?>
      $(document).ready(function() {
        (new bootstrap.Modal(document.getElementById('modalSurveyAI'))).show();
      });

      function submitSurvey(isSkip) {
        let selected = [];
        if (!isSkip) {
          document.querySelectorAll('.survey-checkbox:checked').forEach(cb => selected.push(cb.value));
        }
        fetch('../controllers/luuKhaoSatAI.php', {
          method: 'POST',
          body: JSON.stringify({
            categories: selected
          })
        }).then(() => window.location.reload());
      }
    <?php endif; ?>

    // GỌI API TÌM CỬA HÀNG THEO VỊ TRÍ
    var latDuPhong = <?= $userLat ?? 0 ?>;
    var lngDuPhong = <?= $userLng ?? 0 ?>;
    document.addEventListener("DOMContentLoaded", function() {
      requestLocation();
    });

    // =============================== PHẦN CODE CŨ CÓ HỎI SỬ DỤNG GPS HAY KHÔNG RỒI CHUYỂN QUA MẶC ĐỊNH ===============================
    // function requestLocation() {
    //   let statusDiv = document.getElementById('status-message');
    //   if (!statusDiv) return;

    //   statusDiv.style.display = 'block';
    //   statusDiv.className = 'alert alert-info text-center small';
    //   statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-info"></div> Đang xác định vị trí...';

    //   if (navigator.geolocation) {
    //     navigator.geolocation.getCurrentPosition(
    //       function(position) {
    //         statusDiv.className = 'alert alert-success text-center small shadow-sm';
    //         statusDiv.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Đang gợi ý cửa hàng theo vị trí hiện tại của bạn.';
    //         goiApiTimCuaHang(position.coords.latitude, position.coords.longitude);
    //         goiApiTimSanPhamGanNhat(position.coords.latitude, position.coords.longitude);
    //       },
    //       function(error) {
    //         if (latDuPhong != 0 && lngDuPhong != 0) {
    //           statusDiv.className = 'alert alert-info text-center small shadow-sm';
    //           statusDiv.innerHTML = '<i class="fa-solid fa-house-user"></i> Đang hiển thị cửa hàng quanh <b>địa chỉ mặc định</b> của bạn.';
    //           goiApiTimCuaHang(latDuPhong, lngDuPhong);
    //           goiApiTimSanPhamGanNhat(latDuPhong, lngDuPhong);
    //         } else {
    //           statusDiv.className = 'alert alert-warning text-center small shadow-sm';
    //           statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Vui lòng bật GPS hoặc thiết lập địa chỉ mặc định để tìm cửa hàng.';
    //         }
    //       }
    //     );
    //   } else {
    //     if (latDuPhong != 0 && lngDuPhong != 0) {
    //       statusDiv.className = 'alert alert-info text-center small shadow-sm';
    //       statusDiv.innerHTML = '<i class="fa-solid fa-house-user"></i> Đang hiển thị cửa hàng quanh <b>địa chỉ mặc định</b> của bạn.';
    //       goiApiTimCuaHang(latDuPhong, lngDuPhong);
    //       goiApiTimSanPhamGanNhat(latDuPhong, lngDuPhong);
    //     } else {
    //       statusDiv.className = 'alert alert-warning text-center small shadow-sm';
    //       statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Trình duyệt không hỗ trợ GPS. Vui lòng thiết lập địa chỉ mặc định.';
    //     }
    //   }
    // }

    // =============================== PHẦN CODE MỚI SỬ DỤNG THẲNG ĐỊA CHỈ MẶC ĐỊNH KHÔNG CÓ HỎI GPS ===============================
    function requestLocation() {
        let statusDiv = document.getElementById('status-message');
        if (!statusDiv) return;

        statusDiv.style.display = 'block';

        // Kiểm tra xem đã có tọa độ mặc định chưa (latDuPhong, lngDuPhong khác 0)
        if (typeof latDuPhong !== 'undefined' && typeof lngDuPhong !== 'undefined' && latDuPhong != 0 && lngDuPhong != 0) {
            
            // Hiện ngay thông báo thành công, không có bước "Đang xác định..."
            statusDiv.className = 'alert alert-success text-center small shadow-sm';
            statusDiv.innerHTML = '<i class="fa-solid fa-house-user"></i> Đang hiển thị sản phẩm quanh <b>địa chỉ mặc định</b> của bạn.';
            
            // Trực tiếp gọi API tìm kiếm
            goiApiTimCuaHang(latDuPhong, lngDuPhong);
            goiApiTimSanPhamGanNhat(latDuPhong, lngDuPhong);
            
        } else {
            // Trường hợp người dùng chưa thiết lập địa chỉ
            statusDiv.className = 'alert alert-warning text-center small shadow-sm';
            statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Vui lòng thiết lập địa chỉ trong hồ sơ để xem các cửa hàng và sản phẩm gần bạn.';
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
        .then(res => res.json()).then(data => {
          let html = '';
          let statusDiv = document.getElementById('status-message');
          let listDiv = document.getElementById('danh-sach-cua-hang');
          if (data.status === 'success') {
            if (data.data.length > 0) {
              data.data.forEach(shop => {
                html += `
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><b>${shop.TenCuaHang}</b></h5>
                                <p class="card-text text-muted small"><i class="fa-solid fa-location-dot"></i> ${shop.DiaChi}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-danger">Cách: ${shop.KhoangCachKm} km</span>
                                    <a href="chiTietNguoiBanController.php?IdTaiKhoan=${shop.IdTaiKhoan}" class="btn btn-sm btn-outline-danger">Ghé thăm</a>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } else {
              statusDiv.innerHTML = 'Không có cửa hàng nào trong bán kính 10km.';
            }
          } else {
            statusDiv.innerHTML = 'Lỗi hệ thống: ' + data.message;
          }
          listDiv.innerHTML = html;
        }).catch(err => {
          statusDiv.innerHTML = 'Đã xảy ra lỗi kết nối với máy chủ.';
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
        .then(res => res.json()).then(data => {
          let container = document.getElementById('danh-sach-sp-gan-nhat');
          if (!container) return;
          let html = '';
          if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(sp => {
              let hinhAnhURL = sp.HinhAnh ? sp.HinhAnh : 'assets/images/placeholder.png';
              let imgSrc = hinhAnhURL.startsWith('http') ? hinhAnhURL : '../' + hinhAnhURL;
              html += `<a href="chiTietSanPhamController.php?id=${sp.MaHH}" class="product-link"><div class="product-item"><div class="product-item-top"><img src="${imgSrc}" alt="${sp.TenHH}" style="height: 180px; width: 100%; object-fit: cover; border-radius: 8px 8px 0 0;"><div class="tieude-sanpham">${sp.TenHH}</div><div><small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn ${sp.KhoangCachKm}km</small></div><p class="text-muted small mb-2"><i class="fa-solid fa-store"></i> ${sp.TenCuaHang}</p></div><div class="product-item-bottom"><div class="gia-rating"><div class="rating"><span>5.0</span><i class="fa-solid fa-star text-warning"></i></div><div class="gia-san-pham"><span class="gia-giam">${sp.GiaFormat}</span></div></div></div></div></a>`;
            });
            html += `<a href="sanPhamGanBanController.php?lat=${lat}&lng=${lng}" class="product-link" style="display: flex; align-items: center; justify-content: center; min-width: 160px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-decoration: none; border: 1px dashed #dc3545; margin-right: 15px;"><div class="text-center text-danger p-3"><i class="fa-solid fa-circle-arrow-right fa-3x mb-3"></i><h6 class="mb-0 fw-bold">Xem tất cả<br>sản phẩm gần đây</h6></div></a>`;
          } else {
            html = '<div class="w-100 text-center text-muted py-4">Chưa có sản phẩm nào ở gần bạn.</div>';
          }
          container.innerHTML = html;
        });
    }
  </script>
</body>

</html>