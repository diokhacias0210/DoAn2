<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/chiTietSanPham.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css/sanPham.css" rel="stylesheet">
    <title>Chi tiết sản phẩm</title>

    <style>
        .tieu-de-san-pham {
            background: var(--bs-white);
            padding: 8px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .tieu-de-san-pham h2,
        .tieu-de-danh-muc h2 {
            margin: 0;
            padding: 0;
            padding-left: 32px;
            padding-right: 32px;
            color: var(--bs-pink-500);
            font-weight: 700;

            align-items: flex-end;
            /*canh dưới trái*/
        }

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
            text-decoration: none;
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
        <div class="chi-tiet-san-pham">
            <div class="tieude-chitietsanpham">
                <h2>CHI TIẾT SẢN PHẨM</h2>
                <button onclick="history.back()"><i class="fa-solid fa-angle-left"></i> Trở lại</button>
            </div>
            <div class="container-top">
                <div class="container-top-left">
                    <div class="image-gallery">
                        <div class="main-image">
                            <?php if (!empty($hinhAnhs)): ?>
                                <?php
                                $anhMain = $hinhAnhs[0]['URL'];
                                $imgSrcMain = (strpos($anhMain, 'http') === 0) ? $anhMain : '../' . $anhMain;
                                ?>
                                <img id="product-image" src="<?php echo $imgSrcMain; ?>" alt="<?php echo htmlspecialchars($chiTiet['TenHH']); ?>">
                            <?php else: ?>
                                <img id="product-image" src="../assets/images/placeholder.png" alt="Không có ảnh">
                            <?php endif; ?>
                        </div>
                        <div class="thumbnail-images">
                            <?php if (!empty($hinhAnhs)): ?>
                                <?php foreach ($hinhAnhs as $index => $img): ?>
                                    <?php
                                    $anhThumb = $img['URL'];
                                    $imgSrcThumb = (strpos($anhThumb, 'http') === 0) ? $anhThumb : '../' . $anhThumb;
                                    ?>
                                    <img src="<?php echo $imgSrcThumb; ?>"
                                        alt="ảnh <?php echo $index + 1; ?>"
                                        class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="phan-thong-tin">
                    <div class="phan-thong-tin-tren">
                        <h2 id="product-name"><?php echo htmlspecialchars($chiTiet['TenHH']); ?></h2>

                        <div class="rating">
                            <span><?php echo number_format($chiTiet['Rating'] ?? 0, 1); ?></span>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>

                    <div class="product-price">
                        <span class="new-price"><?php echo number_format($giaSauGiam, 0, ',', '.'); ?> VNĐ</span>
                        <?php if ($giamGia > 0): ?>
                            <span class="old-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VNĐ</span>
                            <span class="discount-badge">Giảm <?php echo number_format($giamGia, 0); ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="thong-tin-san-pham">
                        <?php if (!empty($chiTiet['GiaThiTruong']) && $chiTiet['GiaThiTruong'] > $chiTiet['Gia']): ?>
                            <p id="product-market-price">
                                <i class="fa-solid fa-tag"></i> Giá thị trường:
                                <span>
                                    ~ <?php echo number_format($chiTiet['GiaThiTruong'], 0, ',', '.'); ?> VNĐ
                                </span>
                            </p>
                        <?php endif; ?>
                        <p id="product-trademark"><i class="fa-solid fa-star"></i> Danh Mục: <span><?php echo htmlspecialchars($chiTiet['TenDM']); ?></span></p>
                        <p id="product-quantity"><i class="fa-solid fa-hourglass-half"></i> Số lượng còn: <span id="quantity-value"><?php echo $chiTiet['SoLuongHH']; ?></span></p>
                        <p id="product-condition"><i class="fa-solid fa-arrows-rotate"></i> Chất lượng hàng: <span><?php echo $chiTiet['ChatLuongHang']; ?></span></p>
                        <p id="product-contact"><i class="fa-solid fa-phone-flip"></i> Thời gian đăng: <span><?php echo date('d/m/Y', strtotime($chiTiet['NgayThem'])); ?></span></p>
                    </div>

                    <div class="phan-nut">
                        <div class="phan-nut-tren">
                            <div class="nut-them-vao-gio-hang">
                                <button <?php echo $nutThemGioHangClass; ?> data-mahh="<?php echo $chiTiet['MaHH']; ?>">Thêm vào giỏ hàng</button>
                            </div>
                            <div class="nut-mua-ngay">
                                <button <?php echo $nutMuaNgayClass; ?> data-mahh="<?php echo $chiTiet['MaHH']; ?>">Mua ngay</button>
                            </div>
                        </div>
                        <div class="phan-nut-duoi">
                            <div class="nut-yeu-thich">
                                <button id="save-favorite" onclick="toggleFavorite(this, <?php echo $chiTiet['MaHH']; ?>)">
                                    <i class="<?php echo $daYeuThich ? 'fa-solid' : 'fa-regular'; ?> fa-heart"
                                        style="<?php echo $daYeuThich ? 'color: red;' : ''; ?>"></i>
                                    <?php echo $daYeuThich ? 'Đã yêu thích' : 'Yêu thích'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-mid">
                <div class="nguoi-ban">
                    <div class="avatar-nguoi-ban">
                        <img src="../<?php echo $hoSoNguoiBan['Avatar'] ?? 'assets/images/user.png'; ?>" alt="avatar người bán">
                    </div>
                    <div class="thong-tin-nguoi-ban">
                        <h3><?php echo htmlspecialchars($hoSoNguoiBan['TenCuaHang'] ?? 'Người bán ẩn danh'); ?></h3>
                        <p><i class="fa-solid fa-location-dot"></i> Địa chỉ: <span><?php echo htmlspecialchars($hoSoNguoiBan['DiaChiKhoHang'] ?? 'Chưa cập nhật'); ?></span></p>
                    </div>
                    <div class="nut-xem-nguoi-ban">
                        <a href="chiTietNguoiBanController.php?IdTaiKhoan=<?php echo $chiTiet['IdNguoiBan']; ?>">
                            <button>Xem cửa hàng</button>
                        </a>
                    </div>

                    <div class="chat-voi-nguoi-ban">
                        <?php if (isset($_SESSION['IdTaiKhoan']) && $_SESSION['IdTaiKhoan'] == $chiTiet['IdNguoiBan']): ?>
                            <button disabled style="background:#ccc; cursor:not-allowed;">Bạn là người bán</button>
                        <?php else: ?>
                            <a href="chatController.php?MaHH=<?php echo $chiTiet['MaHH']; ?>">
                                <button class="..." onclick="window.location.href='../controllers/chatController.php?action=tao_phong&MaHH=<?php echo $chiTiet['MaHH']; ?>'">
                                    Chat với người bán
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>

                    <button onclick="moBaoCao('SanPham', <?= $chiTiet['IdNguoiBan'] ?>, <?= $chiTiet['MaHH'] ?>)" class="btn btn-sm btn-outline-danger nut-bao-cao">
                        <i class="fa-solid fa-flag"></i> Báo cáo sản phẩm này
                    </button>
                </div>
            </div>

            <div class="container-bot">
                <div class="thong-tin-them">
                    <h2>Thông tin thêm</h2>
                    <div class="product-description">
                        <?php echo nl2br($chiTiet['MoTa']); ?>
                    </div>
                </div>

                <div class="container-comment">
                    <h2>Đánh giá & Bình luận</h2>

                    <form class="comment-form" id="comment-form">
                        <div class="rating-select">
                            <label>Đánh giá:</label>
                            <i class="fa-regular fa-star" data-rating="1"></i>
                            <i class="fa-regular fa-star" data-rating="2"></i>
                            <i class="fa-regular fa-star" data-rating="3"></i>
                            <i class="fa-regular fa-star" data-rating="4"></i>
                            <i class="fa-regular fa-star" data-rating="5"></i>
                        </div>
                        <textarea placeholder="Viết bình luận của bạn..." name="noidung"></textarea>
                        <button type="submit">Gửi</button>
                    </form>

                    <div class="comment-list">
                        <?php if (!empty($binhLuans)): ?>
                            <?php foreach ($binhLuans as $bl): ?>
                                <div class="comment-item">
                                    <strong><?php echo htmlspecialchars($bl['TenTK']); ?></strong>
                                    <div class="rating">
                                        <?php
                                        $soSao = $bl['SoSao'] ?? 0;
                                        for ($i = 1; $i <= 5; $i++):
                                        ?>
                                            <i class="fa-<?php echo $i <= $soSao ? 'solid' : 'regular'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p><?php echo htmlspecialchars($bl['NoiDung']); ?></p>
                                    <small><?php echo date('d/m/Y H:i', strtotime($bl['NgayBL'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Chưa có bình luận nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="san-pham-moi">
                <div class="tieu-de-san-pham">
                    <h2><i class="fa-solid fa-wand-magic-sparkles"></i> CÓ THỂ BẠN SẼ THÍCH</h2>
                </div>
            </div>

            <div class="horizontal-scroll-wrapper mt-3">
                <?php
                $hasRecommendations = false;
                if (isset($_SESSION['IdTaiKhoan'])) {
                    $idKhachHang = $_SESSION['IdTaiKhoan'];
                    $maHHDangXem = $chiTiet['MaHH'] ?? 0; // ID sản phẩm hiện tại để tránh gợi ý trùng

                    $api_url = "http://127.0.0.1:5000/recommend?user_id=$idKhachHang&top_n=15";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    if ($response) {
                        $goi_y_list = json_decode($response, true);

                        if (!empty($goi_y_list) && !isset($goi_y_list['error'])) {
                            $hasRecommendations = true;
                            $ids = array_column($goi_y_list, 'id');
                            $ids_string = implode(',', $ids);

                            // Bổ sung: Lọc thêm MaHH != $maHHDangXem
                            $sql_ai = "SELECT hh.*, (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) as Anh 
                               FROM HangHoa hh 
                               WHERE MaHH IN ($ids_string) 
                               AND IdNguoiBan != $idKhachHang 
                               AND MaHH != $maHHDangXem
                               ORDER BY FIELD(MaHH, $ids_string)";
                            $result_ai = $conn->query($sql_ai);

                            $sanphams = [];
                            while ($row = $result_ai->fetch_assoc()) {
                                $sanphams[$row['MaHH']] = $row;
                            }

                            $demSP = 0;
                            foreach ($goi_y_list as $item) {
                                $sp = $sanphams[$item['id']] ?? null;
                                if (!$sp) continue;

                                if ($demSP >= 8) break;
                                $demSP++;

                                if (isset($item['reason']) && $item['reason'] == 'Trending') {
                                    $badgeHtml = '<div class="badge-match" style="background:#fd7e14;"><i class="fa-solid fa-fire"></i> Đang thịnh hành</div>';
                                } else {
                                    $badgeHtml = '<div class="badge-match">Phù hợp ' . $item['match'] . '%</div>';
                                }

                                $maHH_item = $sp['MaHH'];
                                $tenHH = htmlspecialchars($sp['TenHH']);

                                $anh = $sp['Anh'] ?? (isset($sp['URL']) ? $sp['URL'] : 'assets/images/placeholder.png');
                                $imgSrc = (strpos($anh, 'http') === 0) ? $anh : '../' . $anh;

                                $rating = isset($sp['Rating']) ? number_format((float)$sp['Rating'], 1) : "0.0";
                                $gia = number_format($sp['Gia'], 0, ',', '.');
                                $soLuong = isset($sp['SoLuongHH']) ? (int)$sp['SoLuongHH'] : 1;

                                $hetHang = $soLuong == 0;
                                $productClass = $hetHang ? "<div class='product-item het-hang' style='background: rgba(0, 0, 0, 0.1);'>" : "<div class='product-item'>";
                                $badgeHetHang = $hetHang ? "<div class='badge-het-hang'>Hết hàng</div>" : '';

                                if (!empty($sp['GiaTri']) && $sp['GiaTri'] > 0) {
                                    $giaGiamVal = $sp['Gia'] - ($sp['Gia'] * ($sp['GiaTri'] / 100));
                                    $giaGiam = number_format($giaGiamVal, 0, ',', '.');
                                    $giaHienThi = "<span class='gia-goc'>{$gia} đ</span><span class='gia-giam'>{$giaGiam} đ</span>";
                                } else {
                                    $giaHienThi = "<span class='gia-giam'>{$gia} đ</span>";
                                }
                ?>
                                <a href="chiTietSanPhamController.php?id=<?= $maHH_item ?>" class="product-link">
                                    <?= $productClass ?>
                                    <button class="btn-bo-qua shadow" onclick="boQuaSanPham(<?= $maHH_item ?>, this, event)" title="Bỏ qua / Không thích">
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
            </div>
            </a>
    <?php
                            }
                        }
                    }
                }

                // Dự phòng
                if (!$hasRecommendations) {
                    $maHHDangXem = $chiTiet['MaHH'] ?? 0;
                    $sql_new = "SELECT hh.*, (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) as Anh 
                        FROM HangHoa hh WHERE TrangThaiDuyet = 'DaDuyet' AND MaHH != $maHHDangXem ORDER BY NgayThem DESC LIMIT 8";
                    $result_new = $conn->query($sql_new);
                    while ($sp = $result_new->fetch_assoc()) {
                        $maHH_item = $sp['MaHH'];
                        $tenHH = htmlspecialchars($sp['TenHH']);
                        $anh = $sp['Anh'] ?? 'assets/images/placeholder.png';
                        $imgSrc = (strpos($anh, 'http') === 0) ? $anh : '../' . $anh;
                        $gia = number_format($sp['Gia'], 0, ',', '.');
    ?>
    <a href="chiTietSanPhamController.php?id=<?= $maHH_item ?>" class="product-link">
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
    </div>

    <div class="modal fade" id="modalBaoCao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formBaoCao">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation"></i> Báo cáo vi phạm</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="bc_idBiBaoCao" name="idBiBaoCao">
                        <input type="hidden" id="bc_maHH" name="maHH">
                        <input type="hidden" id="bc_loaiBaoCao" name="loaiBaoCao">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn lý do chính:</label>
                            <select class="form-select" id="bc_lyDoChinh" name="lyDoChinh" required>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả chi tiết (Tùy chọn):</label>
                            <textarea class="form-control" name="chiTiet" rows="3" placeholder="Nhập thêm thông tin để admin dễ xác minh..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../assets/js/js.js"></script>
    <script src="../assets/js/yeuThich.js"></script>
    <script src="../assets/js/nutThemGioHang_muaNgay.js"></script>
    <script src="../assets/js/danhGia.js"></script>

    <script>
        // Logic mở Modal Báo Cáo
        function moBaoCao(loai, idDoiTuong, maHH = '') {
            document.getElementById('bc_loaiBaoCao').value = loai;
            document.getElementById('bc_idBiBaoCao').value = idDoiTuong;
            document.getElementById('bc_maHH').value = maHH;

            const selectLyDo = document.getElementById('bc_lyDoChinh');
            selectLyDo.innerHTML = '';
            let options = [];
            if (loai === 'SanPham') {
                options = ['Lừa đảo', 'Hàng giả/nhái', 'Thông tin không đúng thực tế', 'Trùng lặp', 'Lý do khác'];
            } else {
                options = ['Người dùng có dấu hiệu lừa đảo', 'Thông tin cá nhân sai phạm', 'Ngôn ngữ đả kích/phản cảm', 'Lý do khác'];
            }

            options.forEach(opt => {
                selectLyDo.innerHTML += `<option value="${opt}">${opt}</option>`;
            });

            new bootstrap.Modal(document.getElementById('modalBaoCao')).show();
        }

        // Logic gửi form AJAX
        document.getElementById('formBaoCao').addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = this.querySelector('button[type="submit"]');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Đang gửi...';

            const formData = new FormData(this);

            fetch('../controllers/baoCaoController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalBaoCao')).hide();
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Gửi báo cáo';
                });
        });

        // GỌI TỌA ĐỘ TÌM SẢN PHẨM GẦN BẠN BẰNG JAVASCRIPT
        document.addEventListener("DOMContentLoaded", function() {
            if (document.getElementById('danh-sach-sp-gan-nhat') && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    let formData = new FormData();
                    formData.append('lat', position.coords.latitude);
                    formData.append('lng', position.coords.longitude);
                    fetch('../controllers/ajaxTimSanPhamGanNhat.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            let container = document.getElementById('danh-sach-sp-gan-nhat');
                            if (data.length > 0) {
                                let html = data.map(sp => {
                                    let hinhAnhURL = sp.HinhAnh ? sp.HinhAnh : 'assets/images/placeholder.png';
                                    let imgSrc = hinhAnhURL.startsWith('http') ? hinhAnhURL : '../' + hinhAnhURL;

                                    return `
                                <a href='chiTietSanPhamController.php?id=${sp.MaHH}' class='product-link'>
                                  <div class='product-item'>
                                    <div class='product-item-top'>
                                      <img src='${imgSrc}' alt='${sp.TenHH}' loading='lazy' style='height: 180px; width: 100%; object-fit: cover; border-radius: 8px 8px 0 0;'>
                                      <div class='tieude-sanpham'>${sp.TenHH}</div>
                                    </div>
                                    <div class='product-item-bottom'>
                                      <div class='gia-rating'>
                                        <div class='rating'><i class='fa-solid fa-location-arrow'></i> <span style="font-size:12px;">${sp.KhoangCachKm} km</span></div>
                                        <div class='gia-san-pham'><span class='gia-giam'>${sp.GiaFormat}</span></div>
                                      </div>
                                    </div>
                                  </div>
                                </a>
                            `
                                }).join('');
                                container.innerHTML = `<div class="horizontal-scroll-wrapper" style="width: 100%;">${html}</div>`;
                            } else {
                                container.innerHTML = "<div class='w-100 text-center py-4 text-muted'>Không có sản phẩm nào ở gần vị trí của bạn.</div>";
                            }
                        });
                });
            }
        });

        // Xử lý nút bỏ qua sản phẩm
        function boQuaSanPham(mahh, btnElement, event) {
            event.preventDefault();
            $(btnElement).closest('.product-link').fadeOut(300);
            $.post('boQuaGoiY.php', {
                mahh: mahh
            }, function(response) {
                console.log("Đã loại bỏ sản phẩm khỏi gợi ý.");
            });
        }
    </script>
</body>

</html>