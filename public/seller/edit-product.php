<?php
session_start();
require_once '../../config/database.php';
include "../../views/components/header.php";
include "../../views/components/navbar.php";

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    header("Location: /login.php");
    exit;
}

$seller = $_SESSION['logged_in_user'];
$sellerId = $seller['id'];
$productId = $_GET['id'] ?? '';
$message = "";
$product = null;

try {
    $sql = "SELECT p.*, u.PRO_QUANTITY 
            FROM PRODUCTS p
            LEFT JOIN UPDATES u ON p.PRODUCTID = u.PRODUCTID 
            WHERE p.PRODUCTID = :pid AND p.SELLERID = :sid";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pid', $productId);
    $stmt->bindParam(':sid', $sellerId);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("<div class='container mt-5 alert alert-danger'>Sản phẩm không tồn tại hoặc bạn không có quyền sửa.</div>");
    }

    $catStmt = $conn->query("SELECT CAT_NAME FROM CATEGORIES");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $cat_name = $_POST['category'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 0);

    if (empty($name) || $price <= 0) {
        $message = "<div class='alert alert-danger'>Vui lòng điền tên và giá hợp lệ (>0).</div>";
    } else {
        try {
            $conn->beginTransaction();
            $sqlInfo = "UPDATE PRODUCTS 
                        SET PRO_NAME = :name, 
                            PRO_DESCRIPTION = :desc, 
                            PRO_PRICE = :price, 
                            CAT_NAME = :cat 
                        WHERE PRODUCTID = :pid AND SELLERID = :sid";
            
            $stmtInfo = $conn->prepare($sqlInfo);
            $stmtInfo->bindParam(':name', $name);
            $stmtInfo->bindParam(':desc', $desc);
            $stmtInfo->bindParam(':price', $price);
            $stmtInfo->bindParam(':cat', $cat_name);
            $stmtInfo->bindParam(':pid', $productId);
            $stmtInfo->bindParam(':sid', $sellerId);
            $stmtInfo->execute();

            $spSql = "CALL UPDATE_PRODUCTS(:pid, :sid, :qty)";
            $spStmt = $conn->prepare($spSql);
            $spStmt->bindParam(':pid', $productId);
            $spStmt->bindParam(':sid', $sellerId);
            $spStmt->bindParam(':qty', $quantity);
            $spStmt->execute();

            $conn->commit();
            
            $message = "<div class='alert alert-success'>Cập nhật sản phẩm thành công!</div>";
            
            $product['PRO_NAME'] = $name;
            $product['PRO_DESCRIPTION'] = $desc;
            $product['PRO_PRICE'] = $price;
            $product['CAT_NAME'] = $cat_name;
            $product['PRO_QUANTITY'] = $quantity;

        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "<div class='alert alert-danger'>Lỗi cập nhật: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<div class="container mt-5" style="max-width: 800px;">
    <h2>Edit Product: <?= htmlspecialchars($product['PRODUCTID']) ?></h2>
    
    <?= $message ?>

    <form method="post" class="card p-4 shadow-sm">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" name="name" 
                       value="<?= htmlspecialchars($product['PRO_NAME']) ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select class="form-control" name="category" required>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['CAT_NAME']) ?>" 
                            <?= ($cat['CAT_NAME'] == $product['CAT_NAME']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['CAT_NAME']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Price (VNĐ)</label>
                <input type="number" class="form-control" name="price" min="1" 
                       value="<?= htmlspecialchars($product['PRO_PRICE']) ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Quantity (Update Stock)</label>
                <input type="number" class="form-control" name="quantity" min="0" 
                       value="<?= htmlspecialchars($product['PRO_QUANTITY'] ?? 0) ?>" required>
                <div class="form-text text-muted">Số lượng này sẽ được cập nhật vào bảng UPDATES qua Stored Procedure.</div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="desc" rows="4" required><?= htmlspecialchars($product['PRO_DESCRIPTION']) ?></textarea>
        </div>

        <div class="d-flex justify-content-end">
            <a href="products.php" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </div>
    </form>
</div>

<?php include "../../views/components/footer.php"; ?>