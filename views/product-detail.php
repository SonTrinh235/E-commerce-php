<?php
if (!$product) {
    echo "<div class='container mt-5 py-5'><div class='alert alert-danger text-center shadow-sm'>
            <i class='bi bi-exclamation-triangle-fill fs-1 d-block mb-3'></i>
            <h4>Sản phẩm không tồn tại hoặc đã bị xóa.</h4>
            <a href='/index.php' class='btn btn-secondary mt-3'>Quay về trang chủ</a>
          </div></div>";
    return;
}

$name     = $product['PRO_NAME'] ?? 'No name';
$desc     = $product['PRO_DESCRIPTION'] ?? 'Chưa có mô tả';
$price    = $product['PRO_PRICE'] ?? 0;
$category = $product['CAT_NAME'] ?? 'Khác';
$shopName = $product['SHOP_NAME'] ?? 'Shop ẩn danh';
$sellerId = $product['SELLERID'] ?? '';
$id       = $product['PRODUCTID'];
$image    = $product['IMAGE'] ?? '/images/product_sample.jpg';
$avgRating = isset($product['AVG_RATING']) ? (float)$product['AVG_RATING'] : 0;
$currentUser = $_SESSION['logged_in_user'] ?? null;
?>

<style>
    .detail-img { width: 100%; height: 400px; object-fit: cover; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    .meta-badge { background-color: #f8f9fa; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; color: #555; border: 1px solid #ddd; margin-right: 10px; display: inline-flex; align-items: center; }
    .detail-price { font-size: 2rem; font-weight: bold; color: #0d6efd; margin: 15px 0; }
    .detail-desc { font-size: 1rem; line-height: 1.6; color: #444; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 25px; }
        .star-rating-input i {
        font-size: 2rem;
        cursor: pointer;
        color: #ddd; 
        transition: color 0.2s;
    }
    .star-rating-input i.active, 
    .star-rating-input i.hover {
        color: #ffc107; 
    }
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
            <h1 class="fw-bold mb-2"><?= htmlspecialchars($name) ?></h1>
            
            <div class="mb-3 d-flex align-items-center">
                <div class="text-warning me-2 fs-5">
                    <?php 
                    $starRound = round($avgRating);
                    for($i=1; $i<=5; $i++): 
                        if($i <= $starRound): echo '<i class="bi bi-star-fill"></i>';
                        else: echo '<i class="bi bi-star text-secondary opacity-25"></i>';
                        endif; 
                    endfor; ?>
                </div>
                <span class="fw-bold pt-1 me-2"><?= $avgRating > 0 ? number_format($avgRating, 1) . "/5" : "" ?></span>
                <span class="text-muted small pt-1 border-start ps-2">
                    <?= count($reviews) ?> đánh giá
                </span>
            </div>

            <div class="mb-4">
                <span class="meta-badge"><i class="bi bi-tag-fill me-1 text-primary"></i> <?= htmlspecialchars($category) ?></span>
                <span class="meta-badge"><i class="bi bi-shop me-1 text-success"></i> <?= htmlspecialchars($shopName) ?></span>
                <span class="meta-badge"><i class="bi bi-upc-scan me-1"></i> ID: <?= htmlspecialchars($id) ?></span>
            </div>

            <div class="detail-price">
                <?= number_format($price, 0, ',', '.') ?> <small class="fs-5 text-muted">VNĐ</small>
            </div>

            <div class="mb-2 fw-bold text-secondary">Mô tả sản phẩm:</div>
            <div class="detail-desc"><?= nl2br(htmlspecialchars($desc)) ?></div>

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
                    <button type="submit" class="btn btn-primary btn-lg py-3 shadow-sm">
                        <i class="bi bi-cart-plus-fill me-2"></i> THÊM VÀO GIỎ HÀNG
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-5 bg-light">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Viết đánh giá của bạn</h5>
            
            <?php if ($currentUser && isset($currentUser['role']) && $currentUser['role'] === 'buyer'): ?>
                
                <?php if (!$hasReviewed): ?>
                    <form method="POST" action="" id="reviewForm" onsubmit="return validateRating()">
                        <input type="hidden" name="action" value="add_review">
                        
                        <input type="hidden" name="rating" id="ratingInput" value="0">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Bạn chấm mấy sao?</label>
                            <div class="star-rating-input d-flex gap-2" id="starContainer">
                                <i class="bi bi-star-fill" data-value="1"></i>
                                <i class="bi bi-star-fill" data-value="2"></i>
                                <i class="bi bi-star-fill" data-value="3"></i>
                                <i class="bi bi-star-fill" data-value="4"></i>
                                <i class="bi bi-star-fill" data-value="5"></i>
                            </div>
                            <div id="ratingError" class="text-danger small mt-2 d-none">
                                <i class="bi bi-exclamation-circle me-1"></i> Vui lòng chọn số sao .
                            </div>
                            <div id="ratingText" class="fw-bold text-warning mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nhận xét chi tiết:</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Sản phẩm dùng có tốt không? Chất lượng thế nào?..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-send-fill me-2"></i>Gửi đánh giá
                        </button>
                    </form>

                <?php else: ?>
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Cảm ơn bạn!</strong><br>
                            Bạn đã đánh giá sản phẩm này.
                        </div>
                    </div>
                <?php endif; ?>

            <?php elseif ($currentUser && $currentUser['role'] !== 'buyer'): ?>
                 <div class="alert alert-warning mb-0">
                    Tài khoản quản trị/người bán không thể đánh giá sản phẩm.
                </div>
            <?php else: ?>
                <div class="alert alert-secondary d-flex align-items-center mb-0">
                    <i class="bi bi-lock-fill fs-4 me-3"></i>
                    <div>
                        Vui lòng <a href="/login.php" class="fw-bold text-decoration-underline">Đăng nhập</a> tài khoản để đánh giá.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0 fw-bold border-start border-4 border-primary ps-3">
                Đánh giá từ khách hàng (<?= count($reviews) ?>)
            </h4>
        </div>
        <div class="card-body">
            <?php if (!empty($reviews)): ?>
                <div class="vstack gap-3">
                    <?php foreach ($reviews as $rv): ?>
                        <div class="border-bottom pb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 text-secondary fw-bold shadow-sm" style="width: 40px; height: 40px;">
                                        <?= substr($rv['LASTNAME'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($rv['FIRSTNAME'] . ' ' . $rv['LASTNAME']) ?></h6>
                                        <div class="text-warning small">
                                            <?php for($i=1; $i<=5; $i++): 
                                                if($i <= $rv['REV_RATING']): echo '<i class="bi bi-star-fill"></i>';
                                                else: echo '<i class="bi bi-star text-secondary opacity-25"></i>';
                                                endif;
                                            endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border"><?= $rv['REV_RATING'] ?>/5</span>
                            </div>
                            <p class="text-dark mb-0 ps-5 ms-2"><?= nl2br(htmlspecialchars($rv['REV_TEXT'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-dots display-4 opacity-25"></i>
                    <p class="mt-3">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating-input i');
    const ratingInput = document.getElementById('ratingInput');
    const ratingText = document.getElementById('ratingText');
    const textMap = {
        1: 'Rất tệ',
        2: 'Tệ',
        3: 'Bình thường',
        4: 'Tốt',
        5: 'Tuyệt vời'
    };

    let selectedValue = 0;

    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const val = this.getAttribute('data-value');
            highlightStars(val);
            if(textMap[val]) ratingText.innerText = textMap[val];
        });

        star.addEventListener('mouseout', function() {
            highlightStars(selectedValue);
            ratingText.innerText = selectedValue > 0 ? textMap[selectedValue] : '';
        });

        star.addEventListener('click', function() {
            selectedValue = this.getAttribute('data-value');
            ratingInput.value = selectedValue;
            
            document.getElementById('ratingError').classList.add('d-none');
            
            this.style.transform = "scale(1.2)";
            setTimeout(() => this.style.transform = "scale(1)", 200);
        });
    });

    function highlightStars(value) {
        stars.forEach(s => {
            if (s.getAttribute('data-value') <= value) {
                s.classList.add('active');
                s.classList.remove('bi-star');
                s.classList.add('bi-star-fill');
            } else {
                s.classList.remove('active');
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            }
        });
    }
});

function validateRating() {
    const val = document.getElementById('ratingInput').value;
    if (val == 0) {
        document.getElementById('ratingError').classList.remove('d-none');
        return false;
    }
    return true;
}
</script>