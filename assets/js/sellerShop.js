document.addEventListener('DOMContentLoaded', function() {
    const sellerId = document.getElementById('seller-id').value;
    const productList = document.getElementById('shop-product-list');
    const loadMoreBtn = document.getElementById('btn-load-more');
    const noProductsMsg = document.getElementById('no-products-msg');
    const totalProductsSpan = document.getElementById('total-products');
    
    let currentOffset = 0;
    let isLoading = false;

    // Load lần đầu
    loadProducts(true);

    // Sự kiện bộ lọc
    document.getElementById('category-filter').addEventListener('change', () => loadProducts(true));
    document.getElementById('sort-filter').addEventListener('change', () => loadProducts(true));
    
    // Sự kiện tìm kiếm
    let timeout = null;
    document.getElementById('search-input').addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => loadProducts(true), 500);
    });

    // Sự kiện xem thêm
    loadMoreBtn.addEventListener('click', () => loadProducts(false));

    function loadProducts(reset) {
        if (isLoading) return;
        isLoading = true;

        if (reset) {
            currentOffset = 0;
            productList.innerHTML = '<div class="text-center w-100 py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
            loadMoreBtn.parentElement.style.display = 'none';
            noProductsMsg.style.display = 'none';
        }

        const category = document.getElementById('category-filter').value;
        const sort = document.getElementById('sort-filter').value;
        const keyword = document.getElementById('search-input').value;

        // Gọi API
        fetch(`sellerApiController.php?action=getProducts&seller_id=${sellerId}&offset=${currentOffset}&category=${category}&sort=${sort}&keyword=${keyword}`)
            .then(res => res.json())
            .then(data => {
                if (reset) productList.innerHTML = ''; // Xóa spinner

                if (data.success) {
                    // Cập nhật tổng số lượng
                    if(totalProductsSpan) totalProductsSpan.innerText = data.total;

                    if (data.products.length > 0) {
                        const html = data.products.map(sp => createProductHTML(sp)).join('');
                        productList.insertAdjacentHTML('beforeend', html);
                        currentOffset += data.products.length;
                    } else if (reset) {
                        noProductsMsg.style.display = 'block';
                    }

                    // Ẩn/Hiện nút xem thêm
                    loadMoreBtn.parentElement.style.display = data.hasMore ? 'flex' : 'none';
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                isLoading = false;
            });
    }

    function createProductHTML(sp) {
        const gia = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(sp.Gia);
        const imgUrl = sp.URL ? `../${sp.URL}` : '../assets/images/placeholder.png';
        
        let hetHangClass = sp.SoLuongHH == 0 ? 'het-hang' : '';
        let hetHangStyle = sp.SoLuongHH == 0 ? 'background: rgba(0,0,0,0.05);' : '';
        let badge = sp.SoLuongHH == 0 ? '<div class="badge-het-hang">Hết hàng</div>' : '';

        // Có thể copy HTML từ file danhSachSanPham.php để giống hệt
        return `
            <a href="chiTietSanPhamController.php?id=${sp.MaHH}" class="product-link">
                <div class="product-item ${hetHangClass}" style="${hetHangStyle}">
                    <div class="product-item-top">
                        <img src="${imgUrl}" alt="${sp.TenHH}" loading="lazy">
                        ${badge}
                    </div>
                    <div class="product-item-bottom">
                        <div class="tieude-sanpham" style="font-weight:bold; margin-bottom:5px;">${sp.TenHH}</div>
                        <div class="gia-rating">
                            <span class="gia-giam" style="color:#d63384; font-weight:bold; font-size:16px;">${gia}</span>
                            <div class="rating"><i class="fa-solid fa-star"></i> ${sp.Rating}</div>
                        </div>
                    </div>
                </div>
            </a>
        `;
    }
});