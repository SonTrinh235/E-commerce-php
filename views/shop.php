<div class="container mt-5">
    <div class="row">

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-bold mb-0">Bộ Lọc</h5>
                        <?php if($selectedCategory != 'All' || !empty($keyword) || $maxPrice < 9999999999): ?>
                            <a href="shop.php" class="text-decoration-none small text-danger">Xóa lọc</a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="get" id="filterForm" action="shop.php">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Tìm kiếm</label>
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control" 
                                       placeholder="Tên sản phẩm..." 
                                       value="<?= htmlspecialchars($keyword) ?>">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Danh mục</label>
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
                            <label class="form-label fw-bold small text-muted text-uppercase d-flex justify-content-between">
                                <span>Giá tối đa</span>
                                <span class="text-primary" id="priceValue"><?= number_format(($maxPrice > 100000000) ? 10000000 : $maxPrice) ?></span>
                            </label>
                            <input type="range" name="maxPrice" min="0" max="10000000" step="100000" 
                                   value="<?= ($maxPrice > 10000000) ? 10000000 : $maxPrice ?>" 
                                   class="form-range" 
                                   oninput="document.getElementById('priceValue').innerText = new Intl.NumberFormat().format(this.value)">
                            <div class="d-flex justify-content-between small text-muted">
                                <span>0</span>
                                <span>10tr+</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Sắp xếp</label>
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="newest" <?= ($sort == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                                <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark">Áp dụng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="fw-bold text-primary mb-0">Cửa Hàng</h3>
                    <?php if(!empty($keyword)): ?>
                        <small class="text-muted">Kết quả tìm kiếm cho: "<strong><?= htmlspecialchars($keyword) ?></strong>"</small>
                    <?php endif; ?>
                </div>
                <span class="badge bg-light text-dark border">
                    <?= count($products) ?> sản phẩm
                </span>
            </div>
            
            <div class="row g-4">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <div class="position-relative">
                                    <a href="/product-detail.php?id=<?= $product['PRODUCTID'] ?>">
                                        <img src="<?= htmlspecialchars($product['IMAGE'] ?? '/images/product_sample.jpg') ?>" 
                                             class="card-img-top" 
                                             alt="<?= htmlspecialchars($product['PRO_NAME']) ?>" 
                                             style="height: 200px; object-fit: contain; background-color: #f8f9fa; padding: 10px;"
                                             onerror="this.src='/images/product_sample.jpg'">
                                    </a>
                                    
                                    </div>

                                <div class="card-body d-flex flex-column pt-2">
                                    <small class="text-muted mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <?= htmlspecialchars($product['CAT_NAME'] ?? 'Khác') ?>
                                    </small>
                                    
                                    <h5 class="card-title text-truncate" title="<?= htmlspecialchars($product['PRO_NAME']) ?>">
                                        <a href="/product-detail.php?id=<?= $product['PRODUCTID'] ?>" class="text-decoration-none text-dark fw-bold">
                                            <?= htmlspecialchars($product['PRO_NAME']) ?>
                                        </a>
                                    </h5>

                                    <p class="card-text small text-secondary mb-2 d-flex align-items-center">
                                        <i class="bi bi-shop me-1"></i> 
                                        <?= htmlspecialchars($product['SHOP_NAME'] ?? 'Shop ẩn danh') ?>
                                    </p>
                                    
                                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary fs-5">
                                            <?= number_format($product['PRO_PRICE']) ?> <small>₫</small>
                                        </span>
                                        
                                        <a href="/cart.php?action=add&id=<?= $product['PRODUCTID'] ?>" 
                                           class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                                           style="width: 38px; height: 38px;"
                                           title="Thêm vào giỏ">
                                            <i class="bi bi-cart-plus fs-5"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-light text-center p-5 border border-dashed rounded-3">
                            <i class="bi bi-search display-1 text-muted opacity-25"></i>
                            <h4 class="mt-3 text-muted">Không tìm thấy sản phẩm nào</h4>
                            <p class="text-muted small">Hãy thử tìm từ khóa khác hoặc điều chỉnh bộ lọc giá.</p>
                            <a href="shop.php" class="btn btn-primary mt-2 px-4 rounded-pill">Xem tất cả sản phẩm</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<style>
    .product-card { transition: all 0.3s ease; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .border-dashed { border-style: dashed !important; }
</style>