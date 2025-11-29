<?php
session_start();
require_once '../config/database.php'; 
include "../views/components/header.php";
include "../views/components/navbar.php";

$products = [];
try {
    $sql = "SELECT p.PRODUCTID, p.PRO_NAME, p.PRO_PRICE, p.IMAGE, 
                   c.CAT_NAME, s.SHOP_NAME 
            FROM PRODUCTS p
            LEFT JOIN CATEGORIES c ON p.CAT_NAME = c.CAT_NAME  
            LEFT JOIN SELLERS s ON p.SELLERID = s.SELLERID
            ORDER BY p.PRODUCTID DESC 
            LIMIT 8";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Lỗi tải sản phẩm: " . $e->getMessage() . "</div>";
}
?>

<div class="container mt-5">
    
    <div class="p-5 text-center bg-light rounded-3 mb-5 shadow-sm border">
        <h1 class="text-primary fw-bold display-5">Chào mừng đến với E-commerce-php</h1>
        <p class="lead text-muted">Nền tảng thương mại điện tử "cây nhà lá vườn" xịn nhất!</p>
        <a href="shop.php" class="btn btn-primary btn-lg mt-3 px-5 rounded-pill">
            <i class="bi bi-cart-fill"></i> Mua sắm ngay
        </a>
    </div>

    <h3 class="mb-4 text-center fw-bold text-uppercase border-bottom pb-2" style="border-color: #e3f2fd !important;">
        Sản Phẩm Nổi Bật
    </h3>

    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm border-0 product-card">
                        <div class="position-relative">
                            <a href="/product-detail.php?id=<?= $product['PRODUCTID'] ?>">
                                <img src="<?= htmlspecialchars($product['IMAGE'] ?? '/images/product_sample.jpg') ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($product['PRO_NAME']) ?>" 
                                     style="height: 200px; object-fit: cover; background-color: #eee;"
                                     onerror="this.src='/images/product_sample.jpg'">
                            </a>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <small class="text-muted mb-1"><?= htmlspecialchars($product['CAT_NAME'] ?? 'Khác') ?></small>
                            
                            <h5 class="card-title text-truncate" title="<?= htmlspecialchars($product['PRO_NAME']) ?>">
                                <a href="/product-detail.php?id=<?= $product['PRODUCTID'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product['PRO_NAME']) ?>
                                </a>
                            </h5>
                            
                            <p class="card-text small text-secondary mb-2">
                                <i class="bi bi-shop"></i> <?= htmlspecialchars($product['SHOP_NAME'] ?? 'Shop ẩn danh') ?>
                            </p>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary fs-5">
                                    <?= number_format($product['PRO_PRICE']) ?> VNĐ
                                </span>
                                <a href="/cart.php?action=add&id=<?= $product['PRODUCTID'] ?>" class="btn btn-outline-primary btn-sm rounded-circle">
                                    <i class="bi bi-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="alert alert-warning">
                    <p class="text-muted fs-5 mb-0">Chưa có sản phẩm nào được bày bán.</p>
                </div>
                <a href="/seller/add-product.php" class="btn btn-outline-success mt-3">Đăng bán sản phẩm ngay</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-5 mb-5">
        <a href="shop.php" class="btn btn-outline-dark btn-lg px-5">Xem toàn bộ cửa hàng</a>
    </div>

</div>
<?php include "../views/components/footer.php"; ?>