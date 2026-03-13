<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Banner Quảng Cáo</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .qlbn-table { width: 100%; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .qlbn-table th { background: #4CAF50; color: white; padding: 15px; text-align: left; }
        .qlbn-table td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        .btn-action { padding: 6px 12px; border-radius: 5px; border: none; color: white; font-size: 13px; cursor: pointer; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-add { background: #4CAF50; margin-bottom: 20px; }
        .btn-delete { background: #e74c3c; }
        .btn-edit { background: #3498db; }
        .banner-img { width: 180px; height: 80px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-hien { background: #d4edda; color: #155724; }
        .status-an { background: #f8d7da; color: #721c24; }

        /* Style Modal */
        .modals-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); display: none; justify-content: center;
            align-items: center; z-index: 1000; animation: fadeIn 0.25s ease-in;
        }
        .modals {
            background: #fff; border-radius: 12px; width: 600px; max-width: 95%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); transform: scale(0.95);
            opacity: 0; transition: all 0.3s ease;
        }
        .modals.show { transform: scale(1); opacity: 1; }
        .modals-header {
            background: linear-gradient(135deg, #2ecc71, #27ae60); color: #fff;
            padding: 12px 16px; border-radius: 12px 12px 0 0; display: flex;
            justify-content: space-between; align-items: center;
        }
        .modals-close { font-size: 1.5rem; background: none; border: none; color: #fff; cursor: pointer; transition: transform 0.2s; }
        .modals-close:hover { transform: scale(1.2); }
        .modals-body { padding: 20px; max-height: 60vh; overflow-y: auto; }
        .modals-body label { font-weight: 600; margin-bottom: 4px; display: block; }
        .modals-body input, .modals-body select {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;
            margin-bottom: 15px; box-sizing: border-box;
        }
        .modals-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 12px 20px; border-top: 1px solid #eee; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include '../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="m-0" style="color: #333; font-size: 24px;">📦 Quản lý Banner</h1>
                    <button onclick="openmodals()" class="btn-action btn-add">
                        <i class="fas fa-plus"></i> Thêm Banner mới
                    </button>
                </div>

                <div class="qldh-table-wrapper">
                    <table class="qlbn-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Tiêu đề</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($banners)): ?>
                                <?php foreach ($banners as $b): ?>
                                    <tr>
                                        <td>#<?= $b['MaBanner'] ?></td>
                                        <td>
                                            <img src="../../assets/images/banners/<?= $b['HinhAnh'] ?>" class="banner-img" alt="banner">
                                        </td>
                                        <td><strong><?= htmlspecialchars($b['TieuDe']) ?></strong></td>
                                        <td>
                                            <span class="status-badge <?= $b['TrangThai'] == 'HienThi' ? 'status-hien' : 'status-an' ?>">
                                                <?= $b['TrangThai'] == 'HienThi' ? 'Đang hiện' : 'Đang ẩn' ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($b['NgayTao'])) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="adminBannerController.php?edit=<?= $b['MaBanner'] ?>" class="btn-action btn-edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="adminBannerController.php?delete=<?= $b['MaBanner'] ?>" 
                                                   class="btn-action btn-delete" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa banner này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có banner nào được tạo.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modals-overlay" id="modals">
        <div class="modals">
            <div class="modals-header">
                <h3 id="modals-title"><?= isset($edit_item) ? 'Sửa Banner' : 'Thêm Banner Mới' ?></h3>
                <button type="button" class="modals-close" onclick="closemodals()">&times;</button>
            </div>
            
            <form method="POST" action="adminBannerController.php" class="modals-form" enctype="multipart/form-data">
                <input type="hidden" name="maBanner" value="<?= $edit_item['MaBanner'] ?? '' ?>">
                
                <div class="modals-body">
                    <div>
                        <label>Tiêu đề</label>
                        <input type="text" name="tieuDe" required placeholder="Nhập tiêu đề banner" 
                               value="<?= isset($edit_item) ? htmlspecialchars($edit_item['TieuDe']) : '' ?>">
                    </div>
                    <div>
                        <label>Hình ảnh</label>
                        <input type="file" name="hinhAnh" accept="image/*" <?= isset($edit_item) ? '' : 'required' ?>>
                        
                        <?php if(isset($edit_item) && !empty($edit_item['HinhAnh'])): ?>
                            <p style="font-size: 13px; margin-top: 5px; color: #666;">
                                <i>Ảnh hiện tại đang dùng: <?= htmlspecialchars($edit_item['HinhAnh']) ?></i>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label>Trạng thái</label>
                        <select name="trangThai">
                            <option value="HienThi" <?= (isset($edit_item) && $edit_item['TrangThai'] == 'HienThi') ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="An" <?= (isset($edit_item) && $edit_item['TrangThai'] == 'An') ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>
                <div class="modals-footer">
                    <button type="submit" class="btn btn-action btn-add" style="margin-bottom: 0;">💾 Lưu thay đổi</button>
                    <button type="button" class="btn btn-action btn-delete" onclick="closemodals()">Đóng</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const modals = document.getElementById('modals');
        const modalsBox = modals.querySelector('.modals');
        const modalsForm = document.querySelector('.modals-form');

        function openmodals() {
            // Nếu MỞ THÊM MỚI (không có biến $edit_item) thì xóa form sạch sẽ
            <?php if (!isset($edit_item)): ?>
                modalsForm.reset(); 
            <?php endif; ?>
            
            modals.style.display = 'flex';
            setTimeout(() => {
                modalsBox.classList.add('show');
            }, 10);
        }

        function closemodals() {
            modalsBox.classList.remove('show');
            setTimeout(() => {
                modals.style.display = 'none';
                
                // Trỏ URL về lại bình thường (xóa ?edit=) khi tắt modal
                const url = new URL(window.location.href);
                if (url.searchParams.has('edit')) {
                    url.searchParams.delete('edit');
                    window.history.pushState({}, '', url);
                    location.reload(); // Reset lại toàn bộ trạng thái về Thêm mới
                }
            }, 200);
        }

        // TỰ ĐỘNG MỞ MODAL NẾU ĐANG CÓ BIẾN SỬA ($edit_item)
        <?php if (isset($edit_item)): ?>
            window.addEventListener('load', () => {
                openmodals();
            });
        <?php endif; ?>

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modals.style.display === 'flex') {
                closemodals();
            }
        });
    </script>
</body>
</html>