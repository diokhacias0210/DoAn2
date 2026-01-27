<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../../assets/css/admin_sidebar.css" rel="stylesheet">
    <link href="../../assets/css/color.css" rel="stylesheet">
    <link href="../../assets/css/navbar.css" rel="stylesheet">
    <style>
        .sidebar-link .danhmuc.active {
            background: #5a6c7d;
        }

        #alert-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
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
            <div class="qlnd-container">
                <h1>👥 Quản lý người dùng</h1>



                <form method="GET" action="adminNguoiDungController.php" class="filter-bar search-bar" style="display:flex; gap:8px;">
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." autocomplete="off"
                        value="<?= htmlspecialchars($keyword ?? '') ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <table class="qlnd-table qldh-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Vai trò</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($danhSachNguoiDung)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Không tìm thấy người dùng nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($danhSachNguoiDung as $r): ?>
                                <tr>
                                    <td><?= $r['IdTaiKhoan'] ?></td>
                                    <td><strong><?= htmlspecialchars($r['TenTK']) ?></strong></td>
                                    <td><?= htmlspecialchars($r['Email']) ?></td>
                                    <td><?= htmlspecialchars($r['Sdt']) ?></td>
                                    <td>
                                        <?php if ($r['VaiTro'] == 1): ?>
                                            <span style="background:#e3f2fd; color:#1565c0; padding:4px 12px; border-radius:4px; font-weight:600; font-size:0.85rem;">👑 Admin</span>
                                        <?php else: ?>
                                            <span style="background:#f3e5f5; color:#6a1b9a; padding:4px 12px; border-radius:4px; font-weight:600; font-size:0.85rem;">👤 User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar trên mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'sidebar-toggle';
            toggleBtn.innerHTML = '☰';
            toggleBtn.onclick = function() {
                sidebar.classList.toggle('active');
            };
            document.body.appendChild(toggleBtn);
        });
    </script>
</body>

</html>