<div class="container mt-5 mb-5">
    <h2 class="mb-4 fw-bold">Giỏ hàng của bạn <span class="badge bg-primary rounded-pill fs-6"><?= count($cartItems) ?></span></h2>

    <?php if (!empty($cartItems)): ?>
        <div class="row">
            <div class="col-md-8">
                <form method="post" action="/cart.php">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="card shadow-sm border-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40%">Sản phẩm</th>
                                        <th style="width: 20%">Giá</th>
                                        <th style="width: 20%">Số lượng</th>
                                        <th style="width: 20%" class="text-end">Tạm tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cartItems as $item): ?>
                                        <?php $lineTotal = $item['price'] * $item['qty']; ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                                                         alt="" 
                                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px;"
                                                         onerror="this.src='/images/product_sample.jpg'">
                                                    
                                                    <div>
                                                        <h6 class="mb-0 text-truncate" style="max-width: 200px;"><?= htmlspecialchars($item['name']) ?></h6>
                                                        <a href="/cart.php?remove=<?= $item['id'] ?>" class="text-danger small text-decoration-none">
                                                            <i class="bi bi-trash"></i> Xóa
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= number_format($item['price']) ?> VNĐ</td>
                                            <td>
                                                <input type="number" name="qty[<?= $item['id'] ?>]" 
                                                       class="form-control form-control-sm text-center" 
                                                       value="<?= $item['qty'] ?>" min="1" style="width: 60px;">
                                            </td>
                                            <td class="text-end fw-bold text-primary">
                                                <?= number_format($lineTotal) ?> VNĐ
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between py-3">
                            <a href="/shop.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Mua thêm
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold">
                                <i class="bi bi-arrow-clockwise"></i> Cập nhật giỏ hàng
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4 mt-4 mt-md-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold py-3">
                        Tóm tắt đơn hàng
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span class="fw-bold"><?= number_format($totalPrice) ?> VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 fs-5 fw-bold text-primary">
                            <span>Tổng cộng:</span>
                            <span><?= number_format($totalPrice) ?> VNĐ</span>
                        </div>
                        <div class="d-grid">
                            <a href="/checkout.php" class="btn btn-primary btn-lg py-3">
                                THANH TOÁN NGAY
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5 bg-light rounded-3">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h3 class="mt-3">Giỏ hàng của bạn đang trống</h3>
            <p class="text-muted">Hãy thêm vài sản phẩm để chúng tôi giao hàng cho bạn nhé!</p>
            <a href="/shop.php" class="btn btn-primary btn-lg mt-3 px-5">
                Đi đến Cửa hàng
            </a>
        </div>
    <?php endif; ?>
</div>