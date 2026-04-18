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
      width: 280px;
      scroll-snap-align: start;
      text-decoration: none;
      /* Tránh gạch chân text */
      position: relative;
    }

    .horizontal-scroll-wrapper .product-item {
      height: 100%;
      min-height: 120px;
      position: relative;
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

    <div class="san-pham-moi">
      <div class="tieu-de-san-pham">
        <h2><i class="fa-solid fa-minus"></i> CỬA HÀNG GẦN BẠN</h2>
      </div>

      <div class="container-fluid mt-3">
        <div id="status-message" class="alert alert-info text-center shadow-sm" style="display: none;"></div>
      </div>

      <div class="horizontal-scroll-wrapper mt-2" id="danh-sach-cua-hang">
      </div>
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

      <div class="horizontal-scroll-wrapper mt-3">
        <?php
        $hasRecommendations = false;
        if (isset($_SESSION['IdTaiKhoan'])) {
          $idKhachHang = $_SESSION['IdTaiKhoan'];

          // Do Python đã tự động lọc hàng tồn kho & loại bỏ đồ tự bán, ta chỉ cần gọi top_n=8
          $api_url = "http://127.0.0.1:5000/recommend?user_id=$idKhachHang&top_n=8";

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
                               AND IdNguoiBan != $idKhachHang
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

                $anh = $sp['Anh'] ?? (isset($sp['URL']) ? $sp['URL'] : 'assets/images/placeholder.png');
                $imgSrc = (strpos($anh, 'http') === 0) ? $anh : '../' . $anh;

                $rating = isset($sp['Rating']) ? number_format((float)$sp['Rating'], 1) : "0.0";
                $gia = number_format($sp['Gia'], 0, ',', '.');

                // Mọi đồ hiển thị đều đã được Python kiểm duyệt là "Còn hàng", không lo bị khuyết.
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

                    <div class='product-item-top'>
                      <img src='<?= $imgSrc ?>' alt='<?= $tenHH ?>' loading='lazy' style='height: 180px; width: 100%; object-fit: cover; border-radius: 8px 8px 0 0;'>
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
                  </div>
                </a>
            <?php
              }
            }
          }
        }

        // LUỒNG DỰ PHÒNG NẾU KHÁCH CHƯA ĐĂNG NHẬP (Hiện Mới Nhất)
        if (!$hasRecommendations) {
          $idCheck = isset($_SESSION['IdTaiKhoan']) ? $_SESSION['IdTaiKhoan'] : 0;
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

  <?php if (isset($_SESSION['IdTaiKhoan'])): ?>
    <script>
      var latDuPhong = <?= $userLat ? $userLat : 0; ?>;
      var lngDuPhong = <?= $userLng ? $userLng : 0; ?>;

      document.addEventListener("DOMContentLoaded", function() {
        requestLocation();
      });

      function requestLocation() {
        let statusDiv = document.getElementById('status-message');
        if (!statusDiv) return;

        statusDiv.style.display = 'block';
        statusDiv.className = 'alert alert-info text-center small';
        statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-info"></div> Đang xác định vị trí...';

        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            function(position) {
              statusDiv.className = 'alert alert-success text-center small shadow-sm';
              statusDiv.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Đang gợi ý cửa hàng theo vị trí hiện tại của bạn.';
              goiApiTimCuaHang(position.coords.latitude, position.coords.longitude);
              goiApiTimSanPhamGanNhat(position.coords.latitude, position.coords.longitude);
            },
            function(error) {
              if (latDuPhong != 0 && lngDuPhong != 0) {
                statusDiv.className = 'alert alert-info text-center small shadow-sm';
                statusDiv.innerHTML = '<i class="fa-solid fa-house-user"></i> Đang hiển thị cửa hàng quanh <b>địa chỉ mặc định</b> của bạn.';
                goiApiTimCuaHang(latDuPhong, lngDuPhong);
                goiApiTimSanPhamGanNhat(latDuPhong, lngDuPhong);
              } else {
                statusDiv.className = 'alert alert-warning text-center small shadow-sm';
                statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Vui lòng bật GPS hoặc thiết lập địa chỉ mặc định để tìm cửa hàng.';
              }
            }
          );
        } else {
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
                data.data.forEach(shop => {
                  html += `
                    <a href="cuaHangController.php?id=${shop.IdTaiKhoan}" class="product-link">
                        <div class="product-item p-3 d-flex flex-column justify-content-between h-100" style="border: 1px solid #eee; background: #fff; border-radius: 12px;">
                            <div>
                                <h5 class="text-primary text-truncate mb-2" title="${shop.TenCuaHang}">
                                    <i class="fa-solid fa-store"></i> <b>${shop.TenCuaHang}</b>
                                </h5>
                                <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <i class="fa-solid fa-location-dot"></i> ${shop.DiaChi}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2" style="border-top: 1px dashed #ddd;">
                                <span class="badge bg-light text-danger fw-bold"><i class="fa-solid fa-route"></i> ${shop.KhoangCachKm} km</span>
                                <span class="btn btn-sm btn-outline-danger" style="font-size: 12px;">Ghé thăm</span>
                            </div>
                        </div>
                    </a>`;
                });
              } else {
                statusDiv.className = "alert alert-secondary text-center shadow-sm";
                statusDiv.innerHTML = '<i class="fa-regular fa-face-frown"></i> Không có cửa hàng nào trong bán kính 10km quanh vị trí này.';
              }
            } else {
              statusDiv.className = "alert alert-danger text-center shadow-sm";
              statusDiv.innerHTML = '<i class="fa-solid fa-bug"></i> Lỗi hệ thống: ' + data.message;
            }
            listDiv.innerHTML = html;
          })
          .catch(err => {
            let statusDiv = document.getElementById('status-message');
            statusDiv.className = "alert alert-danger text-center shadow-sm";
            statusDiv.innerHTML = '<i class="fa-solid fa-link-slash"></i> Đã xảy ra lỗi kết nối với máy chủ.';
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
              data.data.forEach(sp => {
                let hinhAnhURL = sp.HinhAnh ? sp.HinhAnh : 'assets/images/placeholder.png';
                let imgSrc = hinhAnhURL.startsWith('http') ? hinhAnhURL : '../' + hinhAnhURL;

                html += `
                        <a href="chiTietSanPhamController.php?id=${sp.MaHH}" class="product-link">
                          <div class="product-item">
                            <div class="product-item-top">
                              <img src="${imgSrc}" alt="${sp.TenHH}" style="height: 180px; width: 100%; object-fit: cover; border-radius: 8px 8px 0 0;">
                              
                              <div class="tieude-sanpham">${sp.TenHH}</div>
                              <div>
                                  <small class="text-success"><i class="fa-solid fa-location-arrow"></i> Cách bạn ${sp.KhoangCachKm}km</small>
                              </div>
                              <p class="text-muted small mb-2"><i class="fa-solid fa-store"></i> ${sp.TenCuaHang}</p>
                            </div>
                            <div class="product-item-bottom">
                              <div class="gia-rating">
                                <div class="rating"><span>5.0</span><i class="fa-solid fa-star text-warning"></i></div>
                                <div class="gia-san-pham">
                                  <span class="gia-giam">${sp.GiaFormat}</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                    `;
              });
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
          });
      }
    </script>
  <?php else: ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        let statusDiv = document.getElementById('status-message');
        if (statusDiv) {
          statusDiv.style.display = "block";
          statusDiv.className = "alert alert-light text-center border shadow-sm";
          statusDiv.innerHTML = '<i class="fa-solid fa-user-lock text-danger"></i> <a href="dangNhapController.php" class="text-danger fw-bold text-decoration-none">Đăng nhập</a> để xem gợi ý các cửa hàng gần bạn nhất.';
        }
      });
    </script>
  <?php endif; ?>
</body>

</html>