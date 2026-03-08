<?php

session_start();

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/sellerSanPhamModel.php';
require_once __DIR__ . '/../../admin/models/adminDanhMucModel.php'; // Dùng chung model DanhMuc của Admin

// KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['IdTaiKhoan'])) {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

$sql_check_ban = "SELECT TrangThaiBanHang, DiemViPham FROM TaiKhoan WHERE IdTaiKhoan = ?";
$stmt_check = $conn->prepare($sql_check_ban);
$stmt_check->bind_param("i", $_SESSION['IdTaiKhoan']);
$stmt_check->execute();
$tk_info = $stmt_check->get_result()->fetch_assoc();

if ($tk_info && $tk_info['TrangThaiBanHang'] == 'BiKhoa') {
    // Nếu tài khoản bị khóa vĩnh viễn

    // Tìm báo cáo gần nhất khiến người này bị khóa
    $sql_bc = "SELECT MaBC FROM BaoCao WHERE IdDoiTuongBiBaoCao = ? AND TrangThai = 'ViPham' ORDER BY NgayTao DESC LIMIT 1";
    $stmt_bc = $conn->prepare($sql_bc);
    $stmt_bc->bind_param("i", $_SESSION['IdTaiKhoan']);
    $stmt_bc->execute();
    $bc_gannhat = $stmt_bc->get_result()->fetch_assoc();

    // Nếu bấm submit Kháng cáo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['noiDungKhangCao'])) {
        $maBC = $bc_gannhat['MaBC'];
        $noiDung = trim($_POST['noiDungKhangCao']);

        $sql_kc = "INSERT INTO KhangCao (MaBC, IdNguoiKhangCao, NoiDung) VALUES (?, ?, ?)";
        $stmt_kc = $conn->prepare($sql_kc);
        $stmt_kc->bind_param("iis", $maBC, $_SESSION['IdTaiKhoan'], $noiDung);
        if ($stmt_kc->execute()) {
            echo "<script>alert('Đã gửi kháng cáo thành công. Admin sẽ xem xét sớm nhất!'); window.location.href='../controllers/trangChuController.php';</script>";
            exit;
        }
    }

    // HIỂN THỊ MÀN HÌNH CHẶN & CHO PHÉP KHÁNG CÁO
    echo "
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
    <div class='container mt-5' style='max-width: 600px;'>
        <div class='alert alert-danger text-center'>
            <h2><i class='fa-solid fa-lock'></i> TÀI KHOẢN BỊ KHÓA</h2>
            <p>Tài khoản bán hàng của bạn đã bị khóa vĩnh viễn do tích lũy <b>{$tk_info['DiemViPham']} gậy vi phạm</b> chính sách cộng đồng.</p>
        </div>
        
        <div class='card shadow-sm'>
            <div class='card-header bg-warning'><b>Gửi Yêu Cầu Kháng Cáo</b></div>
            <div class='card-body'>
                <p>Nếu bạn cho rằng đây là một sự nhầm lẫn, vui lòng điền lý do và bằng chứng kháng cáo tại đây. Admin sẽ xem xét thủ công để mở khóa cho bạn.</p>
                <form method='POST'>
                    <textarea class='form-control mb-3' name='noiDungKhangCao' rows='4' required placeholder='Ghi rõ lý do bạn không vi phạm chính sách...'></textarea>
                    <button type='submit' class='btn btn-primary w-100'>Gửi Kháng Cáo Cho Admin</button>
                    <a href='../controllers/trangChuController.php' class='btn btn-secondary w-100 mt-2'>Quay lại Trang Chủ</a>
                </form>
            </div>
        </div>
    </div>";
    exit; // Dừng toàn bộ script bên dưới, không cho truy cập Kênh Người Bán nữa
}

$idNguoiBan = $_SESSION['IdTaiKhoan'];
$sanPhamModel = new SellerSanPhamModel($conn);
$danhMucModel = new AdminDanhMucModel($conn);
$message = '';
$error = '';

try {
    // XỬ LÝ POST (THÊM / SỬA / XÓA ẢNH)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        $mahh = (int)($_POST['mahh'] ?? 0);

        // Lấy dữ liệu form
        $ten = trim($_POST['ten'] ?? '');
        $gia = (float)($_POST['gia'] ?? 0);
        $madm = (int)($_POST['madm'] ?? 0);
        $soluong = (int)($_POST['soluong'] ?? 0);
        $mota = $_POST['mota'] ?? '';
        $chatluong = $_POST['chatluong'] ?? 'Mới';
        $tinhtrang = $_POST['tinhtranghang'] ?? 'Còn hàng';
        $giathitruong = 0; // Người dùng bán có thể không cần giá thị trường, hoặc thêm input sau

        // THÊM MỚI
        if ($action === 'add') {
            if (empty($ten) || $gia <= 0) throw new Exception("Vui lòng nhập tên và giá hợp lệ.");

            $newId = $sanPhamModel->themSanPham($idNguoiBan, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtrang, $mota);

            if ($newId) {
                $mahh = $newId; // Gán ID mới để lát upload ảnh
                $message = "Đăng bán thành công! Sản phẩm đang chờ duyệt.";
            } else {
                throw new Exception("Lỗi khi thêm sản phẩm vào CSDL.");
            }
        }
        //CẬP NHẬT
        elseif ($action === 'update' && $mahh > 0) {
            $result = $sanPhamModel->suaSanPham($idNguoiBan, $mahh, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtrang, $mota);
            if ($result) {
                $message = "Cập nhật thành công! Sản phẩm chuyển sang trạng thái chờ duyệt.";
            } else {
                throw new Exception("Lỗi cập nhật hoặc bạn không có quyền sửa sản phẩm này.");
            }
        }

        //XỬ LÝ UPLOAD ẢNH (Dùng chung cho cả Thêm và Sửa, nếu có mã hàng hợp lệ và file ảnh được gửi lên)
        if ($mahh > 0 && isset($_FILES['image_file'])) {
            // Đường dẫn vật lý để lưu file
            $upload_dir_physical = realpath(__DIR__ . "/../../assets/images/products/uploaded/");
            // Đường dẫn tương đối để lưu vào DB
            $web_path_relative = 'assets/images/products/uploaded/';

            if (!file_exists($upload_dir_physical)) {
                mkdir($upload_dir_physical, 0777, true);
            }

            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ds_anh_db = [];

            foreach ($_FILES['image_file']['tmp_name'] as $key => $tmp_name) {
                $name = $_FILES['image_file']['name'][$key];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed) && $_FILES['image_file']['size'][$key] < 5000000) {
                    $filename = time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmp_name, $upload_dir_physical . DIRECTORY_SEPARATOR . $filename)) {
                        $ds_anh_db[] = $web_path_relative . $filename;
                    }
                }
            }

            if (!empty($ds_anh_db)) {
                $sanPhamModel->themNhieuAnh($mahh, $ds_anh_db);
            }
        }

        header("Location: sellerSanPhamController.php?success=1");
        exit;
    }

    //ACTION: XÓA SẢN PHẨM (GET)
    if (isset($_GET['xoa'])) {
        $idXoa = (int)$_GET['xoa'];
        if ($sanPhamModel->xoaSanPham($idNguoiBan, $idXoa)) {
            $message = "Đã xóa sản phẩm.";
        } else {
            $error = "Xóa thất bại (Có thể sản phẩm không phải của bạn).";
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

//LẤY DỮ LIỆU ĐỂ HIỂN THỊ RA VIEW
$keyword = $_GET['search'] ?? '';
$danhSachSanPham = $sanPhamModel->getSanPhamCuaToi($idNguoiBan, $keyword);
$danhSachDanhMuc = $danhMucModel->getTatCaDanhMuc(); // Lấy danh mục để hiển thị modal thêm/sửa

//Xử lý lấy dữ liệu khi bấm Sửa
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_item = $sanPhamModel->getSanPhamById($idNguoiBan, (int)$_GET['edit']);
    if ($edit_item) {
        $edit_item['DanhSachAnh'] = $sanPhamModel->getAnhSanPham($edit_item['MaHH']);
    }
}

// Lấy ảnh đại diện cho danh sách
foreach ($danhSachSanPham as &$sp) {
    $sp['DanhSachAnh'] = $sanPhamModel->getAnhSanPham($sp['MaHH']);
}

include_once __DIR__ . '/../views/sellerQuanLySanPham.php';
