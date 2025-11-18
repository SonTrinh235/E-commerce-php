<div class="container mt-5">
    <h2 class="mb-4"><?= $title ?></h2>

    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <?php $product = $p; ?>
                <?php include __DIR__ . "/product-card.php"; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Chưa có sản phẩm.</p>
        <?php endif; ?>
    </div>
</div>
