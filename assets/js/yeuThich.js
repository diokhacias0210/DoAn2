// --- HÀM XỬ LÝ NÚT "YÊU THÍCH"  ---
function toggleFavorite(button, mahh) {
    button.disabled = true;
    const icon = button.querySelector('i');
    const originalIconClass = icon.className;
    icon.className = 'fas fa-spinner fa-spin';

    const formData = new FormData();
    formData.append('MaHH', mahh);

    fetch('yeuThichApiController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.favorited) {
                icon.className = 'fa-solid fa-heart';
                icon.style.color = 'red';
                button.innerHTML = icon.outerHTML + ' Đã yêu thích';
            } else {
                icon.className = 'fa-regular fa-heart';
                icon.style.color = '';
                button.innerHTML = icon.outerHTML + ' Yêu thích';
            }
        } else {
            showToast(data.message, 'error');
            icon.className = originalIconClass;
        }
    })
    .catch(error => {
        console.error('Lỗi kết nối:', error);
        showToast('Không thể kết nối đến máy chủ.', 'error');
        icon.className = originalIconClass;
    })
    .finally(() => {
        button.disabled = false;
    });
}