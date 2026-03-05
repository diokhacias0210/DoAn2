<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chat - Giống Zalo</title>
    <link href="../assets/css/chat.css" rel="stylesheet">
</head>

<body>

    <div class="chat-container">
        <div class="sidebar">
            <div class="sidebar-header">Tin nhắn của bạn</div>
            <?php
            // Biến $danhSachPhong đã được chatController.php chuẩn bị và truyền sang
            if (!empty($danhSachPhong)) {
                foreach ($danhSachPhong as $r) {
                    $activeClass = ($r['MaPhong'] == $maPhong) ? 'active' : '';
                    // Click thì load lại Controller gọi Action 'index'
                    echo "<div class='room-item $activeClass' onclick='window.location.href=\"../controllers/chatController.php?action=index&MaPhong=" . $r['MaPhong'] . "\"'>
                        " . $r['TenHH'] . "
                      </div>";
                }
            } else {
                echo "<div style='padding:15px; text-align:center;'>Bạn chưa có tin nhắn nào.</div>";
            }
            ?>
        </div>

        <div class="chat-window">
            <div class="chat-header">Đang chat về sản phẩm...</div>

            <div class="chat-body" id="chatBox">
            </div>

            <div class="chat-footer">
                <input type="text" id="txtMessage" placeholder="Nhập tin nhắn..." autocomplete="off" <?php echo ($maPhong == 0) ? 'disabled' : ''; ?>>
                <button onclick="sendMessage()" <?php echo ($maPhong == 0) ? 'disabled' : ''; ?>>Gửi</button>
                <button onclick="history.back()"><i class="fa-solid fa-angle-left"></i> Trở lại</button>
            </div>
        </div>
    </div>

    <script>
        const maPhong = <?= $maPhong ?>;
        const chatBox = document.getElementById('chatBox');
        const txtMessage = document.getElementById('txtMessage');

        // Nếu không có mã phòng thì dừng thực thi JS (tránh lỗi)
        if (maPhong > 0) {
            function loadMessages() {
                // Gọi AJAX về đúng file Controller, dùng action=load
                fetch(`../controllers/chatController.php?action=load&MaPhong=${maPhong}`)
                    .then(res => res.text())
                    .then(data => {
                        chatBox.innerHTML = data;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    });
            }

            function sendMessage() {
                const msg = txtMessage.value.trim();
                if (msg === '') return;

                let formData = new FormData();
                formData.append('action', 'send');
                formData.append('MaPhong', maPhong);
                formData.append('NoiDung', msg);

                // Gọi AJAX gửi tin nhắn qua Controller
                fetch('../controllers/chatController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(() => {
                        txtMessage.value = '';
                        loadMessages();
                    });
            }

            txtMessage.addEventListener("keypress", function(event) {
                if (event.key === "Enter") sendMessage();
            });

            loadMessages();
            setInterval(loadMessages, 2000);
        }
    </script>

</body>

</html>