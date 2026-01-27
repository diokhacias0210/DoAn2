<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục</title>
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #alert-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 90%;
        }

        .alert {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            opacity: 1;
            transition: opacity 0.5s ease;
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
            <div class="qlsp-container">
                <h1>📁 Quản lý danh mục</h1>

                <form method="GET" action="adminDanhMucController.php" class="filter-bar search-bar" style="display:flex; gap:8px; margin-bottom: 20px;  ">
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." autocomplete="off"
                        value="<?= htmlspecialchars($keyword ?? '') ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <div id="alert-container">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                </div>

                <form method="POST" action="adminDanhMucController.php" class="qlsp-form">
                    <h3>Thêm danh mục mới</h3>
                    <input type="text" name="ten_danhmuc" placeholder="Tên danh mục..." required autocomplete="off">
                    <button type="submit" name="action" value="add">Thêm</button>
                </form>

                <table class="qlsp-table">
                    <thead>
                        <tr>
                            <th>Mã DM</th>
                            <th>Tên danh mục</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachDanhMuc as $dm): ?>
                            <tr>
                                <td><?= $dm['MaDM'] ?></td>
                                <td>
                                    <form method="POST" action="adminDanhMucController.php" style="display:flex; gap:8px;">
                                        <input type="hidden" name="capnhat_id" value="<?= $dm['MaDM'] ?>">
                                        <input type="text" name="capnhat_ten" value="<?= htmlspecialchars($dm['TenDM']) ?>" required>
                                        <button type="submit" name="action" value="update" class="qlsp-btn" style="background:#3498db; color:white;">Lưu</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="adminDanhMucController.php?xoa=<?= $dm['MaDM'] ?>"
                                        onclick="return confirm('Bạn chắc chắn muốn xóa? Tất cả sản phẩm trong danh mục này sẽ được chuyển sang Chưa phân loại.')"
                                        class="qlsp-btn" style="background:#e74c3c; color:white;">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertContainer = document.getElementById('alert-container');
            if (alertContainer && alertContainer.children.length > 0) {
                const alerts = alertContainer.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.classList.add('hide'); // Mờ dần
                        setTimeout(() => alert.remove(), 500); // Xóa hẳn
                    }, 3000); // Sau 3 giây
                });
            }
        });
    </script>
</body>

</html>