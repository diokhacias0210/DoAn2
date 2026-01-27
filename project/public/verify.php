<?php
// public/verify.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/db.php';

header('Content-Type: application/json');

// đọc JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$id_token = $data['id_token'] ?? null;
if (!$id_token) {
    http_response_code(400);
    echo json_encode(['success'=>false, 'error'=>'No id_token provided']);
    exit;
}

$client = new Google_Client(['client_id' => GOOGLE_CLIENT_ID]);

try {
    $payload = $client->verifyIdToken($id_token);
} catch (Exception $e) {
    $payload = false;
}

if (!$payload) {
    http_response_code(401);
    echo json_encode(['success'=>false, 'error'=>'Invalid ID token']);
    exit;
}

// Optional: extra checks
if (!isset($payload['aud']) || $payload['aud'] !== GOOGLE_CLIENT_ID) {
    http_response_code(401);
    echo json_encode(['success'=>false, 'error'=>'Token audience mismatch']);
    exit;
}

// Lấy thông tin user từ payload
$google_id = $payload['sub'] ?? null; // unique Google user id
$email     = $payload['email'] ?? '';
$name      = $payload['name'] ?? '';
$picture   = $payload['picture'] ?? '';
$email_verified = $payload['email_verified'] ?? false;

// nếu bạn bắt buộc email phải verified:
if (!$email_verified) {
    // có thể reject hoặc vẫn chấp nhận tuỳ app
    // echo json_encode(['success'=>false,'error'=>'Email not verified']); exit;
}

// Upsert user vào DB
$pdo = getPDO();
$now = date('Y-m-d H:i:s');

try {
    $pdo->beginTransaction();

    // tìm user theo google_id
    $stmt = $pdo->prepare("SELECT id FROM users WHERE google_id = ?");
    $stmt->execute([$google_id]);
    $user = $stmt->fetch();

    if ($user) {
        // update info + last_login
        $stmt = $pdo->prepare("UPDATE users SET email = ?, name = ?, picture = ?, updated_at = ?, last_login = ? WHERE id = ?");
        $stmt->execute([$email, $name, $picture, $now, $now, $user['id']]);
        $user_id = $user['id'];
    } else {
        // nếu có user cùng email (đăng ký trước bằng khác), gán google_id vào account đó
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $exists = $stmt->fetch();
        if ($exists) {
            $stmt = $pdo->prepare("UPDATE users SET google_id = ?, name = ?, picture = ?, updated_at = ?, last_login = ? WHERE id = ?");
            $stmt->execute([$google_id, $name, $picture, $now, $now, $exists['id']]);
            $user_id = $exists['id'];
        } else {
            // insert mới
            $stmt = $pdo->prepare("INSERT INTO users (google_id, email, name, picture, created_at, updated_at, last_login) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$google_id, $email, $name, $picture, $now, $now, $now]);
            $user_id = $pdo->lastInsertId();
        }
    }

    $pdo->commit();

    // tạo session
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $user_id,
        'google_id' => $google_id,
        'email' => $email,
        'name' => $name,
        'picture' => $picture
    ];

    echo json_encode(['success' => true, 'redirect' => '/dashboard.php']);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success'=>false, 'error' => $e->getMessage()]);
    exit;
}
?>