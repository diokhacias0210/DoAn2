<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Mã Giảm Giá</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary {
            background: #007bff;
            color: #fff;
        }

        .btn-success {
            background: #28a745;
            color: #fff;
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        .modals-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.25s ease-in;
        }

        .modals {
            background: #fff;
            border-radius: 12px;
            width: 600px;
            max-width: 95%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modals.show {
            transform: scale(1);
            opacity: 1;
        }

        .modals-header {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: #fff;
            padding: 12px 16px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modals-close {
            font-size: 1.5rem;
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .modals-close:hover {
            transform: scale(1.2);
        }

        .modals-body {
            padding: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modals-body label {
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .modals-body input,
        .modals-body select,
        .modals-body textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .checkbox-group {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px;
            max-height: 130px;
            overflow-y: auto;
            background: #fafafa;
        }

        .checkbox-group div {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 6px;
            border-radius: 5px;
            transition: background 0.2s;
        }

        .checkbox-group div:hover {
            background: #f1f1f1;
        }

        .checkbox-group input[type="checkbox"] {
            accent-color: #28a745;
            /* màu xanh lá cho khớp theme */
            transform: scale(1.1);
        }

        .modals-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 12px 20px;
            border-top: 1px solid #eee;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #alert-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        .alert-success {
            background-color: #28a745;
        }

        .alert-danger {
            background-color: #dc3545;
        }

        .alert.hide {
            opacity: 0;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="admin-container">
                <h1>Quản lý Mã Giảm Giá</h1>

                <!-- Form tìm kiếm -->
                <form method="GET" action="adminMaGiamGiaController.php" class="search-bar" style="display:flex; gap:8px; margin-bottom: 20px; ">
                    <input type="text"
                        class="form-control"
                        name="search"
                        placeholder="Tìm kiếm theo mã giảm giá hoặc mô tả..."
                        value="<?= htmlspecialchars($keyword ?? '') ?>"
                        autocomplete="off">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>

                </form>

                <div id="alert-container">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button class="btn btn-success" onclick="openmodals()">+ Thêm Mã Giảm Giá</button>

                <table class="qldh-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã giảm giá</th>
                            <th>Giá trị</th>
                            <th>Số lượng</th>
                            <th>Loại áp dụng</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                            <th>Danh mục áp dụng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($danhSachMaGiamGia)): ?>
                            <?php foreach ($danhSachMaGiamGia as $row): ?>
                                <tr>
                                    <td><?= $row['MaGG'] ?></td>
                                    <td><?= htmlspecialchars($row['Code']) ?></td>
                                    <td><?= $row['GiaTri'] ?>%</td>
                                    <td><?= $row['SoLuong'] ?></td>
                                    <td><?= htmlspecialchars($row['LoaiApDung']) ?></td>
                                    <td><?= htmlspecialchars($row['TrangThai']) ?></td>
                                    <td>
                                        <?= $row['NgayBatDau'] ? date('d/m/Y H:i', strtotime($row['NgayBatDau'])) : '' ?>
                                        -
                                        <?= $row['NgayKetThuc'] ? date('d/m/Y H:i', strtotime($row['NgayKetThuc'])) : 'Không giới hạn' ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['DanhMucApDung'] ?? 'không áp dụng') ?></td>
                                    <td>
                                        <a href="adminMaGiamGiaController.php?edit=<?= $row['MaGG'] ?>" class="qlsp-btn btn btn-warning">Sửa</a>
                                        <a href="adminMaGiamGiaController.php?delete=<?= $row['MaGG'] ?>" class="qlsp-btn btn btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa mã này?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">Chưa có mã giảm giá nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modals-overlay" id="modals">
        <div class="modals">
            <div class="modals-header">
                <h3 id="modals-title"><?= isset($edit_item) ? 'Sửa' : 'Thêm' ?> Mã Giảm Giá</h3>
                <button type="button" class="modals-close" onclick="closemodals()">&times;</button>
            </div>
            <form method="POST" action="adminMaGiamGiaController.php" class="modals-form">
                <input type="hidden" name="MaGG" value="<?= $edit_item['MaGG'] ?? 0 ?>">

                <div class="modals-body">
                    <div class="form-grid">
                        <div>
                            <label>Mã giảm giá</label>
                            <input type="text" name="Code" required value="<?= htmlspecialchars($edit_item['Code'] ?? '') ?>">
                        </div>

                        <div>
                            <label>Giá trị (%)</label>
                            <input type="number" name="GiaTri" min="0" max="100" step="1" required value="<?= htmlspecialchars($edit_item['GiaTri'] ?? '') ?>">
                        </div>

                        <div>
                            <label>Số lượng</label>
                            <input type="number" name="SoLuong" required value="<?= htmlspecialchars($edit_item['SoLuong'] ?? '') ?>">
                        </div>

                        <div>
                            <label>Loại áp dụng</label>
                            <select name="LoaiApDung">
                                <option value="DongLoat" <?= ($edit_item['LoaiApDung'] ?? '') == 'DongLoat' ? 'selected' : '' ?>>Đồng loạt</option>
                                <option value="MaCode" <?= ($edit_item['LoaiApDung'] ?? '') == 'MaCode' ? 'selected' : '' ?>>Theo mã riêng</option>
                            </select>
                        </div>

                        <div>
                            <label>Ngày bắt đầu</label>
                            <input type="datetime-local" name="NgayBatDau" value="<?= isset($edit_item['NgayBatDau']) ? date('Y-m-d\TH:i', strtotime($edit_item['NgayBatDau'])) : '' ?>">
                        </div>

                        <div>
                            <label>Ngày kết thúc</label>
                            <input type="datetime-local" name="NgayKetThuc" value="<?= isset($edit_item['NgayKetThuc']) ? date('Y-m-d\TH:i', strtotime($edit_item['NgayKetThuc'])) : '' ?>">
                        </div>
                    </div>
                    <div>
                        <label>Mô tả</label>
                        <textarea name="MoTa" rows="3"><?= htmlspecialchars($edit_item['MoTa'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label>Trạng thái</label>
                        <select name="TrangThai">
                            <option value="Hoạt động" <?= ($edit_item['TrangThai'] ?? '') == 'Hoạt động' ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="Hết hạn" <?= ($edit_item['TrangThai'] ?? '') == 'Hết hạn' ? 'selected' : '' ?>>Hết hạn</option>
                            <option value="Ngừng" <?= ($edit_item['TrangThai'] ?? '') == 'Ngừng' ? 'selected' : '' ?>>Ngừng</option>
                        </select>
                    </div>

                </div>

                <div>
                    <label>Áp dụng cho danh mục (để trống nếu áp dụng cho tất cả)</label>
                    <div class="checkbox-group" required>
                        <?php
                        foreach ($danhSachDanhMuc as $dm):
                            // Biến $edit_danhmuc_ids do Controller cung cấp
                            $checked = in_array($dm['MaDM'], $edit_danhmuc_ids ?? []) ? 'checked' : '';
                        ?>
                            <div>
                                <input type="checkbox" name="MaDM[]" value="<?= $dm['MaDM'] ?>" id="dm_<?= $dm['MaDM'] ?>" <?= $checked ?>>
                                <label for="dm_<?= $dm['MaDM'] ?>"><?= htmlspecialchars($dm['TenDM']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="modals-footer">
                    <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
                    <button type="button" class="btn btn-danger" onclick="closemodals()">Đóng</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        const modals = document.getElementById('modals');
        const modalsForm = document.querySelector('.modals-form');
        const modalsTitle = document.getElementById('modals-title');
        const modalsMaGGInput = document.querySelector('input[name="MaGG"]');
        const modalsBox = modals.querySelector('.modals');

        function openmodals() {
            // Nếu không phải chế độ sửa (biến $edit_item không tồn tại), thì reset form
            <?php if (!isset($edit_item)): ?>
                console.log("Chế độ Thêm mới -> Reset form");
                modalsForm.reset();
                modalsTitle.innerText = 'Thêm Mã Giảm Giá';
                modalsMaGGInput.value = 0;
                const checkboxes = modalsForm.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = false);
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

                // SỬA URL: Trỏ về controller
                const url = new URL(window.location.href);
                url.pathname = url.pathname.replace('quanLyMaGiamGia.php', 'adminMaGiamGiaController.php');
                url.searchParams.delete('edit');
                window.history.pushState({}, '', url);

            }, 200);
        }

        // Tự động mở modal nếu đang ở chế độ Sửa
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
        //ẩn thông báo
        document.addEventListener('DOMContentLoaded', function() {
            const alertContainer = document.getElementById('alert-container');
            if (alertContainer && alertContainer.children.length > 0) {
                const alerts = alertContainer.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.classList.add('hide');
                        setTimeout(() => alert.remove(), 500);
                    }, 3000);
                });
            }
        });
    </script>
</body>

</html>