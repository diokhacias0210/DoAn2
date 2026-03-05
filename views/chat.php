
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chat - Giống Zalo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background-color: #d7d7d7; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .chat-container { display: flex; width: 900px; height: 600px; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        /* Cột trái: Danh sách phòng chat */
        .sidebar { width: 300px; border-right: 1px solid #ddd; background: #fff; overflow-y: auto; }
        .sidebar-header { padding: 15px; background: #0068ff; color: white; font-weight: bold; }
        .room-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
        .room-item:hover, .room-item.active { background: #e5efff; }
        
        /* Cột phải: Nội dung chat */
        .chat-window { flex: 1; display: flex; flex-direction: column; background: #e2e8f0; }
        .chat-header { padding: 15px; background: #fff; border-bottom: 1px solid #ddd; font-weight: bold; }
        .chat-body { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
        
        /* Tin nhắn */
        .msg { max-width: 60%; padding: 10px 15px; border-radius: 8px; font-size: 14px; line-height: 1.4; }
        .msg.sent { background: #e5efff; border: 1px solid #cce0ff; align-self: flex-end; }
        .msg.received { background: #fff; border: 1px solid #ddd; align-self: flex-start; }
        
        /* Nhập liệu */
        .chat-footer { padding: 15px; background: #fff; border-top: 1px solid #ddd; display: flex; gap: 10px; }
        .chat-footer input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; outline: none;}
        .chat-footer button { padding: 10px 20px; background: #0068ff; color: white; border: none; border-radius: 4px; cursor: pointer;}
    </style>
</head>
<body>

<div class="chat-container">
    <div class="sidebar">
        <div class="sidebar-header">Tin nhắn của bạn</div>
        <?php
        // Biến $danhSachPhong đã được chatController.php chuẩn bị và truyền sang
        if (!empty($danhSachPhong)) {
            foreach($danhSachPhong as $r) {
                $activeClass = ($r['MaPhong'] == $maPhong) ? 'active' : '';
                // Click thì load lại Controller gọi Action 'index'
                echo "<div class='room-item $activeClass' onclick='window.location.href=\"../controllers/chatController.php?action=index&MaPhong=".$r['MaPhong']."\"'>
                        ".$r['TenHH']."
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
            if(msg === '') return;

            let formData = new FormData();
            formData.append('action', 'send');
            formData.append('MaPhong', maPhong);
            formData.append('NoiDung', msg);

            // Gọi AJAX gửi tin nhắn qua Controller
            fetch('../controllers/chatController.php', { method: 'POST', body: formData })
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