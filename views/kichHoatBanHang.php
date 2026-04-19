<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Kênh Người Bán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css/kichHoatBanHang.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid giua-trang">
        <div class="form-dang-ky">
            <div class="text-center mb-4">
                <i class="fa-solid fa-store icon-header"></i>
                <h3 class="title-header">ĐĂNG KÝ NGƯỜI BÁN</h3>
                <p class="text-muted">Cung cấp thông tin để bắt đầu bán hàng ngay hôm nay</p>
            </div>
            
            <form action="../controllers/kichHoatBanHangController.php" method="POST">
                <input type="hidden" name="action" value="kich_hoat">
                
                <div class="section-title">1. Thông tin cửa hàng</div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên cửa hàng <span class="text-danger">*</span></label>
                    <input type="text" name="TenCuaHang" class="form-control" required placeholder="Nhập tên cửa hàng của bạn">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Số CCCD / CMND <span class="text-danger">*</span></label>
                    <input type="text" name="SoCCCD" class="form-control" placeholder="Nhập đúng số CCCD/CMND của bạn">
                </div>

                <!-- khung hiển thị Bản đồ trong Form -->
                <div class="mb-3">
                    <label for="DiaChiKhoHang" class="form-label fw-bold">Địa chỉ kho hàng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="DiaChiKhoHang" name="DiaChiKhoHang" placeholder="Nhập địa chỉ, ví dụ: Ninh Kiều, Cần Thơ" required>
                    
                    <input type="hidden" id="ViDo" name="ViDo">
                    <input type="hidden" id="KinhDo" name="KinhDo">
                    
                    <button type="button" class="btn btn-sm btn-secondary mt-2 mb-2" id="btnTimBanDo">
                        <i class="fa-solid fa-location-dot"></i> Định vị trên bản đồ
                    </button>
                    <div id="map" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #ddd; display: none;"></div>
                </div>

                <div class="section-title">2. Thông tin thanh toán (Nhận tiền)</div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên ngân hàng <span class="text-danger">*</span></label>
                    <input type="text" name="TenNganHang" class="form-control" required placeholder="Ví dụ: Vietcombank, MB Bank, Agribank...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Số tài khoản <span class="text-danger">*</span></label>
                    <input type="text" name="SoTaiKhoanNganHang" class="form-control" required placeholder="Nhập số tài khoản ngân hàng">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Tên chủ tài khoản <span class="text-danger">*</span></label>
                    <input type="text" name="TenChuTaiKhoan" class="form-control" required placeholder="VIẾT HOA KHÔNG DẤU (VD: NGUYEN VAN A)">
                </div>

                <div class="d-grid gap-3 mt-5">
                    <button type="submit" class="btn btn-xac-nhan btn-lg">
                        Hoàn tất đăng ký
                    </button>
                    <a href="../controllers/thongTinTaiKhoanController.php" class="btn btn-huy btn-lg">Hủy và quay lại</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Khởi tạo biến bản đồ và marker (ghim)
        let map;
        let marker;

        document.getElementById('btnTimBanDo').addEventListener('click', function() {
            const address = document.getElementById('DiaChiKhoHang').value.trim();
            if (address === '') {
                alert('Vui lòng nhập địa chỉ trước khi định vị!');
                return;
            }

            // Hiện khung bản đồ
            document.getElementById('map').style.display = 'block';

            // Khởi tạo bản đồ nếu chưa có
            if (!map) {
                map = L.map('map').setView([14.0583, 108.2772], 5); // Trung tâm VN
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
            }

            //  Dùng API để dịch Địa chỉ -> Tọa độ
            const searchUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address + ', Vietnam')}`;

            fetch(searchUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;

                        // Lưu tọa độ lúc mới tìm thấy vào form ẩn
                        document.getElementById('ViDo').value = lat;
                        document.getElementById('KinhDo').value = lon;

                        map.setView([lat, lon], 16); // Zoom gần hơn một chút (mức 16)

                        // Nếu đã có ghim cũ trên bản đồ thì xóa đi
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        
                        // TẠO GHIM MỚI VÀ CHO PHÉP KÉO THẢ (draggable: true)
                        marker = L.marker([lat, lon], { draggable: true }).addTo(map);
                        marker.bindPopup(`<b>Vị trí ban đầu:</b><br>${data[0].display_name}`).openPopup();

                        //  BẮT SỰ KIỆN KHI NGƯỜI DÙNG KÉO THẢ GHIM 
                        marker.on('dragend', function(event) {
                            const position = marker.getLatLng();
                            
                            // BỔ SUNG DÒNG NÀY: Cập nhật lại tọa độ mới khi thả ghim
                            document.getElementById('ViDo').value = position.lat;
                            document.getElementById('KinhDo').value = position.lng;
                            
                            // Tạo hiệu ứng loading cho ô input để người dùng biết hệ thống đang xử lý
                            const inputDiaChi = document.getElementById('DiaChiKhoHang');
                            inputDiaChi.value = 'Đang dịch tọa độ thành địa chỉ...';

                            // Dùng API Reverse Geocoding để dịch ngược Tọa độ -> Địa chỉ mới
                            const reverseUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.lat}&lon=${position.lng}&accept-language=vi`; // Ưu tiên tiếng Việt

                            fetch(reverseUrl)
                                .then(res => res.json())
                                .then(reverseData => {
                                    if (reverseData && reverseData.display_name) {
                                        // Cập nhật lại tên đường/phường/quận vào ô input
                                        inputDiaChi.value = reverseData.display_name;
                                        // Cập nhật lại bóng thoại trên ghim
                                        marker.bindPopup(`<b>Vị trí đã chọn:</b><br>${reverseData.display_name}`).openPopup();
                                    } else {
                                        inputDiaChi.value = 'Không lấy được thông tin địa chỉ tại điểm này.';
                                    }
                                })
                                .catch(err => {
                                    console.error('Lỗi dịch ngược địa chỉ:', err);
                                    inputDiaChi.value = 'Có lỗi xảy ra khi lấy tên đường.';
                                });
                        });
                        // KẾT THÚC SỰ KIỆN KÉO THẢ 

                    } else {
                        alert('Không tìm thấy địa chỉ này trên bản đồ. Vui lòng nhập chi tiết hơn (ví dụ thêm Phường, Quận, Thành phố)!');
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tìm vị trí:', error);
                    alert('Có lỗi xảy ra khi kết nối với bản đồ.');
                });
        });
    </script>
</body>
</html>