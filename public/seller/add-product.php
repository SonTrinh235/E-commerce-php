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

$errors = [];
$categories = [];
try {
    $stmt = $conn->query("SELECT CAT_NAME FROM CATEGORIES");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Lỗi tải danh mục: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pro_id = $_POST['product_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $cat_name = $_POST['category'] ?? '';

    if (empty($pro_id) || empty($name) || empty($cat_name) || $price <= 0) {
        $errors[] = "Vui lòng điền đầy đủ thông tin và giá phải lớn hơn 0.";
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO PRODUCTS (PRODUCTID, PRO_NAME, PRO_DESCRIPTION, PRO_PRICE, SELLERID, CAT_NAME) 
                    VALUES (:pid, :pname, :pdesc, :pprice, :sid, :pcat)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':pid', $pro_id);
            $stmt->bindParam(':pname', $name);
            $stmt->bindParam(':pdesc', $desc);
            $stmt->bindParam(':pprice', $price);
            $stmt->bindParam(':sid', $sellerId);
            $stmt->bindParam(':pcat', $cat_name);
            
            $stmt->execute();
            echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='products.php';</script>";
            exit;

        } catch (PDOException $e) {
            $errors[] = "Lỗi thêm dữ liệu: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5" style="max-width: 600px;">
    <h2>Add New Product</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach($errors as $err) echo "<li>$err</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Product ID (Mã sản phẩm)</label>
            <input type="text" class="form-control" name="product_id" placeholder="VD: PRO_NEW_1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-control" name="category" required>
                <option value="">-- Select Category --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['CAT_NAME']) ?>">
                        <?= htmlspecialchars($cat['CAT_NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" class="form-control" name="price" min="1" step="1" required>
            <div class="form-text">Giá phải lớn hơn 0 (Ràng buộc CHK_PRO_PRICE)</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="desc" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Add Product</button>
        <a href="products.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
    </form>
</div>

<?php include "../../views/components/footer.php"; ?>