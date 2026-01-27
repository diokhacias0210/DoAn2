// assets/js/loadLocSanPham.js (ĐÃ SỬA)
let currentOffset = 0;
const limit = 12;
let isLoading = false;

// Lấy các elements
const productList = document.getElementById('product-list');
const loadMoreBtn = document.getElementById('nut-xem-them');
const loadMoreContainer = document.getElementById('load-more-container');
const noProductsMsg = document.getElementById('no-products');
const categorySelect = document.getElementById('category');
const sortSelect = document.getElementById('sort');
// --- THÊM MỚI: Lấy ô tìm kiếm ---
const searchInput = document.getElementById('searchInput');

// Hàm format giá
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Hàm tạo HTML cho 1 sản phẩm
function createProductHTML(product) {
    const priceFormatted = formatPrice(product.Gia);
    let giaHienThi = '';
    
    const hetHang = product.SoLuongHH === 0;
    const productClass = hetHang ? "<div class='product-item het-hang' style='background: rgba(0, 0, 0, 0.1);'>" : "<div class='product-item'>";
    
    // Kiểm tra nếu product.URL tồn tại và không rỗng
    let imageSrc = '';
    if (product.URL && product.URL.trim() !== '') {
        // Giữ nguyên logic cũ: thêm ../ phía trước
        imageSrc = `${product.URL}`; 
    } else {
        // Đường dẫn ảnh mặc định (bạn cần đảm bảo file này tồn tại)
        imageSrc = 'assets/images/placeholder.png'; 
    }

    if (product.GiaTri && product.GiaTri > 0) {
        const giaGiam = product.Gia - (product.Gia * (product.GiaTri / 100));
        const giaGiamFormatted = formatPrice(giaGiam);
        
        giaHienThi = `
            <span class='gia-goc'>${priceFormatted}</span>
            <span class='gia-giam'>${giaGiamFormatted}</span>
        `;
    } else {
        giaHienThi = `<span class='gia-giam'>${priceFormatted}</span>`;
    }
    
    const badgeHetHang = hetHang ? `<div class='badge-het-hang'>Hết hàng</div>` : '';
    
    // --- SỬA LẠI ĐƯỜNG DẪN: trỏ đến controller chi tiết
    return `
        <a href='chiTietSanPhamController.php?id=${product.MaHH}' class='product-link'>
          ${productClass}
            <div class='product-item-top'>
              <img src='../${imageSrc}' alt='${product.TenHH}' loading='lazy'>
              ${badgeHetHang}
              <div class='tieude-sanpham'>${product.TenHH}</div>
            </div>
            <div class='product-item-bottom'>
              <div class='gia-rating'>
                <div class='rating'>
                  <i class='fa-solid fa-star'></i>
                  <span>${product.Rating}</span>
                </div>
                <div class='gia-san-pham'>
                  ${giaHienThi}
                </div>
              </div>
            </div>
          </div>
        </a>`;
}

// Hàm load sản phẩm với hiệu ứng mượt
async function loadProducts(reset = false) {
    if (isLoading) return;
    
    isLoading = true;
    
    const scrollPosition = window.scrollY;
    
    if (reset) {
        currentOffset = 0;
        productList.style.opacity = '0.5';
        productList.style.transition = 'opacity 0.3s ease';
    }

    // --- LẤY TẤT CẢ GIÁ TRỊ LỌC ---
    const category = categorySelect.value;
    const sort = sortSelect.value;
    // --- THÊM MỚI: Lấy từ khóa tìm kiếm ---
    const keyword = searchInput.value.trim();

    try {
        const params = new URLSearchParams({
            action: 'getAllSanPham',
            offset: currentOffset,
            limit: limit,
            sort: sort
        });

        if (category && category !== '0' && category !== '') {
            params.append('category', category);
        }
        
        // --- THÊM MỚI: Thêm từ khóa vào request ---
        if (keyword !== '') {
            params.append('keyword', keyword);
        }

        // --- SỬA ĐƯỜNG DẪN: Xóa ../controllers/ ---
        const response = await fetch(`inLocDSSP.php?${params}`);
        const data = await response.json();

        if (data.success) {
            if (data.products.length === 0 && currentOffset === 0) {
                noProductsMsg.style.display = 'block';
                loadMoreContainer.style.display = 'none';
                productList.innerHTML = '';
            } else {
                noProductsMsg.style.display = 'none';
                
                if (reset) {
                    productList.innerHTML = '';
                    data.products.forEach(product => {
                        productList.innerHTML += createProductHTML(product);
                    });
                    window.scrollTo(0, scrollPosition);
                } else {
                    data.products.forEach(product => {
                        productList.innerHTML += createProductHTML(product);
                    });
                }

                currentOffset += data.products.length;

                if (data.hasMore) {
                    loadMoreContainer.style.display = 'block';
                } else {
                    loadMoreContainer.style.display = 'none';
                }
            }
            
            if (reset) {
                setTimeout(() => {
                    productList.style.opacity = '1';
                }, 50);
            }
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Không thể tải sản phẩm. Vui lòng thử lại!');
    }

    isLoading = false;
}

// Đảm bảo DOM đã load xong
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFromUrl = urlParams.get('caterory');

    if(categoryFromUrl && categorySelect) {
        categorySelect.value = categoryFromUrl;
    }
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            loadProducts(false);
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function(e) {
            e.stopPropagation();
            const category = categorySelect.value;
            if(category){
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('caterory', category);
                window.history.replaceState({}, '', newUrl);
            } else {
                const newUrl = new URL(window.location);
                newUrl.searchParams.delete('caterory');
                window.history.replaceState({}, '', newUrl);
            }
            loadProducts(true);
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function(e) {
            e.stopPropagation();
            loadProducts(true);
        });
    }
    
    // --- THÊM MỚI: Lắng nghe sự kiện gõ phím tìm kiếm ---
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('keyup', function(e) {
            e.stopPropagation();
            clearTimeout(searchTimeout);
            // Đợi 300ms sau khi người dùng ngừng gõ mới tìm kiếm
            searchTimeout = setTimeout(() => {
                loadProducts(true); 
            }, 300);
        });
        
        // Ngăn form submit khi nhấn Enter
        searchInput.closest('form').addEventListener('submit', function(e) {
            e.preventDefault();
            loadProducts(true);
        });
    }

    // Load sản phẩm lần đầu
    loadProducts(true);
});