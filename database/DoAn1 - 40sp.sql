drop database hethongquanlydocu;
create database hethongquanlydocu;
use hethongquanlydocu;
    
-- Bảng tài khoản người dùng
CREATE TABLE TaiKhoan ( -- sửa
	IdTaiKhoan INT(10) PRIMARY KEY AUTO_INCREMENT, -- sửa
    TenTK VARCHAR(100) not null,
    Email VARCHAR(100) not null unique,
    Sdt varchar(10) not null, -- sửa
    MatKhau VARCHAR(255) not null, -- sửa
    VaiTro tinyint DEFAULT 0 CHECK (VaiTro IN (0,1)),  -- sửa
    ThoiGianTao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP -- thêm
);
-- alter table taikhoan add column sdt char(10);
CREATE TABLE DiaChi (
    MaDC INT AUTO_INCREMENT PRIMARY KEY, 
    IdTaiKhoan INT(10), 
    DiaChiChiTiet VARCHAR(255) NOT NULL, -- Địa chỉ cụ thể
    MacDinh BOOLEAN DEFAULT 0,           -- Địa chỉ mặc định (0 = không, 1 = có)
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
-- Bảng danh mục hàng hóa
CREATE TABLE DanhMuc ( -- sửa
    MaDM INT(10) PRIMARY KEY AUTO_INCREMENT,
    TenDM VARCHAR(100)
);
-- Bảng hàng hóa
CREATE TABLE HangHoa ( -- sửa
    MaHH INT(10) PRIMARY KEY AUTO_INCREMENT,
    MaDM INT(10), 
    TenHH VARCHAR(255) not null,
    SoLuongHH SMALLINT unsigned,
    Gia DECIMAL(10) unsigned,
    GiaThiTruong DECIMAL(10, 0) DEFAULT 0,
    NgayThem DATETIME DEFAULT CURRENT_TIMESTAMP,
    ChatLuongHang ENUM('Mới', 'Đã qua sử dụng', 'Gần như mới'), -- them
    TinhTrangHang ENUM('Còn hàng', 'Hết hàng', 'Ngưng kinh doanh'), -- chỉnh nặng
    MoTa LONGTEXT,
    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM) ON DELETE CASCADE ON UPDATE CASCADE -- sửa
);

CREATE TABLE YeuThich ( -- sua
    MaYT INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT(10), -- sửa
    MaHH INT(10),
    NgayLuu DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE, -- sửa
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE, -- sửa
    UNIQUE (IdTaiKhoan, MaHH) -- Đảm bảo 1 khách không lưu trùng sản phẩm -- sửa
);
CREATE TABLE DanhGiaSao (
    MaDG INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT,
    MaHH INT,
    SoSao TINYINT CHECK (SoSao BETWEEN 1 AND 5),
    NgayDG DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('Hiển thị', 'Ẩn') DEFAULT 'Hiển thị',
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan),
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH),
    UNIQUE (IdTaiKhoan, MaHH) -- Mỗi người chỉ đánh giá 1 lần cho 1 sản phẩm
);
CREATE TABLE BinhLuan ( -- sửa
    MaBL INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT(10), -- sửa
    MaHH INT(10),
    NoiDung TEXT NOT NULL,
    NgayBL DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('Hiển thị','Ẩn') DEFAULT 'Hiển thị',
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE, -- sửa
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE -- sửa
);



create table HinhAnh ( -- sửa
	IDHinhAnh int(10) primary key auto_increment, -- sửa
    MaHH int(10),
    foreign key (MaHH) references HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE, -- sửa
    URL varchar(255) -- sửa
);

create table GioHang ( -- sửa
	MaGH int(10) primary key auto_increment,
    IdTaiKhoan int(10) unique, -- sửa
    foreign key (IdTaiKhoan) references TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE -- sửa
);

create table ChiTietGioHang ( -- sửa
	MaCTGH int(10) primary key auto_increment,
    MaGH int(10),
    foreign key (MaGH) references GioHang(MaGH) ON DELETE CASCADE ON UPDATE CASCADE, -- sửa
    MaHH int(10),
    foreign key (MaHH) references HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE, -- sửa
    SoLuong smallint unsigned
);

-- Bảng đơn hàng
CREATE TABLE DonHang ( -- sửa
    MaDH INT(10) PRIMARY KEY AUTO_INCREMENT,
    IdTaiKhoan INT(10), -- sửa
    NgayDat DATETIME DEFAULT CURRENT_TIMESTAMP,
    DiaChiGiao VARCHAR(255),
    TongTien DECIMAL(10) UNSIGNED,
    TrangThai ENUM('Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Hoàn tất', 'Đã hủy') DEFAULT 'Chờ xử lý',
    GhiChu TEXT,
    NgaySua DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) -- sửa
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
CREATE TABLE ThanhToan (
    MaTT INT AUTO_INCREMENT PRIMARY KEY,
    MaDH INT,
    MaThanhToan VARCHAR(50) UNIQUE, -- Mã giao dịch của ngân hàng / cổng thanh toán
    SoTien DECIMAL(10,2) NOT NULL,
    NgayThanhToan DATETIME DEFAULT CURRENT_TIMESTAMP,
    PhuongThuc ENUM('Tiền mặt', 'Chuyển khoản', 'Ví điện tử', 'Thẻ ngân hàng'),
    TrangThai ENUM('Thành công', 'Thất bại', 'Đang xử lý') DEFAULT 'Đang xử lý',
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) 
        ON DELETE CASCADE ON UPDATE CASCADE
);
-- Bảng chi tiết đơn hàng
CREATE TABLE ChiTietDonHang ( -- sửa
    MaCTDH INT(10) PRIMARY KEY AUTO_INCREMENT,
    MaDH INT(10),
    MaHH INT(10),
    SoLuongSanPham SMALLINT UNSIGNED, -- sửa
    DonGia DECIMAL(10,2) UNSIGNED,
    GiamGia DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) -- sủa
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) -- sửa
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE LichSuDonHang ( -- sửa
    MaLichSu INT AUTO_INCREMENT PRIMARY KEY, -- sửa
    MaDH INT,
    NgayThayDoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Hoàn tất', 'Đã hủy'),
    GhiChu TEXT,
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) ON DELETE CASCADE ON UPDATE CASCADE -- sửa
);
-- Bảng mã giảm giá
CREATE TABLE MaGiamGia (
    MaGG INT AUTO_INCREMENT PRIMARY KEY,
    Code VARCHAR(50) NOT NULL UNIQUE, -- Mã code giảm giá (ví dụ: SALE20)
    MoTa TEXT,
    GiaTri DECIMAL(5,2) NOT NULL, -- giá trị giảm (vd: 10.00 = 10%)
    SoLuong INT DEFAULT 0, -- số lượng mã phát hành
    LoaiApDung ENUM('MaCode', 'DongLoat') DEFAULT 'DongLoat',
    TrangThai ENUM('Hoạt động', 'Hết hạn', 'Ngừng') DEFAULT 'Hoạt động',
    NgayBatDau DATETIME DEFAULT CURRENT_TIMESTAMP,
    NgayKetThuc DATETIME
);

-- Bảng trung gian: mã giảm giá áp cho danh mục
CREATE TABLE MaGiamGiaDanhMuc (
    MaGG INT,
    MaDM INT,
    PRIMARY KEY (MaGG, MaDM),
    FOREIGN KEY (MaGG) REFERENCES MaGiamGia(MaGG) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ========================
-- Thêm danh mục
-- ========================


-- 2. TẠO DANH MỤC MỚI (Theo yêu cầu)

INSERT INTO DanhMuc (MaDM, TenDM) VALUES 
(1, 'Đồ gia dụng'),
(2, 'Linh kiện PC'),
(3, 'Máy tính'),
(4, 'Nội thất'),
(5, 'Quần áo'),
(6, 'Thiết bị chơi game'),
(7, 'Thiết bị điện tử'),
(8, 'Chưa phân loại'),
(9, 'Khác');

-- =============================================
-- 3. CHÈN 40 SẢN PHẨM VÀ HÌNH ẢNH
-- Lưu ý: Tôi set cứng MaHH = STT để folder ảnh khớp với ID sản phẩm    
-- =============================================

-- 1. Cây lau nhà
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(1, 1, 'Cây lau công nghiệp Cây lau nhà inox', 2, 120000, 150000, 'Mới', 'Còn hàng', '- Cây lau nhà công nghiệp 45 cm gồm có 1 khung và 2 tấm lau. - Cây lau nhà inox loại cán thẳng dài 132cm, thân cài chất liệu inox dày dặn chắc chắn. Tấm lau 45cm×15cm làm bằng sợi tổng hợp.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(1, 'assets/images/products/1/1-1.png'),
(1, 'assets/images/products/1/1-2.png'),
(1, 'assets/images/products/1/1-3.png');

-- 2. Hộp cơm
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(2, 1, 'Hộp cơm giữ nhiệt Lunch Box 4 tầng', 1, 150000, 200000, 'Mới', 'Còn hàng', 'Hộp Cơm Giữ Nhiệt Văn Phòng 4 Tầng Kèm Túi Cao Cấp Quay Được Lò Vi Sóng. Chất liệu: thép 304 và nhựa PP. Dung tích: 1560ml. Giữ nhiệt đến 4h.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(2, 'assets/images/products/2/2-1.png'),
(2, 'assets/images/products/2/2-2.png'),
(2, 'assets/images/products/2/2-3.png');

-- 3. Máy xay tỏi ớt
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(3, 1, 'Máy xay tỏi ớt thủ công Đức Huỳnh DN384', 2, 40000, 65000, 'Mới', 'Còn hàng', 'Xay siêu nhanh (chỉ cần kéo nhẹ vài lần). Lưỡi dao sắc bén làm từ thép không gỉ. Tiết kiệm thời gian. Thiết kế nhỏ gọn.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(3, 'assets/images/products/3/3-1.png'),
(3, 'assets/images/products/3/3-2.png'),
(3, 'assets/images/products/3/3-3.png');

-- 4. Nồi lẩu điện
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(4, 1, 'Nồi lẩu điện mini ZODAN', 1, 180000, 250000, 'Đã qua sử dụng', 'Còn hàng', 'Công suất: 50Hz/600W. Kích thước: ĐK 18cm x cao 10cm, dung tích 1.8 lit. Có xửng hấp. Lớp chống dính, chịu nhiệt độ cao.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(4, 'assets/images/products/4/4-1.png'),
(4, 'assets/images/products/4/4-2.png');

-- 5. Thớt nhựa
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(5, 1, 'Thớt nhựa tròn Việt Nhật', 1, 35000, 50000, 'Mới', 'Còn hàng', 'Thớt nhựa tròn Việt Nhật. Thiết kế nhỏ gọn, tiện lợi, bề mặt láng mịn. Chất liệu nhựa chắc chắn, bền đẹp. Có móc treo tiện lợi.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(5, 'assets/images/products/5/5-1.png');

-- 6. Board HDD Asus
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(6, 2, 'Board Kết Nối Ổ Cứng HDD Asus X540UP', 1, 120000, 180000, 'Mới', 'Còn hàng', 'Board kết nối ổ cứng HDD cho laptop Asus X540UP. Model: X540UP_ODD REV 2.0. Giúp khôi phục chức năng lưu trữ khi bo mạch cũ bị hỏng.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(6, 'assets/images/products/6/6-1.png');

-- 7. CPU i5
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(7, 2, 'CPU Intel Core i5-12400F (TRAY)', 2, 1600000, 2100000, 'Đã qua sử dụng', 'Còn hàng', 'Số nhân: 6, Số luồng: 12. Tốc độ tối đa: 4.4 GHz. Cache: 18MB. Socket: LGA 1700. Phiên bản TRAY không kèm quạt.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(7, 'assets/images/products/7/7-1.png');

-- 8. Dây LAN Cat6
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(8, 2, 'Dây cáp mạng LAN đúc sẵn 2 đầu Cat6 Unitek', 4, 25000, 45000, 'Mới', 'Còn hàng', 'Dây cáp mạng Cat6, 4 cặp dây xoắn, giảm nhiễu. Lõi kim loại đồng/hợp kim. Vỏ nhựa PVC bền bỉ.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(8, 'assets/images/products/8/8-1.png'),
(8, 'assets/images/products/8/8-2.png');

-- 9. Dây nguồn PC
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(9, 2, 'Dây cáp nguồn máy tính pc (2 chân)', 9, 20000, 35000, 'Gần như mới', 'Còn hàng', 'Dây Nguồn Máy Tính loại tốt. Lõi Đồng. Dài 1.8m. Tiết diện 3x 0.75mm. Chịu điện áp 220-250V - 10A. Vỏ ABS chống cháy.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(9, 'assets/images/products/9/9-1.png'),
(9, 'assets/images/products/9/9-2.png');

-- 10. Mạch boost áp
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(10, 2, 'Mạch boost áp TPS61088 mod lên 19V 60W', 8, 60000, 90000, 'Gần như mới', 'Còn hàng', 'IC TPS61088. Input: 2.7V-12VDC. Output mod: 19VDC. Công suất đỉnh: 60W. Hiệu suất ~91%.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(10, 'assets/images/products/10/10-1.png'),
(10, 'assets/images/products/10/10-2.png'),
(10, 'assets/images/products/10/10-3.png'),
(10, 'assets/images/products/10/10-4.png');

-- 11. Quạt tản nhiệt Centaur
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(11, 2, 'Quạt tản nhiệt Fan Case Led CENTAUR M2', 2, 40000, 70000, 'Mới', 'Còn hàng', 'Quạt tản nhiệt RGB 16 triệu màu. Cánh đen chống bụi. Tuổi thọ 20,000 giờ. Trục Hydro Bearing êm ái.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(11, 'assets/images/products/11/11-1.png'),
(11, 'assets/images/products/11/11-2.png');

-- 12. Module hạ áp
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(12, 2, 'Mô-đun Hạ Áp Mini360 DC-DC 2A', 1, 10000, 20000, 'Mới', 'Còn hàng', 'Thay thế LM2596. Input: 4.75V-23V. Output: 1V-17V. Dòng max 2A. Hiệu suất 95%. Kích thước siêu nhỏ.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(12, 'assets/images/products/12/12-1.png'),
(12, 'assets/images/products/12/12-2.png');

-- 13. Màn hình Fujitsu
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(13, 3, 'Màn hình 22 Fujitsu E22-8 Ts Pro (Nội Địa Nhật)', 1, 700000, 950000, 'Gần như mới', 'Còn hàng', 'LCD IPS Panel. Kích thước: 22 inch. Full HD 1920x1080. Kết nối: VGA, DVI, DP, USB. Tích hợp loa.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(13, 'assets/images/products/13/13-1.png'),
(13, 'assets/images/products/13/13-2.png');

-- 14. Màn hình E-Dra
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(14, 3, 'Màn Hình E-Dra EGM22F100 FHD IPS 100Hz', 2, 1100000, 1450000, 'Gần như mới', 'Còn hàng', '21.5 inch, IPS. Full HD 100Hz. Phản hồi 5ms. Cổng HDMI, VGA. Công nghệ bảo vệ mắt.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(14, 'assets/images/products/14/14-1.png'),
(14, 'assets/images/products/14/14-2.png');

-- 15. Màn hình Xiaomi
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(15, 3, 'Màn Hình Xiaomi Monitor A27i / A24i IPS 100Hz', 2, 1400000, 1890000, 'Gần như mới', 'Còn hàng', '27 inch (A27i). Tấm nền IPS. Full HD 100Hz. Phản hồi 6ms. Thiết kế mỏng đẹp.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(15, 'assets/images/products/15/15-1.png'),
(15, 'assets/images/products/15/15-2.png');

-- 16. Bàn trà
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(16, 4, 'Bàn trà tròn kiểu dáng hiện đại / bàn kim cương', 2, 300000, 550000, 'Mới', 'Còn hàng', 'Kích thước: d60 x cao 45. Chân sắt sơn tĩnh điện. Mặt bàn kính trơn hoặc in 3D vân mây.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(16, 'assets/images/products/16/16-1.png'),
(16, 'assets/images/products/16/16-2.png');

-- 17. Đèn ngủ
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(17, 4, 'Đèn Ngủ Để Bàn Chân Gỗ Xếp Ly Hàn Quốc', 5, 60000, 120000, 'Mới', 'Còn hàng', 'Đường kính 22cm, cao 30cm. Chân gỗ, chụp đèn vải xếp ly. Nguồn USB, ánh sáng vàng ấm áp.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(17, 'assets/images/products/17/17-1.png'),
(17, 'assets/images/products/17/17-2.png'),
(17, 'assets/images/products/17/17-3.png');

-- 18. Tranh đèn Led
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(18, 4, 'Tranh Cắm Hoa Đèn Led Treo phòng khách', 1, 150000, 280000, 'Mới', 'Còn hàng', 'Có đèn led điều khiển từ xa. Tranh tráng gương in UV sắc nét trên mica. Tạo điểm nhấn sang trọng.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(18, 'assets/images/products/18/18-1.png');

-- 19. Tranh sắt
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(19, 4, 'Tranh sắt treo tường trang trí MOD Decor', 1, 380000, 650000, 'Mới', 'Còn hàng', 'Kích thước 135x60cm. Hợp kim mạ sơn tĩnh điện 5 lớp, độ bền 20 năm. Phong cách Bắc Âu.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(19, 'assets/images/products/19/19-1.png'),
(19, 'assets/images/products/19/19-2.png');

-- 20. Áo sơ mi nữ
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(20, 5, 'Áo Sơ Mi Hồng Nữ Rời Phong Cách Văn Học', 3, 150000, 250000, 'Mới', 'Ngưng kinh doanh', 'Chất liệu 100% Polyester. Phong cách thường ngày, vải không xuyên thấu, không co giãn. Màu hồng nhẹ nhàng.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(20, 'assets/images/products/20/20-1.png'),
(20, 'assets/images/products/20/20-2.png'),
(20, 'assets/images/products/20/20-3.png');

-- 21. Áo thun Teelab
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(21, 5, 'Áo thun raplang tay lỡ unisex Teelab', 1, 120000, 180000, 'Gần như mới', 'Còn hàng', 'Chất liệu Cotton. Form Oversize rộng rãi. Màu Trắng/Xám Tiêu. Hình in lụa bền màu.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(21, 'assets/images/products/21/21-1.png');

-- 22. Chân váy
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(22, 5, 'Chân váy đính nơ KÈM QUẦN Higtk-fashion', 1, 140000, 220000, 'Gần như mới', 'Còn hàng', 'Chất vải mềm mại. Thiết kế đính nơ dễ thương kèm quần bảo hộ bên trong. Bền màu.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(22, 'assets/images/products/22/22-1.png'),
(22, 'assets/images/products/22/22-2.png');

-- 23. Áo Nasa
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(23, 5, 'Nasa Khớp Ngắn Tay Nam Nữ (Xu hướng Hè 2025)', 3, 90000, 150000, 'Gần như mới', 'Ngưng kinh doanh', 'Áo thun in hình Nasa. Tay ngắn, form rộng. Phong cách học đường trẻ trung. Mùa xuân 2025.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(23, 'assets/images/products/23/23-1.png');

-- 24. Quần jean nữ
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(24, 5, 'Quần jean bò ống suông rộng nữ cạp cao', 1, 220000, 350000, 'Mới', 'Còn hàng', 'Chất liệu Jean cotton cao cấp. Ống suông hack dáng. Màu trắng tinh khôi, thêu họa tiết tinh tế.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(24, 'assets/images/products/24/24-1.png'),
(24, 'assets/images/products/24/24-2.png'),
(24, 'assets/images/products/24/24-3.png');

-- 25. Quần short unisex
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(25, 5, 'Quần short unisex Gapazi', 5, 160000, 240000, 'Mới', 'Còn hàng', 'Chất cotton tổ ong (95% cotton). Relax fit. Vải dày dặn, thoáng mát. Xuất xứ Việt Nam.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(25, 'assets/images/products/25/25-1.png'),
(25, 'assets/images/products/25/25-2.png'),
(25, 'assets/images/products/25/25-3.png');

-- 26. Bộ chuyển đổi game
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(26, 6, 'Bộ chuyển đổi chơi game chuyên nghiệp MOBA', 1, 350000, 590000, 'Mới', 'Còn hàng', 'Hỗ trợ Android. 6 cổng kết nối (phím, chuột, sạc...). Hỗ trợ ghìm tâm, auto tap, xuất hình ra màn hình lớn.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(26, 'assets/images/products/26/26-1.png'),
(26, 'assets/images/products/26/26-2.png'),
(26, 'assets/images/products/26/26-3.png');

-- 27. Tấm dẫn nhiệt
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(27, 6, 'Tấm dẫn nhiệt mở rộng cho điện thoại X Cooler', 2, 60000, 99000, 'Mới', 'Còn hàng', 'Chất liệu hợp kim. Gắn mặt sau điện thoại giúp tăng diện tích tản nhiệt cho quạt sò lạnh.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(27, 'assets/images/products/27/27-1.png'),
(27, 'assets/images/products/27/27-2.png'),
(27, 'assets/images/products/27/27-3.png');

-- 28. Tay cầm Backbone
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(28, 6, 'Tay cầm chơi game Backbone One cho iPhone 15', 1, 1800000, 2600000, 'Mới', 'Còn hàng', 'Kết nối USB-C. Thiết kế tiện dụng giảm mỏi tay. Tương thích cao, biến iPhone thành máy game chuyên nghiệp.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(28, 'assets/images/products/28/28-1.png'),
(28, 'assets/images/products/28/28-2.png'),
(28, 'assets/images/products/28/28-3.png');

-- 29. Tay cầm PC không dây
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(29, 6, 'Tay cầm chơi game PC/Laptop/TV không dây', 2, 250000, 450000, 'Gần như mới', 'Ngưng kinh doanh', 'Tích hợp 666 game cổ điển. Kết nối 2.4G độ trễ thấp. Hỗ trợ chế độ 2 người chơi.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(29, 'assets/images/products/29/29-1.png'),
(29, 'assets/images/products/29/29-2.png'),
(29, 'assets/images/products/29/29-3.png');

-- 30. Nút bấm PUBG
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(30, 6, 'Thiết bị hỗ trợ chơi game PUBG (L1R1)', 3, 80000, 150000, 'Gần như mới', 'Còn hàng', 'Nút bấm vật lý L1R1 cho điện thoại. Chất liệu ABS + Hợp kim kẽm. Nhạy, dễ sử dụng.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(30, 'assets/images/products/30/30-1.png'),
(30, 'assets/images/products/30/30-2.png'),
(30, 'assets/images/products/30/30-3.png');

-- 31. Bút thử điện
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(31, 7, 'Bút tua vít DIYMORE AC100-500V', 2, 120000, 180000, 'Mới', 'Ngưng kinh doanh', 'Đầu tua vít đa năng. Có đèn báo dòng điện (đỏ/xanh). Đo thông mạch, đo điện áp 24-250V. Nam châm mạnh.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(31, 'assets/images/products/31/31-1.png'),
(31, 'assets/images/products/31/31-2.png');

-- 32. Máy đo điện tử
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(32, 7, 'Máy đo điện tử tự động DIYMORE SZ01SZ02', 1, 450000, 650000, 'Mới', 'Còn hàng', 'Đo điện áp, nguồn điện DC 5-24V. Màn hình số thông minh. Công suất max 100W. Chip phát hiện thông minh.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(32, 'assets/images/products/32/32-1.png'),
(32, 'assets/images/products/32/32-2.png'),
(32, 'assets/images/products/32/32-3.png');

-- 33. Đường đua ô tô
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(33, 8, 'Đồ Chơi Đường Đua Xe Ô Tô Màn Hình Điện Tử', 1, 120000, 220000, 'Đã qua sử dụng', 'Ngưng kinh doanh', 'Có vô lăng điều khiển. Màu xanh/cam. Chất liệu ABS an toàn. Dành cho bé trên 6 tuổi.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(33, 'assets/images/products/33/33-1.png'),
(33, 'assets/images/products/33/33-2.png'),
(33, 'assets/images/products/33/33-3.png');

-- 34. Xe bus mô hình
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(34, 8, 'Mô hình xe bus chở khách hạng thương gia KAVY', 2, 250000, 390000, 'Mới', 'Ngưng kinh doanh', 'Tỷ lệ 1:32. Vỏ hợp kim. Có đèn nhạc, mở cửa được. Chạy trớn (pull-back).');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(34, 'assets/images/products/34/34-1.png'),
(34, 'assets/images/products/34/34-2.png'),
(34, 'assets/images/products/34/34-3.png');

-- 35. Xe đạp cân bằng iiko
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(35, 8, 'Xe Đạp Cân Bằng iiko Cao Cấp (360° Xoay)', 3, 950000, 1450000, 'Mới', 'Còn hàng', 'Khung thép carbon chịu lực 80kg. Bánh đúc chống móp. Tay lái xoay 360 độ. Siêu nhẹ 1.4kg. Cho bé 2-8 tuổi.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(35, 'assets/images/products/35/35-1.png'),
(35, 'assets/images/products/35/35-2.png');

-- 36. Xe đạp Jinbao
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(36, 8, 'Xe đạp trẻ em 2 KHUNG Jinbao (hình công chúa)', 1, 650000, 950000, 'Mới', 'Ngưng kinh doanh', 'Xe thăng bằng 2 bánh khung thép. Tải trọng 30kg. Yên tay lái điều chỉnh được. Giúp bé rèn luyện thể chất.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(36, 'assets/images/products/36/36-1.png'),
(36, 'assets/images/products/36/36-2.png');

-- 37. Xe mô tô R1000
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(37, 8, 'Xe Mô tô cho bé R1000 (có đèn nhạc)', 2, 900000, 1350000, 'Mới', 'Còn hàng', 'Xe điện trẻ em R1000. Nhựa ABS. Có đèn pha, nhạc. Di chuyển 4 chiều. Cho bé từ 1 tuổi.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(37, 'assets/images/products/37/37-1.png'),
(37, 'assets/images/products/37/37-2.png'),
(37, 'assets/images/products/37/37-3.png');

-- 38. Ô Gấp Thái Lan
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(38, 9, 'Ô Gấp leo Thái Lan màu xanh', 1, 169000, 250000, 'Mới', 'Còn hàng', 'Khung kim loại tráng bạc chống rỉ. Nhẹ 260g. Vải chống nắng chống ẩm cực tốt.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(38, 'assets/images/products/38/38-1.png'),
(38, 'assets/images/products/38/38-2.png'),
(38, 'assets/images/products/38/38-3.png');

-- 39. Truyện chữ
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(39, 9, 'Truyện chữ Nhật Bản Vol 17', 1, 500000, 800000, 'Đã qua sử dụng', 'Còn hàng', 'Tác giả: Natsume Akatsuki. NXB Little Brown. Nhập khẩu Mỹ. Tình trạng: Mới tinh. Bìa mềm.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(39, 'assets/images/products/39/39-1.png');

-- 40. Viên ngậm
INSERT INTO HangHoa (MaHH, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, MoTa) VALUES 
(40, 9, 'Viên ngậm sát trùng Tyrotab Pharmedic', 2, 39000, 60000, 'Mới', 'Còn hàng', 'Điều trị viêm họng, amidan, viêm lợi. Thành phần Tyrothricin, Tetracain. Hộp còn 8 vỉ.');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(40, 'assets/images/products/40/40-1.png'),
(40, 'assets/images/products/40/40-2.png'),
(40, 'assets/images/products/40/40-3.png');

-- Thêm 6 tài khoản người dùng
INSERT INTO TaiKhoan (TenTK, Email, Sdt, MatKhau, VaiTro) VALUES
('kha', 'vtchoangkha@gmail.com', '0913420982', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm',0), -- mật khẩu 123456
('nha', 'nha@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm',0), -- mật khẩu 123456
('quyen', 'quyen@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm',0), -- mật khẩu 123456
('lai', 'lai@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm',0), -- mật khẩu 123456

('admin', 'admin@gmail.com', '123456780', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm',1), -- mật khẩu 123456
('Nguyễn Văn A', 'vana@example.com', '0912345678', '123456', 0),
('Trần Thị B', 'thib@example.com', '0987654321', '123456', 0),
('Lê Văn C', 'vanc@example.com', '0901111222', '123456', 0),
('Phạm Thị D', 'thid@example.com', '0930304444', '123456', 0),
('Hoàng Văn E', 'vane@example.com', '0945555666', '123456', 0),
('Đỗ Thị F', 'thif@example.com', '0977778888', '123456', 0);


-- Thêm địa chỉ cho mỗi tài khoản
INSERT INTO DiaChi (IdTaiKhoan, DiaChiChiTiet, MacDinh) VALUES
(1, '123 Đường A, Quận 1, TP.HCM', 1),
(2, '456 Đường B, Quận 2, Hà Nội', 1),
(3, '789 Đường C, Quận 3, Đà Nẵng', 1),
(4, '321 Đường D, Quận 4, Cần Thơ', 1),
(5, '654 Đường E, Quận 5, Hải Phòng', 1),
(6, '987 Đường F, Quận 6, Huế', 1);



-- 1. Tạo mã giảm giá 20% cho Đồ gia dụng
INSERT INTO MaGiamGia (Code, MoTa, GiaTri, SoLuong, TrangThai)
VALUES ('SALE20_DG', 'Giảm 20% cho đồ gia dụng', 20.00, 100, 'Hoạt động');

-- Lấy MaGG vừa tạo (giả sử MySQL >= 8, dùng LAST_INSERT_ID)
SET @MaGG_DG = LAST_INSERT_ID();

INSERT INTO MaGiamGiaDanhMuc (MaGG, MaDM)
VALUES (@MaGG_DG, 1);

-- 2. Tạo mã giảm giá 20% cho Linh kiện PC
INSERT INTO MaGiamGia (Code, MoTa, GiaTri, SoLuong, TrangThai)
VALUES ('SALE20_LK', 'Giảm 20% cho linh kiện PC', 20.00, 100, 'Hoạt động');

SET @MaGG_LK = LAST_INSERT_ID();

INSERT INTO MaGiamGiaDanhMuc (MaGG, MaDM)
VALUES (@MaGG_LK, 2);
-- [MỚI] Thêm mã giảm giá cho Thiết bị điện tử
INSERT INTO MaGiamGia (Code, MoTa, GiaTri, SoLuong, TrangThai)
VALUES ('SALE10_DT', 'Giảm 10% cho thiết bị điện tử', 10.00, 50, 'Hoạt động');
SET @MaGG_DT = LAST_INSERT_ID();
INSERT INTO MaGiamGiaDanhMuc (MaGG, MaDM) VALUES (@MaGG_DT, 7);
-- Giả sử bạn test cho sản phẩm MaHH = 1 (Cây lau công nghiệp)
-- 10 bình luận từ 6 user khác nhau

-- =============================================
-- XOÁ DỮ LIỆU ĐÁNH GIÁ VÀ BÌNH LUẬN CŨ
-- =============================================
DELETE FROM BinhLuan;
ALTER TABLE BinhLuan AUTO_INCREMENT = 1; -- Đặt lại AUTO_INCREMENT cho sạch

DELETE FROM DanhGiaSao;
ALTER TABLE DanhGiaSao AUTO_INCREMENT = 1; -- Đặt lại AUTO_INCREMENT cho sạch


-- =============================================
-- TẠO DỮ LIỆU ĐÁNH GIÁ SAO VÀ BÌNH LUẬN MỚI
-- (Tài khoản đánh giá và bình luận cùng lúc cho 1 sản phẩm)
-- =============================================

-- Khởi tạo danh sách các bình luận và đánh giá chung
-- Dùng một bộ (IdTaiKhoan, MaHH, SoSao, NoiDung)
INSERT INTO DanhGiaSao (IdTaiKhoan, MaHH, SoSao, TrangThai) VALUES
(1, 1, 5, 'Hiển thị'), -- Cây lau
(2, 1, 4, 'Hiển thị'),
(3, 1, 5, 'Hiển thị'),
(4, 1, 4, 'Hiển thị'),

(1, 2, 4, 'Hiển thị'), -- Hộp cơm
(5, 2, 5, 'Hiển thị'),
(6, 2, 3, 'Hiển thị'),

(1, 7, 5, 'Hiển thị'), -- CPU i5
(2, 7, 5, 'Hiển thị'),
(3, 7, 4, 'Hiển thị'),
(4, 7, 5, 'Hiển thị'),
(5, 7, 5, 'Hiển thị'),

(1, 13, 4, 'Hiển thị'), -- Màn hình Fujitsu
(6, 13, 4, 'Hiển thị'),

(2, 16, 5, 'Hiển thị'), -- Bàn trà
(3, 16, 5, 'Hiển thị'),
(4, 16, 5, 'Hiển thị'),

(5, 21, 4, 'Hiển thị'), -- Áo thun Teelab
(6, 21, 5, 'Hiển thị'),
(1, 21, 4, 'Hiển thị'),

(2, 28, 5, 'Hiển thị'), -- Tay cầm Backbone
(3, 28, 5, 'Hiển thị'),
(4, 28, 4, 'Hiển thị'),

(5, 30, 3, 'Hiển thị'), -- Nút bấm PUBG
(6, 30, 4, 'Hiển thị'),

(1, 35, 5, 'Hiển thị'), -- Xe đạp cân bằng
(2, 35, 5, 'Hiển thị'),
(3, 35, 5, 'Hiển thị'),

(4, 40, 5, 'Hiển thị'), -- Viên ngậm
(5, 40, 4, 'Hiển thị');


INSERT INTO BinhLuan (IdTaiKhoan, MaHH, NoiDung, TrangThai) VALUES
-- Cây lau (MaHH = 1)
(1, 1, 'Cây lau chắc chắn, lau sạch, giao hàng rất nhanh.', 'Hiển thị'),
(2, 1, 'Mặt hàng gia dụng này chất lượng hơn tôi nghĩ, rất đáng tiền!', 'Hiển thị'),
(3, 1, 'Lau nhà nhẹ nhàng, thiết kế thông minh, không cần dùng tay vắt.', 'Hiển thị'),
(4, 1, 'Chất liệu inox sáng bóng, dùng lâu không sợ rỉ sét.', 'Hiển thị'),

-- Hộp cơm (MaHH = 2)
(1, 2, 'Hộp cơm giữ nhiệt tốt, đủ dùng cho bữa trưa văn phòng.', 'Hiển thị'),
(5, 2, 'Thiết kế 4 tầng tiện lợi, có kèm túi xách đi làm.', 'Hiển thị'),
(6, 2, 'Giữ nhiệt được khoảng 3 tiếng, hơi ít so với quảng cáo 4 tiếng.', 'Hiển thị'),

-- CPU i5 (MaHH = 7)
(1, 7, 'CPU TRAY nhưng hoạt động hoàn hảo, đã test full load 100%.', 'Hiển thị'),
(2, 7, 'Hàng đã qua sử dụng nhưng còn rất mới, hiệu năng tuyệt vời.', 'Hiển thị'),
(3, 7, 'Giá tốt nhất thị trường cho con chip này, nên mua ngay.', 'Hiển thị'),
(4, 7, 'Giao hàng có bọc chống sốc kỹ, lắp vào chạy ngay, không lỗi lầm.', 'Hiển thị'),
(5, 7, 'Làm việc và chơi game đều mượt, rất hài lòng với tốc độ xử lý.', 'Hiển thị'),

-- Màn hình Fujitsu (MaHH = 13)
(1, 13, 'Màn hình nội địa Nhật, màu sắc đẹp, có tích hợp loa khá ổn.', 'Hiển thị'),
(6, 13, 'Chất lượng hình ảnh tốt, không điểm chết, dùng để code rất ok.', 'Hiển thị'),

-- Bàn trà (MaHH = 16)
(2, 16, 'Bàn kim cương rất đẹp, decor phòng khách sang trọng hơn hẳn.', 'Hiển thị'),
(3, 16, 'Mặt kính vân mây nhìn rất nghệ thuật, chân sắt vững chắc.', 'Hiển thị'),
(4, 16, 'Lắp ráp dễ dàng, kích thước vừa phải, rất ưng ý!', 'Hiển thị'),

-- Áo thun Teelab (MaHH = 21)
(5, 21, 'Áo form oversize thoải mái, chất cotton mặc mát.', 'Hiển thị'),
(6, 21, 'Hình in lụa rõ nét, giặt không bị bong tróc, đáng giá 5 sao.', 'Hiển thị'),
(1, 21, 'Màu xám tiêu phối đồ rất dễ, nên có trong tủ đồ.', 'Hiển thị'),

-- Tay cầm Backbone (MaHH = 28)
(2, 28, 'Biến iPhone thành máy game thực thụ, trải nghiệm rất đã.', 'Hiển thị'),
(3, 28, 'Tay cầm nhạy, không độ trễ, chơi game AAA trên điện thoại cực đỉnh.', 'Hiển thị'),
(4, 28, 'Giá hơi cao nhưng xứng đáng cho game thủ chuyên nghiệp.', 'Hiển thị'),

-- Nút bấm PUBG (MaHH = 30)
(5, 30, 'Nút bấm hơi lỏng lẻo một chút, nhưng vẫn dùng được.', 'Hiển thị'),
(6, 30, 'Giá rẻ, dùng tạm ổn để chơi PUBG, cải thiện được tốc độ phản xạ.', 'Hiển thị'),

-- Xe đạp cân bằng (MaHH = 35)
(1, 35, 'Xe siêu nhẹ, bé 3 tuổi nhà tôi tự đạp được ngay.', 'Hiển thị'),
(2, 35, 'Bánh đúc chống móp rất bền, yên xe điều chỉnh dễ dàng.', 'Hiển thị'),
(3, 35, 'Thiết kế đẹp, màu sắc bắt mắt, bé rất thích chiếc xe này.', 'Hiển thị'),

-- Viên ngậm (MaHH = 40)
(4, 40, 'Hàng chính hãng, ngậm đỡ đau họng ngay, sẽ mua lại.', 'Hiển thị'),
(5, 40, 'Thuốc có tác dụng nhanh, vị hơi khó ngậm nhưng hiệu quả.', 'Hiển thị');



-- ---------------------------------------------

INSERT INTO DonHang (IdTaiKhoan, NgayDat, DiaChiGiao, TrangThai, GhiChu, TongTien) VALUES 
-- THÁNG 1 (Tết dương/âm lịch - Nhu cầu cao)
(1, '2025-01-05 09:30:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', 'Giao giờ hành chính', 0),
(2, '2025-01-12 14:15:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', 'Gọi trước khi giao', 0),
(3, '2025-01-25 18:20:00', '789 Đường C, Quận 3, Đà Nẵng', 'Đã hủy', 'Khách đổi ý không mua nữa', 0),

-- THÁNG 2 (Sau tết)
(4, '2025-02-10 10:00:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', NULL, 0),
(5, '2025-02-28 08:45:00', '654 Đường E, Quận 5, Hải Phòng', 'Hoàn tất', 'Giao cho bảo vệ', 0),

-- THÁNG 3
(6, '2025-03-05 11:30:00', '987 Đường F, Quận 6, Huế', 'Hoàn tất', NULL, 0),
(1, '2025-03-15 15:20:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', 'Giao nhanh giúp mình', 0),

-- THÁNG 4
(2, '2025-04-02 09:10:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', NULL, 0),
(3, '2025-04-20 16:50:00', '789 Đường C, Quận 3, Đà Nẵng', 'Hoàn tất', 'Hàng dễ vỡ xin nhẹ tay', 0),

-- THÁNG 5 (Mùa hè - Nhu cầu đồ điện tử/quần áo)
(4, '2025-05-05 13:40:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', NULL, 0),
(5, '2025-05-18 10:25:00', '654 Đường E, Quận 5, Hải Phòng', 'Hoàn tất', NULL, 0),
(6, '2025-05-30 19:15:00', '987 Đường F, Quận 6, Huế', 'Đã hủy', 'Sai địa chỉ', 0),

-- THÁNG 6
(1, '2025-06-10 08:30:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', NULL, 0),
(2, '2025-06-25 14:00:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', 'Giao buổi chiều', 0),

-- THÁNG 7
(3, '2025-07-07 09:45:00', '789 Đường C, Quận 3, Đà Nẵng', 'Hoàn tất', NULL, 0),
(4, '2025-07-22 17:30:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', NULL, 0),

-- THÁNG 8 (Mùa tựu trường - Nhu cầu Laptop/PC)
(5, '2025-08-05 11:15:00', '654 Đường E, Quận 5, Hải Phòng', 'Hoàn tất', 'Cần gấp cho con đi học', 0),
(6, '2025-08-15 15:50:00', '987 Đường F, Quận 6, Huế', 'Hoàn tất', NULL, 0),
(1, '2025-08-28 12:20:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', NULL, 0),

-- THÁNG 9
(2, '2025-09-09 10:10:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', NULL, 0),
(3, '2025-09-21 16:40:00', '789 Đường C, Quận 3, Đà Nẵng', 'Hoàn tất', NULL, 0),

-- THÁNG 10 (Gần hiện tại - Trạng thái đang xử lý/giao hàng)
(4, '2025-10-02 08:50:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', NULL, 0),
(5, '2025-10-10 14:30:00', '654 Đường E, Quận 5, Hải Phòng', 'Đang giao', 'Đang đợi shipper', 0),
(6, '2025-10-12 09:15:00', '987 Đường F, Quận 6, Huế', 'Đã xác nhận', 'Chuẩn bị đóng gói', 0),
(1, '2025-10-13 18:00:00', '123 Đường A, Quận 1, TP.HCM', 'Chờ xử lý', 'Vừa đặt xong', 0);

-- ---------------------------------------------
-- BƯỚC 2: TẠO CHI TIẾT ĐƠN HÀNG (Mapping sản phẩm vào đơn)
-- ---------------------------------------------

INSERT INTO ChiTietDonHang (MaDH, MaHH, SoLuongSanPham, DonGia, GiamGia) VALUES
-- Đơn 1 (Tháng 1): Mua Cây lau nhà (MaHH 1) và Hộp cơm (MaHH 2)
(1, 1, 1, 120000, 0),
(1, 2, 2, 150000, 10000),

-- Đơn 2 (Tháng 1): Mua Màn hình Fujitsu (MaHH 13)
(2, 13, 1, 700000, 0),

-- Đơn 3 (Tháng 1 - Đã hủy): Mua Bàn trà (MaHH 16)
(3, 16, 1, 300000, 0),

-- Đơn 4 (Tháng 2): Mua Áo sơ mi (MaHH 20) và Quần Jean (MaHH 24)
(4, 20, 2, 150000, 0),
(4, 24, 1, 220000, 0),

-- Đơn 5 (Tháng 2): Mua Tay cầm game (MaHH 28)
(5, 28, 1, 1800000, 50000),

-- Đơn 6 (Tháng 3): Mua Máy xay tỏi (MaHH 3) số lượng nhiều
(6, 3, 5, 40000, 0),

-- Đơn 7 (Tháng 3): Mua CPU i5 (MaHH 7) và Tản nhiệt (MaHH 11)
(7, 7, 1, 1600000, 0),
(7, 11, 2, 40000, 0),

-- Đơn 8 (Tháng 4): Mua Xe đạp cân bằng (MaHH 35)
(8, 35, 1, 950000, 20000),

-- Đơn 9 (Tháng 4): Mua Nồi lẩu (MaHH 4)
(9, 4, 1, 180000, 0),

-- Đơn 10 (Tháng 5): Mua Áo thun Teelab (MaHH 21)
(10, 21, 3, 120000, 0),

-- Đơn 11 (Tháng 5): Mua Màn hình Xiaomi (MaHH 15)
(11, 15, 1, 1400000, 0),

-- Đơn 12 (Tháng 5 - Hủy): Mua Tranh sắt (MaHH 19)
(12, 19, 1, 380000, 0),

-- Đơn 13 (Tháng 6): Mua Xe mô tô bé (MaHH 37)
(13, 37, 1, 900000, 0),

-- Đơn 14 (Tháng 6): Mua Dây LAN (MaHH 8)
(14, 8, 10, 25000, 0),

-- Đơn 15 (Tháng 7): Mua Đèn ngủ (MaHH 17)
(15, 17, 2, 60000, 0),

-- Đơn 16 (Tháng 7): Mua Quần short (MaHH 25)
(16, 25, 4, 160000, 0),

-- Đơn 17 (Tháng 8 - Tựu trường): Mua Màn hình E-Dra (MaHH 14) + Dây nguồn (MaHH 9)
(17, 14, 1, 1100000, 0),
(17, 9, 2, 20000, 0),

-- Đơn 18 (Tháng 8): Mua Tay cầm PC (MaHH 29)
(18, 29, 2, 250000, 10000),

-- Đơn 19 (Tháng 8): Mua Bút thử điện (MaHH 31)
(19, 31, 1, 120000, 0),

-- Đơn 20 (Tháng 9): Mua Truyện chữ (MaHH 39)
(20, 39, 1, 500000, 0),

-- Đơn 21 (Tháng 9): Mua Ô gấp (MaHH 38)
(21, 38, 2, 169000, 0),

-- Đơn 22 (Tháng 10): Mua Viên ngậm (MaHH 40)
(22, 40, 5, 39000, 0),

-- Đơn 23 (Tháng 10 - Đang giao): Mua Mạch boost áp (MaHH 10)
(23, 10, 3, 60000, 0),

-- Đơn 24 (Tháng 10 - Đã xác nhận): Mua Thớt nhựa (MaHH 5)
(24, 5, 2, 35000, 0),

-- Đơn 25 (Tháng 10 - Chờ xử lý): Mua CPU i5 (MaHH 7) - Giá cao
(25, 7, 1, 1600000, 50000);

UPDATE DonHang dh
SET TongTien = (
    SELECT SUM(SoLuongSanPham * DonGia - GiamGia)
    FROM ChiTietDonHang ctdh
    WHERE ctdh.MaDH = dh.MaDH
);