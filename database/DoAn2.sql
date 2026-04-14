DROP DATABASE IF EXISTS doan2;
CREATE DATABASE doan2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE doan2;

-- =============================================
-- 1. CẤU TRÚC 27 BẢNG (GIỮ NGUYÊN CỦA BẠN - ĐÃ THÊM CỘT SODU)
-- =============================================
CREATE TABLE TaiKhoan ( 
    IdTaiKhoan INT(10) PRIMARY KEY AUTO_INCREMENT, 
    TenTK VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Sdt VARCHAR(10) NOT NULL, 
    MatKhau VARCHAR(255) NOT NULL, 
    VaiTro TINYINT DEFAULT 0 CHECK (VaiTro IN (0,1)), 
    IdGoogle VARCHAR(255) DEFAULT NULL, 
    TrangThaiBanHang ENUM('ChuaKichHoat','DangHoatDong','BiKhoa') DEFAULT 'ChuaKichHoat', 
    Avatar VARCHAR(255) DEFAULT NULL,
    ThoiGianTao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DiemViPham INT DEFAULT 0,
    ViDo DECIMAL(10, 8) NULL COMMENT 'Latitude - Vĩ độ của Người Mua',
    KinhDo DECIMAL(11, 8) NULL COMMENT 'Longitude - Kinh độ của Người Mua',
    HanKhoaTaiKhoan DATETIME DEFAULT NULL,
    Avatar VARCHAR(255) DEFAULT NULL
);

CREATE TABLE DiaChi (
    MaDC INT AUTO_INCREMENT PRIMARY KEY, 
    IdTaiKhoan INT(10), 
    DiaChiChiTiet VARCHAR(255) NOT NULL, 
    MacDinh BOOLEAN DEFAULT 0, 
    ViDo DECIMAL(10, 8) NULL COMMENT 'Vĩ độ của địa chỉ này',
    KinhDo DECIMAL(11, 8) NULL COMMENT 'Kinh độ của địa chỉ này',
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE DanhMuc ( 
    MaDM INT(10) PRIMARY KEY AUTO_INCREMENT,
    TenDM VARCHAR(100)
);

CREATE TABLE HangHoa ( 
    MaHH INT(10) PRIMARY KEY,
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
    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (IdNguoiBan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE 
);

CREATE TABLE YeuThich (
    MaYT INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT(10),
    MaHH INT(10),
    NgayLuu DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE, 
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE (IdTaiKhoan, MaHH) 
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
    UNIQUE (IdTaiKhoan, MaHH) 
);

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

CREATE TABLE HinhAnh (
    IDHinhAnh INT(10) PRIMARY KEY AUTO_INCREMENT, 
    MaHH INT(10),
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE, 
    URL VARCHAR(255) 
);

CREATE TABLE GioHang ( 
    MaGH INT(10) PRIMARY KEY AUTO_INCREMENT,
    IdTaiKhoan INT(10) UNIQUE,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE ON UPDATE CASCADE 
);

CREATE TABLE ChiTietGioHang ( 
    MaCTGH INT(10) PRIMARY KEY AUTO_INCREMENT,
    MaGH INT(10),
    FOREIGN KEY (MaGH) REFERENCES GioHang(MaGH) ON DELETE CASCADE ON UPDATE CASCADE, 
    MaHH INT(10),
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE ON UPDATE CASCADE,
    SoLuong SMALLINT UNSIGNED
);

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

CREATE TABLE ThanhToan (
    MaTT INT AUTO_INCREMENT PRIMARY KEY,
    MaDH INT,
    MaThanhToan VARCHAR(50) UNIQUE, 
    SoTien DECIMAL(10,2) NOT NULL,
    NgayThanhToan DATETIME DEFAULT CURRENT_TIMESTAMP,
    PhuongThuc ENUM('Tiền mặt', 'Chuyển khoản', 'Ví điện tử', 'Thẻ ngân hàng') DEFAULT 'Tiền mặt',
    TrangThai ENUM('Thành công', 'Thất bại', 'Đang xử lý') DEFAULT 'Đang xử lý',
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) ON DELETE CASCADE ON UPDATE CASCADE
);

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

CREATE TABLE LichSuDonHang ( 
    MaLichSu INT AUTO_INCREMENT PRIMARY KEY, 
    MaDH INT,
    NgayThayDoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThai ENUM('Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Hoàn tất', 'Đã hủy'),
    GhiChu TEXT,
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH) ON DELETE CASCADE ON UPDATE CASCADE
);

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

CREATE TABLE MaGiamGiaDanhMuc (
    MaGG INT,
    MaDM INT,
    PRIMARY KEY (MaGG, MaDM),
    FOREIGN KEY (MaGG) REFERENCES MaGiamGia(MaGG) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE HoSoNguoiBan (
    IdHoSo INT AUTO_INCREMENT PRIMARY KEY,
    IdTaiKhoan INT(10) NOT NULL UNIQUE,
    TenCuaHang VARCHAR(100), 
    DiaChiKhoHang VARCHAR(255),
    ViDo DECIMAL(10, 8) NULL COMMENT 'Latitude - Vĩ độ Cửa Hàng (Kho)',
    KinhDo DECIMAL(11, 8) NULL COMMENT 'Longitude - Kinh độ Cửa Hàng (Kho)',
    SoCCCD VARCHAR(20),
    TenNganHang VARCHAR(50),
    SoTaiKhoanNganHang VARCHAR(30),
    TenChuTaiKhoan VARCHAR(100),
    NgayDuyet DATETIME, 
    SoDu DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE
);

CREATE TABLE ThongBao (
    MaTB INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(255) NOT NULL,
    NoiDung TEXT,
    LoaiTB ENUM('HeThong', 'DonHang', 'KhuyenMai', 'ViPham', 'BaoCao') DEFAULT 'HeThong',
    NguoiGui INT(10) DEFAULT NULL, 
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ThongBaoNguoiDung (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    MaTB INT,
    IdNhan INT(10),
    DaXem BOOLEAN DEFAULT 0,
    NgayXem DATETIME,
    FOREIGN KEY (MaTB) REFERENCES ThongBao(MaTB) ON DELETE CASCADE,
    FOREIGN KEY (IdNhan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE
);

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

CREATE TABLE Banner (
    MaBanner INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(255),
    HinhAnh VARCHAR(255) NOT NULL, 
    LienKet VARCHAR(255),
    TrangThai ENUM('HienThi', 'An') DEFAULT 'HienThi',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE BaoCao (
    MaBC INT AUTO_INCREMENT PRIMARY KEY,
    IdNguoiBaoCao INT(10),
    IdDoiTuongBiBaoCao INT(10), 
    MaHH INT(10) DEFAULT NULL, 
    LoaiBaoCao ENUM('SanPham', 'NguoiBan') NOT NULL,
    LyDoChinh VARCHAR(255) NOT NULL,
    ChiTiet TEXT, 
    TrangThai ENUM('ChoXuLy', 'ViPham', 'KhongViPham') DEFAULT 'ChoXuLy',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdNguoiBaoCao) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE,
    FOREIGN KEY (IdDoiTuongBiBaoCao) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE
);

CREATE TABLE KhangCao (
    MaKC INT AUTO_INCREMENT PRIMARY KEY,
    MaBC INT NOT NULL, 
    IdNguoiKhangCao INT(10), 
    NoiDung TEXT NOT NULL,
    TrangThai ENUM('ChoDuyet', 'ChapNhan', 'TuChoi') DEFAULT 'ChoDuyet',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaBC) REFERENCES BaoCao(MaBC) ON DELETE CASCADE,
    FOREIGN KEY (IdNguoiKhangCao) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE
);

CREATE TABLE CauHinhHeThong (
    MaCH INT PRIMARY KEY AUTO_INCREMENT,
    TenCauHinh VARCHAR(50) UNIQUE,
    GiaTri VARCHAR(255),
    MoTa TEXT
);

CREATE TABLE YeuCauRutTien (
    MaYC INT PRIMARY KEY AUTO_INCREMENT,
    IdTaiKhoan INT NOT NULL,
    SoTien DECIMAL(15,2) NOT NULL,
    NganHang VARCHAR(100),
    SoTaiKhoan VARCHAR(50),
    TenChuTaiKhoan VARCHAR(100),
    TrangThai ENUM('ChoDuyet', 'DaChuyen', 'TuChoi') DEFAULT 'ChoDuyet',
    LyDoTuChoi TEXT,
    NgayYeuCau DATETIME DEFAULT CURRENT_TIMESTAMP,
    NgayXuLy DATETIME NULL,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan)
);

CREATE TABLE BienDongSoDu (
    MaBD INT PRIMARY KEY AUTO_INCREMENT,
    IdTaiKhoan INT NOT NULL,
    LoaiGiaoDich ENUM('CongTienDonHang', 'RutTien', 'HoanTien', 'TruTien') NOT NULL,
    SoTien DECIMAL(15,2) NOT NULL,
    SoDuSauGiaoDich DECIMAL(15,2) NOT NULL,
    NoiDung TEXT,
    MaDH INT NULL, 
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan),
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH)
);
CREATE TABLE HanhVi_AI (
    IdTaiKhoan int(11) NOT NULL,
    MaHH int(11) NOT NULL,
    Diem int(11) NOT NULL,
    PRIMARY KEY (IdTaiKhoan, MaHH),
    FOREIGN KEY (IdTaiKhoan) REFERENCES TaiKhoan(IdTaiKhoan) ON DELETE CASCADE,
    FOREIGN KEY (MaHH) REFERENCES HangHoa(MaHH) ON DELETE CASCADE
);
-- =============================================
-- 2. INSERT DỮ LIỆU CŨ CỦA BẠN
-- =============================================

INSERT INTO CauHinhHeThong (TenCauHinh, GiaTri, MoTa) VALUES ('PhiSan', '5', 'Phí sàn phần trăm (%) thu trên mỗi đơn hàng hoàn tất');

INSERT INTO TaiKhoan (TenTK, Email, Sdt, MatKhau, VaiTro, TrangThaiBanHang, ViDo, KinhDo) VALUES
('kha', 'vtchoangkha@gmail.com', '0913420982', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.038753, 105.782455),
('nha', 'nha@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.041234, 105.785678),
('quyen', 'quyen@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.035432, 105.780123),
('lai', 'lai@gmail.com', '123456789', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.029939, 105.770615),
('admin', 'admin@gmail.com', '123456780', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 1, 'DangHoatDong', 10.030588, 105.787429),
('Nguyễn Văn A', 'vana@example.com', '0912345678', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.026723, 105.776615),
('Trần Thị B', 'thib@example.com', '0987654321', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.056123, 105.748521),
('Lê Văn C', 'vanc@example.com', '0901111222', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'ChuaKichHoat', 9.996111, 105.753333),
('Phạm Thị D', 'thid@example.com', '0930304444', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'ChuaKichHoat', 10.015234, 105.760123),
('Hoàng Văn E', 'vane@example.com', '0945555666', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'ChuaKichHoat', 10.048914, 105.768841),
('Đỗ Thị F', 'thif@example.com', '0977778888', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'ChuaKichHoat', 10.038914, 105.778841);

INSERT INTO DiaChi (IdTaiKhoan, DiaChiChiTiet, MacDinh, ViDo, KinhDo) VALUES
(1, 'Hẻm 59 Xô Viết Nghệ Tĩnh, Phường An Cư, Quận Ninh Kiều, Thành phố Cần Thơ', 1, 10.038753, 105.782455),
(2, 'Vincom Hùng Vương, Số 2 Hùng Vương, Phường Thới Bình, Quận Ninh Kiều, Thành phố Cần Thơ', 1, 10.041234, 105.785678),
(3, 'Công viên Lưu Hữu Phước, Đại lộ Hòa Bình, Phường An Phú, Quận Ninh Kiều, Thành phố Cần Thơ', 1, 10.035432, 105.780123),
(4, 'Đại học Cần Thơ Khu II, Đường 3/2, Phường Xuân Khánh, Quận Ninh Kiều, Thành phố Cần Thơ', 1, 10.029939, 105.770615),
(5, 'Bến Ninh Kiều, Đường Hai Bà Trưng, Phường Tân An, Quận Ninh Kiều, Thành phố Cần Thơ', 1, 10.030588, 105.787429),
(6, 'Vincom Xuân Khánh, 209 Đường 30/4, Phường Xuân Khánh, Quận Ninh Kiều, Thành phố Cần Thơ', 1, 10.026723, 105.776615);

INSERT INTO DanhMuc (MaDM, TenDM) VALUES 
(1, 'Đồ gia dụng'), (2, 'Linh kiện PC'), (3, 'Máy tính'),
(4, 'Nội thất'), (5, 'Quần áo'), (6, 'Thiết bị chơi game'),
(7, 'Thiết bị điện tử'), (8, 'Chưa phân loại'), (9, 'Khác');

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

INSERT INTO Banner (TieuDe, HinhAnh, LienKet, TrangThai) VALUES
('anh-qc-1', 'anh-qc-1.webp', '', 'HienThi'),
('anh-qc-2', 'anh-qc-2.webp', '', 'HienThi'),
('anh-qc-3', 'anh-qc-3.webp', '', 'HienThi'),
('anh-qc-4', 'anh-qc-4.webp', '', 'HienThi');

INSERT INTO MaGiamGia (Code, MoTa, GiaTri, SoLuong, TrangThai) VALUES 
('SALE20_DG', 'Giảm 20% cho đồ gia dụng', 20.00, 100, 'Hoạt động'),
('SALE20_LK', 'Giảm 20% cho linh kiện PC', 20.00, 100, 'Hoạt động'),
('SALE10_DT', 'Giảm 10% cho thiết bị điện tử', 10.00, 50, 'Hoạt động');

INSERT INTO MaGiamGiaDanhMuc (MaGG, MaDM) VALUES 
(1, 1), (2, 2), (3, 7);

-- =============================================
-- 3. THÊM MỚI DỮ LIỆU (CÁC BẢNG CÒN LẠI VÀ SỐ DƯ TÍNH TOÁN)
-- =============================================

-- Thêm Yêu Thích & Giỏ Hàng
INSERT INTO YeuThich (IdTaiKhoan, MaHH) VALUES (6, 1), (7, 2), (8, 4), (9, 7), (10, 10);
INSERT INTO GioHang (IdTaiKhoan) VALUES (6), (7), (8), (9);
INSERT INTO ChiTietGioHang (MaGH, MaHH, SoLuong) VALUES (1, 3, 2), (1, 4, 1), (2, 8, 1), (3, 10, 1);

-- THÊM 10 ĐƠN HÀNG (GIÁ TRỊ VÀ TÍNH TOÁN KHỚP 100%)
-- PhiSan = 5% TongTien. TienNguoiBanNhan = TongTien - PhiSan
INSERT INTO DonHang (MaDH, IdTaiKhoan, IdNguoiBan, DiaChiGiao, TongTien, TrangThai, TrangThaiThanhToan, PhiSan, TienNguoiBanNhan, NgayDat) VALUES
(1, 6, 2, 'Cần Thơ', 14500000, 'Hoàn tất', 'DaThanhToan', 725000, 13775000, '2023-11-01 10:00:00'),
(2, 7, 2, 'Cần Thơ', 4500000, 'Đang giao', 'DaThanhToan', 225000, 4275000, '2023-11-05 14:30:00'),
(3, 8, 3, 'Hà Nội', 300000, 'Hoàn tất', 'DaThanhToan', 15000, 285000, '2023-11-10 09:15:00'),
(4, 9, 3, 'Đà Nẵng', 220000, 'Đã hủy', 'ChuaThanhToan', 11000, 209000, '2023-11-12 16:20:00'),
(5, 10, 4, 'Hải Phòng', 950000, 'Hoàn tất', 'DaThanhToan', 47500, 902500, '2023-11-15 08:45:00'),
(6, 11, 4, 'HCM', 120000, 'Chờ xử lý', 'ChuaThanhToan', 6000, 114000, '2023-11-20 11:10:00'),
(7, 6, 2, 'Cần Thơ', 350000, 'Đã xác nhận', 'DaThanhToan', 17500, 332500, '2023-11-22 19:30:00'),
(8, 7, 3, 'Cần Thơ', 800000, 'Hoàn tất', 'DaThanhToan', 40000, 760000, '2023-11-25 15:00:00'),
(9, 8, 4, 'Hà Nội', 450000, 'Hoàn tất', 'DaThanhToan', 22500, 427500, '2023-11-28 10:00:00'),
(10, 9, 2, 'Đà Nẵng', 650000, 'Hoàn tất', 'DaThanhToan', 32500, 617500, '2023-12-01 14:00:00');

INSERT INTO ChiTietDonHang (MaDH, MaHH, SoLuongSanPham, DonGia, GiamGia) VALUES
(1, 1, 1, 14500000, 0), (2, 2, 1, 4500000, 0), (3, 4, 2, 150000, 0),
(4, 5, 1, 220000, 0), (5, 7, 1, 950000, 0), (6, 8, 1, 120000, 0),
(7, 3, 1, 350000, 0), (8, 6, 1, 800000, 0), (9, 9, 1, 450000, 0), (10, 10, 1, 650000, 0);

INSERT INTO ThanhToan (MaDH, MaThanhToan, SoTien, PhuongThuc, TrangThai) VALUES
(1, 'VNPay_001', 14500000, 'Ví điện tử', 'Thành công'),
(2, 'Bank_002', 4500000, 'Chuyển khoản', 'Thành công'),
(3, 'COD_003', 300000, 'Tiền mặt', 'Thành công'),
(5, 'VNPay_005', 950000, 'Ví điện tử', 'Thành công'),
(7, 'Momo_007', 350000, 'Ví điện tử', 'Thành công'),
(8, 'Bank_008', 800000, 'Chuyển khoản', 'Thành công'),
(9, 'COD_009', 450000, 'Tiền mặt', 'Thành công'),
(10, 'VNPay_010', 650000, 'Ví điện tử', 'Thành công');

INSERT INTO LichSuDonHang (MaDH, TrangThai, GhiChu) VALUES 
(1, 'Chờ xử lý', 'Khách vừa đặt'), (1, 'Hoàn tất', 'Giao hàng thành công'),
(2, 'Đang giao', 'Shipper đã lấy hàng'), (3, 'Hoàn tất', 'Khách đã nhận');

-- Thêm Đánh giá và Bình luận cho các đơn đã Hoàn tất
INSERT INTO DanhGiaSao (IdTaiKhoan, MaHH, SoSao) VALUES (6, 1, 5), (8, 4, 4), (10, 7, 5), (7, 6, 3);
INSERT INTO BinhLuan (IdTaiKhoan, MaHH, NoiDung) VALUES
(6, 1, 'Điện thoại xài siêu mượt, pin trâu, shop đóng gói cẩn thận.'),
(8, 4, 'Áo vải hơi mỏng nhưng form rộng rất đẹp, giá hợp lý.'),
(10, 7, 'Nồi chiên dùng tốt, phím cảm ứng nhạy, nướng gà rất ngon.'),
(7, 6, 'Đồng hồ ngoại hình còn mới nhưng pin hơi yếu, giao hàng chậm.');

-- Thêm Báo cáo và Kháng cáo để Test chức năng vi phạm
INSERT INTO BaoCao (IdNguoiBaoCao, IdDoiTuongBiBaoCao, MaHH, LoaiBaoCao, LyDoChinh, ChiTiet, TrangThai) VALUES
(8, 3, 4, 'SanPham', 'Hàng giả/Nhái', 'Mình phát hiện áo thun này là hàng nhái, không phải Local Brand', 'ViPham'),
(9, 4, NULL, 'NguoiBan', 'Lừa đảo', 'Shop này có thái độ phục vụ kém và bom hàng của mình', 'ChoXuLy');

INSERT INTO KhangCao (MaBC, IdNguoiKhangCao, NoiDung, TrangThai) VALUES
(1, 3, 'Chào Admin, đây là hàng chính hãng mình pass lại, có bill đính kèm trong tin nhắn.', 'ChoDuyet');
UPDATE TaiKhoan SET DiemViPham = 1 WHERE IdTaiKhoan = 3;

-- Thêm Yêu Cầu Rút Tiền và Biến động số dư (Tính khớp với tổng tiền đơn Hoàn tất)
-- Seller 2 (Id 2): Có 2 đơn hoàn tất là DH 1 (13.775.000) + DH 10 (617.500) = 14.392.500 đ
-- Seller 2 rút 10.000.000đ (thành công) và 2.000.000đ (đang chờ) => Số dư = 2.392.500 đ
-- Seller 3 (Id 3): Có 2 đơn hoàn tất là DH 3 (285.000) + DH 8 (760.000) = 1.045.000 đ. Chưa rút => Số dư = 1.045.000 đ
-- Seller 4 (Id 4): Có 2 đơn hoàn tất là DH 5 (902.500) + DH 9 (427.500) = 1.330.000 đ. Rút bị từ chối 300.000đ => Số dư = 1.330.000 đ

INSERT INTO YeuCauRutTien (IdTaiKhoan, SoTien, NganHang, SoTaiKhoan, TenChuTaiKhoan, TrangThai, LyDoTuChoi) VALUES
(2, 10000000, 'Vietcombank', '0123456789', 'NGUYEN VAN SELLER', 'DaChuyen', NULL),
(2, 2000000, 'MB Bank', '0912345678', 'NGUYEN VAN SELLER', 'ChoDuyet', NULL),
(4, 300000, 'Techcombank', '555666777', 'LE HOANG BAN HANG', 'TuChoi', 'Sai số tài khoản ngân hàng');

INSERT INTO BienDongSoDu (IdTaiKhoan, LoaiGiaoDich, SoTien, SoDuSauGiaoDich, NoiDung, MaDH) VALUES
(2, 'CongTienDonHang', 13775000, 13775000, 'Cộng tiền đơn hàng #1', 1),
(2, 'CongTienDonHang', 617500, 14392500, 'Cộng tiền đơn hàng #10', 10),
(2, 'RutTien', 10000000, 4392500, 'Rút tiền về tài khoản ngân hàng', NULL),
(2, 'RutTien', 2000000, 2392500, 'Yêu cầu rút tiền', NULL),
(3, 'CongTienDonHang', 285000, 285000, 'Cộng tiền đơn hàng #3', 3),
(3, 'CongTienDonHang', 760000, 1045000, 'Cộng tiền đơn hàng #8', 8),
(4, 'CongTienDonHang', 902500, 902500, 'Cộng tiền đơn hàng #5', 5),
(4, 'CongTienDonHang', 427500, 1330000, 'Cộng tiền đơn hàng #9', 9),
(4, 'RutTien', 300000, 1030000, 'Yêu cầu rút tiền', NULL),
(4, 'HoanTien', 300000, 1330000, 'Hoàn tiền do lệnh rút bị từ chối', NULL);

-- Cập nhật Số dư cho người bán khớp với logic giao dịch ở trên
INSERT INTO HoSoNguoiBan (IdTaiKhoan, TenCuaHang, DiaChiKhoHang, SoCCCD, TenNganHang, SoTaiKhoanNganHang, TenChuTaiKhoan, NgayDuyet, SoDu, ViDo, KinhDo) VALUES
(2, 'TechZone 2Hand', '123 Đường 3/2, Cần Thơ', '091000000001', 'Vietcombank', '0123456789', 'NGUYEN VAN SELLER', NOW(), 2392500, 10.032111, 105.781222),
(3, 'Thời Trang GenZ', '456 CMT8, Cần Thơ', '091000000002', 'MB Bank', '9876543210', 'TRAN THI SHOP', NOW(), 1045000, 10.038888, 105.775555),
(4, 'Tổng Kho Gia Dụng', '789 Nguyễn Văn Linh, Cần Thơ', '091000000003', 'TPBank', '555566667777', 'LE HOANG BAN HANG', NOW(), 1330000, 10.025555, 105.768888),
(6, 'A Mobile Store', '123 Hẻm 51, Xuân Khánh, Cần Thơ', '091000000001', 'TPBank', '00001234567', 'NGUYEN VAN A', NOW(), 0, 10.030111, 105.771111),
(7, 'Bê Boutique 2Hand', '88 Mậu Thân, An Hòa, Cần Thơ', '091000000002', 'BIDV', '123123123', 'TRAN THI B', NOW(), 0, 10.060222, 105.750333),
(8, 'Tiệm Sách Cũ Ông C', '45 Đường 3/2, Ninh Kiều, Cần Thơ', '091000000003', 'Agribank', '555566667777', 'LE VAN C', NOW(), 0, 9.991222, 105.755444);

-- Thêm thông báo
INSERT INTO ThongBao (TieuDe, NoiDung, LoaiTB, NguoiGui) VALUES
('Chào mừng đến với TwoHand', 'Chúc bạn mua bán thuận lợi trên hệ thống của chúng tôi!', 'HeThong', 1),
('Đơn hàng đã được giao', 'Đơn hàng #1 của bạn đã được giao thành công.', 'DonHang', 1);
INSERT INTO ThongBaoNguoiDung (MaTB, IdNhan, DaXem) VALUES (1, 6, 0), (1, 7, 0), (2, 6, 0);
USE doan2;

-- =============================================
-- 1. THÊM 20 ĐƠN HÀNG MỚI (Mã 101 đến 120 để tránh trùng)
-- =============================================
INSERT INTO DonHang (MaDH, IdTaiKhoan, IdNguoiBan, DiaChiGiao, TongTien, TrangThai, TrangThaiThanhToan, GhiChu, PhiSan, TienNguoiBanNhan, NgayDat) VALUES
(101, 7, 3, '123 Cầu Giấy, Hà Nội', 1100000, 'Hoàn tất', 'DaThanhToan', 'Giao trong giờ hành chính', 55000, 1045000, '2023-12-05 09:00:00'),
(102, 8, 4, '456 Lê Lợi, Đà Nẵng', 1800000, 'Hoàn tất', 'DaThanhToan', 'Nhờ shop bọc kỹ', 90000, 1710000, '2023-12-06 14:30:00'),
(103, 9, 2, '789 Trần Hưng Đạo, HCM', 1600000, 'Đang giao', 'DaThanhToan', '', 80000, 1520000, '2023-12-07 10:15:00'),
(104, 10, 3, '101 Nguyễn Văn Cừ, Cần Thơ', 140000, 'Đã xác nhận', 'ChuaThanhToan', 'Đổi địa chỉ giúp em', 7000, 133000, '2023-12-08 16:45:00'),
(105, 11, 4, '202 Hùng Vương, Huế', 78000, 'Hoàn tất', 'DaThanhToan', '', 3900, 74100, '2023-12-09 08:20:00'),
(106, 5, 2, '303 Phan Đình Phùng, Hải Phòng', 120000, 'Hoàn tất', 'DaThanhToan', '', 6000, 114000, '2023-12-10 11:30:00'),
(107, 6, 3, '404 Hai Bà Trưng, Đà Lạt', 1400000, 'Chờ xử lý', 'ChuaThanhToan', 'Shop rep tin nhắn nha', 70000, 1330000, '2023-12-11 19:10:00'),
(108, 7, 4, '505 Lý Thường Kiệt, Vũng Tàu', 169000, 'Đã hủy', 'ChuaThanhToan', 'Khách đổi ý', 0, 0, '2023-12-12 15:00:00'),
(109, 8, 2, '606 Điện Biên Phủ, Nha Trang', 120000, 'Hoàn tất', 'DaThanhToan', 'Giao cho lễ tân', 6000, 114000, '2023-12-13 09:40:00'),
(110, 9, 3, '707 Nguyễn Huệ, Quy Nhơn', 320000, 'Hoàn tất', 'DaThanhToan', '', 16000, 304000, '2023-12-14 13:25:00'),
(111, 10, 4, '808 Lê Duẩn, BMT', 950000, 'Đang giao', 'DaThanhToan', 'Nhẹ tay nhé shipper', 47500, 902500, '2023-12-15 10:50:00'),
(112, 11, 2, '909 Phạm Văn Đồng, HCM', 100000, 'Hoàn tất', 'DaThanhToan', '', 5000, 95000, '2023-12-16 08:15:00'),
(113, 5, 3, '11A Cách Mạng Tháng 8, Cần Thơ', 380000, 'Đã xác nhận', 'ChuaThanhToan', '', 19000, 361000, '2023-12-17 17:30:00'),
(114, 6, 4, '22B Nguyễn Trãi, Hà Nội', 350000, 'Hoàn tất', 'DaThanhToan', 'Cần gấp trong ngày', 17500, 332500, '2023-12-18 14:10:00'),
(115, 7, 2, '33C Bạch Đằng, Đà Nẵng', 100000, 'Hoàn tất', 'DaThanhToan', '', 5000, 95000, '2023-12-19 11:20:00'),
(116, 8, 3, '44D Trần Phú, HCM', 180000, 'Chờ xử lý', 'ChuaThanhToan', 'Có tặng kèm gì không shop', 9000, 171000, '2023-12-20 09:05:00'),
(117, 9, 4, '55E Quang Trung, Đà Lạt', 450000, 'Đang giao', 'DaThanhToan', '', 22500, 427500, '2023-12-21 16:40:00'),
(118, 10, 2, '66F Ngô Quyền, Huế', 300000, 'Hoàn tất', 'DaThanhToan', '', 15000, 285000, '2023-12-22 13:55:00'),
(119, 11, 3, '77G Lê Hồng Phong, Vũng Tàu', 160000, 'Hoàn tất', 'DaThanhToan', 'Giao buổi tối', 8000, 152000, '2023-12-23 18:25:00'),
(120, 5, 4, '88H Nguyễn Thiện Thuật, Cần Thơ', 120000, 'Đã hủy', 'ChoHoanTien', 'Mua nhầm mẫu', 0, 0, '2023-12-24 10:30:00');

-- =============================================
-- 2. CHI TIẾT 20 ĐƠN HÀNG VÀ LỊCH SỬ
-- =============================================
INSERT INTO ChiTietDonHang (MaDH, MaHH, SoLuongSanPham, DonGia, GiamGia) VALUES
(101, 14, 1, 1100000, 0), (102, 28, 1, 1800000, 0), (103, 7, 1, 1600000, 0),
(104, 22, 1, 140000, 0), (105, 40, 2, 39000, 0), (106, 6, 1, 120000, 0),
(107, 15, 1, 1400000, 0), (108, 38, 1, 169000, 0), (109, 3, 3, 40000, 0),
(110, 25, 2, 160000, 0), (111, 35, 1, 950000, 0), (112, 8, 4, 25000, 0),
(113, 19, 1, 380000, 0), (114, 26, 1, 350000, 0), (115, 9, 5, 20000, 0),
(116, 17, 3, 60000, 0), (117, 32, 1, 450000, 0), (118, 2, 2, 150000, 0),
(119, 11, 4, 40000, 0), (120, 27, 2, 60000, 0);

INSERT INTO ThanhToan (MaDH, MaThanhToan, SoTien, PhuongThuc, TrangThai) VALUES
(101, 'MOMO_101', 1100000, 'Ví điện tử', 'Thành công'),
(102, 'BANK_102', 1800000, 'Chuyển khoản', 'Thành công'),
(105, 'ZALO_105', 78000, 'Ví điện tử', 'Thành công'),
(106, 'COD_106', 120000, 'Tiền mặt', 'Thành công'),
(109, 'BANK_109', 120000, 'Chuyển khoản', 'Thành công'),
(110, 'MOMO_110', 320000, 'Ví điện tử', 'Thành công'),
(112, 'COD_112', 100000, 'Tiền mặt', 'Thành công'),
(114, 'BANK_114', 350000, 'Chuyển khoản', 'Thành công'),
(115, 'MOMO_115', 100000, 'Ví điện tử', 'Thành công'),
(118, 'COD_118', 300000, 'Tiền mặt', 'Thành công'),
(119, 'BANK_119', 160000, 'Chuyển khoản', 'Thành công');

-- =============================================
-- 3. CẬP NHẬT TỰ ĐỘNG VÀO SỐ DƯ VÀ BIẾN ĐỘNG
-- (Tính tổng tiền thực nhận của các đơn Hoàn Tất)
-- =============================================
-- Người bán 2 nhận: DH106, 109, 112, 115, 118 (Tổng: 703,000đ)
UPDATE HoSoNguoiBan SET SoDu = SoDu + 703000 WHERE IdTaiKhoan = 2;
INSERT INTO BienDongSoDu (IdTaiKhoan, LoaiGiaoDich, SoTien, SoDuSauGiaoDich, NoiDung, MaDH) VALUES
(2, 'CongTienDonHang', 114000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=2), 'Đơn #106 hoàn tất', 106),
(2, 'CongTienDonHang', 114000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=2), 'Đơn #109 hoàn tất', 109),
(2, 'CongTienDonHang', 95000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=2), 'Đơn #112 hoàn tất', 112),
(2, 'CongTienDonHang', 95000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=2), 'Đơn #115 hoàn tất', 115),
(2, 'CongTienDonHang', 285000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=2), 'Đơn #118 hoàn tất', 118);

-- Người bán 3 nhận: DH101, 110, 119 (Tổng: 1,501,000đ)
UPDATE HoSoNguoiBan SET SoDu = SoDu + 1501000 WHERE IdTaiKhoan = 3;
INSERT INTO BienDongSoDu (IdTaiKhoan, LoaiGiaoDich, SoTien, SoDuSauGiaoDich, NoiDung, MaDH) VALUES
(3, 'CongTienDonHang', 1045000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=3), 'Đơn #101 hoàn tất', 101),
(3, 'CongTienDonHang', 304000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=3), 'Đơn #110 hoàn tất', 110),
(3, 'CongTienDonHang', 152000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=3), 'Đơn #119 hoàn tất', 119);

-- Người bán 4 nhận: DH102, 105, 114 (Tổng: 2,116,600đ)
UPDATE HoSoNguoiBan SET SoDu = SoDu + 2116600 WHERE IdTaiKhoan = 4;
INSERT INTO BienDongSoDu (IdTaiKhoan, LoaiGiaoDich, SoTien, SoDuSauGiaoDich, NoiDung, MaDH) VALUES
(4, 'CongTienDonHang', 1710000, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=4), 'Đơn #102 hoàn tất', 102),
(4, 'CongTienDonHang', 74100, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=4), 'Đơn #105 hoàn tất', 105),
(4, 'CongTienDonHang', 332500, (SELECT SoDu FROM HoSoNguoiBan WHERE IdTaiKhoan=4), 'Đơn #114 hoàn tất', 114);


-- =============================================
-- 4. THÊM ĐÁNH GIÁ (REVIEW) VÀ BÌNH LUẬN THỰC TẾ
-- =============================================
INSERT IGNORE INTO DanhGiaSao (IdTaiKhoan, MaHH, SoSao) VALUES
(7, 14, 5), (8, 28, 5), (11, 40, 4), (5, 6, 4), (8, 3, 5),
(9, 25, 5), (11, 8, 3), (6, 26, 5), (7, 9, 5), (10, 2, 4), (11, 11, 5);

INSERT INTO BinhLuan (IdTaiKhoan, MaHH, NoiDung) VALUES
(7, 14, 'Màn hình hiển thị sắc nét, tần số 100Hz lướt web cực mượt, không mỏi mắt.'),
(8, 28, 'Tay cầm đỉnh của chóp, kết nối iPhone chơi Genshin không khác gì máy console.'),
(11, 40, 'Viên ngậm công hiệu, ngậm 2 hôm là đỡ hẳn đau rát họng.'),
(5, 6, 'Board mạch tháo máy nhưng hoạt động rất ổn định, đóng gói cẩn thận.'),
(8, 3, 'Xay tỏi ớt nhanh gọn lẹ, vệ sinh cũng dễ dàng. Rất hài lòng!'),
(9, 25, 'Quần short vải tổ ong xịn xò, mặc lên form đẹp mát mẻ, sẽ ủng hộ thêm.'),
(11, 8, 'Dây cáp xài tốt nhưng mình mua nhầm độ dài nên hơi căng, shop hỗ trợ đổi trả nhiệt tình.'),
(6, 26, 'Bộ chuyển đổi map phím nhạy, chơi game FPS bao phê, không bị delay.'),
(7, 9, 'Dây nguồn ruột đồng xịn, dây dẻo mềm chứ không bị cứng đơ như hàng chợ.'),
(10, 2, 'Laptop tuy cũ nhưng vỏ zin đẹp, pin xài được hơn 3 tiếng, đáng tiền.'),
(11, 11, 'Quạt tản nhiệt led tản sáng đều, chạy rất êm không bị ồn ào.');


-- =============================================
-- 5. BÁO CÁO, KHÁNG CÁO & THÔNG BÁO HỆ THỐNG
-- =============================================
INSERT INTO BaoCao (MaBC, IdNguoiBaoCao, IdDoiTuongBiBaoCao, MaHH, LoaiBaoCao, LyDoChinh, ChiTiet, TrangThai, NgayTao) VALUES
(101, 6, 4, NULL, 'NguoiBan', 'Thái độ phục vụ tệ', 'Shop này chửi khách trong tin nhắn khi mình hỏi về bảo hành.', 'ChoXuLy', '2023-12-25 10:00:00'),
(102, 11, 3, 11, 'SanPham', 'Hàng không đúng mô tả', 'Quạt giao tới không có dây cắm led như hình ảnh quảng cáo.', 'ViPham', '2023-12-26 09:30:00'),
(103, 5, 2, 3, 'SanPham', 'Hàng giả/Nhái', 'Nghe quảng cáo là Rep 1:1 nhưng chất âm rất tệ, check mã vạch thì ra hàng dỏm.', 'KhongViPham', '2023-12-27 14:20:00');

INSERT INTO KhangCao (MaKC, MaBC, IdNguoiKhangCao, NoiDung, TrangThai) VALUES
(101, 102, 3, 'Chào Admin, đây là lô quạt đời mới nhà sản xuất đã tích hợp sẵn dây nguồn chung với cổng 4pin PWM, mình đã chat giải thích với khách nhưng khách vẫn ngoan cố báo cáo.', 'ChoDuyet');

INSERT INTO ThongBao (MaTB, TieuDe, NoiDung, LoaiTB, NguoiGui) VALUES
(101, 'SIÊU SALE CUỐI NĂM - PHÍ SÀN GIẢM SÂU', 'Chào mừng lễ hội mua sắm, hệ thống giảm trực tiếp phí sàn xuống còn 2% cho tất cả các đơn hàng hoàn tất trong tuần này!', 'HeThong', 1),
(102, 'Cảnh báo Ngôn từ không phù hợp', 'Chúng tôi phát hiện bạn có sử dụng từ ngữ không phù hợp trong kênh Chat. Vi phạm thêm 1 lần nữa tài khoản của bạn sẽ bị khóa!', 'ViPham', 1),
(103, 'Yêu cầu Rút tiền đang chờ xử lý', 'Lệnh rút tiền 10.000.000đ của bạn đã được ghi nhận. Admin sẽ chuyển khoản trong vòng 24h làm việc.', 'HeThong', 1);

INSERT INTO ThongBaoNguoiDung (MaTB, IdNhan, DaXem) VALUES 
(101, 2, 0), (101,  3, 0), (101, 4, 1), 
(102, 4, 0), 
(103, 2, 1);


-- Bảng TaiKhoan cần thêm tọa độ nhà riêng cho người bán (Mình đặt trùng với Cửa hàng cho tiện test)
-- =====================================================================
INSERT INTO TaiKhoan (IdTaiKhoan, TenTK, Email, Sdt, MatKhau, VaiTro, TrangThaiBanHang, ViDo, KinhDo) VALUES
(12, 'shop_hanoi', 'hanoi@gmail.com', '0901000101', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 21.028511, 105.804817),
(13, 'shop_haiphong', 'haiphong@gmail.com', '0901000102', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 20.844911, 106.688084),
(14, 'shop_quangninh', 'quangninh@gmail.com', '0901000103', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 20.950453, 107.073361),
(15, 'shop_hue', 'hue@gmail.com', '0901000104', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 16.463713, 107.593782),
(16, 'shop_danang', 'danang@gmail.com', '0901000105', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 16.068341, 108.223849),
(17, 'shop_nhatrang', 'nhatrang@gmail.com', '0901000106', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 12.238791, 109.196749),
(18, 'shop_dalat', 'dalat@gmail.com', '0901000107', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 11.940419, 108.438313),
(19, 'shop_hcm', 'hcm@gmail.com', '0901000108', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.773234, 106.700984),
(20, 'shop_vungtau', 'vungtau@gmail.com', '0901000109', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 10.345990, 107.094260),
(21, 'shop_camau', 'camau@gmail.com', '0901000110', '$2y$10$Kyb2Fv7jzCGrx8j3B4sLN.l4nvJ2vLUwUkrfLyDiQh2P.gHMXT1Pm', 0, 'DangHoatDong', 9.176900, 105.150000);

-- =====================================================================
-- 2. THÊM TỌA ĐỘ VÀO BẢNG HoSoNguoiBan (Kho Hàng)
INSERT INTO HoSoNguoiBan (IdTaiKhoan, TenCuaHang, DiaChiKhoHang, ViDo, KinhDo, NgayDuyet, SoDu) VALUES
(12, 'Hà Nội 2Hand Store', '1 Kim Mã, Ba Đình, Hà Nội', 21.028511, 105.804817, NOW(), 0),
(13, 'Hải Phòng Vintage', '12 Lạch Tray, Ngô Quyền, Hải Phòng', 20.844911, 106.688084, NOW(), 0),
(14, 'Hạ Long Store', '1 Trần Hưng Đạo, Hạ Long, Quảng Ninh', 20.950453, 107.073361, NOW(), 0),
(15, 'Huế Cổ Phục', '12 Hùng Vương, Phú Hội, Thừa Thiên Huế', 16.463713, 107.593782, NOW(), 0),
(16, 'Đà Nẵng Retro', '100 Bạch Đằng, Hải Châu, Đà Nẵng', 16.068341, 108.223849, NOW(), 0),
(17, 'Biển Xanh Boutique', '50 Trần Phú, Lộc Thọ, Nha Trang, Khánh Hòa', 12.238791, 109.196749, NOW(), 0),
(18, 'Đà Lạt Len Shop', '1 Nguyễn Thị Minh Khai, Phường 1, Đà Lạt, Lâm Đồng', 11.940419, 108.438313, NOW(), 0),
(19, 'Sài Gòn Secondhand', '65 Lê Lợi, Bến Nghé, Quận 1, TP. Hồ Chí Minh', 10.773234, 106.700984, NOW(), 0),
(20, 'Vũng Tàu Thrift', '15 Thi Sách, Thắng Tam, Bà Rịa - Vũng Tàu', 10.345990, 107.094260, NOW(), 0),
(21, 'Cà Mau Fashion', '1 Trần Hưng Đạo, Phường 5, Cà Mau', 9.176900, 105.150000, NOW(), 0);

INSERT INTO DiaChi (IdTaiKhoan, DiaChiChiTiet, MacDinh, ViDo, KinhDo) VALUES
(12, '1 Kim Mã, Ba Đình, Hà Nội', 1, 21.028511, 105.804817),
(13, '12 Lạch Tray, Ngô Quyền, Hải Phòng', 1, 20.844911, 106.688084),
(14, '1 Trần Hưng Đạo, Hạ Long, Quảng Ninh', 1, 20.950453, 107.073361),
(15, '12 Hùng Vương, Phú Hội, Thừa Thiên Huế', 1, 16.463713, 107.593782),
(16, '100 Bạch Đằng, Hải Châu, Đà Nẵng', 1, 16.068341, 108.223849),
(17, '50 Trần Phú, Lộc Thọ, Nha Trang, Khánh Hòa', 1, 12.238791, 109.196749),
(18, '1 Nguyễn Thị Minh Khai, Phường 1, Đà Lạt, Lâm Đồng', 1, 11.940419, 108.438313),
(19, '65 Lê Lợi, Bến Nghé, Quận 1, TP. Hồ Chí Minh', 1, 10.773234, 106.700984),
(20, '15 Thi Sách, Thắng Tam, Bà Rịa - Vũng Tàu', 1, 10.345990, 107.094260),
(21, '1 Trần Hưng Đạo, Phường 5, Cà Mau', 1, 9.176900, 105.150000);


-- thêm sp từ 41 dến 70
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
-- IdNguoiBan 12 (MaHH 41 - 46)
(41, 12, 1, 'Tủ lạnh Panasonic Inverter 188L', 1, 2500000, 4500000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Tủ xài cực kỳ giữ kỹ, công nghệ Inverter siêu tiết kiệm điện, làm đá nhanh. Bao thợ test thoải mái.'),
(42, 12, 1, 'Nồi chiên không dầu Ariete 11L Đen', 2, 900000, 2000000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Được tặng tân gia nhưng ít xài. Máy chiên nướng cực ngon, không bị khô, nguyên hộp chưa xé bọc phụ kiện.'),
(43, 12, 1, 'Máy giặt lồng ngang LG 9kg AI DD', 1, 4500000, 8500000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Chuyển nhà cần thanh lý gấp, máy chạy êm ru không rung lắc, vắt đồ cực khô, mua về ghim điện là xài.'),
(44, 12, 2, 'Card Nvidia GTX 1660 Super 6Gb GDDR6', 1, 2600000, 3500000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Hàng người dùng chơi game lướt web, cam kết không trâu cày. Tản nhiệt mát rượi, cân tốt các game hiện nay.'),
(45, 12, 2, 'Dell Latitude 7490 i5-8350U 14 inch 8GB/256GB', 3, 2100000, 2800000, 'Mới', 'còn hàng', 'DaDuyet', 'Cấu hình 1: Giá bán: 5.790.000 VNĐ CPU: Intel® Core™ i5-8350U (4 lõi 8 luồng, 1.7GHZ up to 3.6GHz, 6MB Cache) RAM: 8GB DDR4 Ổ cứng: SSD 256GB Màn hình: 14 inch FHD (1920 x 1080)Đồ họa: Intel HD Graphics 620 Trọng lượng 1,4Kg'),
(46, 12, 2, 'RAM XPG SPECTRIX D50 8GB', 2, 950000, 1600000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Tháo máy thanh lý, tản nhiệt LED RGB sáng đẹp chớp nháy bao ngầu, bus 3200Hz chạy mượt mà.'),

-- IdNguoiBan 13 (MaHH 47 - 52)
(47, 13, 3, 'Dell XPS 9310 i7-1185G7 16GB/512GB', 1, 14500000, 21000000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Máy doanh nhân siêu mỏng nhẹ, vỏ nhôm nguyên khối, màn hình tràn viền cực sắc nét. Phù hợp anh chị em văn phòng.'),
(48, 13, 3, 'MacBook Pro M1 2020 8GB 256GB', 1, 16200000, 24000000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Máy nữ xài kỹ, ngoại hình đẹp 99% không cấn móp, pin sạc ít lần. Làm đồ họa nhẹ, chỉnh sửa ảnh vivu.'),
(49, 13, 3, 'THÙNG GAME-H110-i5 7500-GAM 8G-VGA GTX1050-SSD 256', 2, 6800000, 9500000, 'Mới', 'còn hàng', 'DaDuyet', 'Dàn máy quốc dân cho anh em cày cuốc. Chơi mượt LOL, FIFA, Valorant max setting. Vỏ case kính LED siêu xịn.'),
(50, 13, 4, 'Bàn làm việc gỗ cao su 140x60x80cm', 5, 450000, 800000, 'Mới', 'còn hàng', 'DaDuyet', 'Bàn làm việc 5 ngăn kéo .Gỗ cao su tự nhiên . Chắc nặng. Kích thước 140*60*80 '),
(51, 13, 4, 'Ghế xoay công thái học Ergonomic', 2, 1200000, 2200000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Cứu tinh cho cái lưng mùa cày đồ án. Lưới thoáng mát, có tựa đầu, ngả lưng 135 độ ngủ trưa thoải mái.'),
(52, 13, 4, 'Tủ quần áo nhựa Đài Loan 3 cánh', 1, 1400000, 2500000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Kích thước lớn để được nhiều đồ, nhựa không ẩm mốc mối mọt. Hỗ trợ phụ tiền xe ba gác chở về.'),

-- IdNguoiBan 14 (MaHH 53 - 58)
(53, 14, 5, 'Áo khoác da nam lót lông cừu', 3, 450000, 850000, 'Mới', 'còn hàng', 'DaDuyet', 'Lên form biker cực ngầu, da PU cao cấp không bong tróc, lót lông bên trong mặc bao ấm.'),
(54, 14, 5, 'Quần Jean nam ống suông Unisex', 10, 150000, 350000, 'Mới', 'còn hàng', 'DaDuyet', 'Trend Hàn Quốc cực dễ phối đồ. Chất denim dày dặn chuẩn xịn, mặc lên dáng thẳng tắp.'),
(55, 14, 5, 'Váy hoa nhí Vintage kiểu Pháp', 4, 120000, 280000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Pass lại váy đi Đà Lạt mặc đúng 1 lần chụp hình. Vải voan lụa 2 lớp thướt tha, eo thun dễ mặc.'),
(56, 14, 6, 'Máy chơi game Sony PS4 Slim 1TB', 1, 4200000, 6000000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Máy kèm 2 tay cầm xịn zin. Đã chép sẵn full game đá banh PES, God of War... Mua về cắm tivi là chiến.'),
(57, 14, 6, 'Nintendo Switch OLED Neon Fullbox', 1, 5600000, 8500000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Máy mua Hacom còn bảo hành, màn hình OLED siêu nét, đã dán cường lực và tặng kèm bóp đựng bảo vệ.'),
(58, 14, 6, 'Tay cầm Xbox One S kết nối PC', 5, 750000, 1300000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Nút bấm êm ru, cò L R nhạy bén, rung bần bật. Kết nối bluetooth mượt mà chơi game Steam số zách.'),

-- IdNguoiBan 15 (MaHH 59 - 64)
(59, 15, 7, 'Smart Tivi LG 43 inch 4K UHD', 1, 4100000, 7800000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Hình ảnh 4K sắc nét, lên mạng xem Youtube đọc báo vèo vèo. Tặng kèm giá treo tường xịn.'),
(60, 15, 7, 'Loa Bluetooth Marshall Emberton II', 1, 2800000, 4500000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Chính hãng ASH, bass đánh lực nghe cực chill phòng ngủ. Pin trâu xài cả tuần chưa hết.'),
(61, 15, 7, 'Máy ảnh Sony A6000 kèm lens kit', 1, 6500000, 9500000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Khởi đầu hoàn hảo cho dân tập chơi nhiếp ảnh. Lấy nét nhanh, chụp chân dung xóa phông mịt mù.'),
(62, 15, 1, 'Quạt điều hòa làm mát không khí Sunhouse', 2, 1100000, 2200000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Giải nhiệt mùa nóng siêu hiệu quả, có khay đá khô mát rượi như máy lạnh, lướt êm không ồn.'),
(63, 15, 2, 'Ổ cứng SSD Samsung 980 500GB NVMe', 6, 950000, 1500000, 'Mới', 'còn hàng', 'DaDuyet', 'Hàng xách tay Mỹ, tốc độ đọc ghi chớp nhoáng, boot win mở app chỉ tốn vài giây.'),
(64, 15, 3, 'Laptop Lenovo ThinkPad T14 Gen 1', 1, 8800000, 14000000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Bàn phím gõ êm nhất thế giới laptop. Máy nồi đồng cối đá, ngoại hình còn đẹp keng.'),

-- IdNguoiBan 16 (MaHH 65 - 70)
(65, 16, 4, 'Sofa giường', 1, 2200000, 4800000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Ban ngày làm ghế tiếp khách, ban đêm kéo ra thành giường ngủ bao tiện lợi. Nệm êm chưa xẹp lún.'),
(66, 16, 5, 'Áo sơ mi Flannel caro vintage', 8, 80000, 250000, 'Đã qua sử dụng', 'còn hàng', 'DaDuyet', 'Hàng tuyển kiện độ mới siêu cao, lỗi 1 đổi 1. Mặc khoác ngoài bao phong cách street style.'),
(67, 16, 6, 'Bàn phím cơ DareU EK87 Red Switch', 3, 350000, 650000, 'Mới', 'còn hàng', 'DaDuyet', 'Gõ êm ru không ồn ào ảnh hưởng bạn cùng phòng. LED đỏ rực rỡ, phím bấm nảy nhẹ.'),
(68, 16, 7, 'Tai nghe Apple AirPods Pro 2', 2, 3200000, 5800000, 'Gần như mới', 'còn hàng', 'DaDuyet', 'Tai nghe pass lại lên đời, chống ồn chủ động tuyệt đỉnh, pin còn siêu trâu, âm thanh bass đập chắc.'),
(69, 16, 7, 'Máy chiếu mini Beecube X2 Max Gen 3', 3, 1600000, 2500000, 'Mới', 'còn hàng', 'DaDuyet', 'Biến bức tường phòng ngủ thành rạp chiếu phim mini. Tích hợp sẵn Android tha hồ xem Netflix.'),
(70, 16, 1, 'Máy hút bụi cầm tay Xiaomi Deerma', 4, 450000, 850000, 'Mới', 'còn hàng', 'DaDuyet', 'Hút bụi giường nệm, mạng nhện rèm cửa siêu sạch. Thiết kế tháo lắp dễ dàng, lực hút mạnh mẽ.');

INSERT INTO HinhAnh (MaHH, URL) VALUES
(41, 'assets/images/products/41/41-1.jpg'), (41, 'assets/images/products/41/41-2.jpg'), (41, 'assets/images/products/41/41-3.jpg'),
(42, 'assets/images/products/42/42-1.jpg'),
(43, 'assets/images/products/43/43-1.jpg'), (43, 'assets/images/products/43/43-2.jpg'), (43, 'assets/images/products/43/43-3.jpg'),
(44, 'assets/images/products/44/44-1.jpg'), (44, 'assets/images/products/44/44-2.jpg'), (44, 'assets/images/products/44/44-3.jpg'),
(45, 'assets/images/products/45/45-1.jpg'), (45, 'assets/images/products/45/45-2.jpg'), (45, 'assets/images/products/45/45-3.jpg'), (45, 'assets/images/products/45/45-4.jpg'), (45, 'assets/images/products/45/45-5.jpg'),
(46, 'assets/images/products/46/46-1.jpg'), 
(47, 'assets/images/products/47/47-1.jpg'), (47, 'assets/images/products/47/47-2.jpg'), (47, 'assets/images/products/47/47-3.jpg'),
(48, 'assets/images/products/48/48-1.jpg'), (48, 'assets/images/products/48/48-2.jpg'), (48, 'assets/images/products/48/48-3.jpg'),
(49, 'assets/images/products/49/49-1.jpg'), (49, 'assets/images/products/49/49-2.jpg'), (49, 'assets/images/products/49/49-3.jpg'),
(50, 'assets/images/products/50/50-1.jpg'), (50, 'assets/images/products/50/50-2.jpg'), (50, 'assets/images/products/50/50-3.jpg'), (50, 'assets/images/products/50/50-4.jpg'), (50, 'assets/images/products/50/50-5.jpg'),
(51, 'assets/images/products/51/51-1.jpg'), (51, 'assets/images/products/51/51-2.jpg'), (51, 'assets/images/products/51/51-3.jpg'),
(52, 'assets/images/products/52/52-1.jpg'), (52, 'assets/images/products/52/52-2.jpg'), (52, 'assets/images/products/52/52-3.jpg'),
(53, 'assets/images/products/53/53-1.jpg'), (53, 'assets/images/products/53/53-2.jpg'), (53, 'assets/images/products/53/53-3.jpg'),
(54, 'assets/images/products/54/54-1.jpg'), (54, 'assets/images/products/54/54-2.jpg'), 
(55, 'assets/images/products/55/55-1.jpg'), 
(56, 'assets/images/products/56/56-1.jpg'), (56, 'assets/images/products/56/56-2.jpg'), (56, 'assets/images/products/56/56-3.jpg'),
(57, 'assets/images/products/57/57-1.jpg'), (57, 'assets/images/products/57/57-2.jpg'), (57, 'assets/images/products/57/57-3.jpg'),
(58, 'assets/images/products/58/58-1.jpg'), (58, 'assets/images/products/58/58-2.jpg'), (58, 'assets/images/products/58/58-3.jpg'),
(59, 'assets/images/products/59/59-1.jpg'), (59, 'assets/images/products/59/59-2.jpg'), (59, 'assets/images/products/59/59-3.jpg'),
(60, 'assets/images/products/60/60-1.jpg'), (60, 'assets/images/products/60/60-2.jpg'), 
(61, 'assets/images/products/61/61-1.jpg'), (61, 'assets/images/products/61/61-2.jpg'), (61, 'assets/images/products/61/61-3.jpg'),
(62, 'assets/images/products/62/62-1.jpg'), (62, 'assets/images/products/62/62-2.jpg'), (62, 'assets/images/products/62/62-3.jpg'),
(63, 'assets/images/products/63/63-1.jpg'), (63, 'assets/images/products/63/63-2.jpg'), (63, 'assets/images/products/63/63-3.jpg'),
(64, 'assets/images/products/64/64-1.jpg'), (64, 'assets/images/products/64/64-2.jpg'), (64, 'assets/images/products/64/64-3.jpg'),
(65, 'assets/images/products/65/65-1.jpg'), (65, 'assets/images/products/65/65-2.jpg'), (65, 'assets/images/products/65/65-3.jpg'),
(66, 'assets/images/products/66/66-1.jpg'), (66, 'assets/images/products/66/66-2.jpg'), 
(67, 'assets/images/products/67/67-1.jpg'), 
(68, 'assets/images/products/68/68-1.jpg'), (68, 'assets/images/products/68/68-2.jpg'), (68, 'assets/images/products/68/68-3.jpg'),
(69, 'assets/images/products/69/69-1.jpg'), (69, 'assets/images/products/69/69-2.jpg'),
(70, 'assets/images/products/70/70-1.jpg'), (70, 'assets/images/products/70/70-2.jpg'), (70, 'assets/images/products/70/70-3.jpg');




-- thêm sp từ 71 đến 100
-- người bán 21

-- hh71
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(71, 21, 5, 'Giày thể thao Adidas chính hãng đã qua sử dụng', 1, 850000, 1000000, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet', 'Giày thể thao Adidas màu trắng phối xanh lá, phù hợp cho cả nam.
 - Chất liệu da tổng hợp bền đẹp. 
 - Size 42.5 (EU)  
 - Thiết kế năng động, dễ phối đồ.
 - Đã qua sử dụng được 2 lần
 - Không Box
 - Chính hãng 100% (đã sử dụng checkcheck để kiểm tra)
 - Giá cả có thể thương lượng nếu chốt nhanh, chỉ giao dịch tại Ninh Kiều Cần Thơ');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(71, 'assets/images/products/71/71-1.jpg'), (71, 'assets/images/products/71/71-2.jpg'), (71, 'assets/images/products/71/71-3.jpg');

-- hh72
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
                    (72, 21, 5, 'Thanh Lý Giày thể thao Adidas XLG Runner Nam', 2, 3000000, 3500000, 'Mới', 'Còn hàng', 'DaDuyet', '🔥 THANH LÝ NHẸ – HÀNG CHẤT CHO AE BIẾT CHƠI 🔥
Adidas XLG Runner Deluxe – form hầm hố, cực kỳ thời trang 💣
✔️ Tình trạng: Mới 100% do mình đi du lịch bên Bắc Kinh mua về nhưng không hợp style nên để đó ko đi.
✔️ Size: 39 1/3 (fit chân 39–40)
✔️ Full box, tem mác đầy đủ
✔️ Màu đen phối xám – dễ phối đồ, đi street hay casual đều nổi
💎 Điểm nổi bật:
– Đế chunky siêu êm, tôn dáng
– Thiết kế futuristic, nhìn là biết dân chơi
– Mang cực kỳ đầm chân, đi lâu không mỏi
💰 Giá thanh lý: 3.000.000đ (fix nhẹ cho người thiện chí)
🚀 Ai nhanh tay thì còn – hàng đẹp không chờ lâu
⚡ Ưu tiên người chốt nhanh – giao dịch gọn gàng
#Adidas #SneakerThanhLy #GiayDep #StreetStyle');
INSERT INTO HinhAnh (MaHH, URL) VALUES 
(72, 'assets/images/products/72/72-1.jpg'), (72, 'assets/images/products/72/72-2.jpg'), (72, 'assets/images/products/72/72-3.jpg');

-- hh73
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(73, 21, 8, 'Đồng hồ Seiko 7S26 21J Nam', 1, 1800000, 2000000, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet', 'Góc mua bán
Xin phép add
Em lên em xanh dương ít gặp , size 37 độ mới cao, còn hộp k sổ đủ mắc thừa, máy móc dial đẹp như mới, lộ mông máy 7s26 21j, chạy chuẩn chỉ, có dạ quang, lên tay sang hợp ae cổ tay vừa và nhỏ!
Giá 1tr8 (HCM)
Thanks add và ae đã xem tin');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(73, 'assets/images/products/73/73-1.jpg'), (73, 'assets/images/products/73/73-2.jpg'), (73, 'assets/images/products/73/73-3.jpg');

-- hh74
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(74, 21, 5, 'Thể thao Running Nhật', 14, 150000, 180000, 'Mới', 'Còn hàng', 'DaDuyet', 'Thể thao Running Nhật
Tình trạng như áo mới, chất thun lạnh run mỏng nhẹ mát, phối đen đỏ, cổ zip.
Size L ngang 55 dài 70,73 tham khảo 70-77kg
Giá 150ka ck bao ship.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(74, 'assets/images/products/74/74-1.jpg'), (74, 'assets/images/products/74/74-2.jpg');

-- hh75
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(75, 21, 1, 'Túi đựng đa năng vải nhiều màu', 14, 10000, 20000, 'Mới', 'Còn hàng', 'DaDuyet', 'Túi đựng đa năng nhiều màu, chất liệu vải bền đẹp.
 - Thiết kế nhỏ gọn, tiện lợi.
 - Phù hợp cho cả nam và nữ.
 - Giá 10 nghìn/túi.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(75, 'assets/images/products/75/75-1.jpg'), (75, 'assets/images/products/75/75-2.jpg'), (75, 'assets/images/products/75/75-3.jpg');

-- hh76
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(76, 21, 5, 'Áo sơ mi nam vải hoa', 2, 150000, 160000, 'Mới', 'Còn hàng', 'DaDuyet', '150k size XL. XXL
Vải dày, mềm, ko xù lông, ko nhão
Hàng có sẵn');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(76, 'assets/images/products/76/76-1.jpg');

-- người bán 20
-- hh77
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(77, 20, 1, 'Bút bi PIERRE CARDIN Victoria Bạc', 2, 999000, 1200000, 'Mới', 'Còn hàng', 'DaDuyet', 'Bút bi xoay PIERRE CARDIN Victoria
• Thân bút bằng kim loại với các đường rãnh dọc tinh tế.
• Phần cài bút được đính một hàng đá xanh tạo điểm nhấn nổi bật và sang trọng.
Em gl 999k');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(77, 'assets/images/products/77/77-1.jpg'), (77, 'assets/images/products/77/77-2.jpg'), (77, 'assets/images/products/77/77-3.jpg');

-- hh78
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(78, 20, 1, 'Máy may công nghiệp Juki Xám', 1, 1000000, 1200000, 'Mới', 'Còn hàng', 'DaDuyet', '
Máy may công nghiệp Juki màu xám, đã qua sử dụng nhưng còn hoạt động tốt. 
 - Máy may khỏe, đường may đẹp. 
 - Phù hợp cho xưởng may hoặc cá nhân có nhu cầu.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(78, 'assets/images/products/78/78-1.jpg');

-- hh79
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(79, 20, 9, 'Máy bơm nước Panasonic Xanh dương', 1, 500000, 700000, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet', 'Máy bơm nước Panasonic màu xanh dương, thiết kế nhỏ gọn, dễ lắp đặt. 
 - Hoạt động êm ái, bền bỉ. 
 - Phù hợp cho gia đình, tưới tiêu.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(79, 'assets/images/products/79/79-1.jpg'), (79, 'assets/images/products/79/79-2.jpg'), (79, 'assets/images/products/79/79-3.jpg');

-- hh80
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(80, 20, 9, 'Máy in HP 2 mặt tự động đảo giấy', 1, 1400000 , 1800000, 'Mới', 'Còn hàng', 'DaDuyet', 'máy in dành cho hộ kinh doanh, dành cho lam dịch vụ in ân ...dùng cho hộ gia đình in tài liệu cho con cái học tập, in nhanh ,hộp mực lớn,dễ dàng nạp mực tại nhà,  mực phổ thông,gia thành rẻ , in A4 & A5  IN 2 mặt tự động..  bản in sắc nét ,máy bao gồm hộp mực ,cáp kết nối, cài đặt thao tác kết nối là in .. máy thanh lý từ cơ quan tình trạng mới trên 95%');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(80, 'assets/images/products/80/80-1.jpg');

-- hh81
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(81, 20, 8, 'Máy tiệt trùng UV Trắng', 1, 250000  , 300000, 'Mới', 'Còn hàng', 'DaDuyet', 'Máy tiệt trùng đèn UV màu trắng, thiết kế nhỏ gọn, tiện lợi. 
 - Tiệt trùng hiệu quả dụng cụ làm đẹp. 
 - Đảm bảo vệ sinh, an toàn khi sử dụng.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(81, 'assets/images/products/81/81-1.jpg'), (81, 'assets/images/products/81/81-2.jpg'), (81, 'assets/images/products/81/81-3.jpg');

-- hh82
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(82, 20, 8, 'Máy hàn ống nhựa Vàng Đã sử dụng', 1, 120000 , 150000, 'Đã qua sử dụng', 'Còn hàng', 'DaDuyet', 'hàn ống nhựa chịu nhiệt còn hoạt động tốt bán lại cho ai cần');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(82, 'assets/images/products/82/82-1.jpg'), (82, 'assets/images/products/82/82-2.jpg');

-- hh83
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(83, 20, 8, 'Bảng menu gỗ thông 50x70cm', 1, 230000 , 280000, 'Mới', 'Còn hàng', 'DaDuyet', 'Bảng menu KT 50x70cm dùng trưng bày nhà hàng, quán ăn.
Bảng menu dùng treo tường hoặc dùng kết hợp với giá đỡ bảng ( chân gỗ ).
Dùng để trình bày nội dung quảng cáo, khuyến mãi, thực đơn… cho cửa hàng, quán ăn, quán cafe.
Khung bảng bằng gỗ thông với tông màu sáng.
Bạn có thể treo bảng lên tường hoặc dùng giá đỡ nhằm thu hút thêm sự chú ý của khách hàng vào thông điệp quảng cáo của mình.
Với nhiều kích thước cho bạn lựa chọn, bảng phù hợp với cả việc treo menu trên tường hay dựng đứng trên giá gỗ để trước cửa.
Hiện nay có thể thấy menu treo tường được thiết kế hầu hết tại các quán café, trà sữa, nhà hàng, quán ăn … theo nhiều hình thức khác nhau.
Khách hàng sẽ làm gì khi rảnh rỗi ngoài đợi món? Tất nhiên là sẽ lướt trên menu và chọn các món khác rồi.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(83, 'assets/images/products/83/83-1.jpg'), (83, 'assets/images/products/83/83-2.jpg');

-- hh84
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(84, 20, 1, 'Cân đồng hồ 15kg Kim loại Xanh', 1, 250000 , 300000, 'Mới', 'Còn hàng', 'DaDuyet', 'ít dùng đến cần thanh lý cân 15 kg 250k');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(84, 'assets/images/products/84/84-1.jpg');

-- người bán 19
-- hh85
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(85, 19, 4, 'Đồng hồ treo tường Gỗ Nâu', 1, 3500000 , 4000000, 'Mới', 'Còn hàng', 'DaDuyet', 'Đồng hồ treo tường gỗ màu nâu, phong cách cổ điển.
 - Thiết kế tinh tế, tạo điểm nhấn cho không gian.
 - Chất liệu gỗ bền đẹp, chắc chắn.
 - Phù hợp trang trí phòng khách, phòng làm việc.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(85, 'assets/images/products/85/85-1.jpg');

-- hh86
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(86, 19, 4, 'Ổ cắm điện 3 chấu 5m Trắng', 1, 70000 , 80000, 'Mới', 'Còn hàng', 'DaDuyet', 'Ổ điện dây dài 5m. vẫn sử dụng được. pass do không còn nhu cầu sử dụng');
insert into HinhAnh (MaHH, URL) values
(86, 'assets/images/products/86/86-1.jpg'), (86, 'assets/images/products/86/86-2.jpg'); 
 -- hh87
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(87, 19, 1, 'Ấm siêu tốc Thép không gỉ Bạc Hỏng', 1, 15000 , 30000, 'Mới', 'Còn hàng', 'DaDuyet', 'Ấm siêu tốc Thép không gỉ màu bạc, thiết kế hiện đại.');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(87, 'assets/images/products/87/87-1.jpg'), (87, 'assets/images/products/87/87-2.jpg');

-- người bán 18
-- hh88
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(88, 18, 4, 'Ghế dã ngoại gấp gọn Đen', 2, 82000 , 100000, 'Mới', 'Còn hàng', 'DaDuyet', 'Ghế Dã Ngoại Gấp Gọn, Phù Hợp Du Lịch Dã Ngoại Cắm Trại Câu Cá
✨✨✨  Sỉ: 82k/c SL giảm75k
✅ Ghế ngồi cực kì chắc chắn ạ
✅ Khung chữ X, ổn định cao; Gấp mở nhanh chóng.
✅ Có thể sử dụng trong điều kiện địa hình, dùng trong mọi hoạt động như : đi bộ, đạp xe , camping, ');
insert into HinhAnh (MaHH, URL) values
(88, 'assets/images/products/88/88-1.jpg');

-- hh89
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(89, 18, 6, 'Gamesir Dawn star 2, có fix giá', 2, 350000 , 400000, 'Mới', 'Còn hàng','DaDuyet', 'Gamesir
Tay cầm chơi game Qixingming 2
3 mode kết nối');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(89, 'assets/images/products/89/89-1.jpg'), (89, 'assets/images/products/89/89-2.jpg'), (89, 'assets/images/products/89/89-3.jpg');

-- hh90
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(90, 18, 6, 'Máy chơi game cầm tay 16GB Đen', 1, 500000 , 550000, 'Mới', 'Còn hàng', 'DaDuyet', 'T bán máy game retro kèm thẻ 16gb đã cóp nhiều game hoạt động bình thường 500k');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(90, 'assets/images/products/90/90-1.jpg');

-- hh91
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(91, 18, 6, 'Pass nhanh combo game giá rẻ', 1, 200000 , 250000, 'Mới', 'Còn hàng', 'DaDuyet', 'Hàng mới nguyên hộp chưa qua sử dụng. Mình pass nhanh giá rất rẻ. Hỗ trợ game mobile rất ngon lành
Bán nhanh 200K');
INSERT INTO HinhAnh (MaHH, URL) VALUES
(91, 'assets/images/products/91/91-1.jpg'), (91, 'assets/images/products/91/91-2.jpg'), (91, 'assets/images/products/91/91-3.jpg');

-- hh92
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(92, 18, 6, 'Trò chơi điện tử PS4', 1, 600000 , 650000, 'Mới', 'Còn hàng', 'DaDuyet', 'Cần thanh lý một số game PS4. Mỗi game mình bán 200.000. Gdtt tại Hà Nội.');
insert into HinhAnh (MaHH, URL) values
(92, 'assets/images/products/92/92-1.jpg'), (92, 'assets/images/products/92/92-2.jpg'), (92, 'assets/images/products/92/92-3.jpg');

-- hh93
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(93, 18, 6, 'Máy chơi game Sony PS4 Pro Đen', 1, 4800000 , 5200000, 'Mới', 'Còn hàng', 'DaDuyet', 'K có nhu cầu cần bán ps4 pro cuh-7017b hắc ám phiên bản 11.0
Đầy đủ phụ kiện, 1 tay k lên, kèm chân đế
Fix cho ae nhiệt tình');
insert into HinhAnh (MaHH, URL) values
(93, 'assets/images/products/93/93-1.jpg'), (93, 'assets/images/products/93/93-2.jpg'), (93, 'assets/images/products/93/93-3.jpg');

-- hh94
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(94, 18, 6, 'Máy chơi game Sony PS Vita 2000 Xanh', 1, 2700000 , 3000000, 'Mới', 'Còn hàng', 'DaDuyet', 'Máy chơi game cầm tay Sony PS Vita 2000 đã hack
 - Kèm ốp và núm joystick.
 - Thiết kế nhỏ gọn, màn hình đẹp.
 - Kho game đa dạng, giải trí mọi lúc mọi nơi.');
insert into HinhAnh (MaHH, URL) values
(94, 'assets/images/products/94/94-1.jpg'), (94, 'assets/images/products/94/94-2.jpg');

-- hh95
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(95, 18, 6, 'Máy chơi game Sony PS4 Pro 1TB Đen', 1, 5200000 , 6000000, 'Mới', 'Còn hàng', 'DaDuyet', 'Ps4 Pro 1TB Hack đã chép nhiều game khủng, chơi game mượt mà');
insert into HinhAnh (MaHH, URL) values
(95, 'assets/images/products/95/95-1.jpg');

-- người bán 17
-- hh96
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(96, 17, 7, 'Bàn phím cơ Newmen GM601 Pro 99%', 1, 1100000 , 1400000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'newmen gm601 pro ( 99%) full box
full 3 mod kết nối');
insert into HinhAnh (MaHH, URL) values
(96, 'assets/images/products/96/96-1.jpg'), (96, 'assets/images/products/96/96-2.jpg'), (96, 'assets/images/products/96/96-3.jpg');

-- hh97
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(97, 17, 7, 'Cáp sạc Xiaomi Type C 100W', 1, 64890 , 100000, 'Mới', 'Còn hàng', 'DaDuyet', 'Dây cáp sạc Xiaomi Gold Label, 6A-100W, tích hợp chip, công nghệ SuperCharge, dùng cho các dòng Android cổng sạc Type C
 hàng mới 100%');
insert into HinhAnh (MaHH, URL) values
(97, 'assets/images/products/97/97-1.jpg'), (97, 'assets/images/products/97/97-2.jpg'), (97, 'assets/images/products/97/97-3.jpg');

-- hh98
insert into HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(98, 17, 7, 'Bộ chuyển đổi Wifi USB AC1200Mbps', 2, 100000 , 150000, 'Gần như mới', 'Còn hàng', 'DaDuyet', 'Bộ chuyển đổi USB không dây tốc độ AC1200Mbps, chuẩn Wifi 5. 
 - Kết nối USB tiện lợi. 
 - Màu đen nhỏ gọn, dễ mang theo. 
 - Giúp máy tính bàn, laptop kết nối Wifi nhanh chóng, ổn định.');
insert into HinhAnh (MaHH, URL) values
(98, 'assets/images/products/98/98-1.jpg');

-- hh99
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(99, 17, 6, 'Chuột Bluetooth Logitech M650 Đen - Mới nguyên hộp', 1, 390000  , 500000, 'Mới', 'Còn hàng', 'DaDuyet', 'Thanh lý 5 chuột không dây Bluetooth Logitech Signature M650 màu đen.
 - Hàng mới 100% chưa bóc tem, bảo hành chính hãng, size M
 - Độ phân giải 400-4000 DPI, cảm biến quang học nâng cao.
 - Tương thích nhiều hệ điều hành: Windows, macOS, Linux, ChromeOS, iPadOS, Android.
 - Thiết kế 5 nút tiện lợi: nhấp trái/phải, quay lại/tiếp tục, nút cuộn với nhấp chuột giữa.
- Sử dụng cả 2 dạng kết nối USB Logi Bolt và Bluetooth. Có kết nối với app của Logitech để tiện cài đặt điều khiển đa dụng.');
insert into HinhAnh (MaHH, URL) values
(99, 'assets/images/products/99/99-1.jpg'), (99, 'assets/images/products/99/99-2.jpg'), (99, 'assets/images/products/99/99-3.jpg');

-- hh100
INSERT INTO HangHoa (MaHH, IdNguoiBan, MaDM, TenHH, SoLuongHH, Gia, GiaThiTruong, ChatLuongHang, TinhTrangHang, TrangThaiDuyet, MoTa) VALUES 
(100, 17, 7, 'Màn hình MSI G27C4X 27 inch bh hãng 12/2026', 1, 2450000 , 3000000, 'Mới', 'Còn hàng', 'DaDuyet', 'E cần bán màn hình MSI G27C4X 27in fullhd cong 250hz còn bảo hành hãng đến 12/2026 sử dụng bình thường còn thùng đầy đủ 1 chủ. Ai cần liên hệ e qua lấy có fix thêm. Xin cảm ơn');
insert into HinhAnh (MaHH, URL) values
(100, 'assets/images/products/100/100-1.jpg'), (100, 'assets/images/products/100/100-2.jpg'), (100, 'assets/images/products/100/100-3.jpg');



