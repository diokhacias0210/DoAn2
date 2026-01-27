<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/gioHang.css" rel="stylesheet">
    <link href="../assets/css/header.css" rel="stylesheet">
    <link href="../assets/css/color.css" rel="stylesheet">
    <title>Giỏ hàng</title>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="giua-trang">
        <div class="top-banner">
            <h2>│Giỏ hàng</h2>
        </div>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div id="session-alert" style="margin: 0 100px 20px 100px;">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        ?>

        <div class="page-container">
            <div class="cart-header">
                <input type="checkbox" id="select-all">
                <div>Sản phẩm</div>
                <div>Đơn giá</div>
                <div>Số lượng</div>
                <div>Thành tiền</div>
                <div>Thao tác</div>
            </div>

            <div class="cart-items" id="cart-items-container">
                <div id="loading-spinner" style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin fa-3x text-gray-400"></i>
                    <p>Đang tải giỏ hàng...</p>
                </div>
            </div>

            <div class="cart-footer">
                <div class="nut-tro-lai">
                    <button onclick="window.location.href='danhSachSanPhamController.php'" class="back-btn">⬅ Quay lại</button>
                </div>
                <div>
                    <span>Tổng cộng</span>
                    <strong id="total-amount">0đ</strong>
                    <button class="checkout" onclick="prepareCheckout()">Mua hàng</button>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartContainer = document.getElementById('cart-items-container');
            const loadingSpinner = document.getElementById('loading-spinner');
            const selectAllCheckbox = document.getElementById('select-all');

            // Hàm định dạng tiền tệ
            const formatCurrency = (amount) => {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            };

            // Hàm gọi API để cập nhật giỏ hàng
            const updateCartAPI = (action, maHH, soLuong = 1) => {
                const formData = new FormData();
                formData.append('action', action);
                formData.append('MaHH', maHH);
                formData.append('SoLuong', soLuong);

                return fetch('gioHangApiController.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json());
            };

            // Hàm cập nhật tổng tiền
            const updateTotalPrice = () => {
                let total = 0;
                const checkedItems = document.querySelectorAll('.item-check:checked');
                checkedItems.forEach(item => {
                    const cartItem = item.closest('.cart-item');
                    const price = parseFloat(cartItem.dataset.price);
                    const quantity = parseInt(cartItem.querySelector('.quantity-input').value);
                    total += price * quantity;
                });
                document.getElementById('total-amount').textContent = formatCurrency(total);
            };

            // Hàm render các sản phẩm trong giỏ hàng
            const renderCartItems = (items) => {
                cartContainer.innerHTML = '';
                if (items.length === 0) {
                    cartContainer.innerHTML = `<div class="cart-item-empty" style="text-align: center; padding: 40px; color: #555;">Giỏ hàng của bạn đang trống.</div>`;
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.disabled = true;
                    return;
                }

                selectAllCheckbox.disabled = false;

                items.forEach(item => {
                    const itemHTML = `
                        <div class="cart-item" data-mahh="${item.MaHH}" data-price="${item.Gia}">
                            <input type="checkbox" class="item-check">
                            <div class="product-name">
                                <a href="chiTietSanPhamController.php?id=${item.MaHH}">
                                    <img src="../${item.AnhDaiDien || 'assets/images/placeholder.png'}" alt="${item.TenHH}">
                                </a>
                                <span>${item.TenHH}</span>
                            </div>
                            <div class="price">${formatCurrency(item.Gia)}</div>
                            <div class="quantity">
                                <button class="qty-btn minus" ${item.SoLuong <= 1 ? 'disabled' : ''}>−</button>
                                <input type="number" class="quantity-input" value="${item.SoLuong}" min="1" max="${item.TonKho}">
                                <button class="qty-btn plus" ${item.SoLuong >= item.TonKho ? 'disabled' : ''}>+</button>
                            </div>
                            <div class="total">${formatCurrency(item.Gia * item.SoLuong)}</div>
                            <div class="action"><button class="delete-btn">Xóa</button></div>
                        </div>
                    `;
                    cartContainer.insertAdjacentHTML('beforeend', itemHTML);
                });

                updateTotalPrice();
            };

            // Hàm tải giỏ hàng lần đầu
            const loadCart = () => {
                fetch('gioHangApiController.php?action=get')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            renderCartItems(data.items);
                        } else {
                            cartContainer.innerHTML = `<div class_item-empty" style="text-align: center; padding: 40px; color: red;">Lỗi: ${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải giỏ hàng:', error);
                        cartContainer.innerHTML = `<div class="cart-item-empty" style="text-align: center; padding: 40px; color: red;">Không thể tải giỏ hàng. Vui lòng thử lại.</div>`;
                    });
            };

            // Xử lý sự kiện click
            cartContainer.addEventListener('click', (e) => {
                const target = e.target;
                const cartItem = target.closest('.cart-item');
                if (!cartItem) return;

                const maHH = cartItem.dataset.mahh;
                const quantityInput = cartItem.querySelector('.quantity-input');
                let currentQty = parseInt(quantityInput.value);

                if (target.classList.contains('plus')) {
                    const maxQty = parseInt(quantityInput.max);
                    if (currentQty < maxQty) {
                        updateCartAPI('update', maHH, currentQty + 1).then(loadCart);
                    }
                } else if (target.classList.contains('minus')) {
                    if (currentQty > 1) {
                        updateCartAPI('update', maHH, currentQty - 1).then(loadCart);
                    }
                } else if (target.classList.contains('delete-btn')) {
                    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                        updateCartAPI('remove', maHH).then(loadCart);
                    }
                } else if (target.classList.contains('item-check')) {
                    updateTotalPrice();
                }
            });

            // Xử lý sự kiện thay đổi input số lượng
            cartContainer.addEventListener('change', (e) => {
                if (e.target.classList.contains('quantity-input')) {
                    const cartItem = e.target.closest('.cart-item');
                    const maHH = cartItem.dataset.mahh;
                    let newQty = parseInt(e.target.value);
                    const maxQty = parseInt(e.target.max);

                    if (isNaN(newQty) || newQty < 1) newQty = 1;
                    if (newQty > maxQty) newQty = maxQty;

                    updateCartAPI('update', maHH, newQty).then(loadCart);
                }
            });

            // Xử lý nút "Chọn tất cả"
            selectAllCheckbox.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                document.querySelectorAll('.item-check').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateTotalPrice();
            });

            // Tải giỏ hàng khi trang được mở
            loadCart();

            // Tự động ẩn alert
            const sessionAlert = document.getElementById('session-alert');
            if (sessionAlert) {
                setTimeout(() => {
                    sessionAlert.style.transition = 'opacity 0.5s ease';
                    sessionAlert.style.opacity = '0';
                    setTimeout(() => sessionAlert.remove(), 500);
                }, 5000);
            }
        });

        // Hàm xử lý nút "Mua hàng"
        function prepareCheckout() {
            const selectedItems = [];
            document.querySelectorAll('.item-check:checked').forEach(item => {
                const cartItem = item.closest('.cart-item');
                selectedItems.push({
                    MaHH: cartItem.dataset.mahh,
                    SoLuong: cartItem.querySelector('.quantity-input').value
                });
            });

            if (selectedItems.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm để mua hàng.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'prepare_checkout');
            formData.append('items', JSON.stringify(selectedItems));

            fetch('datHangController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'thanhToanController.php';
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => console.error('Lỗi khi chuẩn bị thanh toán:', error));
        }
    </script>
</body>

</html>