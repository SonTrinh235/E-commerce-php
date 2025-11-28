<div class="container mt-5">
    <div class="row">

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Bộ Lọc Tìm Kiếm</h5>
                    <form method="get" id="filterForm">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tìm tên sản phẩm</label>
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control" placeholder="Nhập tên..." value="<?= htmlspecialchars($keyword) ?>">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="All" <?= ($selectedCategory == 'All') ? 'selected' : '' ?>>Tất cả</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($selectedCategory == $cat) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Giá tối đa: <span class="text-primary" id="priceValue"><?= number_format($maxPrice) ?></span> VNĐ
                            </label>
                            <input type="range" name="maxPrice" min="0" max="10000000" step="50000" 
                                   value="<?= ($maxPrice > 10000000) ? 10000000 : $maxPrice ?>" 
                                   class="form-range" 
                                   oninput="document.getElementById('priceValue').innerText = new Intl.NumberFormat().format(this.value)">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Sắp xếp</label>
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="newest" <?= ($sort == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Giá thấp đến cao</option>
                                <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Giá cao đến thấp</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 mt-2">Áp dụng</button>
                        <a href="shop.php" class="btn btn-outline-secondary w-100 mt-2">Xóa bộ lọc</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-primary">Cửa Hàng</h3>
                <span class="text-muted">Tìm thấy <?= count($products) ?> sản phẩm</span>
            </div>
            
            <div class="row g-4">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <div class="position-relative">
                                    <img src="/images/product_sample.jpg" class="card-img-top" 
                                         alt="<?= htmlspecialchars($product['PRO_NAME']) ?>" 
                                         style="height: 200px; object-fit: cover; background-color: #eee;"
                                         onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <small class="text-muted mb-1"><?= htmlspecialchars($product['CAT_NAME'] ?? 'Khác') ?></small>
                                    
                                    <h5 class="card-title text-truncate" title="<?= htmlspecialchars($product['PRO_NAME']) ?>">
                                        <a href="/product-detail.php?id=<?= $product['PRODUCTID'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($product['PRO_NAME']) ?>
                                        </a>
                                    </h5>

                                    <p class="card-text small text-secondary mb-2">
                                        <i class="bi bi-shop"></i> <?= htmlspecialchars($product['SHOP_NAME'] ?? 'Shop ẩn danh') ?>
                                    </p>
                                    
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary fs-5">
                                            <?= number_format($product['PRO_PRICE']) ?> VNĐ
                                        </span>
                                        <a href="/cart.php?action=add&id=<?= $product['PRODUCTID'] ?>" class="btn btn-outline-primary btn-sm rounded-circle">
                                            <i class="bi bi-cart-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center p-5">
                            <i class="bi bi-search fs-1"></i>
                            <p class="mt-3 fs-5">Không tìm thấy sản phẩm nào.</p>
                            <a href="shop.php" class="btn btn-primary mt-2">Xem tất cả</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
