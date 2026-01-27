<?php
// test_autoload.php

// Nạp file autoload của Composer
require_once __DIR__ . '/vendor/autoload.php';

if (class_exists('Google_Client')) {
    echo "✅ Autoload OK: Class Google_Client đã load thành công!";
    $client = new Google_Client();
    echo "<br>Version: " . Google_Client::LIBVER;
} else {
    echo "❌ Autoload lỗi: Không tìm thấy class Google_Client";
}
?>