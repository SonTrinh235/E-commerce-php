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

$fromDate = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$toDate = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

$stats = [
    'TOTAL_REVENUE' => 0,
    'TOTAL_ORDERS' => 0
];
$error = null;

try {
    $sql = "CALL REVENUE_OF_ONE_SELLER(:sid, :from, :to)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sid', $sellerId);
    $stmt->bindParam(':from', $fromDate);
    $stmt->bindParam(':to', $toDate);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $stats = $result;
    }
    $stmt->closeCursor();

} catch (PDOException $e) {
    $error = "Chưa có dữ liệu thống kê phù hợp.";
}
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Xin chào, <span class="text-primary"><?= htmlspecialchars($seller['fullname'] ?? 'Nhà bán hàng') ?></span>! 👋</h2>
            <p class="text-muted">Đây là tổng quan tình hình kinh doanh của Shop.</p>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-0 bg-light">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Từ ngày</label>
                    <input type="date" class="form-control" name="from" value="<?= $fromDate ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Đến ngày</label>
                    <input type="date" class="form-control" name="to" value="<?= $toDate ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="bi bi-funnel"></i> Xem báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3 h-100 shadow border-0">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="d-flex justify-content-between align-items-center z-1 position-relative">
                        <div>
                            <h6 class="card-title text-uppercase bg-white bg-opacity-25 px-2 py-1 rounded d-inline-block small">Doanh Thu</h6>
                            <h2 class="display-5 fw-bold mt-2">
                                <?= number_format($stats['TOTAL_REVENUE'] ?? 0) ?> <span class="fs-4">₫</span>
                            </h2>
                            <p class="card-text small opacity-75"><i class="bi bi-check-circle-fill"></i> Đơn hàng đã xác nhận</p>
                        </div>
                        <i class="bi bi-currency-dollar position-absolute end-0 bottom-0 mb-n2 me-3" style="font-size: 6rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-info mb-3 h-100 shadow border-0">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="d-flex justify-content-between align-items-center z-1 position-relative">
                        <div>
                            <h6 class="card-title text-uppercase bg-white bg-opacity-25 px-2 py-1 rounded d-inline-block small">Đơn Hàng</h6>
                            <h2 class="display-5 fw-bold mt-2">
                                <?= number_format($stats['TOTAL_ORDERS'] ?? 0) ?>
                            </h2>
                            <p class="card-text small opacity-75"><i class="bi bi-bag-check-fill"></i> Số lượng đơn thành công</p>
                        </div>
                        <i class="bi bi-cart-check position-absolute end-0 bottom-0 mb-n2 me-3" style="font-size: 6rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($error): ?>
        <div class="alert alert-light border shadow-sm text-muted text-center py-3">
            <i class="bi bi-info-circle me-2"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <h4 class="mb-3 fw-bold text-secondary border-bottom pb-2">Quản lý Shop</h4>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="orders.php" class="card text-decoration-none h-100 border-0 shadow-sm hover-up">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="bi bi-receipt-cutoff fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Đơn Hàng</h5>
                    <p class="text-muted small">Xử lý và xác nhận đơn hàng mới</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="products.php" class="card text-decoration-none h-100 border-0 shadow-sm hover-up">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="bi bi-box-seam fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Sản Phẩm</h5>
                    <p class="text-muted small">Quản lý kho và danh sách bán</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="add-product.php" class="card text-decoration-none h-100 border-0 shadow-sm hover-up">
                <div class="card-body text-center p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="bi bi-plus-lg fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Đăng Bán</h5>
                    <p class="text-muted small">Thêm sản phẩm mới vào gian hàng</p>
                </div>
            </a>
        </div>
    </div>  
</div>

<style>
    .hover-up { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-up:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
</style>

<?php include "../../views/components/footer.php"; ?>