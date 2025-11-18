<?php
include "components/header.php";
include "components/navbar.php";

// Lấy id sản phẩm từ GET
$productId = isset($_GET['id']) ? $_GET['id'] : '';

// Đọc dữ liệu từ JSON
$jsonPath = __DIR__ . '/../data/products.json';
$products = [];

if (file_exists($jsonPath)) {
    $jsonData = file_get_contents($jsonPath);
    $products = json_decode($jsonData, true);
}

// Tìm sản phẩm theo ID
$product = null;
if ($products && $productId) {
    foreach ($products as $p) {
        if ($p['PRODUCTID'] === $productId) {
            $product = $p;
            break;
        }
    }
}

if (!$product) {
    echo "<div class='container mt-5'><p>Product not found.</p></div>";
    include "components/footer.php";
    exit;
}
?>

<div class="container mt-5">
    <div class="row">

        <div class="col-md-6">
            <?php
            // Nếu JSON có thêm hình ảnh, dùng $product['IMAGE']; tạm thời dùng sample
            $image = isset($product['IMAGE']) ? $product['IMAGE'] : '/images/product_sample.jpg';
            ?>
            <img src="<?= $image ?>" class="img-fluid rounded">
        </div>

        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['PRO_NAME']) ?></h2>
            <p class="text-muted">Category: <?= htmlspecialchars($product['CAT_NAME']) ?></p>

            <h3 class="text-primary">$<?= number_format($product['PRO_PRICE'],2) ?></h3>

            <p><?= htmlspecialchars($product['PRO_DESCRIPTION']) ?></p>

            <form method="post" action="cart.php">
                <input type="hidden" name="id" value="<?= $product['PRODUCTID'] ?>">
                <input type="hidden" name="name" value="<?= htmlspecialchars($product['PRO_NAME']) ?>">
                <input type="hidden" name="price" value="<?= $product['PRO_PRICE'] ?>">
                
                <label>Quantity</label>
                <input type="number" name="qty" value="1" min="1" class="form-control w-25 mb-3">

                <button type="submit" class="btn btn-success btn-lg">Add to Cart</button>
            </form>
        </div>

    </div>
</div>

<?php include "components/footer.php"; ?>
