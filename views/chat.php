<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trò chuyện với người bán</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="../assets/css/color.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/chat.css" rel="stylesheet">
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="container-fluid giua-trang">
        <div style="max-width: 1100px; margin: 20px auto 10px auto;">
            <a href="javascript:history.back()" class="btn-back-chat">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <div class="chat-wrapper">
            <div class="sidebar">
                <div class="sidebar-header" style="background: var(--bs-pink-500); color: white; padding: 15px; font-weight: bold; text-align: center; border-radius: 16px 0 0 0;">
                    Tin nhắn của bạn
                </div>
                
                <div class="sidebar-content" style="overflow-y: auto; height: calc(100% - 54px);">
                    <?php
                    if (!empty($danhSachPhong)) {
                        // 1. Nhóm dữ liệu theo Tên Người Bán (Shop)
                        $danhSachNhom = [];
                        foreach ($danhSachPhong as $r) {
                            $tenShop = $r['TenNguoiChat'];
                            $danhSachNhom[$tenShop][] = $r;
                        }

                        // 2. Hiển thị danh sách đã nhóm
                        foreach ($danhSachNhom as $tenShop => $cacPhongChat) {
                            // Kiểm tra xem nhóm này có chứa phòng đang chat không để tự động mở
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

                            // In ra tiêu đề Shop
                            echo "<div class='shop-group'>";
                            echo "  <div class='shop-group-title {$headerActiveClass}' onclick='toggleShop(this)'>";
                            echo "      <div style='font-weight: bold;'><i class='fa-solid fa-store' style='color: var(--bs-pink-500);'></i> " . htmlspecialchars($tenShop) . "</div>";
                            echo "      <i class='fa-solid {$iconClass} toggle-icon' style='font-size: 12px; color: #888;'></i>";
                            echo "  </div>";
                            
                            // In ra danh sách các phòng chat (sản phẩm) của Shop đó
                            echo "  <div class='shop-group-items' style='{$displayStyle}'>";
                            foreach ($cacPhongChat as $r) {
                                $activeClass = ($r['MaPhong'] == $maPhong) ? 'active' : '';
                                echo "      <div class='room-item {$activeClass}' onclick='window.location.href=\"../controllers/chatController.php?action=index&MaPhong=" . $r['MaPhong'] . "\"'>";
                                echo "          <div class='room-product-name' style='font-size: 14px; color: #444; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'>";
                                echo "              <i class='fa-solid fa-box-open' style='color: #888; margin-right: 5px;'></i> SP: " . htmlspecialchars($r['TenHH']);
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

            <div class="chat-window">
                <div class="chat-header">
                    <?php if (isset($chiTietPhong) && !empty($chiTietPhong)): ?>
                        <div style="font-size: 18px; font-weight: bold; color: var(--bs-pink-600);">
                            <i class="fa-solid fa-store"></i> <?php echo htmlspecialchars($chiTietPhong['TenNguoiChat']); ?>
                        </div>
                        <div style="font-size: 14px; color: #555; margin-top: 5px;">
                            Đang trao đổi về: <strong><?php echo htmlspecialchars($chiTietPhong['TenHH']); ?></strong>
                        </div>
                    <?php else: ?>
                        <div style="font-size: 16px; color: #888; text-align: center; margin-top: 10px;">
                            Vui lòng chọn một cuộc trò chuyện ở bên trái để bắt đầu
                        </div>
                    <?php endif; ?>
                </div>

                <div class="chat-body" id="chatBox">
                    </div>

                <div class="chat-footer">
                    <input type="text" id="txtMessage" placeholder="Nhập tin nhắn..." autocomplete="off" <?php echo ($maPhong > 0) ? '' : 'disabled'; ?>>
                    <button class="btn-send" onclick="sendMessage()" <?php echo ($maPhong > 0) ? '' : 'disabled'; ?>>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const maPhong = <?php echo $maPhong; ?>;
        const chatBox = document.getElementById('chatBox');
        const txtMessage = document.getElementById('txtMessage');

        function toggleShop(element) {
            const items = element.nextElementSibling;
            const icon = element.querySelector('.toggle-icon');
            
            // Đổi trạng thái hiển thị
            if (items.style.display === "none" || items.style.display === "") {
                items.style.display = "block";
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
                element.classList.add('active-shop');
            } else {
                items.style.display = "none";
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
                element.classList.remove('active-shop');
            }
        }
        
        if (maPhong > 0) {
            function loadMessages() {
                fetch(`../controllers/chatController.php?action=load&MaPhong=${maPhong}`)
                    .then(res => res.text())
                    .then(data => {
                        let isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 5;
                        chatBox.innerHTML = data;
                        if (isScrolledToBottom) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    });
            }

            function sendMessage() {
                const msg = txtMessage.value.trim();
                if (msg === '') return;

                let formData = new FormData();
                formData.append('action', 'send');
                formData.append('MaPhong', maPhong);
                formData.append('NoiDung', msg);

                fetch('../controllers/chatController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(() => {
                        txtMessage.value = ''; 
                        loadMessages(); 
                        setTimeout(() => {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }, 100);
                    });
            }

            txtMessage.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); 
                    sendMessage();
                }
            });

            loadMessages();
            setTimeout(() => {
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 300);

            setInterval(loadMessages, 2000);
        }
    </script>
</body>
</html>