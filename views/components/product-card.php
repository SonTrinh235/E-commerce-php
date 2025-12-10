<?php
$qty = $product['QUANTITY'] ?? 0;
$isOutOfStock = ($qty <= 0);
?>

<div class="col">
    <div class="card h-100 shadow-sm border-0 product-card">
        
        <div class="position-relative overflow-hidden">
            <a href="/product-detail.php?id=<?= $product['PRODUCTID'] ?>" 
               class="<?= $isOutOfStock ? 'opacity-50' : '' ?>">
                <img src="<?= htmlspecialchars($product['IMAGE'] ?? '/images/product_sample.jpg') ?>" 
                     class="card-img-top" 
                     alt="<?= htmlspecialchars($product['PRO_NAME']) ?>"
                     style="height: 250px; object-fit: cover;">
            </a>

            <?php if ($isOutOfStock): ?>
                <div class="position-absolute top-50 start-50 translate-middle badge bg-dark text-white px-3 py-2 fs-6 shadow">
                    HẾT HÀNG
                </div>
            <?php endif; ?>
        </div>

        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-truncate">
                <a href="/product-detail.php?id=<?= $product['PRODUCTID'] ?>" class="text-decoration-none text-dark stretched-link">
                    <?= htmlspecialchars($product['PRO_NAME']) ?>
                </a>
            </h5>
            <p class="card-text fw-bold text-primary fs-5 mb-auto">
                <?= number_format($product['PRO_PRICE']) ?> ₫
            </p>
        </div>

        <div class="card-footer bg-white border-top-0 pb-3">
            <?php if ($isOutOfStock): ?>
                <button class="btn btn-secondary w-100" disabled>
                    <i class="bi bi-x-circle me-1"></i> Tạm hết hàng
                </button>
            <?php else: ?>
                <a href="/cart.php?action=add&id=<?= $product['PRODUCTID'] ?>" 
                   class="btn btn-outline-primary w-100 hover-shadow">
                    <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>