<div class="container mt-5 mb-5">
    <h2 class="mb-4 fw-bold">
        <i class="bi bi-cart3 me-2"></i>Giỏ hàng của bạn 
        <span class="badge bg-primary rounded-pill fs-6 align-middle"><?= count($cartItems) ?></span>
    </h2>

    <?php if (!empty($cartItems)): ?>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <form method="post" action="/cart.php">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="card shadow-sm border-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="min-width: 300px;">Sản phẩm</th>
                                        <th style="min-width: 100px;">Giá</th>
                                        <th style="min-width: 120px;">Số lượng</th>
                                        <th style="min-width: 120px;" class="text-end">Tạm tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cartItems as $item): ?>
                                        <?php $lineTotal = $item['price'] * $item['qty']; ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center p-2">
                                                    <img src="<?= htmlspecialchars($item['image'] ?? '/images/product_sample.jpg') ?>" 
                                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                                         class="rounded border"
                                                         style="width: 70px; height: 70px; object-fit: cover; margin-right: 15px;"
                                                         onerror="this.src='/images/product_sample.jpg'">
                                                    
                                                    <div>
                                                        <h6 class="mb-1 text-dark fw-bold text-truncate" style="max-width: 250px;">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </h6>
                                                        <a href="/cart.php?remove=<?= $item['id'] ?>" 
                                                           class="text-danger small text-decoration-none hover-underline"
                                                           onclick="return confirm('Xóa sản phẩm này khỏi giỏ?');">
                                                            <i class="bi bi-trash"></i> Xóa bỏ
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-muted"><?= number_format($item['price']) ?> ₫</td>
                                            <td>
                                                <input type="number" name="qty[<?= $item['id'] ?>]" 
                                                       class="form-control form-control-sm text-center fw-bold border-secondary" 
                                                       value="<?= $item['qty'] ?>" min="1" max="99" style="width: 70px;">
                                            </td>
                                            <td class="text-end fw-bold text-primary fs-5">
                                                <?= number_format($lineTotal) ?> ₫
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                            <a href="/shop.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold btn-sm shadow-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Cập nhật giỏ hàng
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-header bg-white fw-bold py-3 border-bottom-0">
                        <i class="bi bi-receipt me-2"></i>Tóm tắt đơn hàng
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-bold"><?= number_format($totalPrice) ?> ₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="text-success fw-bold">Miễn phí</span>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Tổng cộng:</span>
                            <span class="fs-4 fw-bold text-primary"><?= number_format($totalPrice) ?> ₫</span>
                        </div>
                        <div class="d-grid">
                            <a href="/checkout.php" class="btn btn-primary btn-lg py-3 shadow hover-scale">
                                TIẾN HÀNH THANH TOÁN <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                        <div class="mt-3 text-center">
                            <small class="text-muted"><i class="bi bi-shield-check me-1"></i>Thanh toán bảo mật 100%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" alt="Empty Cart" style="width: 150px; opacity: 0.5;">
            </div>
            <h3 class="fw-bold text-secondary">Giỏ hàng của bạn đang trống!</h3>
            <p class="text-muted mb-4">Có vẻ như bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
            <a href="/shop.php" class="btn btn-primary btn-lg px-5 rounded-pill shadow hover-scale">
                Mua sắm ngay
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    .hover-underline:hover { text-decoration: underline !important; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-2px); }
</style>