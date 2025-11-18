<?php
session_start();
include "../../views/components/header.php";
include "../../views/components/navbar.php";

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    header("Location: /login.php");
    exit;
}

$seller = $_SESSION['logged_in_user'];
$sellerId = $seller['id'];

// Load products.json
$jsonPath = __DIR__ . '/../../data/products.json';
$products = json_decode(file_get_contents($jsonPath), true);
$sellerProducts = array_filter($products, fn($p) => $p['SELLERID'] === $sellerId);
?>

<div class="container mt-5">
    <h2>Your Products</h2>
    <a href="add-product.php" class="btn btn-success mb-3">Add New Product</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>PRODUCTID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sellerProducts as $p): ?>
            <tr>
                <td><?= $p['PRODUCTID'] ?></td>
                <td><?= htmlspecialchars($p['PRO_NAME']) ?></td>
                <td><?= htmlspecialchars($p['CAT_NAME']) ?></td>
                <td>$<?= number_format($p['PRO_PRICE'],2) ?></td>
                <td>
                    <a href="edit-product.php?id=<?= $p['PRODUCTID'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete-product.php?id=<?= $p['PRODUCTID'] ?>" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include "../../views/components/footer.php"; ?>
