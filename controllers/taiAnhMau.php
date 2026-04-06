<?php
set_time_limit(0);

// ✅ File này nằm ở thư mục gốc C:\DoAn2\
// Ảnh mock sẽ được lưu vào: C:\DoAn2\assets\images\mock\
$thuMucLuu = __DIR__ . '/../assets/images/mock/';

// Nếu chưa có thư mục này thì PHP sẽ tự động tạo
if (!is_dir($thuMucLuu)) {
    mkdir($thuMucLuu, 0777, true);
}

// 60 Từ khóa tiếng Anh tương ứng chính xác với 60 sản phẩm
$tuKhoaAnh = [
    // Tech
    "iphone", "macbook", "earbuds", "computer_mouse", "mechanical_keyboard", "computer_monitor", "ipad", "smartwatch", "powerbank", "phone_case", 
    // Thời trang nam
    "leather_jacket", "nike_shoes", "mens_shirt", "jeans", "mens_watch", "backpack", "leather_belt", "leather_shoes", "tshirt", "sunglasses", 
    // Thời trang nữ
    "handbag", "dress", "high_heels", "blazer", "lipstick", "cardigan", "skirt", "bucket_hat", "womens_watch", "necklace", 
    // Gia dụng
    "microwave", "air_fryer", "office_chair", "desk", "blender", "desk_lamp", "induction_stove", "cooking_pot", "wardrobe", "bookshelf", 
    // Thể thao & Xe
    "bicycle", "helmet", "badminton", "yoga_mat", "soccer_shoes", "basketball", "motorcycle", "gym_gloves", "skateboard", "jump_rope", 
    // Sở thích
    "book", "manga", "guitar", "film_camera", "anime_figure", "chess", "programming_book", "rubik", "vinyl_record", "oil_painting" 
];

echo "<h2>Đang tự động tải 60 ảnh mẫu. Quá trình này mất khoảng 10 - 20 giây...</h2>";
echo "Vui lòng không đóng trình duyệt lúc này.<br><br>";

$count = 0;

foreach ($tuKhoaAnh as $index => $keyword) {
    $url = "https://loremflickr.com/400/400/" . $keyword . "?lock=" . rand(1, 10000);
    $duLieuAnh = @file_get_contents($url);

    if ($duLieuAnh !== false) {
        // Tên file: 0_iphone.jpg, 1_macbook.jpg, ...
        $tenFile = $index . "_" . $keyword . ".jpg";
        file_put_contents($thuMucLuu . $tenFile, $duLieuAnh);
        $count++;
    }
}

echo "<h3 style='color:green;'>✔️ Đã tải thành công $count/60 ảnh vào thư mục 'assets/images/mock/'!</h3>";
echo "<p>Bây giờ bạn có thể chạy <a href='taoDuLieuMau.php'>taoDuLieuMau.php</a> để tạo dữ liệu mẫu.</p>";
?>
