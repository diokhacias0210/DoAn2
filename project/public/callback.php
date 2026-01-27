<?php
session_start();
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../../includes/ketnoi.php'; // kết nối DB

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        die("❌ Lỗi xác thực Google: " . htmlspecialchars($token['error']));
    }

    $client->setAccessToken($token);

    $google_service = new Google_Service_Oauth2($client);
    $google_account = $google_service->userinfo->get();

    $email = $google_account->email;
    $name  = $google_account->name;

    // Kiểm tra user trong DB
    $stmt = $conn->prepare("SELECT IdTaiKhoan, TenTK, Email, Sdt, VaiTro FROM TaiKhoan WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $stmt_update = $conn->prepare("UPDATE TaiKhoan SET TenTK = ? WHERE IdTaiKhoan = ?");
        $stmt_update->bind_param("si", $name, $user['IdTaiKhoan']);
        $stmt_update->execute();

        // Lưu vào Session
        $_SESSION['IdTaiKhoan'] = $user['IdTaiKhoan'];
        $_SESSION['TenTK']      = $name; // Lấy biến $name từ Google gán luôn vào session
        $_SESSION['VaiTro']     = $user['VaiTro'];
        $_SESSION['vaitro']     = $user['VaiTro'] == 1 ? 'admin' : 'user';
    } else {
        // --- TRƯỜNG HỢP 2: TÀI KHOẢN MỚI (CHƯA CÓ TRONG DB) ---
        $role = 0;
        $sdt_mac_dinh = ""; // Mặc định số điện thoại là NULL vì Google không cấp

        // Insert đầy đủ các trường
        $stmt = $conn->prepare("INSERT INTO TaiKhoan (TenTK, Email, MatKhau, VaiTro, Sdt) VALUES (?, ?, '', ?, ?)");
        $stmt->bind_param("ssis", $name, $email, $role, $sdt_mac_dinh);

        if ($stmt->execute()) {
            $newUserId = $stmt->insert_id;
            $_SESSION['IdTaiKhoan'] = $newUserId;
            $_SESSION['TenTK']      = $name; // Lấy tên từ Google
            $_SESSION['VaiTro']     = $role;
        } else {
            die("Lỗi tạo tài khoản: " . $stmt->error);
        }
    }

    // ✅ Redirect về trang chủ
    header("Location: ../../controllers/trangChuController.php");
    exit;
} else {
    die("❌ Không nhận được mã xác thực từ Google.");
}
