<?php include "components/header.php"; ?>
<?php include "components/navbar.php"; ?>

<?php
$jsonPath = __DIR__ . '/../data/products.json';
$products = [];

if (file_exists($jsonPath)) {
    $jsonData = file_get_contents($jsonPath);
    $products = json_decode($jsonData, true);
}

$categories = [];
if ($products) {
    foreach ($products as $p) {
        if (isset($p['CAT_NAME']) && !in_array($p['CAT_NAME'], $categories)) {
            $categories[] = $p['CAT_NAME'];
        }
    }
}

$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : PHP_INT_MAX;

$filteredProducts = array_filter($products, function($p) use ($selectedCategory, $maxPrice) {
    $matchCategory = $selectedCategory === '' || $selectedCategory === 'All' || $p['CAT_NAME'] === $selectedCategory;
    $matchPrice = $p['PRO_PRICE'] <= $maxPrice;
    return $matchCategory && $matchPrice;
});
?>

<div class="container mt-5">
    <div class="row">

        <!-- Filter Sidebar -->
        <div class="col-md-3">
            <h5>Filters</h5>
            <form method="get" id="filterForm">
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="All" <?= ($selectedCategory=='' || $selectedCategory=='All')?'selected':'' ?>>All</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($selectedCategory==$cat)?'selected':'' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Max Price: <span id="priceValue"><?= isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 10000 ?></span></label>
                    <input type="range" name="maxPrice" min="0" max="10000" step="100" 
                           value="<?= isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 10000 ?>" 
                           class="form-range" id="priceRange" 
                           oninput="document.getElementById('priceValue').innerText = this.value">
                </div>

                <button type="submit" class="btn btn-dark w-100">Apply</button>
            </form>
        </div>

        <!-- Products Section -->
        <div class="col-md-9">
            <h3 class="mb-4">Shop</h3>
            <div class="row">
                <?php
                if ($filteredProducts) {
                    foreach ($filteredProducts as $product) {
                        include "components/product-card.php";
                    }
                } else {
                    echo "<p>No products match your filter.</p>";
                }
                ?>
            </div>
        </div>

    </div>
</div>

<?php include "components/footer.php"; ?>
