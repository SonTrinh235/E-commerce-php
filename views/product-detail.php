<?php
// Kiểm tra dữ liệu từ Controller gửi sang
if (!$product) {
    echo "<div class='container mt-5 py-5'><div class='alert alert-danger text-center shadow-sm'>
            <i class='bi bi-exclamation-triangle-fill fs-1 d-block mb-3'></i>
            <h4>Sản phẩm không tồn tại hoặc đã bị xóa.</h4>
            <a href='/index.php' class='btn btn-secondary mt-3'>Quay về trang chủ</a>
          </div></div>";
    return; // Dừng việc load phần dưới
}

$name     = $product['PRO_NAME'] ?? 'No name';
$desc     = $product['PRO_DESCRIPTION'] ?? 'Chưa có mô tả';
$price    = $product['PRO_PRICE'] ?? 0;
$category = $product['CAT_NAME'] ?? 'Khác';
$shopName = $product['SHOP_NAME'] ?? 'Shop ẩn danh';
$sellerId = $product['SELLERID'] ?? '';
$id       = $product['PRODUCTID'];

// Ảnh mặc định
$image    = '/images/product_sample.jpg'; 
?>

<style>
    .detail-img { width: 100%; height: 400px; object-fit: cover; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    .meta-badge { background-color: #f8f9fa; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; color: #555; border: 1px solid #ddd; margin-right: 10px; display: inline-flex; align-items: center; }
    .detail-price { font-size: 2rem; font-weight: bold; color: #0d6efd; margin: 15px 0; }
    .detail-desc { font-size: 1rem; line-height: 1.6; color: #444; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 25px; }
</style>

<div class="container mt-5 mb-5">
    <div class="row">

        <div class="col-md-5 mb-4">
            <div class="detail-image-container">
                <img src="<?= htmlspecialchars($image) ?>" class="detail-img" 
                     alt="<?= htmlspecialchars($name) ?>"
                     onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'">
            </div>
        </div>

        <div class="col-md-7">
            <h1 class="fw-bold mb-3"><?= htmlspecialchars($name) ?></h1>
            
            <div class="mb-4">
                <span class="meta-badge">
                    <i class="bi bi-tag-fill me-1 text-primary"></i> <?= htmlspecialchars($category) ?>
                </span>
                <span class="meta-badge">
                    <i class="bi bi-shop me-1 text-success"></i> <?= htmlspecialchars($shopName) ?>
                </span>
                <span class="meta-badge">
                    <i class="bi bi-upc-scan me-1"></i> ID: <?= htmlspecialchars($id) ?>
                </span>
            </div>

            <div class="detail-price">
                <?= number_format($price, 0, ',', '.') ?> <small class="fs-5 text-muted">VNĐ</small>
            </div>

            <div class="mb-2 fw-bold text-secondary">Mô tả sản phẩm:</div>
            <div class="detail-desc">
                <?= nl2br(htmlspecialchars($desc)) ?>
            </div>

            <form method="post" action="/cart.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                
                <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
                <input type="hidden" name="price" value="<?= $price ?>">
                <input type="hidden" name="image" value="<?= htmlspecialchars($image) ?>">
                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded">
                    <label class="fw-bold">Số lượng:</label>
                    <input type="number" name="qty" value="1" min="1" max="99" class="form-control text-center" style="width: 80px;">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg py-3 shadow-sm hover-scale">
                        <i class="bi bi-cart-plus-fill me-2"></i> THÊM VÀO GIỎ HÀNG
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>