<?php
session_start();
require_once '../../config/database.php';
include "../../views/components/header.php";
include "../../views/components/navbar.php";

if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    echo "<script>window.location.href='/login.php';</script>";
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
    $name = $_POST['name'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $price = (int)($_POST['price'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $cat_name = $_POST['category'] ?? '';
    $imageUrl = trim($_POST['image'] ?? '');
    if (empty($imageUrl)) $imageUrl = '/images/product_sample.jpg';

    if (empty($name) || empty($cat_name) || $price <= 0) {
        $errors[] = "Vui lòng điền đầy đủ thông tin và giá phải lớn hơn 0.";
    }

    if (empty($errors)) {
        try {
            $conn->beginTransaction();
            $newProId = 'PRO_' . time() . rand(100,999);
            if(strlen($newProId) > 20) $newProId = substr($newProId, 0, 20);
            $sql1 = "INSERT INTO PRODUCTS (PRODUCTID, PRO_NAME, PRO_DESCRIPTION, PRO_PRICE, SELLERID, CAT_NAME, IMAGE) 
                     VALUES (:id, :name, :desc, :price, :sid, :cat, :img)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute([
                ':id' => $newProId,
                ':name' => $name,
                ':desc' => $desc,
                ':price' => $price,
                ':sid' => $sellerId,
                ':cat' => $cat_name,
                ':img' => $imageUrl
            ]);

            $status = ($quantity > 0) ? 'Còn hàng' : 'Hết hàng';
            $sql2 = "INSERT INTO UPDATES (PRODUCTID, SELLERID, PRO_QUANTITY, PRO_STATUS) 
                     VALUES (:id, :sid, :qty, :status)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([
                ':id' => $newProId,
                ':sid' => $sellerId,
                ':qty' => $quantity,
                ':status' => $status
            ]);

            $conn->commit();
            
            echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='products.php';</script>";
            exit;

        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Thêm Sản Phẩm Mới</h4>
        </div>
        <div class="card-body">
            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3"><?php foreach($errors as $err) echo "<li>$err</li>"; ?></ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sản phẩm</label>
                    <input type="text" class="form-control" name="name" required placeholder="Ví dụ: iPhone 15">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Link Hình Ảnh (URL)</label>
                    <input type="text" class="form-control" name="image" placeholder="https://...">
                    <div class="form-text">Dán link ảnh từ internet vào đây.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Danh mục</label>
                        <select class="form-select" name="category" required>
                            <option value="">-- Chọn --</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['CAT_NAME']) ?>">
                                    <?= htmlspecialchars($cat['CAT_NAME']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                        <input type="number" class="form-control" name="price" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Số lượng tồn kho</label>
                    <input type="number" class="form-control" name="quantity" min="1" value="10" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea class="form-control" name="desc" rows="3"></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
                    <a href="products.php" class="btn btn-secondary">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../../views/components/footer.php"; ?>