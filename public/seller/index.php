<?php
session_start();

// Chặn truy cập nếu chưa login hoặc không phải seller
if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['ROLE'] !== 'seller') {
    header("Location: /login.php");
    exit;
}

include "../../views/components/header.php";
include "../../views/components/navbar.php";

$seller = $_SESSION['logged_in_user'];
$jsonPath = __DIR__ . '/../../data/products.json';

// Load products
$products = [];
if (file_exists($jsonPath)) {
    $jsonData = file_get_contents($jsonPath);
    $decoded = json_decode($jsonData, true);
    if (is_array($decoded)) $products = $decoded;
}

// --- Handle Add / Edit / Delete Product ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $newProduct = [
            'PRODUCTID' => 'P' . str_pad((count($products) + 1), 3, '0', STR_PAD_LEFT),
            'PRO_NAME' => $_POST['name'] ?? 'New Product',
            'PRO_DESCRIPTION' => $_POST['description'] ?? '',
            'PRO_PRICE' => floatval($_POST['price'] ?? 0),
            'SELLERID' => $seller['SELLERID'],
            'CAT_NAME' => $_POST['category'] ?? 'Misc',
            'IMAGE' => $_POST['image'] ?? '/images/product_sample.jpg'
        ];
        $products[] = $newProduct;
    }
    elseif ($action === 'edit' && isset($_POST['product_id'])) {
        foreach ($products as &$p) {
            if ($p['PRODUCTID'] === $_POST['product_id']) {
                $p['PRO_NAME'] = $_POST['name'] ?? $p['PRO_NAME'];
                $p['PRO_DESCRIPTION'] = $_POST['description'] ?? $p['PRO_DESCRIPTION'];
                $p['PRO_PRICE'] = floatval($_POST['price'] ?? $p['PRO_PRICE']);
                $p['CAT_NAME'] = $_POST['category'] ?? $p['CAT_NAME'];
                $p['IMAGE'] = $_POST['image'] ?? $p['IMAGE'] ?? '/images/product_sample.jpg';
                break;
            }
        }
        unset($p);
    }
    elseif ($action === 'delete' && isset($_POST['product_id'])) {
        $pid = $_POST['product_id'];
        $products = array_filter($products, fn($p) => $p['PRODUCTID'] !== $pid);
        $products = array_values($products); // Reindex
    }

    // Save back to JSON
    file_put_contents($jsonPath, json_encode($products, JSON_PRETTY_PRINT));
    // Reload products
    $products = json_decode(file_get_contents($jsonPath), true);
}
?>

<div class="container mt-5">
    <h2>Seller Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($seller['FIRSTNAME'] . ' ' . $seller['LASTNAME']) ?>!</p>

    <!-- Add Product Form -->
    <h3>Add New Product</h3>
    <form method="post" class="mb-4">
        <input type="hidden" name="action" value="add">
        <input class="form-control mb-2" name="name" placeholder="Product Name" required>
        <input class="form-control mb-2" name="description" placeholder="Description">
        <input class="form-control mb-2" name="price" type="number" step="0.01" placeholder="Price" required>
        <input class="form-control mb-2" name="category" placeholder="Category" required>
        <input class="form-control mb-2" name="image" placeholder="Image URL (optional)">
        <button type="submit" class="btn btn-success">Add Product</button>
    </form>

    <!-- Seller Products -->
    <h3>Your Products</h3>
    <div class="row">
        <?php
        $sellerProducts = array_filter($products, fn($p) => $p['SELLERID'] === $seller['SELLERID']);
        if (empty($sellerProducts)) {
            echo "<p>You have no products yet.</p>";
        } else {
            foreach ($sellerProducts as $product):
        ?>
            <div class="col-md-4 mb-3">
                <div class="card p-2">
                    <img src="<?= htmlspecialchars($product['IMAGE'] ?? '/images/product_sample.jpg') ?>" class="card-img-top mb-2" style="height:200px; object-fit:cover;">
                    <h5><?= htmlspecialchars($product['PRO_NAME']) ?></h5>
                    <p><?= htmlspecialchars($product['PRO_DESCRIPTION']) ?></p>
                    <p><strong>$<?= number_format($product['PRO_PRICE'], 2) ?></strong></p>
                    <p>Category: <?= htmlspecialchars($product['CAT_NAME']) ?></p>

                    <div class="d-flex gap-2 mt-2">
                        <!-- Edit button triggers modal -->
                        <button class="btn btn-primary btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#editModal<?= $product['PRODUCTID'] ?>">Edit</button>

                        <!-- Delete form -->
                        <form method="post" class="flex-fill">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="product_id" value="<?= $product['PRODUCTID'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $product['PRODUCTID'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="product_id" value="<?= $product['PRODUCTID'] ?>">
                            <input class="form-control mb-2" name="name" value="<?= htmlspecialchars($product['PRO_NAME']) ?>" required>
                            <input class="form-control mb-2" name="description" value="<?= htmlspecialchars($product['PRO_DESCRIPTION']) ?>">
                            <input class="form-control mb-2" name="price" type="number" step="0.01" value="<?= $product['PRO_PRICE'] ?>" required>
                            <input class="form-control mb-2" name="category" value="<?= htmlspecialchars($product['CAT_NAME']) ?>" required>
                            <input class="form-control mb-2" name="image" value="<?= htmlspecialchars($product['IMAGE'] ?? '') ?>" placeholder="Image URL (optional)">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endforeach; ?>
        <?php } ?>
    </div>
</div>

<?php include "../../views/components/footer.php"; ?>
