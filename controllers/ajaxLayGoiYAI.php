<?php
session_start();
require_once '../includes/ketnoi.php';

$idCheck = isset($_SESSION['IdTaiKhoan']) ? (int)$_SESSION['IdTaiKhoan'] : 0;
$exclude = isset($_GET['exclude']) ? (int)$_GET['exclude'] : 0;

// Gọi API AI
$api_url = "http://127.0.0.1:5000/recommend?user_id=$idCheck&top_n=8";
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$response = curl_exec($ch);
curl_close($ch);

$goi_y_list = $response ? json_decode($response, true) : [];
$html = "";

if (!empty($goi_y_list) && !isset($goi_y_list['error'])) {
    $ids_string = implode(',', array_column($goi_y_list, 'id'));

    // Thêm điều kiện exclude nếu có
    $cond_exclude = ($exclude > 0) ? " AND hh.MaHH != $exclude" : "";

    $sql_ai = "SELECT hh.*, (SELECT URL FROM HinhAnh WHERE MaHH = hh.MaHH LIMIT 1) as Anh 
               FROM HangHoa hh 
               WHERE MaHH IN ($ids_string) AND IdNguoiBan != $idCheck $cond_exclude
               ORDER BY FIELD(MaHH, $ids_string)";
    $res_ai = $conn->query($sql_ai);

    $sanphams = [];
    if ($res_ai) {
        while ($row = $res_ai->fetch_assoc()) {
            $sanphams[$row['MaHH']] = $row;
        }
    }

    foreach ($goi_y_list as $item) {
        $sp = $sanphams[$item['id']] ?? null;
        if (!$sp) continue;

        if (isset($item['reason']) && $item['reason'] == 'Trending') {
            $badge = '<div class="badge-match" style="background:#fd7e14;"><i class="fa-solid fa-fire"></i> Đang thịnh hành</div>';
        } elseif (isset($item['reason']) && $item['reason'] == 'Gợi ý mới') {
            $badge = '<div class="badge-match" style="background:#28a745;"><i class="fa-solid fa-seedling"></i> Phù hợp với bạn</div>';
        } else {
            $badge = '<div class="badge-match">Phù hợp ' . $item['match'] . '%</div>';
        }
        $html .= veTheHtml($sp, $badge);
    }
}

// Nếu không có kết quả từ AI, lấy Trending làm dự phòng
if (empty($html)) {
    $cond_exclude = ($exclude > 0) ? " AND hh.MaHH != $exclude" : "";
    $sql_fallback = "SELECT hh.*, (SELECT URL FROM HinhAnh ha WHERE ha.MaHH = hh.MaHH LIMIT 1) as Anh, 
                    COALESCE((SELECT SUM(Diem) FROM HanhVi_AI WHERE MaHH = hh.MaHH), 0) as TongDiem
                    FROM HangHoa hh 
                    WHERE hh.TrangThaiDuyet = 'DaDuyet' AND hh.SoLuongHH > 0 AND hh.IdNguoiBan != $idCheck $cond_exclude
                    ORDER BY TongDiem DESC, hh.NgayThem DESC LIMIT 8";
    $result_new = $conn->query($sql_fallback);
    while ($sp = $result_new->fetch_assoc()) {
        $badge = '<div class="badge-match" style="background:#fd7e14;"><i class="fa-solid fa-fire"></i> Đang thịnh hành</div>';
        $html .= veTheHtml($sp, $badge);
    }
}

echo $html;

function veTheHtml($sp, $badge)
{
    $gia = number_format($sp['Gia'], 0, ',', '.');
    $anh = (strpos($sp['Anh'], 'http') === 0) ? $sp['Anh'] : '../' . $sp['Anh'];
    $rating = number_format($sp['Rating'] ?? 0, 1);
    $ten = htmlspecialchars($sp['TenHH']);
    return "
    <a href='chiTietSanPhamController.php?id={$sp['MaHH']}' class='product-link'>
        <div class='product-item'>
            $badge
            <div class='product-item-top'><img src='$anh' style='height: 180px; width: 100%; object-fit: cover; border-radius: 8px 8px 0 0;'><div class='tieude-sanpham'>$ten</div></div>
            <div class='product-item-bottom'><div class='gia-rating'><div class='rating'><i class='fa-solid fa-star'></i><span>$rating</span></div><div class='gia-san-pham'><span class='gia-giam'>$gia đ</span></div></div></div>
        </div>
    </a>";
}
