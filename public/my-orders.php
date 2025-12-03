<?php
session_start();
require_once '../config/database.php';
include "../views/components/header.php";
include "../views/components/navbar.php";

$user = $_SESSION['logged_in_user'] ?? null;
if (!$user || $user['role'] !== 'buyer') {
    echo "<script>window.location.href='/login.php';</script>";
    exit;
}

$buyerId = $user['id'];
$orders = [];

try {
    // SỬA SQL: Thêm sắp xếp theo PAY_DATE và PAY_TIME
    // Logic: Ưu tiên "Đã thanh toán" (DESC), sau đó đến Ngày mới nhất, Giờ mới nhất
    $sql = "SELECT o.ORDERID, o.ORD_QUANTITY, o.TOTAL_PRICE, o.SHIP_DATE, o.SHIP_TIME,
                   (
                       SELECT PAY_NUMBER 
                       FROM PAYMENTS p 
                       WHERE p.ORDERID = o.ORDERID 
                       ORDER BY p.STATUS_OF_ORDER DESC, p.PAY_DATE DESC, p.PAY_TIME DESC 
                       LIMIT 1
                   ) as FINAL_PAY_NUM,
                   (
                       SELECT STATUS_OF_ORDER 
                       FROM PAYMENTS p 
                       WHERE p.ORDERID = o.ORDERID 
                       ORDER BY p.STATUS_OF_ORDER DESC, p.PAY_DATE DESC, p.PAY_TIME DESC 
                       LIMIT 1
                   ) as FINAL_STATUS,
                   (
                       SELECT PAY_METHOD 
                       FROM PAYMENTS p 
                       WHERE p.ORDERID = o.ORDERID 
                       ORDER BY p.STATUS_OF_ORDER DESC, p.PAY_DATE DESC, p.PAY_TIME DESC 
                       LIMIT 1
                   ) as FINAL_METHOD
            FROM ORDERS o
            WHERE o.BUYERID = :bid
            ORDER BY o.SHIP_DATE DESC, o.SHIP_TIME DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([':bid' => $buyerId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Lỗi tải đơn hàng: " . $e->getMessage() . "</div>";
}
?>

<div class="container mt-5">
    <h2 class="mb-4 fw-bold border-bottom pb-2 text-primary">
        <i class="bi bi-clock-history me-2"></i>Lịch sử đơn hàng
    </h2>

    <?php if (count($orders) > 0): ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Mã thanh toán</th> <th class="text-center">Số lượng</th> 
                            <th>Tổng tiền</th>
                            <th>Ngày giao dự kiến</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td class="fw-bold text-primary">
                                    #<?= htmlspecialchars($ord['ORDERID']) ?>
                                </td>

                                <td class="font-monospace text-dark fw-bold">
                                    <?= htmlspecialchars($ord['FINAL_PAY_NUM'] ?? '---') ?>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                        <?= htmlspecialchars($ord['ORD_QUANTITY']) ?>
                                    </span>
                                </td>
                                
                                <td class="fw-bold text-danger">
                                    <?= number_format($ord['TOTAL_PRICE']) ?> ₫
                                </td>
                                
                                <td class="text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <?= date('d/m/Y', strtotime($ord['SHIP_DATE'])) ?>
                                </td>
                                
                                <td>
                                    <?php 
                                        $status = $ord['FINAL_STATUS'] ?? 'Chưa xác định';
                                        $method = $ord['FINAL_METHOD'] ?? '';
                                        
                                        $badgeClass = 'secondary';
                                        if ($status === 'Đã thanh toán') {
                                            $badgeClass = 'success';
                                        } elseif ($status === 'Chưa thanh toán') {
                                            $badgeClass = 'warning text-dark';
                                        }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> p-2">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                    <br>
                                    <small class="text-muted fst-italic">
                                        <?= htmlspecialchars($method) ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-light rounded-3">
            <i class="bi bi-box-seam display-1 text-muted opacity-25"></i>
            <h4 class="mt-3 text-secondary">Bạn chưa có đơn hàng nào</h4>
            <a href="/shop.php" class="btn btn-primary mt-3">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<?php include "../views/components/footer.php"; ?>