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
        <div class="chat-wrapper">
            <div class="sidebar">
                <div class="sidebar-header">
                    <i class="fa-solid fa-comments"></i> Tin nhắn của bạn
                </div>
                <div class="room-list">
                    <?php
                    if (!empty($danhSachPhong)) {
                        foreach ($danhSachPhong as $r) {
                            $activeClass = ($r['MaPhong'] == $maPhong) ? 'active' : '';
                            echo "<div class='room-item $activeClass' onclick='window.location.href=\"../controllers/chatController.php?action=index&MaPhong=" . $r['MaPhong'] . "\"'>
                                    <div class='room-name'>
                                        <i class='fa-solid fa-circle-user' style='color:#ccc; font-size: 18px;'></i> 
                                        " . htmlspecialchars($r['TenNguoiChat']) . "
                                    </div>
                                    <div class='room-product-name'>
                                        <i class='fa-solid fa-box-open'></i> SP: " . htmlspecialchars($r['TenHH']) . "
                                    </div>
                                  </div>";
                        }
                    } else {
                        // Gọi class thay vì dùng style
                        echo "<div class='empty-message'>Bạn chưa có cuộc trò chuyện nào.</div>";
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