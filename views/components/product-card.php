<?php
$name = $product['PRO_NAME'] ?? 'No name';
$desc = $product['PRO_DESCRIPTION'] ?? 'No description';
$price = $product['PRO_PRICE'] ?? 0;
$category = $product['CAT_NAME'] ?? 'Unknown';
$seller = $product['SELLERID'] ?? 'Unknown';
$image = $product['IMAGE'] ?? '/images/product_sample.jpg';
$id = $product['PRODUCTID'] ?? '';
?>

<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
    <div class="card h-100">
        <img src="<?= htmlspecialchars($image) ?>" class="card-img-top" alt="Product Image">

        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($name) ?></h5>

            <p class="card-text">
                <?= htmlspecialchars($desc) ?>
            </p>

            <p class="card-text">
                <strong>Category:</strong> <?= htmlspecialchars($category) ?>
            </p>

            <p class="card-text">
                <strong>Price:</strong> $<?= number_format($price, 2) ?>
            </p>

            <p class="card-text">
                <strong>Seller ID:</strong> <?= htmlspecialchars($seller) ?>
            </p>

            <a href="/product-detail.php?id=<?= urlencode($id) ?>" class="btn btn-primary w-100 mb-2">
                View
            </a>
        </div>
    </div>
</div>
