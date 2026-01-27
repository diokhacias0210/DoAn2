DROP DATABASE IF EXISTS doan2;
CREATE DATABASE doan2;
USE doan2;

-- 1. Bảng tài khoản người dùng
CREATE TABLE TaiKhoan ( 
    IdTaiKhoan INT(10) PRIMARY KEY AUTO_INCREMENT, 
    TenTK VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Sdt VARCHAR(10) NOT NULL, 
    MatKhau VARCHAR(255) NOT NULL, 
    VaiTro TINYINT DEFAULT 0 CHECK (VaiTro IN (0,1)), 
    IdGoogle VARCHAR(255) DEFAULT NULL, 
    TrangThaiBanHang ENUM('ChuaKichHoat','DangHoatDong','BiKhoa') DEFAULT 'ChuaKichHoat', 
    ThoiGianTao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Avatar VARCHAR(255) DEFAULT NULL
);

-- 2. Bảng địa chỉ
CREATE TABLE DiaChi (
    MaDC INT AUTO_INCREMENT PRIMARY KEY, 
    IdTaiKhoan INT(10), 
    DiaChiChiTiet VARCHAR(255) NOT NULL, 
    MacDinh BOOLEAN DEFAULT 0, 
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 3. Bảng danh mục
CREATE TABLE DanhMuc ( 
    MaDM INT(10) PRIMARY KEY AUTO_INCREMENT,
    TenDM VARCHAR(100)
);

-- 4. Bảng hàng hóa (ĐÃ SỬA LỖI THIẾU DẤU PHẨY)
CREATE TABLE HangHoa ( 
    MaHH INT(10) PRIMARY KEY AUTO_INCREMENT,
    IdNguoiBan INT(10) NOT NULL, 
    MaDM INT(10), 
    TenHH VARCHAR(255) NOT NULL,
    SoLuongHH SMALLINT UNSIGNED,
    Gia DECIMAL(10,2) UNSIGNED,
    GiaThiTruong DECIMAL(10, 2) DEFAULT 0,
    NgayThem DATETIME DEFAULT CURRENT_TIMESTAMP,
    ChatLuongHang ENUM('Mới', 'Đã qua sử dụng', 'Gần như mới'),
    TinhTrangHang ENUM('Còn hàng', 'Hết hàng', 'Ngưng kinh doanh'), 
    TrangThaiDuyet ENUM('ChoDuyet','DaDuyet','TuChoi') DEFAULT 'ChoDuyet',
    LyDoTuChoi TEXT DEFAULT NULL, 
    MoTa LONGTEXT,
    HienThi TINYINT(1) DEFAULT 1,
    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM) ON DELETE CASCADE ON UPDATE CASCADE, -- Đã thêm dấu phẩy tại đây
    FOREIGN KEY (IdNguoiBan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE 
);

-- 5. Bảng yêu thích
CREATE TABLE YeuThich (
    MaYT INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT(10),
    MaHH INT(10),
    NgayLuu DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE, 
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE (IdTaiKhoan, MaHH) 
);

-- 6. Bảng đánh giá sao
CREATE TABLE DanhGiaSao (
    MaDG INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT,
    MaHH INT,
    SoSao TINYINT CHECK (SoSao BETWEEN 1 AND 5),
    NgayDG DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('Hiển thị', 'Ẩn') DEFAULT 'Hiển thị',
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan),
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH),
    UNIQUE (IdTaiKhoan, MaHH) 
);

-- 7. Bảng bình luận
CREATE TABLE BinhLuan ( 
    MaBL INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT(10), 
    MaHH INT(10),
    NoiDung TEXT NOT NULL,
    NgayBL DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('Hiển thị','Ẩn') DEFAULT 'Hiển thị',
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE, 
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE 
);

-- 8. Bảng hình ảnh
CREATE TABLE HinhAnh (
    IDHinhAnh INT(10) PRIMARY KEY AUTO_INCREMENT, 
    MaHH INT(10),
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE, 
    URL VARCHAR(255) 
);

-- 9. Bảng giỏ hàng
CREATE TABLE GioHang ( 
    MaGH INT(10) PRIMARY KEY AUTO_INCREMENT,
    IdTaiKhoan INT(10) UNIQUE,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE 
);

-- 10. Bảng chi tiết giỏ hàng
CREATE TABLE ChiTietGioHang ( 
    MaCTGH INT(10) PRIMARY KEY AUTO_INCREMENT,
    MaGH INT(10),
    FOREIGN KEY (MaGH) REFERENCES GioHang(MaGH) ON DELETE CASCADE ON UPDATE CASCADE, 
    MaHH INT(10),
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE,
    SoLuong SMALLINT UNSIGNED
);

-- 11. Bảng đơn hàng
CREATE TABLE DonHang ( 
    MaDH INT(10) PRIMARY KEY AUTO_INCREMENT,
    IdTaiKhoan INT(10), 
    IdNguoiBan INT(10) NOT NULL, 
    NgayDat DATETIME DEFAULT CURRENT_TIMESTAMP,
    DiaChiGiao VARCHAR(255),
    TongTien DECIMAL(10) UNSIGNED,
    TrangThai ENUM('Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Hoàn tất', 'Đã hủy') DEFAULT 'Chờ xử lý',
    TrangThaiThanhToan ENUM('ChuaThanhToan', 'DaThanhToan', 'ChoHoanTien') DEFAULT 'ChuaThanhToan', 
    GhiChu TEXT,
    NgaySua DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PhiSan DECIMAL(10,2) DEFAULT 0, 
    TienNguoiBanNhan DECIMAL(10,2) DEFAULT 0, 
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (IdNguoiBan) REFERENCES TaiKhoan(IdTaiKhoan)
);

-- 12. Bảng thanh toán
CREATE TABLE ThanhToan (
    MaTT INT AUTO_INCREMENT PRIMARY KEY,
    MaDH INT,
    MaThanhToan VARCHAR(50) UNIQUE, 
    SoTien DECIMAL(10,2) NOT NULL,
    NgayThanhToan DATETIME DEFAULT CURRENT_TIMESTAMP,
    PhuongThuc ENUM('Tiền mặt', 'Chuyển khoản', 'Ví điện tử', 'Thẻ ngân hàng') DEFAULT 'Tiền mặt',
    TrangThai ENUM('Thành công', 'Thất bại', 'Đang xử lý') DEFAULT 'Đang xử lý',
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 13. Bảng chi tiết đơn hàng
CREATE TABLE ChiTietDonHang ( 
    MaCTDH INT(10) PRIMARY KEY AUTO_INCREMENT,
    MaDH INT(10),
    MaHH INT(10),
    SoLuongSanPham SMALLINT UNSIGNED, 
    DonGia DECIMAL(10,2) UNSIGNED,
    GiamGia DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 14. Bảng lịch sử đơn hàng
CREATE TABLE LichSuDonHang ( 
    MaLichSu INT AUTO_INCREMENT PRIMARY KEY, 
    MaDH INT,
    NgayThayDoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Hoàn tất', 'Đã hủy'),
    GhiChu TEXT,
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 15. Bảng mã giảm giá
CREATE TABLE MaGiamGia (
    MaGG INT AUTO_INCREMENT PRIMARY KEY,
    Code VARCHAR(50) NOT NULL UNIQUE, 
    MoTa TEXT,
    GiaTri DECIMAL(15,2) NOT NULL, 
    SoLuong INT DEFAULT 0, 
    LoaiApDung ENUM('MaCode', 'DongLoat') DEFAULT 'DongLoat',
    TrangThai ENUM('Hoạt động', 'Hết hạn', 'Ngừng') DEFAULT 'Hoạt động',
    NgayBatDau DATETIME DEFAULT CURRENT_TIMESTAMP,
    NgayKetThuc DATETIME
);

-- 16. Bảng trung gian mã giảm giá
CREATE TABLE MaGiamGiaDanhMuc (
    MaGG INT,
    MaDM INT,
    PRIMARY KEY (MaGG, MaDM),
    FOREIGN KEY (MaGG) REFERENCES MaGiamGia(MaGG) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 17. Bảng hồ sơ người bán
CREATE TABLE HoSoNguoiBan (
    IdHoSo INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT(10) NOT NULL UNIQUE,
    TenCuaHang VARCHAR(100), 
    DiaChiKhoHang VARCHAR(255),
    SoCCCD VARCHAR(20),
    TenNganHang VARCHAR(50),
    SoTaiKhoanNganHang VARCHAR(30),
    TenChuTaiKhoan VARCHAR(100),
    NgayDuyet DATETIME, 
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE
);

-- 18. Bảng thông báo
CREATE TABLE ThongBao (
    MaTB INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(255) NOT NULL,
    NoiDung TEXT,
    LoaiTB ENUM('HeThong', 'DonHang', 'KhuyenMai') DEFAULT 'HeThong',
    NguoiGui INT(10) DEFAULT NULL, 
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 19. Bảng thông báo người dùng
CREATE TABLE ThongBaoNguoiDung (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    MaTB INT,
    IdNhan INT(10),
    DaXem BOOLEAN DEFAULT 0,
    NgayXem DATETIME,
    FOREIGN KEY (MaTB) REFERENCES ThongBao(MaTB) ON DELETE CASCADE,
    FOREIGN KEY (IdNhan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE
);

-- 20. Bảng phòng chat
CREATE TABLE PhongChat (
    MaPhong INT AUTO_INCREMENT PRIMARY KEY,
    IdNguoiMua INT(10),
    IdNguoiBan INT(10),
    MaHH INT(10),
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdNguoiMua) REFERENCES TaiKhoan(IdTaiKhoan),
    FOREIGN KEY (IdNguoiBan) REFERENCES TaiKhoan(IdTaiKhoan),
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH)
);

-- 21. Bảng tin nhắn
CREATE TABLE TinNhan (
    MaTN INT AUTO_INCREMENT PRIMARY KEY,
    MaPhong INT,
    IdNguoiGui INT(10),
    NoiDung TEXT,
    LoaiTin ENUM('VanBan', 'HinhAnh') DEFAULT 'VanBan',
    DaXem BOOLEAN DEFAULT 0,
    NgayGui DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaPhong) REFERENCES PhongChat(MaPhong) ON DELETE CASCADE
);

-- 22. Bảng banner
CREATE TABLE Banner (
    MaBanner INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(255),
    HinhAnh VARCHAR(255) NOT NULL, 
    LienKet VARCHAR(255),
    TrangThai ENUM('HienThi', 'An') DEFAULT 'HienThi',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 23. Bảng báo cáo
CREATE TABLE BaoCao (
    MaBC INT AUTO_INCREMENT PRIMARY KEY,
    IdNguoiBaoCao INT(10),
    IdDoiTuongBiBaoCao INT(10), 
    MaHH INT(10) DEFAULT NULL, 
    LyDo VARCHAR(255),
    TrangThai ENUM('ChoXuLy', 'DaXuLy', 'BoQua') DEFAULT 'ChoXuLy',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdNguoiBaoCao) REFERENCES TaiKhoan(IdTaiKhoan)
);


-- =============================================
-- INSERT DỮ LIỆU
-- =============================================

INSERT INTO TaiKhoan (TenTK, Email, Sdt, MatKhau, VaiTro, TrangThaiBanHang) VALUES
('kha', 'vtchoangkha@gmail.com', '0913420982', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'ChuaKichHoat'),
('nha', 'nha@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong'),
('quyen', 'quyen@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong'),
('lai', 'lai@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong'),
('admin', 'admin@gmail.com', '123456780', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 1, 'ChuaKichHoat'),
('Nguyễn Văn A', 'vana@example.com', '0912345678', '123456', 0, 'ChuaKichHoat'),
('Trần Thị B', 'thib@example.com', '0987654321', '123456', 0, 'ChuaKichHoat'),
('Lê Văn C', 'vanc@example.com', '0901111222', '123456', 0, 'ChuaKichHoat'),
('Phạm Thị D', 'thid@example.com', '0930304444', '123456', 0, 'ChuaKichHoat'),
('Hoàng Văn E', 'vane@example.com', '0945555666', '123456', 0, 'ChuaKichHoat'),
('Đỗ Thị F', 'thif@example.com', '0977778888', '123456', 0, 'ChuaKichHoat');

INSERT INTO HoSoNguoiBan (IdTaiKhoan, TenCuaHang, DiaChiKhoHang, SoCCCD, TenNganHang, SoTaiKhoanNganHang, TenChuTaiKhoan, NgayDuyet) VALUES
(2, 'Shop Cũ Người Mới Ta', '456 Đường B, Hà Nội', '098123456789', 'MB Bank', '999999999', 'NGUYEN VAN NHA', NOW()),
(3, 'Quyên Secondhand', '789 Đường C, Đà Nẵng', '098123456000', 'Vietcombank', '888888888', 'LE THI QUYEN', NOW()),
(4, 'Lai Gaming Store', '321 Đường D, Cần Thơ', '098123456111', 'Techcombank', '777777777', 'PHAM VAN LAI', NOW());

INSERT INTO DiaChi (IdTaiKhoan, DiaChiChiTiet, MacDinh) VALUES
(1, '123 Đường A, Quận 1, TP.HCM', 1),
(2, '456 Đường B, Quận 2, Hà Nội', 1),
(3, '789 Đường C, Quận 3, Đà Nẵng', 1),
(4, '321 Đường D, Quận 4, Cần Thơ', 1),
(5, '654 Đường E, Quận 5, Hải Phòng', 1),
(6, '987 Đường F, Quận 6, Huế', 1);

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

INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(1, 2, 1, 'Cây lau công nghiệp Cây lau nhà inox', 2, 120000, 150000, 'Mới', 'Còn hàng', 'DaDuyet', 'Cây lau nhà công nghiệp 45 cm...'),
(2, 2, 1, 'Hộp cơm giữ nhiệt Lunch Box 4 tầng', 1, 150000, 200000, 'Mới', 'Còn hàng', 'DaDuyet', 'Hộp Cơm Giữ Nhiệt Văn Phòng...'),
(3, 2, 1, 'Máy xay tỏi ớt thủ công Đức Huỳnh DN384', 2, 40000, 65000, 'Mới', 'Còn hàng', 'DaDuyet', 'Xay siêu nhanh...'),
(4, 2, 1, 'Nồi lẩu điện mini ZODAN', 1, 180000, 250000, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet', 'Công suất: 50Hz/600W...'),
(5, 2, 1, 'Thớt nhựa tròn Việt Nhật', 1, 35000, 50000, 'Mới', 'Còn hàng', 'DaDuyet', 'Thớt nhựa tròn Việt Nhật...'),
(6, 2, 2, 'Board Kết Nối Ổ Cứng HDD Asus X540UP', 1, 120000, 180000, 'Mới', 'Còn hàng', 'DaDuyet', 'Board kết nối ổ cứng HDD...'),
(7, 2, 2, 'CPU Intel Core i5-12400F (TRAY)', 2, 1600000, 2100000, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet', 'Số nhân: 6, Số luồng: 12...'),
(8, 2, 2, 'Dây cáp mạng LAN đúc sẵn 2 đầu Cat6 Unitek', 4, 25000, 45000, 'Mới', 'Còn hàng', 'DaDuyet', 'Dây cáp mạng Cat6...'),
(9, 2, 2, 'Dây cáp nguồn máy tính pc (2 chân)', 9, 20000, 35000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'Dây Nguồn Máy Tính loại tốt...'),
(10, 2, 2, 'Mạch boost áp TPS61088 mod lên 19V 60W', 8, 60000, 90000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'IC TPS61088. Input: 2.7V-12VDC...'),
(11, 3, 2, 'Quạt tản nhiệt Fan Case Led CENTAUR M2', 2, 40000, 70000, 'Mới', 'Còn hàng', 'DaDuyet', 'Quạt tản nhiệt RGB 16 triệu màu...'),
(12, 3, 2, 'Mô-đun Hạ Áp Mini360 DC-DC 2A', 1, 10000, 20000, 'Mới', 'Còn hàng', 'DaDuyet', 'Thay thế LM2596...'),
(13, 3, 3, 'Màn hình 22 Fujitsu E22-8 Ts Pro (Nội Địa Nhật)', 1, 700000, 950000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'LCD IPS Panel. Kích thước: 22 inch...'),
(14, 3, 3, 'Màn Hình E-Dra EGM22F100 FHD IPS 100Hz', 2, 1100000, 1450000, 'Gần như mới', 'Còn hàng', 'DaDuyet', '21.5 inch, IPS...'),
(15, 3, 3, 'Màn Hình Xiaomi Monitor A27i / A24i IPS 100Hz', 2, 1400000, 1890000, 'Gần như mới', 'Còn hàng', 'DaDuyet', '27 inch (A27i)...'),
(16, 3, 4, 'Bàn trà tròn kiểu dáng hiện đại / bàn kim cương', 2, 300000, 550000, 'Mới', 'Còn hàng', 'DaDuyet', 'Kích thước: d60 x cao 45...'),
(17, 3, 4, 'Đèn Ngủ Để Bàn Chân Gỗ Xếp Ly Hàn Quốc', 5, 60000, 120000, 'Mới', 'Còn hàng', 'DaDuyet', 'Đường kính 22cm...'),
(18, 3, 4, 'Tranh Cắm Hoa Đèn Led Treo phòng khách', 1, 150000, 280000, 'Mới', 'Còn hàng', 'DaDuyet', 'Có đèn led điều khiển từ xa...'),
(19, 3, 4, 'Tranh sắt treo tường trang trí MOD Decor', 1, 380000, 650000, 'Mới', 'Còn hàng', 'DaDuyet', 'Kích thước 135x60cm...'),
(20, 3, 5, 'Áo Sơ Mi Hồng Nữ Rời Phong Cách Văn Học', 3, 150000, 250000, 'Mới', 'Ngưng kinh doanh', 'DaDuyet', 'Chất liệu 100% Polyester...'),
(21, 3, 5, 'Áo thun raplang tay lỡ unisex Teelab', 1, 120000, 180000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'Chất liệu Cotton...'),
(22, 3, 5, 'Chân váy đính nơ KÈM QUẦN Higtk-fashion', 1, 140000, 220000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'Chất vải mềm mại...'),
(23, 3, 5, 'Nasa Khớp Ngắn Tay Nam Nữ (Xu hướng Hè 2025)', 3, 90000, 150000, 'Gần như mới', 'Ngưng kinh doanh', 'DaDuyet', 'Áo thun in hình Nasa...'),
(24, 3, 5, 'Quần jean bò ống suông rộng nữ cạp cao', 1, 220000, 350000, 'Mới', 'Còn hàng', 'DaDuyet', 'Chất liệu Jean cotton cao cấp...'),
(25, 3, 5, 'Quần short unisex Gapazi', 5, 160000, 240000, 'Mới', 'Còn hàng', 'DaDuyet', 'Chất cotton tổ ong...'),
(26, 4, 6, 'Bộ chuyển đổi chơi game chuyên nghiệp MOBA', 1, 350000, 590000, 'Mới', 'Còn hàng', 'DaDuyet', 'Hỗ trợ Android...'),
(27, 4, 6, 'Tấm dẫn nhiệt mở rộng cho điện thoại X Cooler', 2, 60000, 99000, 'Mới', 'Còn hàng', 'DaDuyet', 'Chất liệu hợp kim...'),
(28, 4, 6, 'Tay cầm chơi game Backbone One cho iPhone 15', 1, 1800000, 2600000, 'Mới', 'Còn hàng', 'DaDuyet', 'Kết nối USB-C...'),
(29, 4, 6, 'Tay cầm chơi game PC/Laptop/TV không dây', 2, 250000, 450000, 'Gần như mới', 'Ngưng kinh doanh', 'DaDuyet', 'Tích hợp 666 game cổ điển...'),
(30, 4, 6, 'Thiết bị hỗ trợ chơi game PUBG (L1R1)', 3, 80000, 150000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'Nút bấm vật lý L1R1...'),
(31, 4, 7, 'Bút tua vít DIYMORE AC100-500V', 2, 120000, 180000, 'Mới', 'Ngưng kinh doanh', 'DaDuyet', 'Đầu tua vít đa năng...'),
(32, 4, 7, 'Máy đo điện tử tự động DIYMORE SZ01SZ02', 1, 450000, 650000, 'Mới', 'Còn hàng', 'DaDuyet', 'Đo điện áp, nguồn điện DC 5-24V...'),
(33, 4, 8, 'Đồ Chơi Đường Đua Xe Ô Tô Màn Hình Điện Tử', 1, 120000, 220000, 'Đã qua sử dụng', 'Ngưng kinh doanh', 'DaDuyet', 'Có vô lăng điều khiển...'),
(34, 4, 8, 'Mô hình xe bus chở khách hạng thương gia KAVY', 2, 250000, 390000, 'Mới', 'Ngưng kinh doanh', 'DaDuyet', 'Tỷ lệ 1:32...'),
(35, 4, 8, 'Xe Đạp Cân Bằng iiko Cao Cấp (360° Xoay)', 3, 950000, 1450000, 'Mới', 'Còn hàng', 'DaDuyet', 'Khung thép carbon chịu lực 80kg...'),
(36, 4, 8, 'Xe đạp trẻ em 2 KHUNG Jinbao (hình công chúa)', 1, 650000, 950000, 'Mới', 'Ngưng kinh doanh', 'DaDuyet', 'Xe thăng bằng 2 bánh...'),
(37, 4, 8, 'Xe Mô tô cho bé R1000 (có đèn nhạc)', 2, 900000, 1350000, 'Mới', 'Còn hàng', 'DaDuyet', 'Xe điện trẻ em R1000...'),
(38, 4, 9, 'Ô Gấp leo Thái Lan màu xanh', 1, 169000, 250000, 'Mới', 'Còn hàng', 'DaDuyet', 'Khung kim loại tráng bạc...'),
(39, 4, 9, 'Truyện chữ Nhật Bản Vol 17', 1, 500000, 800000, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet', 'Tác giả: Natsume Akatsuki...'),
(40, 4, 9, 'Viên ngậm sát trùng Tyrotab Pharmedic', 2, 39000, 60000, 'Mới', 'Còn hàng', 'DaDuyet', 'Điều trị viêm họng...');

INSERT INTO HinhAnh (MaHH, URL) VALUES 
(1, 'assets/images/products/1/1-1.png'), (1, 'assets/images/products/1/1-2.png'), (1, 'assets/images/products/1/1-3.png'),
(2, 'assets/images/products/2/2-1.png'), (2, 'assets/images/products/2/2-2.png'), (2, 'assets/images/products/2/2-3.png'),
(3, 'assets/images/products/3/3-1.png'), (3, 'assets/images/products/3/3-2.png'), (3, 'assets/images/products/3/3-3.png'),
(4, 'assets/images/products/4/4-1.png'), (4, 'assets/images/products/4/4-2.png'),
(5, 'assets/images/products/5/5-1.png'),
(6, 'assets/images/products/6/6-1.png'),
(7, 'assets/images/products/7/7-1.png'),
(8, 'assets/images/products/8/8-1.png'), (8, 'assets/images/products/8/8-2.png'),
(9, 'assets/images/products/9/9-1.png'), (9, 'assets/images/products/9/9-2.png'),
(10, 'assets/images/products/10/10-1.png'), (10, 'assets/images/products/10/10-2.png'), (10, 'assets/images/products/10/10-3.png'), (10, 'assets/images/products/10/10-4.png'),
(11, 'assets/images/products/11/11-1.png'), (11, 'assets/images/products/11/11-2.png'),
(12, 'assets/images/products/12/12-1.png'), (12, 'assets/images/products/12/12-2.png'),
(13, 'assets/images/products/13/13-1.png'), (13, 'assets/images/products/13/13-2.png'),
(14, 'assets/images/products/14/14-1.png'), (14, 'assets/images/products/14/14-2.png'),
(15, 'assets/images/products/15/15-1.png'), (15, 'assets/images/products/15/15-2.png'),
(16, 'assets/images/products/16/16-1.png'), (16, 'assets/images/products/16/16-2.png'),
(17, 'assets/images/products/17/17-1.png'), (17, 'assets/images/products/17/17-2.png'), (17, 'assets/images/products/17/17-3.png'),
(18, 'assets/images/products/18/18-1.png'),
(19, 'assets/images/products/19/19-1.png'), (19, 'assets/images/products/19/19-2.png'),
(20, 'assets/images/products/20/20-1.png'), (20, 'assets/images/products/20/20-2.png'), (20, 'assets/images/products/20/20-3.png'),
(21, 'assets/images/products/21/21-1.png'),
(22, 'assets/images/products/22/22-1.png'), (22, 'assets/images/products/22/22-2.png'),
(23, 'assets/images/products/23/23-1.png'),
(24, 'assets/images/products/24/24-1.png'), (24, 'assets/images/products/24/24-2.png'), (24, 'assets/images/products/24/24-3.png'),
(25, 'assets/images/products/25/25-1.png'), (25, 'assets/images/products/25/25-2.png'), (25, 'assets/images/products/25/25-3.png'),
(26, 'assets/images/products/26/26-1.png'), (26, 'assets/images/products/26/26-2.png'), (26, 'assets/images/products/26/26-3.png'),
(27, 'assets/images/products/27/27-1.png'), (27, 'assets/images/products/27/27-2.png'), (27, 'assets/images/products/27/27-3.png'),
(28, 'assets/images/products/28/28-1.png'), (28, 'assets/images/products/28/28-2.png'), (28, 'assets/images/products/28/28-3.png'),
(29, 'assets/images/products/29/29-1.png'), (29, 'assets/images/products/29/29-2.png'), (29, 'assets/images/products/29/29-3.png'),
(30, 'assets/images/products/30/30-1.png'), (30, 'assets/images/products/30/30-2.png'), (30, 'assets/images/products/30/30-3.png'),
(31, 'assets/images/products/31/31-1.png'), (31, 'assets/images/products/31/31-2.png'),
(32, 'assets/images/products/32/32-1.png'), (32, 'assets/images/products/32/32-2.png'), (32, 'assets/images/products/32/32-3.png'),
(33, 'assets/images/products/33/33-1.png'), (33, 'assets/images/products/33/33-2.png'), (33, 'assets/images/products/33/33-3.png'),
(34, 'assets/images/products/34/34-1.png'), (34, 'assets/images/products/34/34-2.png'), (34, 'assets/images/products/34/34-3.png'),
(35, 'assets/images/products/35/35-1.png'), (35, 'assets/images/products/35/35-2.png'),
(36, 'assets/images/products/36/36-1.png'), (36, 'assets/images/products/36/36-2.png'),
(37, 'assets/images/products/37/37-1.png'), (37, 'assets/images/products/37/37-2.png'), (37, 'assets/images/products/37/37-3.png'),
(38, 'assets/images/products/38/38-1.png'), (38, 'assets/images/products/38/38-2.png'), (38, 'assets/images/products/38/38-3.png'),
(39, 'assets/images/products/39/39-1.png'),
(40, 'assets/images/products/40/40-1.png'), (40, 'assets/images/products/40/40-2.png'), (40, 'assets/images/products/40/40-3.png');

INSERT INTO MaGiamGia (Code, MoTa, GiaTri, SoLuong, TrangThai)
VALUES 
('SALE20_DG', 'Giảm 20% cho đồ gia dụng', 20.00, 100, 'Hoạt động'),
('SALE20_LK', 'Giảm 20% cho linh kiện PC', 20.00, 100, 'Hoạt động'),
('SALE10_DT', 'Giảm 10% cho thiết bị điện tử', 10.00, 50, 'Hoạt động');

INSERT INTO MaGiamGiaDanhMuc (MaGG, MaDM) VALUES 
(1, 1), (2, 2), (3, 7);

INSERT INTO DanhGiaSao (IdTaiKhoan, MaHH, SoSao, TrangThai) VALUES
(1, 1, 5, 'Hiển thị'), (2, 1, 4, 'Hiển thị'), (3, 1, 5, 'Hiển thị'), (4, 1, 4, 'Hiển thị'),
(1, 2, 4, 'Hiển thị'), (5, 2, 5, 'Hiển thị'), (6, 2, 3, 'Hiển thị'),
(1, 7, 5, 'Hiển thị'), (2, 7, 5, 'Hiển thị'), (3, 7, 4, 'Hiển thị'), (4, 7, 5, 'Hiển thị'), (5, 7, 5, 'Hiển thị'),
(1, 13, 4, 'Hiển thị'), (6, 13, 4, 'Hiển thị'),
(2, 16, 5, 'Hiển thị'), (3, 16, 5, 'Hiển thị'), (4, 16, 5, 'Hiển thị'),
(5, 21, 4, 'Hiển thị'), (6, 21, 5, 'Hiển thị'), (1, 21, 4, 'Hiển thị'),
(2, 28, 5, 'Hiển thị'), (3, 28, 5, 'Hiển thị'), (4, 28, 4, 'Hiển thị'),
(5, 30, 3, 'Hiển thị'), (6, 30, 4, 'Hiển thị'),
(1, 35, 5, 'Hiển thị'), (2, 35, 5, 'Hiển thị'), (3, 35, 5, 'Hiển thị'),
(4, 40, 5, 'Hiển thị'), (5, 40, 4, 'Hiển thị');

INSERT INTO BinhLuan (IdTaiKhoan, MaHH, NoiDung, TrangThai) VALUES
(1, 1, 'Cây lau chắc chắn, lau sạch, giao hàng rất nhanh.', 'Hiển thị'),
(2, 1, 'Mặt hàng gia dụng này chất lượng hơn tôi nghĩ, rất đáng tiền!', 'Hiển thị'),
(3, 1, 'Lau nhà nhẹ nhàng, thiết kế thông minh, không cần dùng tay vắt.', 'Hiển thị'),
(4, 1, 'Chất liệu inox sáng bóng, dùng lâu không sợ rỉ sét.', 'Hiển thị'),
(1, 2, 'Hộp cơm giữ nhiệt tốt, đủ dùng cho bữa trưa văn phòng.', 'Hiển thị'),
(5, 2, 'Thiết kế 4 tầng tiện lợi, có kèm túi xách đi làm.', 'Hiển thị'),
(6, 2, 'Giữ nhiệt được khoảng 3 tiếng, hơi ít so với quảng cáo 4 tiếng.', 'Hiển thị'),
(1, 7, 'CPU TRAY nhưng hoạt động hoàn hảo, đã test full load 100%.', 'Hiển thị'),
(2, 7, 'Hàng đã qua sử dụng nhưng còn rất mới, hiệu năng tuyệt vời.', 'Hiển thị'),
(3, 7, 'Giá tốt nhất thị trường cho con chip này, nên mua ngay.', 'Hiển thị'),
(4, 7, 'Giao hàng có bọc chống sốc kỹ, lắp vào chạy ngay, không lỗi lầm.', 'Hiển thị'),
(5, 7, 'Làm việc và chơi game đều mượt, rất hài lòng với tốc độ xử lý.', 'Hiển thị'),
(1, 13, 'Màn hình nội địa Nhật, màu sắc đẹp, có tích hợp loa khá ổn.', 'Hiển thị'),
(6, 13, 'Chất lượng hình ảnh tốt, không điểm chết, dùng để code rất ok.', 'Hiển thị'),
(2, 16, 'Bàn kim cương rất đẹp, decor phòng khách sang trọng hơn hẳn.', 'Hiển thị'),
(3, 16, 'Mặt kính vân mây nhìn rất nghệ thuật, chân sắt vững chắc.', 'Hiển thị'),
(4, 16, 'Lắp ráp dễ dàng, kích thước vừa phải, rất ưng ý!', 'Hiển thị'),
(5, 21, 'Áo form oversize thoải mái, chất cotton mặc mát.', 'Hiển thị'),
(6, 21, 'Hình in lụa rõ nét, giặt không bị bong tróc, đáng giá 5 sao.', 'Hiển thị'),
(1, 21, 'Màu xám tiêu phối đồ rất dễ, nên có trong tủ đồ.', 'Hiển thị'),
(2, 28, 'Biến iPhone thành máy game thực thụ, trải nghiệm rất đã.', 'Hiển thị'),
(3, 28, 'Tay cầm nhạy, không độ trễ, chơi game AAA trên điện thoại cực đỉnh.', 'Hiển thị'),
(4, 28, 'Giá hơi cao nhưng xứng đáng cho game thủ chuyên nghiệp.', 'Hiển thị'),
(5, 30, 'Nút bấm hơi lỏng lẻo một chút, nhưng vẫn dùng được.', 'Hiển thị'),
(6, 30, 'Giá rẻ, dùng tạm ổn để chơi PUBG, cải thiện được tốc độ phản xạ.', 'Hiển thị'),
(1, 35, 'Xe siêu nhẹ, bé 3 tuổi nhà tôi tự đạp được ngay.', 'Hiển thị'),
(2, 35, 'Bánh đúc chống móp rất bền, yên xe điều chỉnh dễ dàng.', 'Hiển thị'),
(3, 35, 'Thiết kế đẹp, màu sắc bắt mắt, bé rất thích chiếc xe này.', 'Hiển thị'),
(4, 40, 'Hàng chính hãng, ngậm đỡ đau họng ngay, sẽ mua lại.', 'Hiển thị'),
(5, 40, 'Thuốc có tác dụng nhanh, vị hơi khó ngậm nhưng hiệu quả.', 'Hiển thị');

INSERT INTO DonHang (IdTaiKhoan, IdNguoiBan, NgayDat, DiaChiGiao, TrangThai, TrangThaiThanhToan, GhiChu, TongTien) VALUES 
(1, 2, '2025-01-05 09:30:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', 'DaThanhToan', 'Giao giờ hành chính', 0),
(2, 2, '2025-01-12 14:15:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', 'DaThanhToan', 'Gọi trước khi giao', 0),
(3, 2, '2025-01-25 18:20:00', '789 Đường C, Quận 3, Đà Nẵng', 'Đã hủy', 'ChuaThanhToan', 'Khách đổi ý không mua nữa', 0),
(4, 2, '2025-02-10 10:00:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(5, 2, '2025-02-28 08:45:00', '654 Đường E, Quận 5, Hải Phòng', 'Hoàn tất', 'DaThanhToan', 'Giao cho bảo vệ', 0),
(6, 2, '2025-03-05 11:30:00', '987 Đường F, Quận 6, Huế', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(1, 2, '2025-03-15 15:20:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', 'DaThanhToan', 'Giao nhanh giúp mình', 0),
(2, 2, '2025-04-02 09:10:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(3, 2, '2025-04-20 16:50:00', '789 Đường C, Quận 3, Đà Nẵng', 'Hoàn tất', 'DaThanhToan', 'Hàng dễ vỡ xin nhẹ tay', 0),
(4, 2, '2025-05-05 13:40:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(5, 2, '2025-05-18 10:25:00', '654 Đường E, Quận 5, Hải Phòng', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(6, 2, '2025-05-30 19:15:00', '987 Đường F, Quận 6, Huế', 'Đã hủy', 'ChuaThanhToan', 'Sai địa chỉ', 0),
(1, 2, '2025-06-10 08:30:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(2, 2, '2025-06-25 14:00:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', 'DaThanhToan', 'Giao buổi chiều', 0),
(3, 2, '2025-07-07 09:45:00', '789 Đường C, Quận 3, Đà Nẵng', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(4, 2, '2025-07-22 17:30:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(5, 2, '2025-08-05 11:15:00', '654 Đường E, Quận 5, Hải Phòng', 'Hoàn tất', 'DaThanhToan', 'Cần gấp cho con đi học', 0),
(6, 2, '2025-08-15 15:50:00', '987 Đường F, Quận 6, Huế', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(1, 2, '2025-08-28 12:20:00', '123 Đường A, Quận 1, TP.HCM', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(2, 2, '2025-09-09 10:10:00', '456 Đường B, Quận 2, Hà Nội', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(3, 2, '2025-09-21 16:40:00', '789 Đường C, Quận 3, Đà Nẵng', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(4, 2, '2025-10-02 08:50:00', '321 Đường D, Quận 4, Cần Thơ', 'Hoàn tất', 'DaThanhToan', NULL, 0),
(5, 2, '2025-10-10 14:30:00', '654 Đường E, Quận 5, Hải Phòng', 'Đang giao', 'DaThanhToan', 'Đang đợi shipper', 0),
(6, 2, '2025-10-12 09:15:00', '987 Đường F, Quận 6, Huế', 'Đã xác nhận', 'ChuaThanhToan', 'Chuẩn bị đóng gói', 0),
(1, 2, '2025-10-13 18:00:00', '123 Đường A, Quận 1, TP.HCM', 'Chờ xử lý', 'ChuaThanhToan', 'Vừa đặt xong', 0);

INSERT INTO ChiTietDonHang (MaDH, MaHH, SoLuongSanPham, DonGia, GiamGia) VALUES
(1, 1, 1, 120000, 0), (1, 2, 2, 150000, 10000),
(2, 13, 1, 700000, 0),
(3, 16, 1, 300000, 0),
(4, 20, 2, 150000, 0), (4, 24, 1, 220000, 0),
(5, 28, 1, 1800000, 50000),
(6, 3, 5, 40000, 0),
(7, 7, 1, 1600000, 0), (7, 11, 2, 40000, 0),
(8, 35, 1, 950000, 20000),
(9, 4, 1, 180000, 0),
(10, 21, 3, 120000, 0),
(11, 15, 1, 1400000, 0),
(12, 19, 1, 380000, 0),
(13, 37, 1, 900000, 0),
(14, 8, 10, 25000, 0),
(15, 17, 2, 60000, 0),
(16, 25, 4, 160000, 0),
(17, 14, 1, 1100000, 0), (17, 9, 2, 20000, 0),
(18, 29, 2, 250000, 10000),
(19, 31, 1, 120000, 0),
(20, 39, 1, 500000, 0),
(21, 38, 2, 169000, 0),
(22, 40, 5, 39000, 0),
(23, 10, 3, 60000, 0),
(24, 5, 2, 35000, 0),
(25, 7, 1, 1600000, 50000);

-- Cập nhật tổng tiền
UPDATE DonHang dh
SET TongTien = (
    SELECT SUM(SoLuongSanPham * DonGia - GiamGia)
    FROM ChiTietDonHang ctdh
    WHERE ctdh.MaDH = dh.MaDH
);