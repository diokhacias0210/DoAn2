document.addEventListener("DOMContentLoaded", function () {
    // DÀNH CHO LIVE SEARCH Ở HEADER 
    const searchInput = document.getElementById("search");
    const resultBox = document.getElementById("live-search-result");

    if (!searchInput || !resultBox) return;

    searchInput.addEventListener("keydown", function (e) {
        const keyword = this.value.trim();
        if (e.key === "Enter") {
            e.preventDefault(); 
            if (keyword.length >= 2) {
                // Chuyển hướng đến trang danh sách sản phẩm (Controller)
                window.location.href = `danhSachSanPhamController.php?keyword=${encodeURIComponent(keyword)}`;
            }
        } else if (keyword.length >= 2) {
            doSearch(keyword);
        } else {
            resultBox.innerHTML = "";
            resultBox.style.display = "none";
        }
    });

    function doSearch(keyword) {
        // Gọi API tìm kiếm (Controller)
        fetch(`searchController.php?q=${encodeURIComponent(keyword)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    resultBox.style.display = "block";
                    resultBox.innerHTML = data.map(item => {
                        let giaHienThi = '';
                        // Logic hiển thị giá
                        if(item.GiaTri && item.GiaTri > 0){
                            const giaGoc = Number(item.Gia);
                            const giaGiam = giaGoc - (giaGoc * (item.GiaTri / 100));
                            giaHienThi = `
                                <span class="gia-goc">${giaGoc.toLocaleString('vi-VN')} đ</span><br>
                                <span class="gia-giam">${giaGiam.toLocaleString('vi-VN')} đ</span>
                            `;
                        } else {
                            giaHienThi = `<span class="gia-giam">${Number(item.Gia).toLocaleString('vi-VN')} đ</span>`;
                        }
                        
                        // --- PHẦN BẠN ĐÃ BỊ THIẾU ---
                        return `
                            <div class="search-item"
                             style="display:flex;align-items:center;gap:10px;padding:6px;border-bottom:1px solid #eee;cursor:pointer;"
                             onclick="window.location='chiTietSanPhamController.php?id=${item.MaHH}'">
                            <img src="${item.URL || '../assets/images/no-image.png'}"
                                alt="${item.TenHH}"
                                style="width:45px;height:45px;object-fit:cover;border-radius:4px;">
                            <div>
                                <strong>${item.TenHH}</strong><br>
                                ${giaHienThi}
                                <small style="color:gold;"><i class='fa-solid fa-star'></i> ${item.Rating}</small>
                            </div>
                        </div>
                        `;
                        // -----------------------------
                    }).join('');
                } else {
                    resultBox.style.display = "block";
                    resultBox.innerHTML = "<p class='thong-bao-khong-tim-thay' style='padding:10px;text-align:center;color:#666;'>Không tìm thấy sản phẩm nào</p>";
                }
            })
            .catch(err => console.error("Lỗi tìm kiếm:", err));
    }
    
    // Ẩn khi click ra ngoài
    document.addEventListener("click", function (e) {
        if (!resultBox.contains(e.target) && e.target !== searchInput) {
            resultBox.style.display = "none";
        }
    });
});