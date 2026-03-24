<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kênh người bán - Tin nhắn</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/header.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">

    <style>
        /* --- ĐỒNG BỘ KHUNG SƯỜN --- */
        .seller-wrapper {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .seller-content-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            min-height: 600px;
        }

        /* --- MENU BÊN TRÁI (SIDEBAR) --- */
        .seller-sidebar {
            background: #ffffff;
            border-radius: 10px;
            padding: 15px 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .seller-sidebar a {
            text-decoration: none;
            color: #555;
            font-weight: 600;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .seller-sidebar a:hover {
            background-color: #f8f9fa;
            color: var(--bs-pink-500);
        }

        .seller-sidebar a.active {
            background-color: var(--bs-pink-100);
            color: var(--bs-pink-600);
            border-left: 4px solid var(--bs-pink-600);
        }

        .chat-badge {
            position: absolute;
            top: -4px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 50px;
            line-height: 1;
            border: 2px solid #fff;
        }

        /* Tinh chỉnh khung chat cho Seller */
        .chat-wrapper {
            margin: 10px auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* CSS Accordion Khách hàng */
        .shop-group {
            border-bottom: 1px solid #eee;
        }

        .shop-group-title {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            background-color: #fcfcfc;
            transition: background 0.2s;
        }

        .shop-group-title:hover,
        .shop-group-title.active-shop {
            background-color: var(--bs-pink-50);
        }

        .shop-group-items {
            background-color: #fff;
            border-left: 3px solid var(--bs-pink-300);
        }

        .room-item {
            padding: 12px 15px 12px 25px !important;
            border-bottom: 1px solid #f9f9f9;
            cursor: pointer;
            transition: background 0.2s;
        }

        .room-item:hover {
            background: #f1f1f1;
        }

        .room-item.active {
            background: var(--bs-pink-100) !important;
            border-left: 4px solid var(--bs-pink-500) !important;
        }
    </style>
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <?php
    // --- ĐẾM SỐ TIN NHẮN KHÁCH HÀNG CHƯA ĐỌC ---
    $soTinNhanChuaDoc = 0;
    if (isset($_SESSION['IdTaiKhoan']) && isset($conn)) {
        $idSellerCurrent = $_SESSION['IdTaiKhoan'];

        // Câu lệnh đếm các tin nhắn thuộc phòng chat của Seller này, 
        // do người khác gửi (Khách hàng) và có trạng thái DaDoc = 0
        $sqlDemTinNhan = "SELECT COUNT(tn.MaTN) AS SoLuong 
                            FROM TinNhan tn 
                            JOIN PhongChat p ON tn.MaPhong = p.MaPhong 
                            WHERE p.IdNguoiBan = $idSellerCurrent 
                            AND tn.IdNguoiGui != $idSellerCurrent 
                            AND tn.DaXem = 0";

        $rsDem = $conn->query($sqlDemTinNhan);
        if ($rsDem && $rsDem->num_rows > 0) {
            $rowDem = $rsDem->fetch_assoc();
            $soTinNhanChuaDoc = $rowDem['SoLuong'];
        }
    }
    ?>

    <div class="seller-wrapper mt-4 mb-5">
        <h3 class="mb-4 text-secondary text-center"><i class="fa-solid fa-shop"></i> KÊNH NGƯỜI BÁN</h3>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="seller-sidebar">
                    <a href="sellerThongTinController.php"><i class="fa-solid fa-circle-info"></i> Thông tin cửa hàng</a>
                    <a href="sellerSanPhamController.php"><i class="fa-solid fa-box-open"></i> Quản lý sản phẩm</a>
                    <a href="sellerDonHangController.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a>
                    <a href="sellerChatController.php" class="active"><i class="fa-solid fa-comments"></i> Tin nhắn <?php if (isset($soTinNhanChuaDoc) && $soTinNhanChuaDoc > 0): ?><span class="chat-badge"><?php echo $soTinNhanChuaDoc; ?></span><?php endif; ?></a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="seller-content-box">
                    <div class="chat-wrapper" style="height: 70vh; display: flex; border: 1px solid #ddd; border-radius: 12px; overflow: hidden;">
                        <div class="sidebar" style="width: 320px; border-right: 1px solid #ddd; background: #fff; display: flex; flex-direction: column;">
                            <div class="sidebar-header" style="background: var(--bs-pink-500); color: white; padding: 15px; font-weight: bold; text-align: center;">
                                Khách hàng nhắn tin
                            </div>
                            <div class="sidebar-content" style="overflow-y: auto; flex: 1;">
                                <?php
                                if (!empty($danhSachPhong)) {
                                    // Nhóm theo Khách Hàng
                                    $danhSachNhom = [];
                                    foreach ($danhSachPhong as $r) {
                                        $tenKhach = $r['TenNguoiChat'];
                                        $danhSachNhom[$tenKhach][] = $r;
                                    }

                                    foreach ($danhSachNhom as $tenKhach => $cacPhongChat) {
                                        $isActiveGroup = false;
                                        foreach ($cacPhongChat as $p) {
                                            if ($p['MaPhong'] == $maPhong) {
                                                $isActiveGroup = true;
                                                break;
                                            }
                                        }

                                        $displayStyle = $isActiveGroup ? "display: block;" : "display: none;";
                                        $iconClass = $isActiveGroup ? "fa-chevron-up" : "fa-chevron-down";
                                        $headerActiveClass = $isActiveGroup ? "active-shop" : "";

                                        echo "<div class='shop-group'>";
                                        echo "  <div class='shop-group-title {$headerActiveClass}' onclick='toggleShop(this)'>";
                                        echo "      <div style='font-weight: bold;'><i class='fa-solid fa-user' style='color: var(--bs-pink-500);'></i> " . htmlspecialchars($tenKhach) . "</div>";
                                        echo "      <i class='fa-solid {$iconClass} toggle-icon' style='font-size: 12px; color: #888;'></i>";
                                        echo "  </div>";

                                        echo "  <div class='shop-group-items' style='{$displayStyle}'>";

                                        foreach ($cacPhongChat as $r) {
                                            $activeClass = ($r['MaPhong'] == $maPhong) ? 'active' : '';

                                            // Tạo cục badge đỏ nếu có tin nhắn mới
                                            $badgeHTML = '';
                                            if ($r['SoTinNhanMoi'] > 0) {
                                                $badgeHTML = "<span style='background-color: #dc3545; color: white; font-size: 11px; font-weight: bold; padding: 2px 6px; border-radius: 10px; margin-left: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);'>{$r['SoTinNhanMoi']}</span>";
                                            }

                                            echo "      <div class='room-item {$activeClass}' onclick='window.location.href=\"sellerChatController.php?MaPhong=" . $r['MaPhong'] . "\";'>";
                                            echo "          <div class='room-product-name' style='font-size: 14px; color: #444; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center;'>";
                                            echo "              <i class='fa-solid fa-box-open' style='color: #888; margin-right: 5px;'></i> SP: " . htmlspecialchars($r['TenHH']) . $badgeHTML;
                                            echo "          </div>";
                                            echo "      </div>";
                                        }
                                        echo "  </div>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<div style='padding: 20px; text-align: center; color: #888; font-style: italic;'>Chưa có tin nhắn nào</div>";
                                }
                                ?>
                            </div>
                        </div>

                        <div style="flex: 1; display: flex; flex-direction: column; background: #fafafa;">
                            <?php if ($maPhong > 0): ?>
                                <div class="chat-header" style="padding: 15px; background: white; border-bottom: 1px solid #ddd; font-weight: bold;">
                                    Đang chat với khách hàng
                                </div>
                                <div class="chat-body" id="chatBox" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                                </div>
                                <div class="chat-footer" style="padding: 15px; background: white; border-top: 1px solid #ddd; display: flex; gap: 10px;">
                                    <input type="text" id="txtMessage" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off">
                                    <button class="btn btn-primary" onclick="sendMessage()" style="background: var(--bs-pink-500); border: none;">
                                        <i class="fa-solid fa-paper-plane"></i> Gửi
                                    </button>
                                </div>
                            <?php else: ?>
                                <div style="flex: 1; display: flex; justify-content: center; align-items: center; flex-direction: column; color: #888;">
                                    <i class="fa-regular fa-comments" style="font-size: 60px; color: #ccc; margin-bottom: 15px;"></i>
                                    <h4>Chọn một khách hàng để bắt đầu chat</h4>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
        const maPhong = <?php echo $maPhong; ?>;
        const chatBox = document.getElementById('chatBox');
        const txtMessage = document.getElementById('txtMessage');

        function toggleShop(element) {
            const items = element.nextElementSibling;
            const icon = element.querySelector('.toggle-icon');
            if (items.style.display === "none" || items.style.display === "") {
                items.style.display = "block";
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                element.classList.add('active-shop');
            } else {
                items.style.display = "none";
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                element.classList.remove('active-shop');
            }
        }

        if (maPhong > 0) {
            function loadMessages() {
                // ĐƯỜNG DẪN GỌI VỀ CONTROLLER GỐC
                fetch(`../../controllers/chatController.php?action=load&MaPhong=${maPhong}`)
                    .then(res => res.text())
                    .then(data => {
                        let isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 5;
                        chatBox.innerHTML = data;
                        if (isScrolledToBottom) chatBox.scrollTop = chatBox.scrollHeight;
                    });
            }

            function sendMessage() {
                const msg = txtMessage.value.trim();
                if (msg === '') return;

                let formData = new FormData();
                formData.append('action', 'send');
                formData.append('MaPhong', maPhong);
                formData.append('NoiDung', msg);

                // ĐƯỜNG DẪN GỌI VỀ CONTROLLER GỐC
                fetch('../../controllers/chatController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(() => {
                        txtMessage.value = '';
                        loadMessages();
                        setTimeout(() => chatBox.scrollTop = chatBox.scrollHeight, 100);
                    });
            }

            txtMessage.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    sendMessage();
                }
            });

            loadMessages();
            setTimeout(() => chatBox.scrollTop = chatBox.scrollHeight, 300);
            setInterval(loadMessages, 2000);
        }
    </script>
    <script src="../../assets/js/js.js"></script>

</body>

</html>