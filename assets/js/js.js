/* ẩn hiện nền thanh điều hướng khi cuộn*/
let lastScrollTop = 0;
const header = document.querySelector(".dau-trang");

if (header) {
    window.addEventListener("scroll", function () {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop) {
            header.style.transform = "translateY(-100%)";
        } else {
            header.style.transform = "translateY(0)";
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
}

/* ------------------- nút chuyển về đầu trang -------------- */
const btnUp = document.getElementById('btnUp');
if (btnUp) {
    btnUp.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/* -----------------------đổi ảnh lớn nhỏ -------------- */
document.addEventListener('DOMContentLoaded', function() {
    const anhchinh = document.getElementById('product-image');
    const album = document.querySelectorAll('.thumbnail');

    if (anhchinh && album.length > 0) {
        album.forEach(anh => {
            anh.addEventListener("click", function() {
                anhchinh.src = this.src;
                album.forEach(t => t.classList.remove("active"));
                this.classList.add("active");
            });
        });
    }
});

/*thêm địa chỉ*/
const btnThemDiaChi = document.getElementById("btn-them-diachi");
const formThemDiaChi = document.getElementById("form-them-diachi");
const luuDiaChi = document.getElementById("luu-diachi");

if (btnThemDiaChi && formThemDiaChi) {
    btnThemDiaChi.addEventListener("click", function () {
        formThemDiaChi.style.display = "block";
    });
}

if (luuDiaChi) {
    luuDiaChi.addEventListener("click", function () {
        const input = document.getElementById("diachi-moi");
        const diachi = input.value.trim();
        if (diachi !== "") {
            const select = document.getElementById("diachi-select");
            const option = document.createElement("option");
            option.text = diachi;
            option.value = Date.now();
            select.add(option);
            select.value = option.value;
            input.value = "";
            formThemDiaChi.style.display = "none";
        } else {
            alert("Vui lòng nhập địa chỉ!");
        }
    });
}

/* Xóa địa chỉ */
const btnXoaDiaChi = document.getElementById("btn-xoa-diachi");
if (btnXoaDiaChi) {
    btnXoaDiaChi.addEventListener("click", function () {
        const select = document.getElementById("diachi-select");
        if (select && select.options.length > 1) {
            const index = select.selectedIndex;
            select.remove(index);
            select.selectedIndex = 0;
        } else {
            alert("Phải có ít nhất một địa chỉ, không thể xóa hết!");
        }
    });
}

// XEM THÊM MÔ TẢ SẢN PHẨM 
document.addEventListener('DOMContentLoaded', function() {
    const thongTinThem = document.querySelector('.thong-tin-them');
    const productDescription = document.querySelector('.product-description');
    
    if (thongTinThem && productDescription) {
        // Kiểm tra xem đã có nút chưa
        const existingBtn = thongTinThem.querySelector('.btn-xem-them');
        if (!existingBtn) {
            const btnXemThem = document.createElement('button');
            btnXemThem.className = 'btn-xem-them';
            btnXemThem.innerHTML = '<span>Xem thêm</span><i class="fa-solid fa-chevron-down"></i>';
            
            productDescription.parentNode.insertBefore(btnXemThem, productDescription.nextSibling);
            
            btnXemThem.addEventListener('click', function() {
                productDescription.classList.toggle('expanded');
                btnXemThem.classList.toggle('expanded');
                
                if (productDescription.classList.contains('expanded')) {
                    btnXemThem.innerHTML = '<span>Thu gọn</span><i class="fa-solid fa-chevron-up"></i>';
                } else {
                    btnXemThem.innerHTML = '<span>Xem thêm</span><i class="fa-solid fa-chevron-down"></i>';
                }
            });
        }
    }
});

// XEM THÊM BÌNH LUẬN 
document.addEventListener('DOMContentLoaded', function() {
    const commentList = document.querySelector('.comment-list');
    
    if (commentList) {
        const allComments = commentList.querySelectorAll('.comment-item');
        const commentsPerPage = 3;
        let currentPage = 1;
        let btnLoadMore;
        
        function showComments() {
            const totalComments = allComments.length;
            const endIndex = currentPage * commentsPerPage;
            
            allComments.forEach((comment, index) => {
                if (index < endIndex) {
                    comment.style.display = 'block';
                } else {
                    comment.style.display = 'none';
                }
            });
            
            if (btnLoadMore && endIndex >= totalComments) {
                btnLoadMore.style.display = 'none';
            }
        }
        
        if (allComments.length > commentsPerPage) {
            // Kiểm tra xem đã có nút chưa
            const existingPagination = document.querySelector('.comment-pagination');
            if (!existingPagination) {
                const paginationDiv = document.createElement('div');
                paginationDiv.className = 'comment-pagination';
                
                btnLoadMore = document.createElement('button');
                btnLoadMore.className = 'btn-load-more';
                btnLoadMore.innerHTML = '<span>Xem thêm bình luận</span><i class="fa-solid fa-chevron-down"></i>';
                
                paginationDiv.appendChild(btnLoadMore);
                commentList.parentNode.insertBefore(paginationDiv, commentList.nextSibling);
                
                showComments();
                
                btnLoadMore.addEventListener('click', function() {
                    currentPage++;
                    showComments();
                });
            }
        }
    }
});

// ĐỔI ẢNH SẢN PHẨM TRONG TRANG CHI TIẾT
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail-images img');
    const mainImage = document.querySelector('#product-image');
    
    if (thumbnails.length > 0 && mainImage) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                mainImage.src = this.src;
            });
        });
    }
});

