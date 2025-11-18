<?php
session_start();
include "../../views/components/header.php";
include "../../views/components/navbar.php";

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    header("Location: /login.php");
    exit;
}

$seller = $_SESSION['logged_in_user'];
$jsonPath = __DIR__ . '/../../data/products.json';
$products = json_decode(file_get_contents($jsonPath), true);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $cat = $_POST['category'] ?? '';

    if (!$name || !$cat || $price <= 0) $errors[] = "Please fill all fields correctly.";

    if (!$errors) {
        $newProduct = [
            'PRODUCTID' => 'P'.(count($products)+1),
            'PRO_NAME' => $name,
            'PRO_DESCRIPTION' => $desc,
            'PRO_PRICE' => $price,
            'SELLERID' => $seller['id'],
            'CAT_NAME' => $cat
        ];
        $products[] = $newProduct;
        file_put_contents($jsonPath, json_encode($products, JSON_PRETTY_PRINT));
        header("Location: products.php");
        exit;
    }
}
?>

<div class="container mt-5" style="max-width: 500px;">
    <h2>Add Product</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach($errors as $err) echo "<li>$err</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="text" class="form-control mb-3" name="name" placeholder="Product Name" required>
        <textarea class="form-control mb-3" name="desc" placeholder="Description"></textarea>
        <input type="number" class="form-control mb-3" name="price" placeholder="Price" min="1" step="0.01" required>
        <input type="text" class="form-control mb-3" name="category" placeholder="Category" required>
        <button class="btn btn-success w-100">Add Product</button>
    </form>
</div>

<?php include "../../views/components/footer.php"; ?>
