<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "HeThongQuanLyDoCu";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // Trả JSON thay vì die text
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối database'
    ]);
    exit;
}

$conn->set_charset("utf8mb4");
