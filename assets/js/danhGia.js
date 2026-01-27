    // ==================== ĐÁNH GIÁ SAO ====================
    document.addEventListener('DOMContentLoaded', function() {
        const ratingStars = document.querySelectorAll('.rating-select i');
        const commentForm = document.getElementById('comment-form');
        let currentRating = 0;

        // Kiểm tra xem form có bị disable không
        const isFormDisabled = commentForm && commentForm.classList.contains('disabled-form');

        if (!isFormDisabled) {
            // Lấy rating hiện tại từ số sao đã được đánh dấu
            ratingStars.forEach((star, index) => {
                if (star.classList.contains('fa-solid')) {
                    currentRating = index + 1;
                }
            });

            ratingStars.forEach((star, index) => {
                // Hiệu ứng hover
                star.addEventListener('mouseenter', function() {
                    for (let i = 0; i <= index; i++) {
                        ratingStars[i].classList.add('fa-solid');
                        ratingStars[i].classList.remove('fa-regular');
                    }
                });

                // Click để chọn
                star.addEventListener('click', function() {
                    currentRating = index + 1;
                    updateStars(currentRating);
                });
            });

            // Reset khi rời khỏi vùng sao
            const ratingContainer = document.querySelector('.rating-select');
            if (ratingContainer) {
                ratingContainer.addEventListener('mouseleave', function() {
                    updateStars(currentRating);
                });
            }
        }

        function updateStars(rating) {
            ratingStars.forEach((star, i) => {
                if (i < rating) {
                    star.classList.add('fa-solid');
                    star.classList.remove('fa-regular');
                } else {
                    star.classList.remove('fa-solid');
                    star.classList.add('fa-regular');
                }
            });
        }

    // Xử lý submit form bình luận
    if (commentForm && !isFormDisabled) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const noiDung = this.querySelector('textarea[name="noidung"]')?.value.trim();
            
            // Lấy MaHH từ URL (hỗ trợ cả 'id' và 'MaHH')
            const urlParams = new URLSearchParams(window.location.search);
            const maHH = urlParams.get('MaHH') || urlParams.get('id');
            
            if (!maHH) {
                alert('Không tìm thấy mã sản phẩm!');
                return;
            }
            
            // Lấy số sao đã chọn
            const stars = this.querySelectorAll('.rating-select i.fa-solid');
            const soSao = stars.length;
            
            // Validate
            if (!noiDung) {
                alert('Vui lòng nhập nội dung bình luận!');
                return;
            }
            
            if (soSao === 0) {
                alert('Vui lòng chọn số sao đánh giá!');
                return;
            }

            // Hiển thị loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';
            
            // Gửi AJAX
            const formData = new FormData();
            formData.append('maHH', maHH);
            formData.append('noiDung', noiDung);
            formData.append('soSao', soSao);
            
            fetch('xuLyBinhLuan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    
                    // Thêm bình luận mới vào đầu danh sách
                    if (data.data) {
                        addNewComment(data.data);
                    }
                    
                    // Reset form
                    commentForm.reset();
                    currentRating = 0;
                    updateStars(0);
                    
                    // Reload trang để cập nhật rating tổng thể
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }

    // Hàm thêm bình luận mới vào danh sách
    function addNewComment(data) {
        const commentList = document.querySelector('.comment-list');
        if (!commentList) return;

        // Kiểm tra xem có thông báo "Chưa có bình luận" không
        const emptyMessage = commentList.querySelector('p');
        if (emptyMessage && emptyMessage.textContent.includes('Chưa có bình luận')) {
            emptyMessage.remove();
        }

        const commentItem = document.createElement('div');
        commentItem.className = 'comment-item';
        
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += `<i class="fa-${i <= data.soSao ? 'solid' : 'regular'} fa-star"></i>`;
        }
        
        commentItem.innerHTML = `
            <strong>${escapeHtml(data.tenTK)}</strong>
            <div class="rating">${starsHtml}</div>
            <p>${escapeHtml(data.noiDung)}</p>
            <small>${data.ngayBL}</small>
        `;
        
        // Thêm vào đầu danh sách
        commentList.insertBefore(commentItem, commentList.firstChild);
    }

    // Hàm escape HTML để tránh XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});