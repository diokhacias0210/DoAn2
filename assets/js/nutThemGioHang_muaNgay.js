document.addEventListener('DOMContentLoaded', function() {
    //  xử lý nút thêm vào giỏ hàng
    const addToCartBtn = document.getElementById('add-to-cart-button');
    if (addToCartBtn) {
        addToCartBtn.onclick = function(e) {
            e.preventDefault();
            const maHH = this.dataset.mahh;
            handleAddToCart(maHH, false); // false = Thêm và ở lại
        };
    }

    // nút mua ngay
    const buyNowBtn = document.getElementById('buy-now-button');
    if (buyNowBtn) {
        buyNowBtn.onclick = function(e) {
            e.preventDefault();
            const maHH = this.dataset.mahh;
            handleAddToCart(maHH, true); // true = Mua ngay chuyển hướng
        };
    }
});

// Hàm chung để xử lý
function handleAddToCart(maHH, isBuyNow) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('MaHH', maHH);
    formData.append('SoLuong', 1); // Mặc định là 1

    //  Gọi API thêm vào giỏ hàng (CSDL)
    fetch('gioHangApiController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isBuyNow) {
                //  NẾU LÀ MUA NGAY: Gọi thêm API chuẩn bị thanh toán
                prepareCheckoutForBuyNow(maHH);
            } else {
                // NẾU THÊM VÀO GIỎ: Hiện thông báo đẹp (Toast)
                showToast(data.message, 'success');
            }
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showToast('Lỗi kết nối server.', 'error');
    });
}

// Hàm chuẩn bị session thanh toán cho Mua Ngay
function prepareCheckoutForBuyNow(maHH) {
    const items = [{
        MaHH: maHH,
        SoLuong: 1
    }];

    const formData = new FormData();
    formData.append('action', 'prepare_checkout');
    formData.append('items', JSON.stringify(items));

    fetch('datHangController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'thanhToanController.php';
        } else {
            showToast('Lỗi chuẩn bị thanh toán: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showToast('Lỗi khi chuyển hướng thanh toán.', 'error');
    });
}

// --- HÀM HIỂN THỊ THÔNG BÁO (TOAST) ---
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.padding = '12px 24px';
    toast.style.borderRadius = '8px';
    toast.style.color = 'white';
    toast.style.zIndex = '9999';
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s ease';
    toast.style.fontSize = '14px';
    toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    
    if (type === 'success') {
        toast.style.backgroundColor = '#28a745'; 
    } else if (type === 'error') {
        toast.style.backgroundColor = '#dc3545'; 
    } else {
        toast.style.backgroundColor = '#17a2b8'; 
    }

    document.body.appendChild(toast);
    
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000); 
}