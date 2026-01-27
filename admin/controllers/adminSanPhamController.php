<?php

require_once __DIR__ . '/../../includes/ketnoi.php';
require_once __DIR__ . '/../models/adminSanPhamModel.php';
require_once __DIR__ . '/../models/adminDanhMucModel.php';

if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] !== 'admin') {
    header("Location: ../../controllers/dangNhapController.php");
    exit;
}

// KHỞI TẠO MODEL VÀ CÁC BIẾN
$sanPhamModel = new AdminSanPhamModel($conn);
$danhMucModel = new AdminDanhMucModel($conn); // Khởi tạo model danh mục
$message = '';

// LẤY TỪ KHÓA TÌM KIẾM
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$danhSachSanPham = $sanPhamModel->getTatCaSanPham($keyword);
try {
    // đường dẫn ãnh
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_image') {
        $mahh = (int)($_POST['mahh'] ?? 0);
        $image_url = $_POST['image_url'] ?? '';

        if ($mahh > 0 && !empty($image_url)) {
            $stmt = $conn->prepare("DELETE FROM HinhAnh WHERE MaHH = ? AND URL = ?");
            $stmt->bind_param("is", $mahh, $image_url);
            if ($stmt->execute()) {
                $file_to_delete = realpath(__DIR__ . "/../../" . $image_url);
                if ($file_to_delete && file_exists($file_to_delete)) {
                    @unlink($file_to_delete);
                }
                header("Location: adminSanPhamController.php?edit=$mahh&img_deleted=1");
                exit;
            }
        }
    }
    // Xử lý THÊM/SỬA (POST) 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        $mahh = (int)($_POST['mahh'] ?? 0);
        $ten = trim($_POST['ten'] ?? '');
        $mota = $_POST['mota'] ?? '';
        $gia = (float)($_POST['gia'] ?? 0);
        $giathitruong = (float)($_POST['giathitruong'] ?? 0);
        $madm = (int)($_POST['madm'] ?? 0);
        $chatluong = $_POST['chatluong'] ?? 'Mới';
        $tinhtranghang = $_POST['tinhtranghang'] ?? 'ngưng kinh doanh';
        $soluong = (int)($_POST['soluong'] ?? 0);

        if (empty($ten) || $gia <= 0 || $madm <= 0) {
            throw new Exception("Vui lòng nhập đủ thông tin bắt buộc (Tên, Giá, Danh mục).");
        }

        $conn->begin_transaction();

        if ($action === 'add') {
            $mahh = $sanPhamModel->themSanPham($ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota);
            if (!$mahh) throw new Exception("Lỗi CSDL khi thêm sản phẩm.");
            $message = ' Thêm sản phẩm mới thành công!';
        } elseif ($action === 'update' && $mahh > 0) {
            if (!$sanPhamModel->suaSanPham($mahh, $ten, $madm, $soluong, $gia, $giathitruong, $chatluong, $tinhtranghang, $mota)) {
                throw new Exception("Lỗi CSDL khi cập nhật sản phẩm.");
            }
            $message = ' Cập nhật sản phẩm thành công!';
        }

        // Xử lý Upload NHIỀU Ảnh
        if ($mahh > 0 && isset($_FILES['image_file']) && is_array($_FILES['image_file']['tmp_name'])) {
            $upload_dir_physical = realpath(__DIR__ . "/../../assets/images/products/uploaded/");
            $web_path_relative = 'assets/images/products/uploaded/';

            if ($upload_dir_physical === false) {
                if (!mkdir(__DIR__ . '/../../assets/images/products/uploaded/', 0777, true)) {
                    throw new Exception("Không thể tạo thư mục upload.");
                }
                $upload_dir_physical = realpath(__DIR__ . "/../../assets/images/products/uploaded/");
            }

            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ds_anh = [];

            foreach ($_FILES['image_file']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['image_file']['name'][$key];
                $file_size = $_FILES['image_file']['size'][$key];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if (!in_array($file_ext, $allowed_exts)) continue;
                if ($file_size > 5 * 1024 * 1024) continue;

                $filename = time() . '_' . uniqid() . '.' . $file_ext;
                $target_physical = $upload_dir_physical . DIRECTORY_SEPARATOR . $filename;

                if (move_uploaded_file($tmp_name, $target_physical)) {
                    $ds_anh[] = $web_path_relative . $filename;
                }
            }

            if (!empty($ds_anh)) {
                $sanPhamModel->themNhieuAnh($mahh, $ds_anh);
            }
        }

        $conn->commit();
        header("Location: adminSanPhamController.php?success=1"); // Redirect để tránh F5
        exit;
    }
    // ---- Xử lý XÓA (GET) ----
    if (isset($_GET['xoa'])) {
        $id = (int)$_GET['xoa'];
        if ($id <= 0) throw new Exception("ID sản phẩm không hợp lệ.");

        $conn->begin_transaction();
        if ($sanPhamModel->xoaSanPham($id)) {
            $conn->commit();
            header("Location: adminSanPhamController.php?deleted=1");
            exit;
        } else {
            $conn->rollback();
            throw new Exception("Không tìm thấy sản phẩm để xóa hoặc lỗi khi xóa.");
        }
    }
    // ---- XỬ LÝ DUYỆT, TỪ CHỐI, ẨN, HIỆN ----
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $id = (int)$_GET['id'];
        $msg = "";

        if ($action === 'duyet') {
            $sanPhamModel->duyetSanPham($id, 'DaDuyet');
            $msg = 'Đã duyệt sản phẩm!';
        } elseif ($action === 'tuchoi') {
            $sanPhamModel->duyetSanPham($id, 'TuChoi', 'Vi phạm tiêu chuẩn cộng đồng');
            $msg = 'Đã từ chối sản phẩm!';
        } elseif ($action === 'an') {
            $sanPhamModel->capNhatHienThi($id, 0); // Set HienThi = 0
            $msg = 'Đã ẩn sản phẩm khỏi trang chủ!';
        } elseif ($action === 'hien') {
            $sanPhamModel->capNhatHienThi($id, 1); // Set HienThi = 1
            $msg = 'Đã cho phép hiển thị lại!';
        }

        header("Location: adminSanPhamController.php?msg=" . urlencode($msg));
        exit;
    }
} catch (Exception $e) {
    if (isset($conn) && $conn->begin_transaction()) $conn->rollback(); // $conn->in_transaction
    $message = '<p class="form-message error">❌ ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// ---- Xử lý Thông báo  ----
if (isset($_GET['success'])) $message = '<p class="form-message success"> Thao tác thành công!</p>';
if (isset($_GET['deleted'])) $message = '<p class="form-message success"> Đã xóa sản phẩm!</p>';

//LẤY DỮ LIỆU CHO VIEW
$danhSachDanhMuc = $danhMucModel->getTatCaDanhMuc(); // Lấy danh mục cho modal

foreach ($danhSachSanPham as &$sp) {
    $sp['DanhSachAnh'] = $sanPhamModel->getAnhSanPham($sp['MaHH']);
}
$edit_item = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $edit_item = $sanPhamModel->getSanPhamTheoId($id_edit);
}

include_once __DIR__ . '/../views/quanLySanPham.php';
