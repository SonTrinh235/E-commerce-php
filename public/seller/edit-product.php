<?php
session_start();
require_once '../../config/database.php';
include "../../views/components/header.php";
include "../../views/components/navbar.php";

// Kiểm tra quyền Seller
if (!isset($_SESSION['logged_in_user']) || $_SESSION['logged_in_user']['role'] !== 'seller') {
    echo "<script>window.location.href='/login.php';</script>";
    exit;
}

$seller = $_SESSION['logged_in_user'];
$sellerId = $seller['id'];
$productId = $_GET['id'] ?? '';
$message = "";
$product = null;

// 1. Lấy thông tin sản phẩm (bao gồm số lượng từ bảng UPDATES)
try {
    $sql = "SELECT p.*, u.PRO_QUANTITY 
            FROM PRODUCTS p
            LEFT JOIN UPDATES u ON p.PRODUCTID = u.PRODUCTID 
            WHERE p.PRODUCTID = :pid AND p.SELLERID = :sid";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':pid' => $productId, ':sid' => $sellerId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("<div class='container mt-5 alert alert-danger'>Sản phẩm không tồn tại hoặc bạn không có quyền sửa.</div>");
    }

    $catStmt = $conn->query("SELECT CAT_NAME FROM CATEGORIES");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// 2. Xử lý Form Submit (Cập nhật)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $price = (int)($_POST['price'] ?? 0);
    $cat_name = $_POST['category'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0); // Lấy số lượng mới
    
    $imageUrl = trim($_POST['image'] ?? '');
    if (empty($imageUrl)) $imageUrl = '/images/product_sample.jpg';

    if (empty($name) || $price <= 0) {
        $message = "<div class='alert alert-danger'>Vui lòng điền tên và giá hợp lệ (>0).</div>";
    } else {
        try {
            $conn->beginTransaction();

            // A. Cập nhật thông tin cơ bản (Bảng PRODUCTS)
            $sqlInfo = "UPDATE PRODUCTS 
                        SET PRO_NAME = :name, 
                            PRO_DESCRIPTION = :desc, 
                            PRO_PRICE = :price, 
                            CAT_NAME = :cat,
                            IMAGE = :img 
                        WHERE PRODUCTID = :pid AND SELLERID = :sid";
            
            $stmtInfo = $conn->prepare($sqlInfo);
            $stmtInfo->execute([
                ':name' => $name,
                ':desc' => $desc,
                ':price' => $price,
                ':cat' => $cat_name,
                ':img' => $imageUrl,
                ':pid' => $productId,
                ':sid' => $sellerId
            ]);

            // B. Cập nhật số lượng kho (Bảng UPDATES)
            // Logic: Số lượng > 0 là "Còn hàng", ngược lại là "Hết hàng"
            $status = ($quantity > 0) ? 'Còn hàng' : 'Hết hàng';
            
            // Kiểm tra xem đã có dòng nào trong bảng UPDATES chưa
            $check = $conn->prepare("SELECT 1 FROM UPDATES WHERE PRODUCTID = :pid");
            $check->execute([':pid' => $productId]);
            
            if ($check->fetch()) {
                // Đã có -> UPDATE
                $sqlUpdate = "UPDATE UPDATES 
                              SET PRO_QUANTITY = :qty, PRO_STATUS = :st 
                              WHERE PRODUCTID = :pid AND SELLERID = :sid";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':qty' => $quantity,
                    ':st' => $status,
                    ':pid' => $productId,
                    ':sid' => $sellerId
                ]);
            } else {
                // Chưa có -> INSERT mới
                $sqlInsert = "INSERT INTO UPDATES (PRODUCTID, SELLERID, PRO_QUANTITY, PRO_STATUS) 
                              VALUES (:pid, :sid, :qty, :st)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':pid' => $productId,
                    ':sid' => $sellerId,
                    ':qty' => $quantity,
                    ':st' => $status
                ]);
            }

            $conn->commit();
            $message = "<div class='alert alert-success'>Cập nhật thành công!</div>";
            
            // Cập nhật lại biến $product để hiển thị dữ liệu mới ngay lập tức
            $product['PRO_NAME'] = $name;
            $product['PRO_DESCRIPTION'] = $desc;
            $product['PRO_PRICE'] = $price;
            $product['CAT_NAME'] = $cat_name;
            $product['PRO_QUANTITY'] = $quantity;
            $product['IMAGE'] = $imageUrl;

        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "<div class='alert alert-danger'>Lỗi cập nhật: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<div class="container mt-5" style="max-width: 800px;">
    <h2>Sửa sản phẩm: <?= htmlspecialchars($product['PRODUCTID']) ?></h2>
    
    <?= $message ?>

    <form method="post" class="card p-4 shadow-sm">
        <div class="row">
            <div class="col-md-4 mb-3 text-center">
                <label class="form-label fw-bold">Ảnh hiện tại</label>
                <div class="border p-2 rounded bg-light mb-2">
                    <img src="<?= htmlspecialchars($product['IMAGE'] ?? '/images/product_sample.jpg') ?>" 
                         style="width: 100%; height: 180px; object-fit: contain;" 
                         onerror="this.src='/images/product_sample.jpg'">
                </div>
                <input type="text" class="form-control form-control-sm" name="image" 
                       value="<?= htmlspecialchars($product['IMAGE'] ?? '') ?>" 
                       placeholder="Link ảnh mới...">
            </div>

            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sản phẩm</label>
                    <input type="text" class="form-control" name="name" 
                           value="<?= htmlspecialchars($product['PRO_NAME']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Danh mục</label>
                        <select class="form-select" name="category" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['CAT_NAME']) ?>" 
                                    <?= ($cat['CAT_NAME'] == $product['CAT_NAME']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['CAT_NAME']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                        <input type="number" class="form-control" name="price" min="1" 
                               value="<?= htmlspecialchars($product['PRO_PRICE']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">Số lượng tồn kho (Updates)</label>
                    <input type="number" class="form-control border-primary" name="quantity" min="0" 
                           value="<?= htmlspecialchars($product['PRO_QUANTITY'] ?? 0) ?>" required>
                    <div class="form-text text-muted">Nhập 0 sẽ chuyển trạng thái thành "Hết hàng".</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea class="form-control" name="desc" rows="4"><?= htmlspecialchars($product['PRO_DESCRIPTION']) ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="products.php" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include "../../views/components/footer.php"; ?>