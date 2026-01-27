<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý đánh giá & bình luận</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .qldh-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .qldh-table th {
            background: #4CAF50;
            color: white;
            padding: 15px;
            text-align: left;
        }

        .qldh-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .qldh-table tr:hover {
            background: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-action {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-hide {
            background: #ffc107;
            color: black;
        }

        .btn-show {
            background: #28a745;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .btn-hide:hover {
            background: #e0a800;
        }

        .btn-show:hover {
            background: #218838;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>

        <main class="main-content">
            <div class="qldh-container">
                <?php if (!isset($maHH)): ?>
                    <h1>Danh sách sản phẩm có đánh giá / bình luận</h1>

                    <form method="GET" action="adminDanhGiaController.php" class="filter-bar search-bar" style="display:flex; gap:8px; margin-bottom: 20px;  ">
                        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." autocomplete="off"
                            value="<?= htmlspecialchars($keyword ?? '') ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <table class="qldh-table">
                        <thead>
                            <tr>
                                <th>Mã sản phẩm</th>
                                <th>Tên sản phẩm</th>
                                <th>Số lượt đánh giá</th>
                                <th>Số bình luận</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dsSanPham)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Chưa có sản phẩm nào được đánh giá</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($dsSanPham as $sp): ?>
                                    <tr>
                                        <td><?= $sp['MaHH'] ?></td>
                                        <td><?= htmlspecialchars($sp['TenHH']) ?></td>
                                        <td><?= $sp['SoDanhGia'] ?></td>
                                        <td><?= $sp['SoBinhLuan'] ?></td>
                                        <td>
                                            <a href="adminDanhGiaController.php?maHH=<?= $sp['MaHH'] ?>" class="btn-action btn-show">Xem chi tiết</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <h1>Chi tiết đánh giá & bình luận cho sản phẩm #<?= $maHH ?></h1>
                    <a href="adminDanhGiaController.php" class="btn-action btn-show">← Quay lại danh sách</a>

                    <table class="qldh-table">
                        <thead>
                            <tr>
                                <th>Tên tài khoản</th>
                                <th>Số sao</th>
                                <th>Nội dung</th>
                                <th>Ngày bình luận</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dsDanhGia)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có đánh giá hoặc bình luận nào</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($dsDanhGia as $dg): ?>
                                    <tr data-mabl="<?= $dg['MaBL'] ?>">
                                        <td><?= htmlspecialchars($dg['TenTK']) ?></td>
                                        <td><?= $dg['SoSao'] ?? '-' ?></td>
                                        <td><?= htmlspecialchars($dg['NoiDung'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($dg['NgayBL'] ?? '') ?></td>
                                        <td class="trang-thai"><?= $dg['TrangThai'] ?? 'Ẩn' ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-delete" onclick="capNhatBinhLuan(<?= $dg['MaBL'] ?>, 'xoa')">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                                <?php if (($dg['TrangThai'] ?? '') === 'Hiển thị'): ?>
                                                    <button class="btn-action btn-hide" onclick="capNhatBinhLuan(<?= $dg['MaBL'] ?>, 'an')">
                                                        <i class="fas fa-eye-slash"></i> Ẩn
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn-action btn-show" onclick="capNhatBinhLuan(<?= $dg['MaBL'] ?>, 'hien')">
                                                        <i class="fas fa-eye"></i> Hiện
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function capNhatBinhLuan(maBL, action) {
            $.ajax({
                url: "../../controllers/hangDongBinhLuanController.php",
                type: "POST",
                data: {
                    maBL: maBL,
                    action: action
                },
                dataType: "json",
                success: function(result) {
                    if (!result.success) return console.error(result.error);
                    const row = $(`tr[data-mabl='${maBL}']`);
                    if (!row.length) return;
                    if (result.deleted) return row.remove();

                    // Code cập nhật nút Ẩn/Hiện
                    const statusCell = row.find(".trang-thai");
                    const btnCell = row.find(".action-buttons");
                    statusCell.text(result.newStatus);
                    if (result.newStatus === "Ẩn") {
                        btnCell.html(`
                            <button class="btn-action btn-delete" onclick="capNhatBinhLuan(${maBL}, 'xoa')"><i class="fas fa-trash"></i> Xóa</button>
                            <button class="btn-action btn-show" onclick="capNhatBinhLuan(${maBL}, 'hien')"><i class="fas fa-eye"></i> Hiện</button>
                        `);
                    } else {
                        btnCell.html(`
                            <button class="btn-action btn-delete" onclick="capNhatBinhLuan(${maBL}, 'xoa')"><i class="fas fa-trash"></i> Xóa</button>
                            <button class="btn-action btn-hide" onclick="capNhatBinhLuan(${maBL}, 'an')"><i class="fas fa-eye-slash"></i> Ẩn</button>
                        `);
                    }
                },
                error: (xhr, s, e) => console.error("AJAX lỗi:", e)
            });
        }
    </script>
</body>

</html>