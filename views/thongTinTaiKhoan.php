
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/thongTinTaiKhoan.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="container">
            <?php
            $soThongBaoMoi_Sidebar = 0;
            if (isset($_SESSION['IdTaiKhoan'])) {
                if (!class_exists('ThongBaoModel')) {
                    require_once __DIR__ . '/../models/thongBaoModel.php';
                }
                global $conn;
                if ($conn) {
                    $modelTB_Sidebar = new ThongBaoModel($conn);
                    $soThongBaoMoi_Sidebar = $modelTB_Sidebar->demThongBaoChuaDoc($_SESSION['IdTaiKhoan']);
                }
            }
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>
            <div class="side">
                <div class="avatar-ten" style="text-align: center; margin-bottom: 20px;">
                    <div class="avatar" style="width: 100px; height: 100px; margin: 0 auto 10px; border-radius: 50%; border: 2px solid var(--bs-pink-200); padding: 3px; display: flex; justify-content: center; align-items: center;">
                        <img src="../<?php echo isset($_SESSION['Avatar']) && !empty($_SESSION['Avatar']) ? $_SESSION['Avatar'] : 'assets/images/placeholder.png'; ?>"
                            alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    </div>
                </div>

                <nav class="menu">
                    <ul>
                        <li>
                            <a href="../controllers/thongTinTaiKhoanController.php" class="<?= ($current_page == 'thongTinTaiKhoanController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-id-badge"></i> Quản lý tài khoản
                            </a>
                        </li>
                        <li>
                            <a href="../controllers/thongBaoController.php" class="<?= ($current_page == 'thongBaoController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-bell"></i> Thông báo hệ thống
                                <?php if ($soThongBaoMoi_Sidebar > 0): ?>
                                    <span class="menu-badge-count"><?= $soThongBaoMoi_Sidebar ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="../controllers/gioHangController.php" class="<?= ($current_page == 'gioHangController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của tôi
                            </a>
                        </li>
                        <li>
                            <a href="../controllers/danhSachYeuThichController.php" class="<?= ($current_page == 'danhSachYeuThichController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-heart"></i> Sản phẩm yêu thích
                            </a>
                        </li>
                        <li>
                            <a href="../controllers/lichSuDonHangController.php" class="<?= ($current_page == 'lichSuDonHangController.php') ? 'active' : '' ?>">
                                <i class="fa-solid fa-clipboard-list"></i> Lịch sử giao dịch
                            </a>
                        </li>
                        <li>
                            <?php 
                            $trangThai = 'ChuaKichHoat'; 
                            if (isset($_SESSION['IdTaiKhoan']) && isset($conn)) {
                                $idUser_check = (int)$_SESSION['IdTaiKhoan'];
                                $sql_check = "SELECT TrangThaiBanHang FROM TaiKhoan WHERE IdTaiKhoan = $idUser_check";
                                $res_check = $conn->query($sql_check);
                                if ($res_check && $res_check->num_rows > 0) {
                                    $trangThai = $res_check->fetch_assoc()['TrangThaiBanHang'];
                                }
                            }
                            if ($trangThai === 'DangHoatDong'): 
                            ?>
                                <a href="../seller/controllers/sellerSanPhamController.php" class="btn btn-warning">
                                    <i class="fa-solid fa-store"></i> Kênh người bán
                                </a>
                            <?php else: ?>
                                <a href="kichHoatBanHangController.php" class="btn">
                                    <i class="fa-solid fa-rocket"></i> Kích hoạt chức năng bán hàng
                                </a>
                            <?php endif; ?>
                        </li>
                        <li>
                            <a href="../controllers/dangXuatController.php" class="logout-link">
                                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <main class="content">
                <h1><i class='bx bx-user'></i> TÀI KHOẢN CỦA TÔI</h1>

                <?php
                $latUser = 10.0299; 
                $lngUser = 105.7706;

                if (isset($_SESSION['IdTaiKhoan']) && isset($conn)) {
                    $idTK = $_SESSION['IdTaiKhoan'];
                    $sqlToaDo = "SELECT ViDo, KinhDo FROM TaiKhoan WHERE IdTaiKhoan = $idTK";
                    $kqToaDo = $conn->query($sqlToaDo);
                    if ($kqToaDo && $kqToaDo->num_rows > 0) {
                        $rowToaDo = $kqToaDo->fetch_assoc();
                        if (!empty($rowToaDo['ViDo']) && !empty($rowToaDo['KinhDo'])) {
                            $latUser = $rowToaDo['ViDo'];
                            $lngUser = $rowToaDo['KinhDo'];
                        }
                    }
                }

                $trangThaiMenu = 'ChuaKichHoat';
                if (isset($_SESSION['IdTaiKhoan']) && isset($conn)) {
                    $idUser_menu = (int)$_SESSION['IdTaiKhoan'];
                    $sql_menu = "SELECT TrangThaiBanHang FROM TaiKhoan WHERE IdTaiKhoan = $idUser_menu";
                    $res_menu = $conn->query($sql_menu);
                    if ($res_menu && $res_menu->num_rows > 0) {
                        $trangThaiMenu = $res_menu->fetch_assoc()['TrangThaiBanHang'];
                    }
                }
                if ($trangThaiMenu === 'DangHoatDong'): 
                ?>
                    <li>
                        <a href="#" onclick="xacNhanHuyBanHang(); return false;" style="color: #dc3545;">
                            <i class='bx bx-store-alt'></i> Hủy kích hoạt bán hàng
                        </a>
                        <form id="form-huy-ban-hang" action="banHangController.php" method="POST" style="display: none;">
                            <input type="hidden" name="action" value="huy">
                        </form>
                        <script>
                            function xacNhanHuyBanHang() {
                                if (confirm("Bạn có chắc chắn muốn hủy chức năng bán hàng không? Hệ thống sẽ thu hồi quyền bán hàng của bạn.")) {
                                    document.getElementById('form-huy-ban-hang').submit();
                                }
                            }
                        </script>
                    </li>
                <?php endif; ?>

                <div id="alert-container">
                    <?php if (!empty($message)) echo $message; ?>
                </div>

                <div class="form-thongtintaikhoan">
                    <div class="form-group">
                        <label><i class='bx bx-id-card'></i> Họ và Tên</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['TenTK'] ?? ''); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label><i class='bx bxs-phone'></i> Số điện thoại</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" value="<?php echo htmlspecialchars($user['Sdt'] ?? 'Chưa cập nhật'); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class='bx bxs-envelope'></i> Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label><i class='bx bx-home'></i> Địa chỉ</label>
                        <div class="d-flex gap-2">
                            <select id="diachi-select" name="MaDC" class="form-select">
                                <?php if (empty($addresses)): ?>
                                    <option value="">Bạn chưa có địa chỉ nào.</option>
                                <?php else: ?>
                                    <?php foreach ($addresses as $addr): ?>
                                        <option value="<?php echo $addr['MaDC']; ?>" 
                                                data-lat="<?php echo $addr['ViDo'] ?? ''; ?>" 
                                                data-lng="<?php echo $addr['KinhDo'] ?? ''; ?>"
                                                <?php echo $addr['MacDinh'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($addr['DiaChiChiTiet']); ?>
                                            <?php echo $addr['MacDinh'] ? ' (Mặc định)' : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            
                            <button type="button" class="btn btn-outline-primary text-nowrap" onclick="bamNutDatMacDinh()">
                                Đặt làm mặc định
                            </button>
                        </div>
                    </div>

                    <div class="address-buttons mt-3 mb-2">
                        <?php if ($address_count < 5): ?>
                            <button type="button" id="btn-them-diachi" class="btn btn-sm btn-success"><i class="fa-solid fa-plus"></i> Thêm địa chỉ mới</button>
                        <?php else: ?>
                            <p class="text-muted">Bạn đã đạt số lượng địa chỉ tối đa.</p>
                        <?php endif; ?>

                        <?php if ($address_count > 0): ?>
                            <button type="button" id="btn-xoa-diachi" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Xóa địa chỉ</button>
                        <?php endif; ?>
                    </div>
                    
                    <form id="form-them-diachi" action="thongTinTaiKhoanController.php" method="POST" style="display: none; margin-top: 15px; padding: 15px; border: 1px solid #ccc; border-radius: 8px;">
                        <input type="hidden" name="action" value="them_diachi">
                        
                        <label class="fw-bold mb-2">Nhập địa chỉ mới (Vui lòng chọn trên bản đồ hoặc gõ vào đây):</label>
                        <input type="text" name="diachi_moi" class="form-control" placeholder="Nhập địa chỉ...">
                        
                        <input type="hidden" id="ViDo_moi" name="ViDo_moi">
                        <input type="hidden" id="KinhDo_moi" name="KinhDo_moi">
                        
                        <button type="submit" class="btn btn-primary mt-3">Lưu địa chỉ</button>
                    </form>

                    <div class="mt-4 mb-4">
                        <h6 class="text-primary"><i class="fa-solid fa-map-location-dot"></i> Vị trí trên bản đồ</h6>
                        <div id="mapThongTin" style="height: 300px; width: 100%; border-radius: 12px; border: 1px solid #ddd; z-index: 1;"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // HÀM XỬ LÝ ĐẶT ĐỊA CHỈ MẶC ĐỊNH
        function bamNutDatMacDinh() {
            let selectTag = document.getElementById('diachi-select');
            let maDC = selectTag.value;
            
            if (!maDC) {
                alert("Vui lòng chọn một địa chỉ!");
                return;
            }

            let form = document.createElement('form');
            form.method = 'POST';
            form.action = 'thongTinTaiKhoanController.php'; 

            let actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'dat_mac_dinh';
            form.appendChild(actionInput);

            let maDCInput = document.createElement('input');
            maDCInput.type = 'hidden';
            maDCInput.name = 'MaDC';
            maDCInput.value = maDC;
            form.appendChild(maDCInput);

            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // XỬ LÝ NÚT THÊM / XÓA ĐỊA CHỈ
            const btnThem = document.getElementById('btn-them-diachi');
            const formThem = document.getElementById('form-them-diachi');
            const btnXoa = document.getElementById('btn-xoa-diachi');
            const diachiSelect = document.getElementById('diachi-select');

            if (btnThem) {
                btnThem.addEventListener('click', function() {
                    if (formThem.style.display === 'none' || formThem.style.display === '') {
                        formThem.style.display = 'block';
                    } else {
                        formThem.style.display = 'none';
                    }
                });
            }

            if (btnXoa) {
                btnXoa.addEventListener('click', function() {
                    if (!diachiSelect.value) {
                        alert("Không có địa chỉ nào để xóa!");
                        return;
                    }
                    const confirmation = window.confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');
                    if (confirmation) {
                        const deleteForm = document.createElement('form');
                        deleteForm.method = 'POST';
                        deleteForm.action = 'thongTinTaiKhoanController.php';

                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'xoa_diachi';
                        deleteForm.appendChild(actionInput);

                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'MaDC_xoa';
                        idInput.value = diachiSelect.value;
                        deleteForm.appendChild(idInput);

                        document.body.appendChild(deleteForm);
                        deleteForm.submit();
                    }
                });
            }

            // ẨN THÔNG BÁO SAU 3 GIÂY
            const alertContainer = document.getElementById('alert-container');
            if (alertContainer && alertContainer.children.length > 0) {
                const alerts = alertContainer.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.classList.add('hide');
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    }, 3000);
                });
            }

            // KÍCH HOẠT BÁN HÀNG
            const btnMoForm = document.getElementById('btn-mo-form-kich-hoat');
            const formKichHoat = document.getElementById('form-kich-hoat-ban-hang');
            const btnHuy = document.getElementById('btn-huy-kich-hoat');

            if (btnMoForm) {
                btnMoForm.addEventListener('click', function () {
                    formKichHoat.style.display = 'block';
                    btnMoForm.style.display = 'none'; 
                });
            }

            if (btnHuy) {
                btnHuy.addEventListener('click', function () {
                    formKichHoat.style.display = 'none';
                    btnMoForm.style.display = 'inline-block';
                });
            }

            // KHỞI TẠO BẢN ĐỒ VÀ XỬ LÝ TỌA ĐỘ
            let latDefault = <?= $latUser ?>;
            let lngDefault = <?= $lngUser ?>;

            let mapTT = L.map('mapThongTin').setView([latDefault, lngDefault], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapTT);
            let markerTT = L.marker([latDefault, lngDefault], {draggable: true}).addTo(mapTT);
            
            setTimeout(function(){ mapTT.invalidateSize(); }, 500);

            let inputDiaChiMoi = document.querySelector('input[name="diachi_moi"]');
            let selectDiaChi = document.getElementById('diachi-select'); 

            function capNhatBanDo(diaChiText) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(diaChiText)}`)
                .then(res => res.json())
                .then(data => {
                    if(data.length > 0) {
                        mapTT.setView([data[0].lat, data[0].lon], 16);
                        markerTT.setLatLng([data[0].lat, data[0].lon]);
                        
                        let viDoMoi = document.getElementById('ViDo_moi');
                        let kinhDoMoi = document.getElementById('KinhDo_moi');
                        if(viDoMoi) viDoMoi.value = data[0].lat;
                        if(kinhDoMoi) kinhDoMoi.value = data[0].lon;
                    }
                });
            }

            if (selectDiaChi) {
                selectDiaChi.addEventListener('change', function() {
                    let selectedOption = this.options[this.selectedIndex];
                    let lat = selectedOption.getAttribute('data-lat');
                    let lng = selectedOption.getAttribute('data-lng');
                    
                    if (lat && lng && lat !== '' && lng !== '') {
                        mapTT.setView([lat, lng], 16);
                        markerTT.setLatLng([lat, lng]);
                    } else {
                        let text = selectedOption.text.replace('(Mặc định)', '').trim();
                        if(text !== 'Bạn chưa có địa chỉ nào.') {
                            capNhatBanDo(text);
                        }
                    }
                });
                selectDiaChi.dispatchEvent(new Event('change'));
            }

            if(inputDiaChiMoi) {
                let typingTimer;
                inputDiaChiMoi.addEventListener('keyup', function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        if(this.value.trim() !== '') capNhatBanDo(this.value);
                    }, 1000); 
                });
            }

            markerTT.on('dragend', function() {
                let latlng = markerTT.getLatLng();
                
                let viDoMoi = document.getElementById('ViDo_moi');
                let kinhDoMoi = document.getElementById('KinhDo_moi');
                if(viDoMoi) viDoMoi.value = latlng.lat;
                if(kinhDoMoi) kinhDoMoi.value = latlng.lng;

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
                .then(res => res.json())
                .then(data => {
                    if(data && data.display_name && inputDiaChiMoi) {
                        inputDiaChiMoi.value = data.display_name;
                    }
                });
            });
        });
    </script>
</body>
</html>