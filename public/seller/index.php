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
$fromDate = date('Y-m-01');
$toDate = date('Y-m-d');

if (isset($_GET['from']) && isset($_GET['to'])) {
    $fromDate = $_GET['from'];
    $toDate = $_GET['to'];
}

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
    $error = "Chưa thể tải thống kê. Hãy đảm bảo bạn đã chạy câu lệnh tạo thủ tục REVENUE_OF_ONE_SELLER trong MySQL.";
}
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Xin chào, <?= htmlspecialchars($seller['fullname'] ?? 'Nhà bán hàng') ?>! 👋</h2>
            <p class="text-muted">Đây là trang tổng quan tình hình kinh doanh của bạn.</p>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
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
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Xem thống kê
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3 h-100 shadow">
                <div class="card-header border-0 fs-5">Doanh Thu</div>
                <div class="card-body">
                    <h2 class="card-title display-5 fw-bold">
                        <?= number_format($stats['TOTAL_REVENUE'] ?? 0) ?> <span class="fs-4">VNĐ</span>
                    </h2>
                    <p class="card-text opacity-75">
                        Tổng số tiền từ các đơn hàng <span class="badge bg-light text-success">Đã xác nhận</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-info mb-3 h-100 shadow">
                <div class="card-header border-0 fs-5">Đơn Hàng Thành Công</div>
                <div class="card-body">
                    <h2 class="card-title display-5 fw-bold">
                        <?= number_format($stats['TOTAL_ORDERS'] ?? 0) ?>
                    </h2>
                    <p class="card-text opacity-75">Số lượng đơn hàng trong khoảng thời gian này.</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($error): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <h4 class="mb-3">Thao tác nhanh</h4>
    <div class="row g-3">
        <div class="col-md-6">
            <a href="products.php" class="card text-decoration-none h-100 border-primary text-primary hover-shadow">
                <div class="card-body text-center p-4">
                    <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-3">Quản lý Sản Phẩm</h5>
                    <p class="text-muted small">Xem danh sách, tìm kiếm và xóa sản phẩm</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="add-product.php" class="card text-decoration-none h-100 border-success text-success hover-shadow">
                <div class="card-body text-center p-4">
                    <i class="bi bi-plus-circle" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-3">Thêm Sản Phẩm Mới</h5>
                    <p class="text-muted small">Đăng bán sản phẩm mới lên sàn</p>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: box-shadow 0.3s ease-in-out;
        background-color: #f8f9fa;
    }
</style>

<?php include "../../views/components/footer.php"; ?>