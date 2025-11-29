<?php
session_start();
require_once '../../config/database.php'; 
include "../../views/components/header.php";
include "../../views/components/navbar.php";

// 1. Kiểm tra quyền Seller
if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    echo "<script>window.location.href='/login.php';</script>";
    exit;
}

$sellerId = $_SESSION['logged_in_user']['id'];
$message = "";

// 2. Xử lý Xóa (Gọi Procedure)
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['product_id'];
    try {
        // Gọi thủ tục DELETE_PRODUCTS đã tạo trong MySQL
        $sql = "CALL DELETE_PRODUCTS(:id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $message = "<div class='alert alert-success alert-dismissible fade show'>
                        <i class='bi bi-check-circle me-2'></i> Xóa sản phẩm thành công!
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show'>
                        <i class='bi bi-exclamation-triangle me-2'></i> Lỗi: " . $e->getMessage() . "
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    }
}

// 3. Lấy danh sách sản phẩm
$keyword = $_GET['keyword'] ?? '';
try {
    $sql = "SELECT p.PRODUCTID, p.PRO_NAME, p.PRO_PRICE, p.CAT_NAME, p.IMAGE, 
                   u.PRO_QUANTITY, u.PRO_STATUS
            FROM products p
            LEFT JOIN updates u ON p.PRODUCTID = u.PRODUCTID
            WHERE p.SELLERID = :sid";
    
    if ($keyword) {
        $sql .= " AND p.PRO_NAME LIKE :kw";
    }
    
    // Sắp xếp mới nhất lên đầu
    $sql .= " ORDER BY p.PRODUCTID DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sid', $sellerId);
    if ($keyword) {
        $kw = "%$keyword%";
        $stmt->bindParam(':kw', $kw);
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Lỗi tải dữ liệu: " . $e->getMessage() . "</div>";
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Quản lý sản phẩm</h2>
            <p class="text-muted small mb-0">Danh sách các sản phẩm đang bán của bạn</p>
        </div>
        <a href="add-product.php" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm mới
        </a>
    </div>

    <?= $message ?>

    <div class="card mb-4 border-0 shadow-sm bg-light">
        <div class="card-body p-3">
            <form method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0 ps-0" 
                           placeholder="Tìm kiếm theo tên sản phẩm..." 
                           value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <button type="submit" class="btn btn-primary px-4">Tìm</button>
                <?php if($keyword): ?>
                    <a href="products.php" class="btn btn-outline-secondary">Xóa lọc</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá bán</th>
                            <th class="text-center">Kho</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center pe-4" style="width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($p['IMAGE'] ?? '/images/product_sample.jpg') ?>" 
                                                 class="rounded border me-3"
                                                 width="48" height="48" 
                                                 style="object-fit:cover" 
                                                 alt="img"
                                                 onerror="this.src='/images/product_sample.jpg'">
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['PRO_NAME']) ?></div>
                                                <small class="text-muted">ID: <?= htmlspecialchars($p['PRODUCTID']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($p['CAT_NAME'] ?? 'Khác') ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        <?= number_format($p['PRO_PRICE']) ?> ₫
                                    </td>
                                    <td class="text-center">
                                        <?= htmlspecialchars($p['PRO_QUANTITY'] ?? 0) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($p['PRO_QUANTITY'] ?? 0) > 0): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Còn hàng</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Hết hàng</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="edit-product.php?id=<?= $p['PRODUCTID'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Chỉnh sửa">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            
                                            <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không? Hành động này không thể hoàn tác.');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="product_id" value="<?= $p['PRODUCTID'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Không tìm thấy sản phẩm nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "../../views/components/footer.php"; ?>